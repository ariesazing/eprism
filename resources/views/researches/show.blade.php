<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Research Information Management') }}</h2>
            <div class="flex items-center gap-3">
                @if ($research->submitted_at === null)
                    <form method="POST" action="{{ route('researches.submit', $research) }}">
                        @csrf
                        <x-primary-button>Submit Research</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('proponents') || $errors->has('documents'))
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 space-y-1">
                    @error('proponents')
                        <p>{{ $message }}</p>
                    @enderror
                    @error('documents')
                        <p>{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Research Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Submitted researches remain editable from this page.</p>
                    </div>
                    <div class="text-sm text-gray-600 text-right">
                        <p><span class="font-semibold">Code:</span> {{ $research->research_code }}</p>
                        <p><span class="font-semibold">Lead Proponent:</span> {{ $research->leadProponent?->full_name }}</p>
                        <p><span class="font-semibold">Submitted At:</span> {{ optional($research->submitted_at)?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('researches.update', $research) }}" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <div class="md:col-span-2">
                        <x-input-label for="title" :value="__('Research Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $research->title)" required maxlength="500" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="category_id" :value="__('Research Category')" />
                        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('category_id', $research->category_id) === (int) $category->id)>
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
                                <option value="{{ $unit->id }}" @selected((int) old('organizational_unit_id', $research->organizational_unit_id) === (int) $unit->id)>
                                    {{ $unit->unit_name }} @if($unit->unit_code) ({{ $unit->unit_code }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('organizational_unit_id')" class="mt-2" />
                    </div>

                    @if ($canManageStatus)
                        <div>
                            <x-input-label for="status_id" :value="__('Research Status Tracking')" />
                            <select id="status_id" name="status_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Keep current status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" @selected((int) old('status_id', $research->status_id) === (int) $status->id)>
                                        {{ $status->status_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                        </div>
                    @endif

                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button>Save Research Information</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Research Proponent Management</h3>
                        <p class="mt-1 text-sm text-gray-500">View proponents in the table and manage entries from modal forms.</p>
                    </div>
                    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-proponent-modal')">
                        Add Proponent
                    </x-primary-button>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Photo</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Name</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Position</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Organizational Unit</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Contact</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($research->proponents as $proponent)
                                <tr>
                                    <td class="px-3 py-2">
                                        @if ($proponent->photo_path && $proponent->photo_disk)
                                            <a href="{{ route('researches.proponents.photo', [$research, $proponent]) }}" class="text-indigo-600 hover:text-indigo-700">View Photo</a>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-900">{{ trim($proponent->first_name.' '.$proponent->middle_name.' '.$proponent->last_name.' '.$proponent->suffix) }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $proponent->position_title }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $proponent->organizational_unit_name }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $proponent->email }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $proponent->contact_number }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap gap-3">
                                            <button class="text-indigo-600 hover:text-indigo-700" type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-proponent-{{ $proponent->id }}')">Edit</button>
                                            <form method="POST" action="{{ route('researches.proponents.destroy', [$research, $proponent]) }}" onsubmit="return confirm('Remove this proponent?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-rose-600 hover:text-rose-700" type="submit">Remove</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-gray-500">No proponents added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-modal name="add-proponent-modal" :show="$errors->has('first_name') || $errors->has('middle_name') || $errors->has('last_name') || $errors->has('position_title') || $errors->has('organizational_unit_id') || $errors->has('email') || $errors->has('contact_number') || $errors->has('photo')" maxWidth="2xl" focusable>
                    <form method="POST" action="{{ route('researches.proponents.store', $research) }}" enctype="multipart/form-data" class="p-6">
                        @csrf
                        <h4 class="text-lg font-semibold text-gray-900">Add Proponent</h4>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" />
                                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name')" required />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="middle_name" :value="__('Middle Initial')" />
                                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name')" maxlength="1" required />
                                <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" />
                                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name')" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="suffix" :value="__('Suffix')" />
                                <x-text-input id="suffix" name="suffix" type="text" class="mt-1 block w-full" :value="old('suffix')" />
                                <x-input-error :messages="$errors->get('suffix')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="position_title" :value="__('Position Title')" />
                                <select id="position_title" name="position_title" class="mt-1 block w-full rounded-md border-gray-300" required>
                                    <option value="">Select position title</option>
                                    @foreach ($positionTitles as $title)
                                        <option value="{{ $title }}" @selected(old('position_title') === $title)>{{ $title }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('position_title')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="organizational_unit_id" :value="__('Organizational Unit')" />
                                <select id="organizational_unit_id" name="organizational_unit_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                    <option value="">Select organizational unit</option>
                                    @foreach ($organizationalUnits as $unit)
                                        <option value="{{ $unit->id }}" @selected((int) old('organizational_unit_id', $research->organizational_unit_id) === (int) $unit->id)>
                                            {{ $unit->unit_name }}@if($unit->unit_code) ({{ $unit->unit_code }})@endif
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('organizational_unit_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="contact_number" :value="__('Contact Number')" />
                                <x-text-input id="contact_number" name="contact_number" type="text" class="mt-1 block w-full" :value="old('contact_number')" required />
                                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="photo" :value="__('Proponent Photo')" />
                                <input id="photo" name="photo" type="file" accept="image/png,image/jpeg" class="mt-1 block w-full rounded-md border-gray-300" required />
                                <p class="mt-1 text-xs text-gray-500">JPG or PNG only.</p>
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                            <x-primary-button>Add Proponent</x-primary-button>
                        </div>
                    </form>
                </x-modal>

                @foreach($research->proponents as $proponent)
                    <x-modal name="edit-proponent-{{ $proponent->id }}" maxWidth="2xl" focusable>
                        <form method="POST" action="{{ route('researches.proponents.update', [$research, $proponent]) }}" enctype="multipart/form-data" class="p-6">
                            @csrf
                            @method('PUT')
                            <h4 class="text-lg font-semibold text-gray-900">Edit Proponent</h4>
                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <x-input-label for="first_name_{{ $proponent->id }}" :value="__('First Name')" />
                                    <x-text-input id="first_name_{{ $proponent->id }}" name="first_name" type="text" class="mt-1 block w-full" :value="$proponent->first_name" required />
                                </div>
                                <div>
                                    <x-input-label for="middle_name_{{ $proponent->id }}" :value="__('Middle Initial')" />
                                    <x-text-input id="middle_name_{{ $proponent->id }}" name="middle_name" type="text" class="mt-1 block w-full" :value="$proponent->middle_name" maxlength="1" required />
                                </div>
                                <div>
                                    <x-input-label for="last_name_{{ $proponent->id }}" :value="__('Last Name')" />
                                    <x-text-input id="last_name_{{ $proponent->id }}" name="last_name" type="text" class="mt-1 block w-full" :value="$proponent->last_name" required />
                                </div>
                                <div>
                                    <x-input-label for="suffix_{{ $proponent->id }}" :value="__('Suffix')" />
                                    <x-text-input id="suffix_{{ $proponent->id }}" name="suffix" type="text" class="mt-1 block w-full" :value="$proponent->suffix" />
                                </div>
                                <div>
                                    <x-input-label for="position_title_{{ $proponent->id }}" :value="__('Position Title')" />
                                    <select id="position_title_{{ $proponent->id }}" name="position_title" class="mt-1 block w-full rounded-md border-gray-300" required>
                                        <option value="">Select position title</option>
                                        @foreach ($positionTitles as $title)
                                            <option value="{{ $title }}" @selected($proponent->position_title === $title)>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="organizational_unit_id_{{ $proponent->id }}" :value="__('Organizational Unit')" />
                                    <select id="organizational_unit_id_{{ $proponent->id }}" name="organizational_unit_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                        <option value="">Select organizational unit</option>
                                        @foreach ($organizationalUnits as $unit)
                                            <option value="{{ $unit->id }}" @selected($proponent->organizational_unit_name === $unit->unit_name)>
                                                {{ $unit->unit_name }}@if($unit->unit_code) ({{ $unit->unit_code }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="email_{{ $proponent->id }}" :value="__('Email')" />
                                    <x-text-input id="email_{{ $proponent->id }}" name="email" type="email" class="mt-1 block w-full" :value="$proponent->email" required />
                                </div>
                                <div>
                                    <x-input-label for="contact_number_{{ $proponent->id }}" :value="__('Contact Number')" />
                                    <x-text-input id="contact_number_{{ $proponent->id }}" name="contact_number" type="text" class="mt-1 block w-full" :value="$proponent->contact_number" required />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="photo_{{ $proponent->id }}" :value="__('Proponent Photo')" />
                                    <input id="photo_{{ $proponent->id }}" name="photo" type="file" accept="image/png,image/jpeg" class="mt-1 block w-full rounded-md border-gray-300" required />
                                    <p class="mt-1 text-xs text-gray-500">JPG or PNG only.</p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button>Save Proponent</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900">Research Document Upload and Storage Management (Local)</h3>
                <p class="mt-1 text-sm text-gray-500">Upload updated PDF files for Research Manuscript, Narrative Form Document, or Research Documentation.</p>

                <form method="POST" action="{{ route('researches.documents.store', $research) }}" enctype="multipart/form-data" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <x-input-label for="document_class" :value="__('Document Type')" />
                        <select id="document_class" name="document_class" class="mt-1 block w-full rounded-md border-gray-300" required>
                            <option value="research_manuscript" @selected(old('document_class') === 'research_manuscript')>Research Manuscript</option>
                            <option value="narrative_form_document" @selected(old('document_class') === 'narrative_form_document')>Narrative Form Document</option>
                            <option value="research_documentation" @selected(old('document_class', 'research_documentation') === 'research_documentation')>Research Documentation</option>
                        </select>
                        <x-input-error :messages="$errors->get('document_class')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="file" :value="__('Document File')" />
                        <input id="file" name="file" type="file" accept=".pdf,application/pdf" class="mt-1 block w-full rounded-md border-gray-300" required />
                        <p class="mt-1 text-xs text-gray-500">PDF files only.</p>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>
                    <div class="md:col-span-3">
                        <x-primary-button>Upload or Update Document</x-primary-button>
                    </div>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Type</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">File</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Storage</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Uploaded By</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($research->documents as $document)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900">{{ $document->document_type }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $document->original_filename }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $document->storage_disk }} / {{ $document->file_path }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $document->uploader?->full_name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('researches.documents.download', [$research, $document]) }}" class="text-indigo-600 hover:text-indigo-700">Download</a>
                                            <form method="POST" action="{{ route('researches.documents.destroy', [$research, $document]) }}" onsubmit="return confirm('Delete this document?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-rose-600 hover:text-rose-700" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">No documents uploaded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
