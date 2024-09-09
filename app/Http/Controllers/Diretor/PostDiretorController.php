<?php

namespace App\Http\Controllers\Diretor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MoodleServiceController;
use App\Http\Controllers\TokenServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;
use Symfony\Polyfill\Intl\Normalizer\Normalizer as NormalizerNormalizer;

class PostDiretorController extends Controller
{

    protected $moodleService;

    public function __construct(MoodleServiceController $moodleService)
    {
        $this->moodleService = $moodleService;
    }
    public function viewDiretor()
    {
        return view('diretor');
    }
    public function getDiretor()
    {
        set_time_limit(5000);
        $response = Http::withToken(TokenServiceController::TOKEN_SEED)->timeout(0)->get(TokenServiceController::API_DIR);

        if ($response->failed()) {
            return response()->json(['error' => 'Falha ao obter dados.'], 500);
        }

        return response()->json(json_decode($response->body(), true));
    }

    public function importDiretor($username)
    {
        date_default_timezone_set('America/Sao_Paulo');
        set_time_limit(5000);

        $diretorResponse = $this->getDiretor();
        $data = $diretorResponse->getData(true);

        $successCount = 0;

        $html = "<html lang='en'>
        <head>
              <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
        </head>
        <div class='container px-2'>
        <table border='1' class='table table-sm'>
                <tr>
                    <th>EnrolStatus</th>
                    <th>ID Diretor</th>
                    <th>Diretor</th>
                    <th>ID Curso</th>
                    <th>Curso</th>
                    <th>ID Grupo</th>
                    <th>Grupo</th>
                </tr>";

        foreach ($data as $usr) {

            if ($usr['username'] === $username && $usr['enrolstatus1'] === '0') {
                $paramUsers = $this->formatUserParameters($usr);

                $usrId = $this->insertUser($paramUsers);
                $courseId = $this->getCourse($usr['course1']);
                $groupId = $this->getGroup($courseId, $usr['group1']);
                $this->addCohort($usrId);
                $enrolmentStatus = $this->enrolUser($usrId, $courseId, $usr['enrolstatus1'], $groupId);

                try {


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

            }

        }

        $html .= "</table>" . 'Total: ' . $successCount . ' Data: ' . $username;
        "</div>";
        //return $html;
        return view('diretor', compact('html'));
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
                    'country' => 'BR',
                    'customfields' => [
                        ['type' => 'datadeinscricao', 'value' => date('d/m/Y')],
                        ['type' => 'rg', 'value' => $userData['profile_field_rg']],
                        ['type' => 'codmec', 'value' => $userData['profile_field_codmec']],
                        ['type' => 'codturma', 'value' => $userData['profile_field_codturma']],
                        ['type' => 'turno', 'value' => trim($userData['profile_field_turno'])],
                        ['type' => 'codcurso', 'value' => $userData['profile_field_codcurso']],
                        ['type' => 'codserie', 'value' => $userData['profile_field_codserie']],
                        ['type' => 'serie', 'value' => $userData['profile_field_serie']]
                    ]
                ]
            ]
        ];
    }

    // private function postToMoodle($function, $key, $value)
    // {
    //     $response = Http::asForm()->post(TokenServiceController::MOODLE_URL, [
    //         'wstoken' => TokenServiceController::TOKEN,
    //         'wsfunction' => $function,
    //         'moodlewsrestformat' => 'json',
    //         $key => $value,
    //     ]);

    //     if ($response->failed()) {
    //         throw new Exception('Falha na comunicação com a API do Moodle.');
    //     }

    //     return json_decode($response->body()); // Decodificar JSON para objetos
    // }

    private function insertUser($paramUsers)
    {
        $functionCreate = 'core_user_create_users';
        $key = 'users';
        $value = $paramUsers['users'];

        $usr = $this->moodleService->postToMoodle($functionCreate, $key, $value);
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

        $user = $this->moodleService->postToMoodle($functionGetUser, $key, $value);

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

        $allCourses = $this->moodleService->postToMoodle($functionGetCourse, null, null);

        return $this->findCourseIdByShortname($allCourses, $courseShortname);

    }

    private function enrolUser($usrId, $courseId, $enrolStatus, $groupId)
    {
        $functionEnrol = 'enrol_manual_enrol_users';
        $paramsEnrol = [
            'enrolments' => [
                [
                    'roleid' => 13,
                    'userid' => $usrId,
                    'courseid' => $courseId,
                    'suspend' => $enrolStatus,
                ],
            ]
        ];

        $this->moodleService->postToMoodle($functionEnrol, 'enrolments', $paramsEnrol['enrolments']);

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

        $groups = $this->moodleService->postToMoodle($functionGetGroup, $key, $value);

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

        $data = $this->moodleService->postToMoodle($functionCreateGroup, $key, $value);

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

        $this->moodleService->postToMoodle($functionAddGroup, 'members', $paramsAddGroup['members']);
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

        $this->moodleService->postToMoodle($functionDelGroup, 'members', $paramsDelGroup['members']);
    }

    public function addCohort($usrId)
    {
        $functionAddCohort = 'core_cohort_add_cohort_members';
        $paramsAddCohort = [
            'members' => [
                [
                    'cohorttype' => [
                        'type' => 'id',
                        'value' => 8,
                    ],
                    'usertype' => [
                        'type' => 'id',
                        'value' => $usrId,
                    ],
                ],
            ]
        ];

        $this->moodleService->postToMoodle($functionAddCohort, 'members', $paramsAddCohort['members']);
    }

    public function handleForm(Request $request)
    {
        $username = $request->input('username');
        $action = $request->input('action');

        // Redirecionar para a rota correta


        if ($action === 'import') {
            return redirect()->route('import.diretor', ['username' => $username]);
        }


        return back()->withErrors(['message' => 'Ação inválida selecionada.']);
    }
}
