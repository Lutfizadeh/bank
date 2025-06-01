<?php

namespace App\Filament\Resources\TransactionResource\Api\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check(); // Pastikan user sudah login
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();

        return [
			// user_id hanya required untuk admin, user biasa akan auto-assign
            'user_id' => $user && $user->id === 1 ? 'required|exists:users,id' : 'nullable',
            
            'bank_id' => 'required|exists:banks,id',
            'ref_id' => 'nullable|string|unique:transactions,ref_id', // Biasanya di-generate otomatis
            'type' => 'required|in:topup,transfer', // Sesuaikan dengan enum Anda
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|in:Pending,Success,Failed', // Default bisa pending
            
            // Field balance biasanya di-calculate otomatis
            'current' => 'nullable|numeric',
            'add' => 'nullable|numeric', 
            'final' => 'nullable|numeric',
            
            'date' => 'nullable|date|date_format:Y-m-d H:i:s' // Default bisa now()
		];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be at least 1',
            'bank_id.required' => 'Bank is required',
            'bank_id.exists' => 'Selected bank does not exist',
        ];
    }
}
