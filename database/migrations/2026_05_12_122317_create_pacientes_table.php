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
        Schema::dropIfExists('pacientes');
        Schema::create('pacientes', function (Blueprint $table) {

            $table->id();

            $table->string('atendimento')->unique();
            $table->string('paciente')->unique();

            $table->string('recepcao')->nullable();

            $table->string('nome')->nullable();
            $table->string('telefone')->nullable();

            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();

            $table->string('sexo', 20)->nullable();

            $table->date('dt_nasc')->nullable();

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