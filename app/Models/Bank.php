<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Bank extends Model
{
    use Sushi;
 
    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $banks = Http::asForm()->post('https://atlantich2h.com/transfer/bank_list', [
            'api_key' => env('API_KEY')
        ])->json();
 
        //filtering some attributes dan memastikan semua field ada
        $banks = Arr::map($banks['data'], function ($item) {
            // Ekstrak field yang dibutuhkan
            $extractedItem = Arr::only($item,
                [
                    'id',
                    'bank_code',
                    'bank_name',
                    'type',
                ]
            );
            
            // Pastikan semua field memiliki nilai, tambahkan default jika tidak ada
            return [
                'id' => $extractedItem['id'] ?? null,
                'bank_code' => $extractedItem['bank_code'] ?? '',
                'bank_name' => $extractedItem['bank_name'] ?? '',
                'type' => $extractedItem['type'] ?? 0,
            ];
        });
 
        return $banks;
    }
}