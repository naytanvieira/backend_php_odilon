<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ponto_saldos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('mes');

            $table->integer('ano');

            /*
    minutos positivos ou negativos
    */

            $table->integer('saldo_minutos')
                ->default(0);

            $table->integer('horas_previstas')
                ->default(0);

            $table->integer('horas_trabalhadas')
                ->default(0);

            $table->integer('horas_extras')
                ->default(0);

            $table->integer('faltas_atrasos')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ponto_saldo');
    }
};
