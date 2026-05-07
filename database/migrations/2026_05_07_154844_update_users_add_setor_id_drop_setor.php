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
        Schema::table('users', function (Blueprint $table) {

            // remove coluna antiga
            $table->dropColumn('setor');

            // nova FK
            $table->foreignId('setor_id')
                ->nullable()
                ->constrained('sectors')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // remove FK
            $table->dropForeign(['setor_id']);
            $table->dropColumn('setor_id');

            // recria coluna antiga
            $table->string('setor')->nullable();
        });
    }
};