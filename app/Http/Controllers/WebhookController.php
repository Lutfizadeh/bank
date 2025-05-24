<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleTransfer(Request $request)
    {
        $payload = $request->all();

        $providedSignature = $request->header('X-ATL-Signature');
        $expectedSignature = md5(env('ATL_SECRET'));

        $status = $payload['status'];
        $ref_id = $payload['data']['reff_id'];

        if ($providedSignature !== $expectedSignature) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transfer = Transaction::where('ref_id', $ref_id)->first();
        if (!$transfer) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($status == 'success') {
            $transfer->status = 'Success';
            $transfer->save();
        } else {
            $transfer->status = 'Failed';
            $transfer->save();
        }

        return response()->json(['message' => 'Webhook handled successfully'], 200);
    }
}