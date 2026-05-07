<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spreadsheet_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->boolean('multiple_files')
                ->default(false);

            $table->string('manual_time')
                ->nullable();

            $table->string('status')
                ->default('Ativo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spreadsheet_types');
    }
};