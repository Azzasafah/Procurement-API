<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_name'     => 'required|string|max:255',
            'category'      => 'required|string|max:100',
            'quantity'      => 'required|integer|min:0',
            'unit'          => 'required|string|max:50',
            'location'      => 'nullable|string|max:255',
            'minimum_stock' => 'nullable|integer|min:0',
        ];
    }
}
