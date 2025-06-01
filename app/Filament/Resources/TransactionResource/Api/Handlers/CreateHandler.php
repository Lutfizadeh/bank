<?php

namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Api\Requests\CreateTransactionRequest;

class CreateHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = TransactionResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Create Transaction
     *
     * @param CreateTransactionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateTransactionRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Auto assign user_id
        if ($user && $user->id !== 1) {
            $data['user_id'] = $user->id;
        }

        // Set default values jika tidak ada
        $data['status'] = $data['status'] ?? 'Pending';
        $data['date'] = $data['date'] ?? now();
        $data['ref_id'] = $data['ref_id'] ?? 'TRX-' . time() . '-' . rand(1000, 9999);

        // Calculate balance jika perlu
        if (!isset($data['current']) && $user) {
            $data['current'] = $user->balance ?? 0;
            $data['add'] = $data['amount'];
            $data['final'] = $data['current'] + $data['add'];
        }

        try {
            $model = static::getModel()::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $model
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}
