<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        /** @var User $targetUser */
        $targetUser = $this->route('user');

        return [
            'deped_id' => ['nullable', 'string', 'max:30', Rule::unique('users', 'deped_id')->ignore($targetUser->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUser->id)],
            'position_title' => ['required', 'string', 'max:150'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status_id' => ['required', 'integer', 'exists:user_statuses,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'return_to' => ['nullable', 'string'],
        ];
    }
}
