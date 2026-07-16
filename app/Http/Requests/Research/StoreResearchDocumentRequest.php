<?php

namespace App\Http\Requests\Research;

use Illuminate\Foundation\Http\FormRequest;

class StoreResearchDocumentRequest extends FormRequest
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
            'document_class' => ['required', 'string', 'in:research_manuscript,narrative_form_document,research_documentation'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
