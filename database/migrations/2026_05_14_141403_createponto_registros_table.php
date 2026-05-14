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
        Schema::create('tipos_ponto', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->timestamps();
        });

        Schema::create('tipos_justificativa', function (Blueprint $table) {
            $table->id();

            $table->text('name');

            $table->timestamps();
        });

        Schema::create('ponto_registros', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('tipo_ponto_id')
                ->constrained('tipos_ponto')
                ->cascadeOnDelete();

            $table->foreignId('tipo_justificativa_id')
                ->nullable()
                ->constrained('tipos_justificativa')
                ->cascadeOnDelete();

            $table->timestamp('registrado_em');

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
        Schema::dropIfExists('ponto_registros');
        Schema::dropIfExists('tipo_justificativa');
        Schema::dropIfExists('tipo_ponto');
    }
};
