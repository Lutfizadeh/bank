<?php
namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Api\Requests\UpdateTransactionRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = TransactionResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Transaction
     *
     * @param UpdateTransactionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateTransactionRequest $request)
    {
        $user = Auth::user();
        if ($user && $user->id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        $model->fill($request->all());

        $model->save();

        return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'data' => $model
            ], 201);
    }
}