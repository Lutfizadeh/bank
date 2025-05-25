<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $userId = Auth::user()->id;
        $userField = $userId === 1
            ? Forms\Components\Select::make('user_id')->relationship('user', 'name')->required()
            : Forms\Components\Hidden::make('user_id')->default($userId);

        return $form
            ->schema([
                $userField,
                Forms\Components\Select::make('bank_id')
                    ->relationship('bank', 'bank_name')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'topup' => 'Topup',
                        'transfer' => 'Transfer',
                    ])
                    ->required(),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('account_number')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, $set) {
                                // Reset account name when account number changes
                                $set('account_name', null);
                            })
                            ->suffixAction(
                                Action::make('checkAccount')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->label('Cek')
                                    ->tooltip('Cek Nama Rekening')
                                    ->action(function ($state, $set, $get) {
                                        // Validasi input
                                        if (empty($state)) {
                                            $set('account_name', null);
                                            $set('validation_message', 'Nomor rekening tidak boleh kosong');
                                            return;
                                        }

                                        if (empty($get('bank_id'))) {
                                            $set('account_name', null);
                                            $set('validation_message', 'Silakan pilih bank terlebih dahulu');
                                            return;
                                        }

                                        try {
                                            // Get bank code dari bank id
                                            $bankId = $get('bank_id');
                                            $bank = \App\Models\Bank::find($bankId);

                                            if (!$bank || empty($bank->bank_code)) {
                                                $set('account_name', null);
                                                $set('validation_message', 'Bank tidak ditemukan atau tidak memiliki kode bank');
                                                return;
                                            }

                                            // Ganti URL API sesuai dengan API yang Anda gunakan
                                            $response = Http::asForm()->post(env('API_URL') . '/cek_rekening', [
                                                'api_key' => env('API_KEY'),
                                                'bank_code' => $bank->bank_code,
                                                'account_number' => $state,
                                            ]);

                                            // dd('Status code: ' . $response->status());
                                            // dd('Response body: ' . $response->body());

                                            if ($response->successful()) {
                                                $data = $response->json();
                                                $data = $data['data'] ?? null;
                                                // dd($data);
                                                if (isset($data['nama_pemilik'])) {
                                                    $set('account_name', $data['nama_pemilik']);
                                                    $set('validation_message', 'Berhasil mendapatkan nama rekening');
                                                } else {
                                                    $set('account_name', null);
                                                    $set('validation_message', 'Nama rekening tidak ditemukan');
                                                }
                                            } else {
                                                $set('account_name', null);
                                                $set('validation_message', 'Gagal memeriksa nama rekening: ' . ($response->json()['message'] ?? 'Error tidak diketahui'));
                                            }
                                        } catch (\Exception $e) {
                                            $set('account_name', null);
                                            $set('validation_message', 'Terjadi kesalahan: ' . $e->getMessage());
                                        }
                                    })
                            ),
                        Forms\Components\TextInput::make('account_name')
                            ->required()
                            ->maxLength(255)
                            ->readOnly(true),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $userId = Auth::user()->id;
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->visible($userId === 1),
                Tables\Columns\TextColumn::make('bank.bank_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ref_id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->icons([
                        'heroicon-o-arrow-down-circle' => 'topup',
                        'heroicon-o-arrow-up-circle' => 'transfer',
                    ])
                    ->colors([
                        'success' => 'topup',
                        'danger' => 'transfer',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->icons([
                        'heroicon-o-arrow-down-circle' => 'Success',
                        'heroicon-o-arrow-up-circle' => 'Failed',
                    ])
                    ->colors([
                        'success' => 'Success',
                        'danger' => 'Failed',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('current')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('add')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('final')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('proses')
                    ->label('Proses')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'Pending' && $userId === 1)
                    ->action(function ($record) {
                        $user = $record->user;
                        $current = $user->balance;
                        $amount = $record->amount;

                        if ($record->type === 'topup') {
                            $final = $current + $amount;
                            $record->add = $amount;
                        } elseif ($record->type === 'transfer') {
                            if ($current < $amount) {
                                return Notification::make()
                                    ->title('Saldo tidak cukup!')
                                    ->danger()
                                    ->send();
                            }
                            $final = $current - $amount;
                            $record->add = -$amount;
                        } else {
                            return Notification::make()
                                ->title('Tipe transaksi tidak valid!')
                                ->warning()
                                ->send();
                        }

                        $user->balance = $final;
                        $user->save();

                        $record->current = $current;
                        $record->status = 'Success';
                        $record->date = now();
                        $record->final = $final;
                        $record->save();

                        return Notification::make()
                            ->title('Transaksi berhasil diproses!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
