<?php

namespace App\Filament\Resources\BankResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\BankResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\BankResource\Api\Transformers\BankTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = BankResource::class;

    public static bool $public = true;


    /**
     * Show Bank
     *
     * @param Request $request
     * @return BankTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new BankTransformer($query);
    }
}
