<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::middleware('api')->get('/test', function (Request $request) {
    return response()->json(['message' => 'API is working!']);
});

Route::post('/auth/register', [AuthController::class, 'register']);