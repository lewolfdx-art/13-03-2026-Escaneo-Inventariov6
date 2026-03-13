<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Codificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'codificacion',    // el nombre de la familia que vos escribís
        'codigo',          // el código/prefijo que vos decidís poner
    ];
}