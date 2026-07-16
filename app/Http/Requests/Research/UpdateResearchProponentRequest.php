<?php

namespace App\Http\Requests\Research;

class UpdateResearchProponentRequest extends StoreResearchProponentRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['photo'] = ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'];

        return $rules;
    }
}
