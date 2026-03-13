<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CriticidadResource\Pages;
use App\Models\Criticidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CriticidadResource extends Resource
{
    protected static ?string $model = Criticidad::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Criterios Criticidad';

    protected static ?string $pluralModelLabel = 'Criterios de Criticidad';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('codigo')
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->helperText('Código corto opcional (ej: COS, FRE)'),

                Forms\Components\Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('condicion')
                    ->maxLength(100)
                    ->helperText('Ej: >=15000 USD, Uso frecuente, Sí cumple normativa, Riesgo alto'),

                Forms\Components\TextInput::make('peso')
                    ->numeric()
                    ->default(1)
                    ->helperText('Peso para cálculo automático de criticidad (1-5)'),

                Forms\Components\TextInput::make('aplica_a')
                    ->helperText('A qué familias aplica, separado por coma (opcional)'),

                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('condicion')
                    ->searchable(),

                Tables\Columns\TextColumn::make('peso')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
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
            'index'  => Pages\ListCriticidads::route('/'),
            'create' => Pages\CreateCriticidad::route('/create'),
            'edit'   => Pages\EditCriticidad::route('/{record}/edit'),
        ];
    }
}