<?php

namespace App\Http\Requests\Research;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        $additionalProponents = collect((array) $this->input('additional_proponents', []))
            ->map(function (mixed $proponent): array {
                $data = is_array($proponent) ? $proponent : [];
                $data['middle_name'] = strtoupper(trim((string) ($data['middle_name'] ?? '')));

                return $data;
            })
            ->values()
            ->all();

        $this->merge([
            'uploader_proponent_first_name' => trim((string) $this->input('uploader_proponent_first_name', $user?->first_name ?? '')),
            'uploader_proponent_middle_name' => strtoupper(trim((string) $this->input('uploader_proponent_middle_name', $user?->middle_name ?? ''))),
            'uploader_proponent_last_name' => trim((string) $this->input('uploader_proponent_last_name', $user?->last_name ?? '')),
            'uploader_proponent_suffix' => trim((string) $this->input('uploader_proponent_suffix', $user?->suffix ?? '')),
            'uploader_proponent_position_title' => trim((string) $this->input('uploader_proponent_position_title', $user?->position_title ?? '')),
            'uploader_proponent_organizational_unit_id' => (int) $this->input('uploader_proponent_organizational_unit_id', $user?->organizational_unit_id ?? 0),
            'uploader_proponent_email' => trim((string) $this->input('uploader_proponent_email', $user?->email ?? '')),
            'uploader_proponent_contact_number' => trim((string) $this->input('uploader_proponent_contact_number', $user?->contact_number ?? '')),
            'additional_proponents' => $additionalProponents,
        ]);
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
            'uploader_proponent_first_name' => ['required', 'string', 'max:100'],
            'uploader_proponent_middle_name' => ['required', 'string', 'size:1', 'regex:/^[A-Z]$/'],
            'uploader_proponent_last_name' => ['required', 'string', 'max:100'],
            'uploader_proponent_suffix' => ['nullable', 'string', 'max:20'],
            'uploader_proponent_position_title' => ['required', Rule::in(User::positionTitles())],
            'uploader_proponent_organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'uploader_proponent_email' => ['required', 'email', 'max:255'],
            'uploader_proponent_contact_number' => ['required', 'string', 'max:20'],
            'uploader_proponent_photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'additional_proponents' => ['nullable', 'array'],
            'additional_proponents.*.first_name' => ['required', 'string', 'max:100'],
            'additional_proponents.*.middle_name' => ['required', 'string', 'size:1', 'regex:/^[A-Z]$/'],
            'additional_proponents.*.last_name' => ['required', 'string', 'max:100'],
            'additional_proponents.*.suffix' => ['nullable', 'string', 'max:20'],
            'additional_proponents.*.position_title' => ['required', Rule::in(User::positionTitles())],
            'additional_proponents.*.organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'additional_proponents.*.email' => ['required', 'email', 'max:255'],
            'additional_proponents.*.contact_number' => ['required', 'string', 'max:20'],
            'additional_proponents.*.photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'research_manuscript_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'narrative_form_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'documentation_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'uploader_proponent_middle_name' => 'uploader proponent middle initial',
            'uploader_proponent_organizational_unit_id' => 'uploader proponent organizational unit',
            'uploader_proponent_photo' => 'uploader proponent photo',
            'additional_proponents.*.middle_name' => 'additional proponent middle initial',
            'additional_proponents.*.organizational_unit_id' => 'additional proponent organizational unit',
            'additional_proponents.*.photo' => 'additional proponent photo',
            'research_manuscript_file' => 'research manuscript document',
            'narrative_form_file' => 'narrative form document',
            'documentation_file' => 'research documentation document',
        ];
    }
}
