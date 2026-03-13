<?php

namespace App\Filament\Resources\EstadoItemResource\Pages;

use App\Filament\Resources\EstadoItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstadoItem extends EditRecord
{
    protected static string $resource = EstadoItemResource::class;

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