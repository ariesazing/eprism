<?php

namespace App\Http\Requests\Research;

use Illuminate\Foundation\Http\FormRequest;

class StoreResearchVersionRequest extends FormRequest
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
            'remarks' => ['nullable', 'string', 'max:2000'],
            'research_manuscript_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'narrative_form_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'documentation_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
