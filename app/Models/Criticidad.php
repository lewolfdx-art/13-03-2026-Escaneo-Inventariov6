<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criticidad extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'condicion',
        'peso',
        'aplica_a',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Accesor opcional para mostrar nombre + condición
    public function getNombreConCondicionAttribute(): string
    {
        return $this->condicion 
            ? "{$this->nombre} ({$this->condicion})"
            : $this->nombre;
    }
}