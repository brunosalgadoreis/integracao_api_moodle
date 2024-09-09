<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'enrolstatus1',
        'descnre',
        'city',
        'profile_field_codmec',
        'institution',
        'profile_field_codetapa',
        'profile_field_descetapa',
        'profile_field_codserie',
        'profile_field_serie',
        'profile_field_codcurso',
        'department',
        'profile_field_codturma',
        'profile_field_cgm',
        'firstname',
        'lastname',
        'username',
        'password',
        'email',
        'group1',
        'course1',
        'data',
    ];
}
