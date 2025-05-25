<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TransactionResource;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();
        $user = Auth::user();

        // Hanya superadmin (id = 1) yang bisa lihat semua transaksi
        if ($user->id !== 1) {
            $query->where('user_id', $user->id);
        }


        return $query;
    }
}
