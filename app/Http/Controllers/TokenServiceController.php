<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenServiceController extends Controller
{
    //PRODUCAO
    public const MOODLE_URL = 'seu_url';
    public const TOKEN = 'seu_token';

    //TESTE
    public const TESTE_MOODLE_URL = 'http://localhost/webservice/rest/server.php';
    public const TESTE_TOKEN = '4503edada5d32f1ccb6d2846ead72ce5';

    //SEED
    public const TOKEN_SEED = 'seu token';
    public const API_PROF = 'seu_url';
    public const API_STUD = 'seu_url';
    public const API_DIR = 'seu_url';
    public const API_TEC = 'seu_url';
}
