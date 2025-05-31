<?php

namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Api\Transformers\TransactionTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = TransactionResource::class;


    /**
     * Show Transaction
     *
     * @param Request $request
     * @return TransactionTransformer
     */
    public function handler(Request $request)
    {
        $user = Auth::user();
        
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        // Filter berdasarkan user
        if ($user && $user->id !== 1) {
            $query = $query->where('user_id', $user->id);
        }

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return response()->json(['message' => 'Transaction not found'], 404);

        return new TransactionTransformer($query);
    }
}
