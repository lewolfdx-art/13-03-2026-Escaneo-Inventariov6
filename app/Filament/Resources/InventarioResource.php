<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioResource\Pages;
use App\Models\Inventario;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Inventarios / Conteos';
    protected static ?string $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Conteo de Inventario')
                    ->schema([
                        // Fecha del Conteo movida al inicio
                        Forms\Components\DatePicker::make('fecha_conteo')
                            ->label('Fecha del Conteo')
                            ->default(now())
                            ->disabled()
                            ->dehydrated(true)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('stock_id')
                            ->label('Ítem de Stock *')
                            ->relationship('stock', 'descripcion')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->codigo . ' - ' . $record->descripcion)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $stock = Stock::find($state);
                                $set('codigo_mostrar', $stock?->codigo ?? '');
                                $set('descripcion_mostrar', $stock?->descripcion ?? '');
                                $set('marca_mostrar', $stock?->marca?->nombre ?? '—');
                                $set('modelo', $stock?->modelo ?? '');
                                $set('numero_serie', $stock?->numero_serie ?? '');
                                $set('talla', $stock?->talla ?? '');
                                $set('unidad_medida_mostrar', $stock?->medida?->nombre ?? '—');
                                $set('tiene_codigo_barras', $stock?->codigo ? true : false);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('codigo_mostrar')
                            ->label('Código')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('descripcion_mostrar')
                            ->label('Descripción')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('marca_mostrar')
                                ->label('Marca')
                                ->disabled()
                                ->dehydrated(false),

                            Forms\Components\TextInput::make('modelo')
                                ->label('Modelo')
                                ->maxLength(100),

                            Forms\Components\TextInput::make('numero_serie')
                                ->label('N/S (Número de Serie)')
                                ->maxLength(100),

                            Forms\Components\TextInput::make('talla')
                                ->label('Talla')
                                ->maxLength(50),

                            Forms\Components\TextInput::make('unidad_medida_mostrar')
                                ->label('Unidad de Medida')
                                ->disabled()
                                ->dehydrated(false),

                            Forms\Components\Toggle::make('tiene_codigo_barras')
                                ->label('Tiene código de barras')
                                ->inline(false)
                                ->hint('Activar si este ítem usa código de barras para escaneo rápido'),
                        ]),

                        // Vista previa del código de barras - ahora con HtmlString
                        Forms\Components\Placeholder::make('barcode_preview')
                            ->label('Vista previa de código de barras')
                            ->content(function (Forms\Get $get): HtmlString {
                                $stockId = $get('stock_id');

                                if (!filled($stockId)) {
                                    return new HtmlString(
                                        '<p class="text-sm text-gray-500 dark:text-gray-400 italic">Seleccione un ítem de stock primero.</p>'
                                    );
                                }

                                $stock = Stock::find($stockId);

                                if (!$stock) {
                                    return new HtmlString(
                                        '<p class="text-sm text-red-600 dark:text-red-400">Ítem no encontrado.</p>'
                                    );
                                }

                                if (empty($stock->codigo)) {
                                    return new HtmlString(
                                        '<p class="text-sm text-gray-500 dark:text-gray-400 italic">Este ítem no tiene código de barras registrado.</p>'
                                    );
                                }

                                // Renderizamos la vista blade y la devolvemos como HTML seguro
                                $renderedView = view('filament.inventario.barcode-preview', [
                                    'stock' => $stock,
                                ])->render();

                                return new HtmlString($renderedView);
                            })
                            ->visible(fn (Forms\Get $get): bool => filled($get('stock_id')) && $get('tiene_codigo_barras')),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones / Diferencias')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_conteo')
                    ->label('FECHA')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock.codigo')
                    ->label('CÓDIGO')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock.descripcion')
                    ->label('DESCRIPCIÓN')
                    ->searchable()
                    ->limit(55),

                Tables\Columns\TextColumn::make('stock.medida.nombre')
                    ->label('UNIDAD MEDIDA'),

                Tables\Columns\TextColumn::make('stock.codificacion.codificacion')
                    ->label('FAMILIA'),

                Tables\Columns\TextColumn::make('marca')
                    ->label('MARCA'),

                Tables\Columns\TextColumn::make('modelo')
                    ->label('MODELO'),

                Tables\Columns\TextColumn::make('numero_serie')
                    ->label('N/S'),

                Tables\Columns\TextColumn::make('talla')
                    ->label('TALLA'),

                Tables\Columns\TextColumn::make('stock_sistema')
                    ->label('STOCK SISTEMA')
                    ->numeric(),

                Tables\Columns\IconColumn::make('tiene_codigo_barras')
                    ->label('Con barras')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                    Tables\Columns\ImageColumn::make('barcode_image')
                    ->label('CÓDIGO BARRAS')
                    ->getStateUsing(fn (Inventario $record) => $record->stock && $record->stock->codigo ? route('barcode.stock', $record->stock) : null)
                    ->size(200)  // ← sube de 120 a 200–240 para ver detalle como en Product
                    ->extraImgAttributes([
                        'alt'     => 'Código de barras del ítem',
                        'loading' => 'lazy',
                        'style'   => 'image-rendering: crisp-edges; width: 100%; height: auto; object-fit: contain;',  // nitidez + proporción
                    ])
                    ->placeholder('Sin código')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('observaciones')
                    ->label('OBSERVACIONES')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('tiene_codigo_barras')
                    ->label('Con código de barras'),
            ])
            ->actions([
                Tables\Actions\Action::make('imprimir_etiqueta')
                    ->label('Imprimir Etiqueta')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Inventario $record) => $record->stock ? route('etiqueta.stock', $record->stock) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Inventario $record): bool => $record->stock && filled($record->stock->codigo)),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('fecha_conteo', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInventarios::route('/'),
            'create' => Pages\CreateInventario::route('/create'),
            'edit'   => Pages\EditInventario::route('/{record}/edit'),
        ];
    }
}