<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->role_name === 'Administrator';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        $allowedRoleIds = Role::query()
            ->whereIn('role_name', ['Administrator', 'Reviewer'])
            ->pluck('id')
            ->all();

        return [
            'deped_id' => ['nullable', 'string', 'max:30', 'unique:users,deped_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'position_title' => ['required', Rule::in(User::positionTitles())],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'role_id' => ['required', 'integer', Rule::in($allowedRoleIds)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
