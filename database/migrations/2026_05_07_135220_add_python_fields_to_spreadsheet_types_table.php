<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spreadsheet_types', function (Blueprint $table) {

            $table->string('nome_funcao_python')
                ->nullable()
                ->after('manual_time');

            $table->integer('index_select')
                ->nullable()
                ->after('nome_funcao_python');

        });
    }

    public function down(): void
    {
        Schema::table('spreadsheet_types', function (Blueprint $table) {

            $table->dropColumn([
                'nome_funcao_python',
                'index_select'
            ]);

        });
    }
};