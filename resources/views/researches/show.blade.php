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
                <a href="{{ route('researches.edit', $research) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-400">
                    Edit Record
                </a>
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
                <h3 class="text-lg font-semibold text-gray-900">Research Summary</h3>
                <dl class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 text-sm">
                    <div>
                        <dt class="font-semibold text-gray-600">Research Code Generation</dt>
                        <dd class="text-gray-900">{{ $research->research_code }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-600">Research Status Tracking</dt>
                        <dd class="text-gray-900">{{ $research->status?->status_name ?? 'N/A' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-600">Title</dt>
                        <dd class="text-gray-900">{{ $research->title }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-600">Research Category Selection</dt>
                        <dd class="text-gray-900">{{ $research->category?->category_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-600">Organizational Unit Assignment</dt>
                        <dd class="text-gray-900">{{ $research->organizationalUnit?->unit_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-600">Lead Proponent</dt>
                        <dd class="text-gray-900">{{ $research->leadProponent?->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-600">Submitted At</dt>
                        <dd class="text-gray-900">{{ optional($research->submitted_at)?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900">Research Proponent Management</h3>

                <form method="POST" action="{{ route('researches.proponents.store', $research) }}" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                    @csrf
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
                    <div class="md:col-span-3">
                        <x-primary-button>Add Proponent</x-primary-button>
                    </div>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Name</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Position</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Contact</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($research->proponents as $proponent)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900">{{ trim($proponent->first_name.' '.$proponent->middle_name.' '.$proponent->last_name.' '.$proponent->suffix) }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $proponent->position_title ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $proponent->email ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $proponent->contact_number ?? 'N/A' }}</td>
                                    <td class="px-3 py-2">
                                        <form method="POST" action="{{ route('researches.proponents.destroy', [$research, $proponent]) }}" onsubmit="return confirm('Remove this proponent?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-rose-600 hover:text-rose-700" type="submit">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">No proponents added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900">Research Document Upload and Storage Management (Local)</h3>

                <form method="POST" action="{{ route('researches.documents.store', $research) }}" enctype="multipart/form-data" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <x-input-label for="document_type" :value="__('Document Type')" />
                        <x-text-input id="document_type" name="document_type" type="text" class="mt-1 block w-full" :value="old('document_type')" required />
                        <x-input-error :messages="$errors->get('document_type')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="file" :value="__('Document File')" />
                        <input id="file" name="file" type="file" accept=".pdf,application/pdf" class="mt-1 block w-full rounded-md border-gray-300" required />
                        <p class="mt-1 text-xs text-gray-500">PDF files only.</p>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>
                    <div class="md:col-span-3">
                        <x-primary-button>Upload Document</x-primary-button>
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
