<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransactionResource;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Generate ref_id
        $refId = Str::uuid()->toString();
        $data['ref_id'] = $refId;

        // Validasi hanya berlaku jika tipe transaksi adalah transfer
        if ($data['type'] === 'transfer') {
            // Validasi saldo cukup
            $saldoUser = $user->balance ?? 0;
            if ($saldoUser < $data['amount']) {
                Notification::make()
                    ->title('Saldo tidak cukup')
                    ->danger()
                    ->send();

                // Hentikan proses penyimpanan data
                abort(400, 'Saldo tidak cukup untuk melakukan transfer.');
            }

            // Ambil bank
            $bank = \App\Models\Bank::find($data['bank_id']);

            // Kirim request ke API
            $response = Http::asForm()->post(env('API_URL') . '/create', [
                'api_key'       => env('API_KEY'),
                'ref_id'        => $refId,
                'kode_bank'     => $bank->bank_code ?? '',
                'nomor_akun'    => $data['account_number'],
                'nama_pemilik'  => $data['account_name'],
                'nominal'       => $data['amount'],
            ]);

            $json = $response->json();

            if ($response->json()['code'] !== 200) {
                Notification::make()
                    ->title('Gagal membuat transaksi')
                    ->body($json['message'] ?? 'Unknown error')
                    ->danger()
                    ->send();

                return $json['message'];
            }
        }

        // Lanjutkan penyimpanan transaksi ke database
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return TransactionResource::getUrl('index');
    }
}
