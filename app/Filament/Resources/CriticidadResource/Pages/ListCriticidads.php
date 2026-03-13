<?php

namespace App\Filament\Resources\CriticidadResource\Pages;

use App\Filament\Resources\CriticidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCriticidads extends ListRecords
{
    protected static string $resource = CriticidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
