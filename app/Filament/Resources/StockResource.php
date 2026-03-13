<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Stock / Items';

    protected static ?string $pluralModelLabel = 'Stock';

    protected static ?string $modelLabel = 'Ítem de stock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información principal')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Se generará automáticamente (ej: EPP-001)')
                            ->hint('El sistema sugerirá el siguiente número según la codificación seleccionada')
                            ->disabledOn('edit') // opcional: no permitir editar en edición (seguridad)
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($state, $record, callable $set, callable $get) {
                                // En creación: si ya seleccionaron codificacion → sugerir
                                if (!$record?->exists && $get('codificacion_id')) {
                                    $sugerido = Stock::generarSiguienteCodigo($get('codificacion_id'));
                                    $set('codigo', $sugerido);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('descripcion')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('codificacion_id')
                            ->relationship('codificacion', 'codificacion', fn ($query) => $query->orderBy('codificacion'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codificacion} ({$record->codigo})")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live() // clave para reactividad
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $sugerido = Stock::generarSiguienteCodigo($state);
                                    $set('codigo', $sugerido);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('marca_id')
                            ->relationship('marca', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('medida_id')
                            ->relationship('medida', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Características específicas')
                    ->schema([
                        Forms\Components\TextInput::make('modelo')->maxLength(100),
                        Forms\Components\TextInput::make('numero_serie')->maxLength(100),
                        Forms\Components\TextInput::make('talla')->maxLength(50),
                    ])->columns(3),

                Forms\Components\Section::make('Control de stock y estado')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('stock_minimo')
                                ->numeric()
                                ->default(10)
                                ->required(),

                            Forms\Components\TextInput::make('stock_actual')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    $minimo = $get('stock_minimo') ?? 10;
                                    $set('es_critico', $state <= $minimo);
                                }),

                            Forms\Components\Toggle::make('es_critico')
                                ->label('Crítico (stock bajo)')
                                ->disabled() // se calcula automáticamente
                                ->dehydrated(true),
                        ]),

                        Forms\Components\Select::make('condicion')
                            ->options([
                                'Nuevo'         => 'Nuevo',
                                'Bueno'         => 'Bueno',
                                'Regular'       => 'Regular',
                                'Malo'          => 'Malo',
                                'En reparación' => 'En reparación',
                            ])
                            ->default('Bueno'),

                        Forms\Components\DatePicker::make('ultima_compra')
                            ->maxDate(now()),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'Activo'   => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'Baja'     => 'Baja',
                            ])
                            ->default('Activo'),

                        Forms\Components\Textarea::make('observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Mutar datos antes de crear un nuevo registro
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si el código está vacío al crear → generarlo automáticamente
        if (empty($data['codigo']) && !empty($data['codificacion_id'])) {
            $data['codigo'] = Stock::generarSiguienteCodigo($data['codificacion_id']);
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(fn ($record) => $record->descripcion),

                Tables\Columns\TextColumn::make('codificacion.codificacion')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('codificacion.codigo')
                    ->label('Cód.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('marca.nombre')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('medida.nombre')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_actual')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->stock_actual <= $record->stock_minimo ? 'danger' : null),

                Tables\Columns\TextColumn::make('stock_minimo')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('es_critico')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('condicion')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nuevo'         => 'success',
                        'Bueno'         => 'success',
                        'Regular'       => 'warning',
                        'Malo'          => 'danger',
                        'En reparación' => 'danger',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo'   => 'success',
                        'Inactivo' => 'warning',
                        'Baja'     => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('codificacion_id')
                    ->relationship('codificacion', 'codificacion')
                    ->label('Categoría')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('marca_id')
                    ->relationship('marca', 'nombre')
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('es_critico')
                    ->label('Crítico'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('codigo', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit'   => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}