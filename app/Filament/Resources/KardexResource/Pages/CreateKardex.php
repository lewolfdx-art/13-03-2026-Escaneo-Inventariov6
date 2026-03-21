<?php

namespace App\Filament\Resources\KardexResource\Pages;

use App\Filament\Resources\KardexResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKardex extends CreateRecord
{
    protected static string $resource = KardexResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
        // O también: KardexResource::getUrl('index');
    }
}