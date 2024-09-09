<?php

namespace App\Http\Controllers;
use App\Models\Professor;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SeedController extends Controller
{
    public function viewSeed()
    {
        $professorMaisRecente = DB::table('professors')
            ->select('data')
            ->orderBy(DB::raw("STR_TO_DATE(data, '%d-%m-%Y')"), 'desc')
            ->first();

        $alunoMaisRecente = DB::table('students')
            ->select('data')
            ->orderBy(DB::raw("STR_TO_DATE(data, '%d-%m-%Y')"), 'desc')
            ->first();



        return view('seed', ['professorMaisRecente' => $professorMaisRecente, 'alunoMaisRecente' => $alunoMaisRecente]);
    }

    






}
