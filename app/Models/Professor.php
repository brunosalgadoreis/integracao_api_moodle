<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrolstatus1',
        'profile_field_rg',
        'firstname',
        'lastname',
        'profile_field_descnre',
        'city',
        'profile_field_codmec',
        'institution',
        'profile_field_codturma',
        'course1',
        'group1',
        'profile_field_codturno',
        'profile_field_turno',
        'profile_field_codserieprof',
        'profile_field_serieprof',
        'email',
        'username',
        'password',
        'data',
    ];
}
