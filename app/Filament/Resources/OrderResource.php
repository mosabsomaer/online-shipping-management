<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\NotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Order Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_number')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($operation) => $operation === 'edit'),
                        Forms\Components\Select::make('merchant_id')
                            ->relationship('merchant', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->dehydrated(fn ($operation) => $operation === 'create'),
                        Forms\Components\Hidden::make('route_id')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending_approval' => 'Pending Approval',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'awaiting_payment' => 'Awaiting Payment',
                                'paid' => 'Paid',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending_approval')
                            ->native(false),
                        Forms\Components\TextInput::make('total_cost')
                            ->label('Total Order Cost')
                            ->prefix('LYD')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->default(0)
                            ->helperText('Automatically calculated from all shipments'),
                    ])->columns(3),

                Forms\Components\Section::make('Recipient Information')
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Recipient Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('recipient_phone')
                            ->label('Recipient Phone')
                            ->required()
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('recipient_address')
                            ->label('Recipient Address')
                            ->required()
                            ->maxLength(65535)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Shipments')
                    ->schema([
                        Forms\Components\Repeater::make('shipments')
                            ->relationship()
                            ->schema([
                                Forms\Components\Section::make('Container')
                                    ->schema([
                                        Forms\Components\Select::make('container_id')
                                            ->label('Container')
                                            ->relationship('container', 'name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->localized_name ?? $record->name ?? 'Unknown Container')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $livewire) {
                                                if ($state) {
                                                    $container = \App\Models\Container::find($state);
                                                    if ($container) {
                                                        $total = $container->price + ($get('customs_fee') ?? 0);
                                                        $set('total_cost', $total);

                                                        // Recalculate order total
                                                        static::updateOrderTotal($livewire);
                                                    }
                                                }
                                            })
                                            ->disabled(fn ($operation) => $operation === 'edit')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Shipment Details')
                                    ->schema([
                                        Forms\Components\Textarea::make('item_description')
                                            ->label('Item Description')
                                            ->required()
                                            ->maxLength(65535)
                                            ->rows(3)
                                            ->disabled(fn ($operation) => $operation === 'edit')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Pricing')
                                    ->schema([

                                        Forms\Components\TextInput::make('customs_fee')
                                            ->label('Customs Fee')
                                            ->prefix('LYD')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $livewire) {
                                                // Recalculate order total when customs fee changes
                                                static::updateOrderTotal($livewire);
                                            })
                                            ->disabled(fn (Forms\Get $get, $operation) => $operation === 'edit' && $get('../../status') !== 'pending_approval'),
                                    ])->columns(2),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Shipment')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state, $component): ?string {
                                $index = 0;
                                foreach ($component->getState() as $item) {
                                    if ($item === $state) {
                                        return 'Shipment #'.($index + 1);
                                    }
                                    $index++;
                                }

                                return 'Shipment';
                            })
                            ->deleteAction(
                                fn ($action, $livewire) => $action->after(fn () => static::updateOrderTotal($livewire))
                            )
                            ->addable(fn ($operation) => $operation === 'create')
                            ->deletable(fn ($operation) => $operation === 'create')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Forms\Components\Section::make('Rejection Reason')
                    ->schema([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for rejection')
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get): bool => $get('status') === 'rejected'),
                    ])
                    ->visible(fn (Forms\Get $get): bool => $get('status') === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('merchant.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipments_count')
                    ->label('Shipments')
                    ->counts('shipments')
                    ->sortable(),
                Tables\Columns\TextColumn::make('route')
                    ->label('Route')
                    ->formatStateUsing(fn (Order $record): string => $record->route ? "{$record->route->originPort->name} → {$record->route->destinationPort->name}" : 'N/A')
                    ->searchable(['route.originPort.name', 'route.destinationPort.name'])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_approval' => 'warning',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        'awaiting_payment' => 'warning',
                        'paid' => 'success',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->money('LYD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ordered At')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_approval' => 'Pending Approval',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'awaiting_payment' => 'Awaiting Payment',
                        'paid' => 'Paid',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status === 'pending_approval')
                    ->action(function (Order $record): void {
                        $record->update([
                            'status' => 'awaiting_payment',
                        ]);

                        // Send approval email to merchant
                        $notificationService = app(NotificationService::class);
                        $emailSent = $notificationService->sendOrderApprovalEmail($record);

                        Notification::make()
                            ->success()
                            ->title('Order Approved')
                            ->body("Order {$record->tracking_number} has been approved with total cost of LYD ".number_format($record->total_cost, 2).'.'.($emailSent ? ' Email sent to merchant.' : ''))
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Order $record): bool => $record->status === 'pending_approval')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        // Send rejection email to merchant
                        $notificationService = app(NotificationService::class);
                        $emailSent = $notificationService->sendOrderRejectionEmail($record);

                        Notification::make()
                            ->warning()
                            ->title('Order Rejected')
                            ->body("Order {$record->tracking_number} has been rejected.".($emailSent ? ' Email sent to merchant.' : ''))
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

    protected static function updateOrderTotal($livewire): void
    {
        $shipments = $livewire->data['shipments'] ?? [];
        $orderTotal = collect($shipments)->sum(function ($shipment) {
            $containerPrice = $shipment['container_price'] ?? 0;
            $customsFee = $shipment['customs_fee'] ?? 0;

            return $containerPrice + $customsFee;
        });

        $livewire->data['total_cost'] = $orderTotal;
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
