<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankApiController;
use App\Http\Controllers\BankSyncController;

Route::get('/', function () {
    return view('welcome');
});