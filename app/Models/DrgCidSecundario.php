<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgCidSecundario extends Model
{
    use HasFactory;

    protected $table = 'drg_cids_secundarios';

    protected $fillable = [
        'drg_id',
        'ordem',
        'codigo',
        'nome'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer'
    ];


    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
