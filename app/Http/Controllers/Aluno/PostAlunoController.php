<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TokenServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;
use Symfony\Polyfill\Intl\Normalizer\Normalizer as NormalizerNormalizer;

class PostAlunoController extends Controller
{

    public function getAluno($date)
    {
        $response = Http::withToken(TokenServiceController::TOKEN_SEED)->get(TokenServiceController::API_STUD . $date);

        if ($response->failed()) {
            return response()->json(['error' => 'Falha ao obter dados.'], 500);
        }

        return response()->json(json_decode($response->body(), true));
    }

    public function importAluno(Request $request, $date)
    {
        date_default_timezone_set('America/Sao_Paulo');
        set_time_limit(5000);
        //$date = '23-08-2024';
        $alunoResponse = $this->getAluno($date);//date('d-m-Y', strtotime('-1 day'))
        $data = $alunoResponse->getData(true);

        $successCount = 0;

        $csvFilePath = storage_path('app/public/aluno'.$date.'.csv');
        $csvFile = fopen($csvFilePath, 'w');

        fputcsv($csvFile, ['EnrolStatus', 'ID Aluno', 'Aluno', 'ID Curso', 'Curso', 'ID Grupo', 'Grupo']);

        $html = "<html lang='en'>
        <head>
              <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
        </head>
        <div class='container px-2'>
        <table border='1' class='table table-sm'>
                <tr>
                    <th>EnrolStatus</th>
                    <th>ID Aluno</th>
                    <th>Aluno</th>
                    <th>ID Curso</th>
                    <th>Curso</th>
                    <th>ID Grupo</th>
                    <th>Grupo</th>
                </tr>";

        foreach ($data as $usr) {
            $paramUsers = $this->formatUserParameters($usr);

            try {
                $usrId = $this->insertUser($paramUsers);
                $courseId = $this->getCourse($usr['course1']);
                $groupId = $this->getGroup($courseId, $usr['group1']);
                $enrolmentStatus = $this->enrolUser($usrId, $courseId, $usr['enrolstatus1'], $groupId);

                $html .= "<tr>
                    <td>{$usr['enrolstatus1']}</td>
                    <td>{$usrId}</td>
                    <td>{$usr['username']}</td>
                    <td>{$courseId}</td>
                    <td>{$usr['course1']}</td>
                    <td>{$groupId}</td>
                    <td>{$usr['group1']}</td>
                  </tr>";

                if ($enrolmentStatus != null) {
                    $successCount++;
                }

            } catch (Exception $e) {
                logger()->error($e->getMessage());
                return response()->json(['error' => 'Ocorreu um erro durante a matrícula.' . $e], 500);
            }

            fputcsv($csvFile, [$usr['enrolstatus1'], $usrId, $usr['username'], $courseId, $usr['course1'], $groupId, $usr['group1']]);

        }
        fclose($csvFile);
        //response()->download($csvFilePath);

        $html .= "</table>" . 'Total: ' . $successCount . ' Data: ' . $date;
        "</div>";
        //return $html;
        return view('professor', compact('html'));
    }

    private function formatUserParameters($userData)
    {
        return [
            'users' => [
                [
                    'firstname' => $userData['firstname'],
                    'lastname' => $userData['lastname'],
                    'city' => $userData['city'],
                    'email' => $userData['email'],
                    'username' => $userData['username'],
                    'password' => $userData['password'],
                    'institution' => $userData['institution'],
                    'department' => $userData['department'],
                    'country' => 'BR',
                    'customfields' => [
                        ['type' => 'datadeinscricao', 'value' => date('d/m/Y')],
                        ['type' => 'codmec', 'value' => $userData['profile_field_codmec']],
                        ['type' => 'codturma', 'value' => $userData['profile_field_codturma']],
                        ['type' => 'codetapa', 'value' => $userData['profile_field_codetapa']],
                        ['type' => 'descetapa', 'value' => $userData['profile_field_descetapa']],
                        ['type' => 'codserie', 'value' => $userData['profile_field_codserie']],
                        ['type' => 'serie', 'value' => $userData['profile_field_serie']],
                        ['type' => 'codcurso', 'value' => $userData['profile_field_codcurso']],
                        ['type' => 'cgm', 'value' => $userData['profile_field_cgm']],

                    ]
                ]
            ]
        ];
    }

    private function postToMoodle($function, $key, $value)
    {
        $response = Http::asForm()->post(TokenServiceController::MOODLE_URL, [
            'wstoken' => TokenServiceController::TOKEN,
            'wsfunction' => $function,
            'moodlewsrestformat' => 'json',
            $key => $value,
        ]);

        if ($response->failed()) {
            throw new Exception('Falha na comunicação com a API do Moodle.');
        }

        return json_decode($response->body()); // Decodificar JSON para objetos
    }

    private function insertUser($paramUsers)
    {
        $functionCreate = 'core_user_create_users';
        $key = 'users';
        $value = $paramUsers['users'];

        $usr = $this->postToMoodle($functionCreate, $key, $value);
        $usrname = $paramUsers['users'][0]['username'];

        if (isset($usr->exception)) {
            $usrid = $this->getUser($usrname);
            //$this->updateUser($paramUsers, $usrid);
            return $usrid;
        } else {
            return $usr[0]->id;
        }
    }

