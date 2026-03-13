<?php

namespace App\Filament\Resources\MarcaResource\Pages;

use App\Filament\Resources\MarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMarca extends EditRecord
{
    protected static string $resource = MarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // ← Agrega esto
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Opcional: mostrar notificación más clara
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Marca actualizada')
            ->body('La marca se ha guardado correctamente.')
            ->send();
    }
}