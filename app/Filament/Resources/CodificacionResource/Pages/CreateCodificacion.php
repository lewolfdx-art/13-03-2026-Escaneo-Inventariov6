<?php

namespace App\Filament\Resources\CodificacionResource\Pages;

use App\Filament\Resources\CodificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCodificacion extends CreateRecord
{
    protected static string $resource = CodificacionResource::class;

    // Redirige al índice después de guardar
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}