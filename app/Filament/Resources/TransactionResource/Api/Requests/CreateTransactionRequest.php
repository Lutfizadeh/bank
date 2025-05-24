<?php

namespace App\Filament\Resources\TransactionResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'user_id' => 'required',
			'bank_id' => 'required',
			'ref_id' => 'required',
			'type' => 'required',
			'account_number' => 'required',
			'account_name' => 'required',
			'amount' => 'required',
			'description' => 'required',
			'status' => 'required',
			'current' => 'required',
			'add' => 'required',
			'final' => 'required',
			'date' => 'required|date'
		];
    }
}
