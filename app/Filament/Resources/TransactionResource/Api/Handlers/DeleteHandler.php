<?php
namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TransactionResource;

class DeleteHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = TransactionResource::class;

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Delete Transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(Request $request)
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

        $model->delete();

        return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully',
            ], 200);
    }
}