@csrf

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="title" :value="__('Research Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $research->title ?? '')" required maxlength="500" />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category_id" :value="__('Research Category')" />
        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300" required>
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $research->category_id ?? 0) === (int) $category->id)>
                    {{ $category->category_name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="organizational_unit_id" :value="__('Organizational Unit Assignment')" />
        <select id="organizational_unit_id" name="organizational_unit_id" class="mt-1 block w-full rounded-md border-gray-300" required>
            <option value="">Select organizational unit</option>
            @foreach ($organizationalUnits as $unit)
                <option value="{{ $unit->id }}" @selected((int) old('organizational_unit_id', $research->organizational_unit_id ?? $defaultOrganizationalUnitId ?? 0) === (int) $unit->id)>
                    {{ $unit->unit_name }} @if($unit->unit_code) ({{ $unit->unit_code }}) @endif
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('organizational_unit_id')" class="mt-2" />
    </div>

    @if (!empty($isAdmin) && isset($statuses))
        <div>
            <x-input-label for="status_id" :value="__('Research Status Tracking')" />
            <select id="status_id" name="status_id" class="mt-1 block w-full rounded-md border-gray-300">
                <option value="">Keep current status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" @selected((int) old('status_id', $research->status_id ?? 0) === (int) $status->id)>
                        {{ $status->status_name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
        </div>
    @endif
</div>

@if ($research === null)
    <div class="mt-8 border-t border-gray-200 pt-6">
        <h3 class="text-lg font-semibold text-gray-900">Research Proponents</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="uploader_proponent_first_name" :value="__('First Name')" />
                <x-text-input id="uploader_proponent_first_name" name="uploader_proponent_first_name" type="text" class="mt-1 block w-full" :value="old('uploader_proponent_first_name', request()->user()?->first_name)" required />
                <x-input-error :messages="$errors->get('uploader_proponent_first_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_middle_name" :value="__('Middle Initial')" />
                <x-text-input id="uploader_proponent_middle_name" name="uploader_proponent_middle_name" type="text" class="mt-1 block w-full" :value="old('uploader_proponent_middle_name', request()->user()?->middle_name)" maxlength="1" required />
                <x-input-error :messages="$errors->get('uploader_proponent_middle_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_last_name" :value="__('Last Name')" />
                <x-text-input id="uploader_proponent_last_name" name="uploader_proponent_last_name" type="text" class="mt-1 block w-full" :value="old('uploader_proponent_last_name', request()->user()?->last_name)" required />
                <x-input-error :messages="$errors->get('uploader_proponent_last_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_suffix" :value="__('Suffix')" />
                <x-text-input id="uploader_proponent_suffix" name="uploader_proponent_suffix" type="text" class="mt-1 block w-full" :value="old('uploader_proponent_suffix', request()->user()?->suffix)" />
                <x-input-error :messages="$errors->get('uploader_proponent_suffix')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_position_title" :value="__('Position Title')" />
                <select id="uploader_proponent_position_title" name="uploader_proponent_position_title" class="mt-1 block w-full rounded-md border-gray-300" required>
                    <option value="">Select position title</option>
                    @foreach ($positionTitles as $title)
                        <option value="{{ $title }}" @selected(old('uploader_proponent_position_title', request()->user()?->position_title) === $title)>{{ $title }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('uploader_proponent_position_title')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_organizational_unit_id" :value="__('Organizational Unit')" />
                <select id="uploader_proponent_organizational_unit_id" name="uploader_proponent_organizational_unit_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                    <option value="">Select organizational unit</option>
                    @foreach ($organizationalUnits as $unit)
                        <option value="{{ $unit->id }}" @selected((int) old('uploader_proponent_organizational_unit_id', request()->user()?->organizational_unit_id) === (int) $unit->id)>
                            {{ $unit->unit_name }} @if($unit->unit_code) ({{ $unit->unit_code }}) @endif
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('uploader_proponent_organizational_unit_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_email" :value="__('Email')" />
                <x-text-input id="uploader_proponent_email" name="uploader_proponent_email" type="email" class="mt-1 block w-full" :value="old('uploader_proponent_email', request()->user()?->email)" required />
                <x-input-error :messages="$errors->get('uploader_proponent_email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_contact_number" :value="__('Contact Number')" />
                <x-text-input id="uploader_proponent_contact_number" name="uploader_proponent_contact_number" type="text" class="mt-1 block w-full" :value="old('uploader_proponent_contact_number', request()->user()?->contact_number)" required />
                <x-input-error :messages="$errors->get('uploader_proponent_contact_number')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="uploader_proponent_photo" :value="__('Uploader Proponent Photo')" />
                <input id="uploader_proponent_photo" name="uploader_proponent_photo" type="file" accept="image/png,image/jpeg" class="mt-1 block w-full rounded-md border-gray-300" required />
                <p class="mt-1 text-xs text-gray-500">JPG or PNG only. Required before submitting research.</p>
                <x-input-error :messages="$errors->get('uploader_proponent_photo')" class="mt-2" />
            </div>
        </div>
    </div>

    @php($oldAdditionalProponents = old('additional_proponents', []))
    @php($hasAdditionalProponentErrors =
        count($errors->get('additional_proponents.*.first_name')) > 0 ||
        count($errors->get('additional_proponents.*.middle_name')) > 0 ||
        count($errors->get('additional_proponents.*.last_name')) > 0 ||
        count($errors->get('additional_proponents.*.position_title')) > 0 ||
        count($errors->get('additional_proponents.*.organizational_unit_id')) > 0 ||
        count($errors->get('additional_proponents.*.email')) > 0 ||
        count($errors->get('additional_proponents.*.contact_number')) > 0 ||
        count($errors->get('additional_proponents.*.photo')) > 0)
    <div class="mt-8 border-t border-gray-200 pt-6" x-data="{ proponents: {{ json_encode($oldAdditionalProponents) }} }">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Additional Research Proponents</h3>
                <p class="mt-1 text-sm text-gray-500">Add supporting proponents through the modal form, similar to Research Proponent Management.</p>
            </div>
            <x-secondary-button type="button" x-on:click="$dispatch('open-modal', 'add-members-modal')">
                Add Member
            </x-secondary-button>
        </div>

        <div class="mt-4 space-y-4">
            <template x-for="(proponent, index) in proponents" :key="index">
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700" x-text="`Proponent #${index + 1}`"></h4>
                        <button type="button" class="text-sm font-semibold text-rose-600 hover:text-rose-700" x-on:click="proponents.splice(index, 1)">Remove</button>
                    </div>

                    <p class="text-sm text-gray-700" x-text="`${proponent.first_name} ${proponent.middle_name} ${proponent.last_name}`"></p>
                    <p class="mt-1 text-xs text-gray-500" x-text="`${proponent.position_title || 'No position title'} | ${proponent.email || 'No email'}`"></p>

                    <input type="hidden" x-bind:name="`additional_proponents[${index}][first_name]`" x-bind:value="proponent.first_name" />
                    <input type="hidden" x-bind:name="`additional_proponents[${index}][middle_name]`" x-bind:value="proponent.middle_name" />
                    <input type="hidden" x-bind:name="`additional_proponents[${index}][last_name]`" x-bind:value="proponent.last_name" />
                    <input type="hidden" x-bind:name="`additional_proponents[${index}][suffix]`" x-bind:value="proponent.suffix" />
                    <input type="hidden" x-bind:name="`additional_proponents[${index}][position_title]`" x-bind:value="proponent.position_title" />
                    <input type="hidden" x-bind:name="`additional_proponents[${index}][organizational_unit_id]`" x-bind:value="proponent.organizational_unit_id" />
                    <input type="hidden" x-bind:name="`additional_proponents[${index}][email]`" x-bind:value="proponent.email" />
                    <input type="hidden" x-bind:name="`additional_proponents[${index}][contact_number]`" x-bind:value="proponent.contact_number" />

                    <div class="mt-3">
                        <x-input-label :value="__('Proponent Photo')" />
                        <input x-bind:name="`additional_proponents[${index}][photo]`" type="file" accept="image/png,image/jpeg" class="mt-1 block w-full rounded-md border-gray-300" required />
                        <p class="mt-1 text-xs text-gray-500">JPG or PNG only. Required for each additional proponent.</p>
                        <x-input-error :messages="$errors->get('additional_proponents.*.photo')" class="mt-2" />
                    </div>
                </div>
            </template>

            <p x-show="proponents.length === 0" class="text-sm text-gray-500">No additional proponents yet. You may still submit with only the uploader as proponent.</p>
        </div>

        <x-modal name="add-members-modal" :show="$hasAdditionalProponentErrors" maxWidth="2xl" focusable>
            <div class="p-6" x-data="{ draft: { first_name: '', middle_name: '', last_name: '', suffix: '', position_title: '', organizational_unit_id: '', email: '', contact_number: '' } }">
                <h4 class="text-lg font-semibold text-gray-900">Add Additional Proponent</h4>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label :value="__('First Name')" />
                        <x-text-input type="text" class="mt-1 block w-full" x-model="draft.first_name" />
                    </div>
                    <div>
                        <x-input-label :value="__('Middle Initial')" />
                        <x-text-input type="text" class="mt-1 block w-full" x-model="draft.middle_name" maxlength="1" />
                    </div>
                    <div>
                        <x-input-label :value="__('Last Name')" />
                        <x-text-input type="text" class="mt-1 block w-full" x-model="draft.last_name" />
                    </div>
                    <div>
                        <x-input-label :value="__('Suffix')" />
                        <x-text-input type="text" class="mt-1 block w-full" x-model="draft.suffix" />
                    </div>
                    <div>
                        <x-input-label :value="__('Position Title')" />
                        <select class="mt-1 block w-full rounded-md border-gray-300" x-model="draft.position_title">
                            <option value="">Select position title</option>
                            @foreach ($positionTitles as $title)
                                <option value="{{ $title }}">{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Organizational Unit')" />
                        <select class="mt-1 block w-full rounded-md border-gray-300" x-model="draft.organizational_unit_id">
                            <option value="">Select organizational unit</option>
                            @foreach ($organizationalUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_name }} @if($unit->unit_code) ({{ $unit->unit_code }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Email')" />
                        <x-text-input type="email" class="mt-1 block w-full" x-model="draft.email" />
                    </div>
                    <div>
                        <x-input-label :value="__('Contact Number')" />
                        <x-text-input type="text" class="mt-1 block w-full" x-model="draft.contact_number" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button type="button" x-on:click="proponents.push({ ...draft }); draft = { first_name: '', middle_name: '', last_name: '', suffix: '', position_title: '', organizational_unit_id: '', email: '', contact_number: '' }; $dispatch('close')">Add Member</x-primary-button>
                </div>
            </div>
        </x-modal>
    </div>

    <div class="mt-8 border-t border-gray-200 pt-6 space-y-6">
        <h3 class="text-lg font-semibold text-gray-900">Required Research Documents</h3>
        <p class="text-sm text-gray-500">Upload all three required PDF documents before submitting research. Document type is assigned automatically.</p>

        <div class="rounded-lg border border-gray-200 p-4">
            <x-input-label :value="__('Research Manuscript')" />
            <input id="research_manuscript_file" name="research_manuscript_file" type="file" accept=".pdf,application/pdf" class="mt-1 block w-full rounded-md border-gray-300" required />
            <p class="mt-1 text-xs text-gray-500">PDF files only.</p>
            <x-input-error :messages="$errors->get('research_manuscript_file')" class="mt-2" />
        </div>

        <div class="rounded-lg border border-gray-200 p-4">
            <x-input-label :value="__('Narrative Form Document')" />
            <input id="narrative_form_file" name="narrative_form_file" type="file" accept=".pdf,application/pdf" class="mt-1 block w-full rounded-md border-gray-300" required />
            <p class="mt-1 text-xs text-gray-500">PDF files only.</p>
            <x-input-error :messages="$errors->get('narrative_form_file')" class="mt-2" />
        </div>

        <div class="rounded-lg border border-gray-200 p-4">
            <x-input-label :value="__('Research Documentation')" />
            <input id="documentation_file" name="documentation_file" type="file" accept=".pdf,application/pdf" class="mt-1 block w-full rounded-md border-gray-300" required />
            <p class="mt-1 text-xs text-gray-500">PDF files only.</p>
            <x-input-error :messages="$errors->get('documentation_file')" class="mt-2" />
        </div>
    </div>
@endif

<div class="mt-6 flex gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('researches.index') }}" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
</div>
