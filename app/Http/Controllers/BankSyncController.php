<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class BankSyncController extends Controller
{
    /**
     * Sync products from external API to local database.
     */

     
    public function syncFromApi()
    {
        // try {
        //     $response = Http::asForm()->post('https://atlantich2h.com/transfer/bank_list', [
        //         'api_key' => 'C5V41sY3QFRga2MunZkbw2v4Jg3cjsjtCItD0lKNhCroQj5PW2Y8ocVuuqPLTCFkzXtofDd7oQBX4Ww7cdZEWnCtHLJrv7ZgAZE2'
        //     ]);

        //     // dd('Status code: ' . $response->status());
        //     // dd('Response body: ' . $response->body());

        //     if ($response->successful()) {
        //         $banks = $response->json()['data'];

        //         foreach ($banks as $item) {
        //             $data = Arr::only($item, [
        //                 'id',
        //                 'bank_code',
        //                 'bank_name',
        //                 'type',
        //             ]);
                    
        //             $data = array_merge([
        //                 'bank_code' => '',
        //                 'bank_name' => '',
        //                 'type' => '',
        //             ], $data);

        //             // Simpan ke DB
        //             foreach ($banks as $item) {
        //                 $data = Arr::only($item, [
        //                     'id',
        //                     'bank_code',
        //                     'bank_name',
        //                     'type',
        //                 ]);
        //                 $data = array_merge([
        //                     'id',
        //                     'bank_code',
        //                     'bank_name',
        //                     'type',
        //                 ], $data);

        //                 Bank::updateOrCreate(['id' => $data['id']], $data);
        //             }
        //         }

        //         return response()->json(['message' => 'Data Bank berhasil disinkronkan!']);
        //     }

        //     return response()->json(['error' => 'Gagal mengambil data dari API'], 500);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        // }
    }
}
