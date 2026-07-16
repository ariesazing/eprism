<?php

namespace App\Http\Requests\Research;

use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResearchProponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $organizationalUnit = null;

        if ($this->filled('organizational_unit_id')) {
            $organizationalUnit = OrganizationalUnit::query()->find($this->integer('organizational_unit_id'));
        }

        $middleInitial = strtoupper(trim((string) $this->input('middle_name', '')));

        $this->merge([
            'middle_name' => $middleInitial,
            'organizational_unit_name' => $organizationalUnit?->unit_name,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['required', 'string', 'size:1', 'regex:/^[A-Z]$/'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'position_title' => ['required', Rule::in(User::positionTitles())],
            'organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'organizational_unit_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'middle_name' => 'middle initial',
            'organizational_unit_id' => 'organizational unit',
        ];
    }
}
