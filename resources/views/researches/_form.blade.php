@csrf

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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

<div class="mt-6 flex gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('researches.index') }}" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
</div>
