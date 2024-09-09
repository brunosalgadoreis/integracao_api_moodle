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
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->string('enrolstatus1');
            $table->string('profile_field_rg');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('profile_field_descnre');
            $table->string('city');
            $table->string('profile_field_codmec');
            $table->string('institution');
            $table->string('profile_field_codturma');
            $table->string('course1');
            $table->string('group1');
            $table->string('profile_field_codturno');
            $table->string('profile_field_turno');
            $table->string('profile_field_codserieprof');
            $table->string('profile_field_serieprof');
            $table->string('email');
            $table->string('username');
            $table->string('password');
            $table->string('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professors');
    }
};
