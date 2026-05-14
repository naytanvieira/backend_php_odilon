<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontoSaldo extends Model
{
    use HasFactory;

    protected $table = 'ponto_saldos';

    protected $fillable = [
        'user_id',
        'mes',
        'ano',
        'saldo_minutos',
        'horas_previstas',
        'horas_trabalhadas',
        'horas_extras',
        'faltas_atrasos',
    ];

    protected $casts = [
        'mes' => 'integer',
        'ano' => 'integer',
        'saldo_minutos' => 'integer',
        'horas_previstas' => 'integer',
        'horas_trabalhadas' => 'integer',
        'horas_extras' => 'integer',
        'faltas_atrasos' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getSaldoFormatadoAttribute()
    {
        return $this->formatarMinutos(
            $this->saldo_minutos
        );
    }

    public function getHorasPrevistasFormatadoAttribute()
    {
        return $this->formatarMinutos(
            $this->horas_previstas
        );
    }

    public function getHorasTrabalhadasFormatadoAttribute()
    {
        return $this->formatarMinutos(
            $this->horas_trabalhadas
        );
    }

    public function getHorasExtrasFormatadoAttribute()
    {
        return $this->formatarMinutos(
            $this->horas_extras
        );
    }

    public function getFaltasAtrasosFormatadoAttribute()
    {
        return $this->formatarMinutos(
            $this->faltas_atrasos
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMATADOR
    |--------------------------------------------------------------------------
    */

    private function formatarMinutos($minutos)
    {
        $sinal = $minutos < 0 ? '-' : '+';

        $minutos = abs($minutos);

        $horas = floor($minutos / 60);

        $mins = $minutos % 60;

        return sprintf(
            '%s%02dh %02dm',
            $sinal,
            $horas,
            $mins
        );
    }
}