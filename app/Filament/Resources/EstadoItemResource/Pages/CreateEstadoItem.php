<?php

namespace App\Filament\Resources\EstadoItemResource\Pages;

use App\Filament\Resources\EstadoItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEstadoItem extends CreateRecord
{
    protected static string $resource = EstadoItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}