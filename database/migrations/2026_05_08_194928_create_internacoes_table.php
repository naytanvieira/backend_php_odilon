<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internacoes', function (Blueprint $table) {

            $table->id();

            // 🔗 CHAVE ESTRANGEIRA (pacientes)
            $table->string('cod_paciente')
                ->unique();
            $table->string('codigo_atendimento')
                ->unique();
            
            

            $table->string('tipo_internacao')->nullable();
            $table->string('leito')->nullable();

            $table->date('dt_interna')->nullable();
            $table->date('data_alta')->nullable();

            $table->integer('qtd_dias_int')->nullable();

            $table->string('convenio')->nullable();
            $table->string('medico')->nullable();
            $table->string('setor')->nullable();

            $table->timestamps();

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internacoes');
    }
};