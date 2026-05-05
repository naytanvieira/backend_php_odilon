<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('planilha_processamentos_logs', function (Blueprint $table) {
            $table->id();

            // identificação
            $table->string('nome_arquivo');
            $table->string('tipo')->nullable(); // ex: estoque, clientes, vendas

            // status do processamento
            $table->enum('status', ['pendente', 'processando', 'sucesso', 'erro'])
                  ->default('pendente');

            // métricas
            $table->integer('total_registros')->default(0);
            $table->integer('processados')->default(0);
            $table->integer('erros')->default(0);

            // tempo
            $table->timestamp('inicio_processamento')->nullable();
            $table->timestamp('fim_processamento')->nullable();
            $table->integer('tempo_execucao_ms')->nullable();

            // erro detalhado
            $table->longText('mensagem_erro')->nullable();

            // relacionamento (quem enviou)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planilha_processamentos');
    }
};