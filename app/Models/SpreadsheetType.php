<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpreadsheetType extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'multiple_files',
    'manual_time',
    'status',
    'nome_funcao_python',
    'index_select',
];
}