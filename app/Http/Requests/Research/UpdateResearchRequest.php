<?php

namespace App\Http\Requests\Research;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $statusRules = ['nullable', 'integer', 'exists:research_statuses,id'];

        if ($this->user()?->role?->role_name !== 'Administrator') {
            $statusRules[] = 'prohibited';
        }

        return [
            'title' => ['required', 'string', 'max:500'],
            'category_id' => ['required', 'integer', 'exists:research_categories,id'],
            'organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'status_id' => $statusRules,
        ];
    }
}
