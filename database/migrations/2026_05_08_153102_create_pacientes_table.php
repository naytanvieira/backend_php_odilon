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
        Schema::create('pacientes', function (Blueprint $table) {

            $table->id();

            // código único do paciente
            $table->string('cod_paciente')
                ->unique();

            // dados pessoais
            $table->string('nome');

            $table->string('cpf')
                ->nullable();

            $table->string('rg')
                ->nullable();

            $table->string('sexo', 20)
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};