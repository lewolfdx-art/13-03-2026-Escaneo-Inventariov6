<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadoItemResource\Pages;
use App\Models\EstadoItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;

class EstadoItemResource extends Resource
{
    protected static ?string $model = EstadoItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Criterios de Estado';

    protected static ?string $pluralModelLabel = 'Criterios de Estado';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('slug')
                    ->maxLength(60)
                    ->unique(ignoreRecord: true)
                    ->helperText('Slug automático o manual (ej: bueno, regular)'),

                Forms\Components\Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Select::make('color')
                    ->options([
                        'success' => 'Verde (Bueno)',
                        'warning' => 'Amarillo/Naranja (Regular)',
                        'danger'  => 'Rojo (Malo)',
                        'gray'    => 'Gris (Deshecho)',
                    ])
                    ->default('gray'),

                Forms\Components\TextInput::make('prioridad')
                    ->numeric()
                    ->default(10)
                    ->helperText('Menor número = más urgente (ej: Deshecho=1, Bueno=4)'),

                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (EstadoItem $record): string => $record->color ?? 'gray')
                    ->icon(fn (EstadoItem $record): ?string => match (strtolower(trim($record->nombre))) {
                        'bueno'       => 'heroicon-o-check-circle',
                        'regular'     => 'heroicon-o-wrench-screwdriver',
                        'malo'        => 'heroicon-o-exclamation-triangle',
                        'deshecho'    => 'heroicon-o-clock',  // relojito para Deshecho
                        default       => null,
                    }),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('prioridad')
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
            'index'  => Pages\ListEstadoItems::route('/'),
            'create' => Pages\CreateEstadoItem::route('/create'),
            'edit'   => Pages\EditEstadoItem::route('/{record}/edit'),
        ];
    }
}