<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Order Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Shipment Information')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'tracking_number')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select the order for this shipment')
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->dehydrated(fn ($operation) => $operation === 'create'),
                        Forms\Components\Select::make('container_id')
                            ->relationship('container', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->localized_name ?? $record->name ?? 'Unknown Container')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($operation) => $operation === 'edit'),
                        Forms\Components\Select::make('current_status')
                            ->options([
                                'pending' => 'Pending',
                                'loaded' => 'Loaded',
                                'in_transit' => 'In Transit',
                                'arrived' => 'Arrived',
                                'delivered' => 'Delivered',
                            ])
                            ->required()
                            ->default('pending')
                            ->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('Shipment Details')
                    ->schema([
                        Forms\Components\Textarea::make('item_description')
                            ->required()
                            ->maxLength(65535)
                            ->rows(3)
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('container_price')
                            ->label('Container Price')
                            ->prefix('LYD')
                            ->numeric()
                            ->required()
                            ->disabled(fn ($operation) => $operation === 'edit'),
                        Forms\Components\TextInput::make('customs_fee')
                            ->label('Customs Fee')
                            ->prefix('LYD')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->disabled(fn ($operation) => $operation === 'edit'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.tracking_number')
                    ->label('Order')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('order.merchant.name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.route')
                    ->label('Route')
                    ->formatStateUsing(fn (Shipment $record): string => $record->order?->route ? "{$record->order->route->originPort->name} → {$record->order->route->destinationPort->name}" : 'N/A'
                    )
                    ->searchable(['order.route.originPort.name', 'order.route.destinationPort.name']),
                Tables\Columns\TextColumn::make('container.name')
                    ->label('Container')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'loaded' => 'info',
                        'in_transit' => 'warning',
                        'arrived' => 'primary',
                        'delivered' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->money('LYD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('current_status')
                    ->options([
                        'pending' => 'Pending',
                        'loaded' => 'Loaded',
                        'in_transit' => 'In Transit',
                        'arrived' => 'Arrived',
                        'delivered' => 'Delivered',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\Action::make('update_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->label('New Status')
                            ->options([
                                'pending' => 'Pending',
                                'loaded' => 'Loaded',
                                'in_transit' => 'In Transit',
                                'arrived' => 'Arrived',
                                'delivered' => 'Delivered',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes (optional)')
                            ->rows(3)
                            ->placeholder('Add any notes about this status update'),
                    ])
                    ->action(function (Shipment $record, array $data): void {
                        // Update shipment status
                        $record->update([
                            'current_status' => $data['new_status'],
                            'last_updated' => now(),
                        ]);

                        // Create status history record
                        $record->statusHistory()->create([
                            'status' => $data['new_status'],
                            'notes' => $data['notes'] ?? null,
                        ]);

                        // Check if all shipments in the order are delivered
                        if ($data['new_status'] === 'delivered') {
                            $allDelivered = $record->order->shipments()
                                ->where('current_status', '!=', 'delivered')
                                ->count() === 0;

                            if ($allDelivered) {
                                $record->order->update(['status' => 'completed']);
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Status Updated')
                            ->body('Shipment status updated to '.ucfirst(str_replace('_', ' ', $data['new_status'])))
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
