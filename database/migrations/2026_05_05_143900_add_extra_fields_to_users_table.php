<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefone')->nullable()->after('email');
            $table->string('setor')->nullable()->after('telefone');
            $table->string('perfil')->nullable()->after('setor');
            $table->text('endereco')->nullable()->after('perfil');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telefone',
                'setor',
                'perfil',
                'endereco'
            ]);
        });
    }
};
