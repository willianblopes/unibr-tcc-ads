﻿<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriarTabelaUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('usuario', function (Blueprint $table) {
            
            // Básico
            $table->increments('cd_usuario');
            $table->string('nm_usuario', 50);
            $table->string('nm_email', 50)->unique();
            $table->string('nm_senha', 50);
            
            // Chaves estrangeiras
            $table->integer('cd_departamento')->unsigned();
            $table->foreign('fk_departamento_usuario')->references('cd_departamento')->on('departamento');
            
            $table->rememberToken();
            $table->timestamps();
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}
