<?php

namespace App\Filament\Resources\CriticidadResource\Pages;

use App\Filament\Resources\CriticidadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCriticidad extends EditRecord
{
    protected static string $resource = CriticidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}