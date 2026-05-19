<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateUserRequest extends FormRequest
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
        // ambil {id} dari URL untuk ignore uniqueness check email
        $userId = $this->route('id');
        return [
            'name'          => 'sometimes|string|max:255',
            'email'         => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            'password'      => 'sometimes|nullable|string|min:8',
            'department_id' => 'sometimes|nullable|exists:departments,id',
            'role'          => ['sometimes', 'nullable', Rule::in([
                User::ROLE_EMPLOYEE,
                User::ROLE_PURCHASING,
                User::ROLE_MANAGER,
                User::ROLE_WAREHOUSE,
                User::ROLE_ADMIN,
            ])],
            'phone'         => 'sometimes|nullable|string|max:20',
            'photo'         => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
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
