<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->string('enrolstatus1');
        $table->string('descnre');
        $table->string('city');
        $table->integer('profile_field_codmec');
        $table->string('institution');
        $table->integer('profile_field_codetapa');
        $table->string('profile_field_descetapa');
        $table->integer('profile_field_codserie');
        $table->string('profile_field_serie');
        $table->integer('profile_field_codcurso');
        $table->string('department');
        $table->string('profile_field_codturma');
        $table->string('profile_field_cgm');
        $table->string('firstname');
        $table->string('lastname');
        $table->string('username');
        $table->string('password');
        $table->string('email');
        $table->string('group1');
        $table->string('course1');
        $table->string('data');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_data');
    }
};
