<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KardexResource\Pages;
use App\Models\Kardex;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class KardexResource extends Resource
{
    protected static ?string $model = Kardex::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Kardex';
    protected static ?string $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Movimiento')
                    ->schema([
                        Forms\Components\Select::make('stock_id')
                            ->label('Stock')
                            ->relationship('stock', 'descripcion')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->codigo . ' - ' . $record->descripcion)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $stock = $state ? Stock::find($state) : null;
                                $set('codigo_mostrar', $stock?->codigo ?? '');
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('codigo_mostrar')
                            ->label('Código seleccionado')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Seleccione un stock')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('tipo_movimiento')
                            ->label('Tipo de movimiento')
                            ->options([
                                'entrada' => 'Entrada (+)',
                                'salida'  => 'Salida (-)',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Forms\Components\Select::make('medida_id')
                            ->label('Unidad de medida')
                            ->relationship('medida', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('proyecto')
                            ->label('Proyecto')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('servicio')
                            ->label('Servicio')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('area')
                            ->label('Área')
                            ->maxLength(100),

                        // CAMBIO AQUÍ: siempre visible, label y placeholder dinámicos, requerido solo en salidas
                        Forms\Components\TextInput::make('entregado_a')
                            ->label(fn (Forms\Get $get) => $get('tipo_movimiento') === 'salida'
                                ? 'Entregado a (persona o área)'
                                : 'Entregado a (persona o área)')
                            ->placeholder(fn (Forms\Get $get) => $get('tipo_movimiento') === 'salida'
                                ? 'Ej: Juan Pérez - Operario'
                                : 'Ej: Marco Hilario')
                            ->maxLength(120)
                            ->required(fn (Forms\Get $get): bool => $get('tipo_movimiento') === 'salida'),

                        Forms\Components\Textarea::make('observacion')
                            ->label('Observación')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->select('kardexes.*')
                    ->whereIn('kardexes.id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('kardexes')
                            ->groupBy('stock_id');
                    })
                    ->orderBy('fecha', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock.codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock.descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(55),

                Tables\Columns\TextColumn::make('tipo_movimiento')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'entrada' ? 'ENTRADA' : 'SALIDA')
                    ->color(fn ($state) => $state === 'entrada' ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Último movimiento')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->tipo_movimiento === 'entrada' ? 'success' : 'danger')
                    ->icon(fn ($record) => $record->tipo_movimiento === 'entrada' ? 'heroicon-o-plus' : 'heroicon-o-minus')
                    ->description(fn ($record) => $record->entregado_a ? "Entregado a: {$record->entregado_a}" : null),

                Tables\Columns\TextColumn::make('stock.stock_actual')
                    ->label('Stock actual')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->stock->stock_actual <= $record->stock->stock_minimo ? 'danger' : 'success')
                    ->description('Existencia real hoy'),

                Tables\Columns\TextColumn::make('proyecto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('servicio')
                    ->searchable(),

                Tables\Columns\TextColumn::make('area')
                    ->searchable(),

                Tables\Columns\TextColumn::make('entregado_a')
                    ->label('Entregado a')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->icon('heroicon-o-user')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('observacion')
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_movimiento')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida'  => 'Salida',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('increment')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->iconButton()
                    ->size('sm')
                    ->tooltip('Entrada rápida +1')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        self::quickStockUpdate($record->id, 'entrada', 1);
                    }),

                Tables\Actions\Action::make('decrement')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->iconButton()
                    ->size('sm')
                    ->tooltip('Salida rápida -1')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        self::quickStockUpdate($record->id, 'salida', 1);
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('fecha', 'desc')
            ->poll('60s');
    }

    public static function quickStockUpdate($kardexId, string $tipo, int $cantidad): void
    {
        $kardex = Kardex::findOrFail($kardexId);
        $stock = $kardex->stock;

        if ($tipo === 'salida' && $stock->stock_actual < $cantidad) {
            Notification::make()
                ->danger()
                ->title('Stock insuficiente')
                ->body("Disponible: {$stock->stock_actual}")
                ->persistent()
                ->send();

            return;
        }

        Kardex::create([
            'stock_id'        => $stock->id,
            'fecha'           => now(),
            'tipo_movimiento' => $tipo,
            'cantidad'        => $cantidad,
            'medida_id'       => $kardex->medida_id,
            'proyecto'        => $kardex->proyecto,
            'servicio'        => $kardex->servicio,
            'area'            => $kardex->area,
            'entregado_a'     => null, // ← aquí puedes poner un valor por defecto si quieres, o dejarlo null
            'observacion'     => $tipo === 'entrada' ? 'Entrada rápida +1' : 'Salida rápida -1',
        ]);

        Notification::make()
            ->success()
            ->title($tipo === 'entrada' ? 'Entrada +1 registrada' : 'Salida -1 registrada')
            ->send();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKardexes::route('/'),
            'create' => Pages\CreateKardex::route('/create'),
            'edit'   => Pages\EditKardex::route('/{record}/edit'),
        ];
    }
}