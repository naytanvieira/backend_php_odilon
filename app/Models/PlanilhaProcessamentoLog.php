<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanilhaProcessamentoLog extends Model
{
    use HasFactory;

    protected $table = 'planilha_processamentos_logs';

    protected $fillable = [
        'nome_arquivo',
        'tipo',
        'status',
        'total_registros',
        'processados',
        'erros',
        'inicio_processamento',
        'fim_processamento',
        'tempo_execucao_ms',
        'mensagem_erro',
        'user_id',
    ];

    protected $casts = [
        'inicio_processamento' => 'datetime',
        'fim_processamento' => 'datetime',
    ];

    /**
     * Relacionamento com usuário (opcional)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spreadsheetType()
{
    return $this->belongsTo(
        SpreadsheetType::class,
        'tipo',                 // coluna da tabela logs
        'nome_funcao_python'   // coluna da spreadsheet_types
    );
}
}