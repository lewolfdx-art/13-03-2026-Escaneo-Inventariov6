<?php

namespace App\Filament\Resources\CodificacionResource\Pages;

use App\Filament\Resources\CodificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCodificacion extends EditRecord
{
    protected static string $resource = CodificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Redirige al índice después de guardar (update)
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}