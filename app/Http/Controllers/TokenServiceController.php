<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenServiceController extends Controller
{
    //PRODUCAO
    public const MOODLE_URL = 'apiMoodle';
    public const TOKEN = 'tokenMoodle';

    //TESTE
    public const TESTE_MOODLE_URL = 'http://localhost/webservice/rest/server.php';
    public const TESTE_TOKEN = '4503edada5d32f1ccb6d2846ead72ce5';

    //SEED
    public const TOKEN_SEED = 'TokenSeed';
    public const API_PROF = 'https://api-eja-homolog.seed.pr.gov.br/Teacher/GetProfessor/';
    public const API_STUD = 'https://api-eja-homolog.seed.pr.gov.br/Student/GetAluno/';
    public const API_DIR = 'https://api-eja-homolog.seed.pr.gov.br/Diretor/GetDiretor/3';
    public const API_TEC = 'https://api-eja-homolog.seed.pr.gov.br/Nre/GetAssistente/4';
}
