<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CodificacionResource\Pages;
use App\Models\Codificacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CodificacionResource extends Resource
{
    protected static ?string $model = Codificacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationLabel = 'Codificaciones';

    protected static ?string $pluralModelLabel = 'Codificaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codificacion')
                    ->label('Codificación / Familia')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)
                    ->placeholder('Ej: EPPs, Útiles de escritorio, Herramientas, Insumos')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('codigo')
                    ->label('Código / Prefijo')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->placeholder('Ej: EPP-, UES-, HER-, INS-')
                    ->helperText('Este es el código que vos decidís asignar (lo escribís manualmente)')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codificacion')
                    ->label('Codificación / Familia')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código / Prefijo')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCodificacions::route('/'),
            'create' => Pages\CreateCodificacion::route('/create'),
            'edit'   => Pages\EditCodificacion::route('/{record}/edit'),
        ];
    }
}