<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->get('/test', function (Request $request) {
    return response()->json(['message' => 'API is working!']);
});