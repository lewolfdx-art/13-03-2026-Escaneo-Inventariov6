<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'color',
        'prioridad',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Accesor para mostrar con color (útil en tablas)
    public function getBadgeAttribute()
    {
        return match ($this->color) {
            'success' => 'success',
            'warning' => 'warning',
            'danger'  => 'danger',
            default   => 'gray',
        };
    }
}