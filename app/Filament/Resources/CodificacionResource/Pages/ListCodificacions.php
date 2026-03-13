<?php

namespace App\Filament\Resources\CodificacionResource\Pages;

use App\Filament\Resources\CodificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCodificacions extends ListRecords
{
    protected static string $resource = CodificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
