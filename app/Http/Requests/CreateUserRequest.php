<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'role'          => ['nullable', Rule::in([
                User::ROLE_EMPLOYEE,
                User::ROLE_PURCHASING,
                User::ROLE_MANAGER,
                User::ROLE_WAREHOUSE,
                User::ROLE_ADMIN,
            ])],
            'phone'         => 'nullable|string|max:20',
            'is_active'     => 'nullable|boolean'
        ];
    }

    public function messages()
    {
        return [
            'email.unique'         => 'Email already registered',
            'department_id.exists' => 'Department not found',
            'role.in'              => 'Invaliad role. Allowed: emplloyee, purchasing, manager, warehouse, admin.'
        ];
    }
}
