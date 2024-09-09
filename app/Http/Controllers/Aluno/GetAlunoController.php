<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TokenServiceController;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GetAlunoController extends Controller
{

    public function viewAluno()
    {
        return view('aluno');
    }
    public function getAluno(Request $request, $date)
    {
        set_time_limit(10000);

        $query = DB::table('students')->where('data', $date)->first();
        if (isset($query)) {
            $response = '<div class="alert alert-danger" role="alert"> Data já importada ' . $date . '</div>';
            return view('aluno', compact('response'));
        }

        $request = Http::withToken(TokenServiceController::TOKEN_SEED)->get(TokenServiceController::API_STUD . $date);
        $json = response()->json(json_decode($request->body(), true));
        $data = $json->getData(true);

        foreach ($data as $item) {
            Student::create([
                'enrolstatus1' => $item['enrolstatus1'],
                'descnre' => $item['descnre'],
                'city' => $item['city'],
                'profile_field_codmec' => $item['profile_field_codmec'],
                'institution' => $item['institution'],
                'profile_field_codetapa' => $item['profile_field_codetapa'],
                'profile_field_descetapa' => $item['profile_field_descetapa'],
                'profile_field_codserie' => $item['profile_field_codserie'],
                'profile_field_serie' => $item['profile_field_serie'],
                'profile_field_codcurso' => $item['profile_field_codcurso'],
                'department' => $item['department'],
                'profile_field_codturma' => $item['profile_field_codturma'],
                'profile_field_cgm' => $item['profile_field_cgm'],
                'firstname' => $item['firstname'],
                'lastname' => $item['lastname'],
                'username' => $item['username'],
                'password' => $item['password'],
                'email' => $item['email'],
                'group1' => $item['group1'],
                'course1' => $item['course1'],
                'data' => $date,
            ]);
        }

        $response = '<div class="alert alert-success" role="alert">Dados salvos com sucesso! ALUNO: ' . $date . '</div>';
        return view('aluno', ['response' => $response]);

    }

    public function getAlunoDate(Request $request, $date1, $date2)
    {

        $query = DB::table('students')->where('data', $date1)->first();
        if (isset($query)) {
            return 'Data já importada ' . $date1;
        }

        $startDate = $date1;
        $endDate = $date2;

        $currentDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        while ($currentDate <= $endDate) {
            $formattedDate = date('d-m-Y', $currentDate);

            $request = Http::withToken(TokenServiceController::TOKEN_SEED)->get(TokenServiceController::API_STUD . $formattedDate);
            $json = response()->json(json_decode($request->body(), true));
            $data = $json->getData(true);


            foreach ($data as $item) {
                Student::create([
                    'enrolstatus1' => $item['enrolstatus1'],
                    'descnre' => $item['descnre'],
                    'city' => $item['city'],
                    'profile_field_codmec' => $item['profile_field_codmec'],
                    'institution' => $item['institution'],
                    'profile_field_codetapa' => $item['profile_field_codetapa'],
                    'profile_field_descetapa' => $item['profile_field_descetapa'],
                    'profile_field_codserie' => $item['profile_field_codserie'],
                    'profile_field_serie' => $item['profile_field_serie'],
                    'profile_field_codcurso' => $item['profile_field_codcurso'],
                    'department' => $item['department'],
                    'profile_field_codturma' => $item['profile_field_codturma'],
                    'profile_field_cgm' => $item['profile_field_cgm'],
                    'firstname' => $item['firstname'],
                    'lastname' => $item['lastname'],
                    'username' => $item['username'],
                    'password' => $item['password'],
                    'email' => $item['email'],
                    'group1' => $item['group1'],
                    'course1' => $item['course1'],
                    'data' => $formattedDate,
                ]);
            }
            $currentDate = strtotime('+1 day', $currentDate);
        }
        return response()->json(['message' => 'Dados salvos com sucesso! ']);
    }

    public function search(Request $request)
    {

        $professorMaisRecente = DB::table('professors')
            ->select('data')
            ->orderBy(DB::raw("STR_TO_DATE(data, '%d-%m-%Y')"), 'desc')
            ->first();

        $alunoMaisRecente = DB::table('students')
            ->select('data')
            ->orderBy(DB::raw("STR_TO_DATE(data, '%d-%m-%Y')"), 'desc')
            ->first();



        $username = $request->input('username');

        // Faz a consulta no banco de dados com base no username
        $users = Student::where('username', $username)->get();

        if ($users->isNotEmpty()) {
            $html = '<table border="1">
                        <thead>
                            <tr>
                                <th>Enrolstatus</th>
                                <th>Nome</th>
                                <th>Cidade</th>
                                <th>Instituição</th>
                                <th>Curso</th>
                                <th>Grupo</th>
                                <th>Username</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>';

            foreach ($users as $user) {
                $html .= '<tr>
                            <td>' . $user->enrolstatus1 . '</td>
                            <td>' . $user->firstname . ' ' . $user->lastname . '</td>
                            <td>' . $user->city . '</td>
                            <td>' . $user->institution . '</td>
                            <td>' . $user->course1 . '</td>
                            <td>' . $user->group1 . '</td>
                            <td>' . $user->username . '</td>
                            <td>' . $user->data . '</td>
                          </tr>';
            }

            $html .= '</tbody></table>';

            return view('seed', ['professorMaisRecente' => $professorMaisRecente, 'alunoMaisRecente' => $alunoMaisRecente], compact('html'), );

        } else {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }
    }


}