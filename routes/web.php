<?php

use App\Http\Controllers\BankApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});