    private function getUser($username)
    {
        $functionGetUser = 'core_user_get_users';
        $paramsGetUser = ['criteria' => [['key' => 'username', 'value' => $username]]];

        $key = 'criteria';
        $value = $paramsGetUser['criteria'];

        $user = $this->postToMoodle($functionGetUser, $key, $value);

        return $user->users[0]->id;
    }

    private function normalizeString($string)
    {
        // Converte para minúsculas
        $string = mb_strtolower($string);

        // Remove acentos
        $string = NormalizerNormalizer::normalize($string, NormalizerNormalizer::FORM_D);
        $string = preg_replace('/[\p{Mn}]/u', '', $string);

        return $string;
    }

    private function findCourseIdByShortname($courses, $shortname)
    {
        // Converte o shortname de entrada para minúsculas e remove acentos
        $normalizedShortname = $this->normalizeString($shortname);

        foreach ($courses as $course) {
            // Converte o shortname do curso para minúsculas e remove acentos
            $normalizedCourseShortname = $this->normalizeString($course->shortname);

            // Compara os shortnames normalizados
            if ($course->visible == 1 && $normalizedCourseShortname === $normalizedShortname) {
                return $course->id;
            }
        }
    }

    private function getCourse($courseShortname)
    {
        $functionGetCourse = 'core_course_get_courses';

        $allCourses = $this->postToMoodle($functionGetCourse, null, null);

        return $this->findCourseIdByShortname($allCourses, $courseShortname);

    }

    private function enrolUser($usrId, $courseId, $enrolStatus, $groupId)
    {
        $functionEnrol = 'enrol_manual_enrol_users';
        $paramsEnrol = [
            'enrolments' => [
                [
                    'roleid' => 5,
                    'userid' => $usrId,
                    'courseid' => $courseId,
                    'suspend' => $enrolStatus,
                ],
            ]
        ];

        $this->postToMoodle($functionEnrol, 'enrolments', $paramsEnrol['enrolments']);

        if ($enrolStatus === '0') {
            $this->addGroup($groupId, $usrId);
        } elseif ($enrolStatus === '1') {
            $this->deleteGroup($groupId, $usrId);
        }

        return 'Aluno Id= ' . $usrId . ' atualizado no curso: ' . $courseId . ' grupo: ' . $groupId;
    }

    private function findGroupIdByName($groups, $name)
    {
        foreach ($groups as $group) {
            if (is_object($group) && isset($group->name) && $group->name === $name) {
                return $group->id;
            }
        }

        return null;
    }

    private function getGroup($courseId, $groupName)
    {
        $functionGetGroup = 'core_group_get_course_groups';
        $key = 'courseid';
        $value = $courseId;

        $groups = $this->postToMoodle($functionGetGroup, $key, $value);

        $groupId = $this->findGroupIdByName($groups, $groupName);

        if ($groupId === null) {
            return $this->createGroup($courseId, $groupName);
        }

        return $groupId;
    }

    // private function updateUser($paramUsers, $id)
    // {
    //     $functionUpdate = 'core_user_update_users';
    //     $updateUsers = array_merge(['id' => $id], $paramUsers['users'][0]);
    //     $paramUsers['users'][0] = $updateUsers;

    //     $this->postToMoodle($functionUpdate, 'users', $paramUsers['users']);
    // }

    private function createGroup($courseId, $groupName)
    {
        $functionCreateGroup = 'core_group_create_groups';
        $paramsCgroup = [
            'groups' => [
                [
                    'courseid' => $courseId,
                    'name' => $groupName,
                    'description' => $groupName,
                ],
            ]
        ];

        $key = 'groups';
        $value = $paramsCgroup['groups'];

        $data = $this->postToMoodle($functionCreateGroup, $key, $value);

        if (is_array($data) && isset($data[0]->id)) {
            return $data[0]->id;
        } else {
            return 'Erro: Não foi possível criar o grupo.';
        }
    }

    private function addGroup($groupId, $usrId)
    {
        $functionAddGroup = 'core_group_add_group_members';
        $paramsAddGroup = [
            'members' => [
                [
                    'groupid' => $groupId,
                    'userid' => $usrId,
                ],
            ]
        ];

        $this->postToMoodle($functionAddGroup, 'members', $paramsAddGroup['members']);
    }

    private function deleteGroup($groupId, $usrId)
    {
        $functionDelGroup = 'core_group_delete_group_members';
        $paramsDelGroup = [
            'members' => [
                [
                    'groupid' => $groupId,
                    'userid' => $usrId,
                ],
            ]
        ];

        $this->postToMoodle($functionDelGroup, 'members', $paramsDelGroup['members']);
    }

    public function handleForm(Request $request)
    {
        $date = $request->input('date');
        $action = $request->input('action');

        // Redirecionar para a rota correta


        if ($action === 'get') {
            return redirect()->route('get.aluno', ['date' => $date]);
        } elseif ($action === 'import') {
            return redirect()->route('import.aluno', ['date' => $date]);
        }


        return back()->withErrors(['message' => 'Ação inválida selecionada.']);
    }

}


