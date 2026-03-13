<?php

namespace App\Filament\Resources\CriticidadResource\Pages;

use App\Filament\Resources\CriticidadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCriticidad extends CreateRecord
{
    protected static string $resource = CriticidadResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}