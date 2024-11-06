<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Http\Controllers\TokenServiceController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetProfessorController extends Controller
{

    public function viewProfessor()
    {
        return view('professor');
    }
    public function getprofessor(Request $request, $date)
    {

        set_time_limit(10000);

        $query = DB::table('professors')->where('data', $date)->first();
        if (isset($query)) {
            $response = '<div class="alert alert-danger" role="alert"> Data já importada ' . $date . '</div>';
            return view('professor', compact('response'));
        }

        $request = Http::withToken(TokenServiceController::TOKEN_SEED)->get(TokenServiceController::API_PROF . $date);
        $json = response()->json(json_decode($request->body(), true));
        $data = $json->getData(true);

        foreach ($data as $item) {
            Professor::create([
                'enrolstatus1' => $item['enrolstatus1'],
                'profile_field_rg' => $item['profile_field_rg'],
                'firstname' => $item['firstname'],
                'lastname' => $item['lastname'],
                'profile_field_descnre' => $item['profile_field_descnre'],
                'city' => $item['city'],
                'profile_field_codmec' => $item['profile_field_codmec'],
                'institution' => $item['institution'],
                'profile_field_codturma' => $item['profile_field_codturma'],
                'course1' => $item['course1'],
                'group1' => $item['group1'],
                'profile_field_codturno' => $item['profile_field_codturno'],
                'profile_field_turno' => $item['profile_field_turno'],
                'profile_field_codserieprof' => $item['profile_field_codserieprof'],
                'profile_field_serieprof' => $item['profile_field_serieprof'],
                'email' => $item['email'],
                'username' => $item['username'],
                'password' => $item['password'],
                'data' => $date,
            ]);
        }

        $response = '<div class="alert alert-success" role="alert">Dados salvos com sucesso! PROFESSOR: ' . $date . '</div>';
        return view('professor', ['response' => $response]);

    }

    public function getProfessorDate(Request $request, $date1, $date2)
    {

        $query = DB::table('professors')->where('data', $date1)->first();
        if (isset($query)) {
            $response = '<div class="alert alert-danger" role="alert"> Data já importada ' . $date1 . '</div>';
            return view('professor', compact('response'));
        }

        $startDate = $date1;
        $endDate = $date2;

        $currentDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        while ($currentDate <= $endDate) {
            $formattedDate = date('d-m-Y', $currentDate);

            $request = Http::withToken(TokenServiceController::TOKEN_SEED)->get(TokenServiceController::API_PROF . $formattedDate);
            $json = response()->json(json_decode($request->body(), true));
            $data = $json->getData(true);

            foreach ($data as $item) {
                Professor::create([
                    'enrolstatus1' => $item['enrolstatus1'],
                    'profile_field_rg' => $item['profile_field_rg'],
                    'firstname' => $item['firstname'],
                    'lastname' => $item['lastname'],
                    'profile_field_descnre' => $item['profile_field_descnre'],
                    'city' => $item['city'],
                    'profile_field_codmec' => $item['profile_field_codmec'],
                    'institution' => $item['institution'],
                    'profile_field_codturma' => $item['profile_field_codturma'],
                    'course1' => $item['course1'],
                    'group1' => $item['group1'],
                    'profile_field_codturno' => $item['profile_field_codturno'],
                    'profile_field_turno' => $item['profile_field_turno'],
                    'profile_field_codserieprof' => $item['profile_field_codserieprof'],
                    'profile_field_serieprof' => $item['profile_field_serieprof'],
                    'email' => $item['email'],
                    'username' => $item['username'],
                    'password' => $item['password'],
                    'data' => $formattedDate,
                ]);
            }
            $currentDate = strtotime('+1 day', $currentDate);
        }
        $response = '<div class="alert alert-success" role="alert">Dados salvos com sucesso! PROFESSOR: ' . $date1 . ' a ' . $date2 . '</div>';
        return view('professor', ['response' => $response]);
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
        $users = Professor::where('username', $username)->get();

        //return view('seed', ['users' => $users, 'professorMaisRecente' => $professorMaisRecente, 'alunoMaisRecente' => $alunoMaisRecente]);


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
