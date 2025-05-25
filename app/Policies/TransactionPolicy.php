<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_transaction');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        // Cek permission dulu, lalu cek ownership jika bukan admin
        if ($user->can('view_transaction')) {
            // Jika punya permission view_all_transactions, bisa lihat semua
            if ($user->can('view_all_transactions')) {
                return true;
            }
            // Jika tidak, hanya bisa lihat milik sendiri
            return $user->id === $transaction->user_id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_transaction');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        if ($user->can('update_transaction')) {
            // Admin bisa update semua, user lain hanya miliknya
            return $user->can('update_all_transactions') || $user->id === $transaction->user_id;
        }
        return false;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->can('delete_transaction');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_transaction');
    }
}