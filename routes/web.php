<?php

use App\Http\Controllers\Aluno\GetAlunoController;
use App\Http\Controllers\Diretor\PostDiretorController;
use \App\Http\Controllers\Professor\GetProfessorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Professor\PostProfessorController;
use App\Http\Controllers\Aluno\PostAlunoController;
use App\Http\Controllers\SeedController;
use App\Http\Controllers\Tecnico\PostTecnicoController;


//SEED

Route::get('/seed/', [SeedController::class, 'viewseed'])->name('seed');

Route::post('/aluno-action', [PostAlunoController::class, 'handleForm'])->name('aluno.action');
Route::post('/professor-action', [PostProfessorController::class, 'handleForm'])->name('professor.action');
Route::post('/diretor-action', [PostDiretorController::class, 'handleForm'])->name('diretor.action');
Route::post('/tecnico-action', [PostTecnicoController::class, 'handleForm'])->name('tecnico.action');

Route::post('/searchprof', [GetProfessorController::class, 'search'])->name('search.professor');
Route::post('/searchstud', [GetAlunoController::class, 'search'])->name('search.aluno');

//Route::post('/aluno-action', [SeedController::class, 'handleForm'])->name('aluno.action');


//Get Professor--
Route::get('/professor/', [GetProfessorController::class, 'viewprofessor'])->name('professor');

Route::get('/getProfessor/{date}', [GetProfessorController::class, 'getprofessor'])->name('get.professor');

Route::get('/getProfessor/{date1}/{date2}', [GetProfessorController::class, 'getprofessorDate'])->name('get.professordate');


//Post Professor--

Route::get('/importProfessor/{date}', [PostProfessorController::class, 'importprofessor'])->name('import.professor');

Route::get('/importProfessor/{date1}/{date2}', [PostProfessorController::class, 'importprofessordate']);



//Get Aluno--
Route::get('/aluno/', [GetAlunoController::class, 'viewaluno'])->name('aluno');

Route::get('/getAluno/{date}', [GetAlunoController::class, 'getAluno'])->name('get.aluno');

Route::get('/getAluno/{date1}/{date2}', [GetAlunoController::class, 'getAlunoDate'])->name('get.alunodate');


//Post Aluno--

Route::get('/importAluno/{date}', [PostAlunoController::class, 'importaluno'])->name('import.aluno');;


//Get Diretor
Route::get('/diretor/', [PostDiretorController::class, 'viewdiretor'])->name('diretor');

Route::get('/getDiretor/', [PostDiretorController::class, 'getdiretor']);


//Post Diretor

Route::get('/importDiretor/{username}', [PostDiretorController::class, 'importdiretor'])->name('import.diretor');


//Post Tecnico
Route::get('/tecnico/', [PostTecnicoController::class, 'viewtecnico'])->name('tecnico');

Route::get('/importTecnico/{username}', [PostTecnicoController::class, 'importtecnico'])->name('import.tecnico');