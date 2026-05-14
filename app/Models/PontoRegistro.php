<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontoRegistro extends Model
{
    use HasFactory;

    protected $table = 'ponto_registros';

    protected $fillable = [
        'user_id',
        'tipo_ponto_id',
        'tipo_justificativa_id',
        'registrado_em',
    ];

    protected $casts = [
        'registrado_em' => 'datetime',
    ];

    /* =========================
       RELATIONS
    ========================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tipoPonto()
    {
        return $this->belongsTo(
            TipoPonto::class,
            'tipo_ponto_id'
        );
    }

    public function tipoJustificativa()
    {
        return $this->belongsTo(
            TipoJustificativa::class,
            'tipo_justificativa_id'
        );
    }
}