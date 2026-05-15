<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgCateterCentral extends Model
{
    use HasFactory;

    protected $table = 'drg_cateteres_centrais';

    protected $fillable = [
        'drg_id',
        'ordem',
        'local',
        'data_inicial',
        'data_final'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer'
    ];


    public function condicoes(): HasMany
    {
        return $this->hasMany(DrgCateterCondicao::class, 'cateter_central_id')->orderBy('contexto')->orderBy('ordem');
    }

    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
