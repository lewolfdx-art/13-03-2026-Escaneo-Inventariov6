<?php

namespace App\Filament\Resources\EstadoItemResource\Pages;

use App\Filament\Resources\EstadoItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoItems extends ListRecords
{
    protected static string $resource = EstadoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
