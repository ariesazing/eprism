<?php

namespace App\Http\Requests\Research;

use Illuminate\Foundation\Http\FormRequest;

class StoreResearchRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:500'],
            'category_id' => ['required', 'integer', 'exists:research_categories,id'],
            'organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
        ];
    }
}
