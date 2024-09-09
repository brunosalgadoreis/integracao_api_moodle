<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\TokenServiceController;

class MoodleServiceController extends Controller
{
    public function postToMoodle($function, $key, $value)
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

        return json_decode($response->body());
    }
}

