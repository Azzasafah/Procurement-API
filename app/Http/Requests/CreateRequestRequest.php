<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Override;

class CreateRequestRequest extends FormRequest
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
            'notes'                   => 'nullable|string|max:1000',
            'items'                   => 'required|array|min:1',
            'items.*.item_name'       => 'required|string|max:255',
            'items.*.category'        => 'required|string|max:100',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.unit'            => 'required|string|max:50',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes'           => 'nullable|string|max:550',
        ];
    }

    public function messages()
    {
        return [
            'items.required'             => "Request must've minimal 1 item",
            'items.*.item_name_required' => 'item name required',
            'items.*.category.required'  => 'category name required',
            'items.*.quantity.required'  => 'amount of item required',
            'items.*.unit.required'      => 'unit of item required',
        ];
    }
}
