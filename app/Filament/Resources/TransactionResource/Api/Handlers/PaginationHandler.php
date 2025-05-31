<?php
namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Api\Transformers\TransactionTransformer;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = TransactionResource::class;


    /**
     * List of Transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $user = Auth::user();
        
        $query = static::getEloquentQuery();

        // Filter berdasarkan user - hanya superadmin (id = 1) yang bisa lihat semua
        if ($user && $user->id !== 1) {
            $query = $query->where('user_id', $user->id);
        }

        // Cek apakah ada data sebelum pagination
        $totalCount = $query->count();
        
        // Return 404 jika tidak ada data
        if ($totalCount === 0) {
            return response()->json([
                'message' => 'No transactions found',
                'error' => 'Not Found'
            ], 404);
        }

        $query = QueryBuilder::for($query)
        ->allowedFields($this->getAllowedFields() ?? [])
        ->allowedSorts($this->getAllowedSorts() ?? [])
        ->allowedFilters($this->getAllowedFilters() ?? [])
        ->allowedIncludes($this->getAllowedIncludes() ?? [])
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return TransactionTransformer::collection($query);
    }
}
