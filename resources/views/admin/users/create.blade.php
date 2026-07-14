<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create User') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="surface-card overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="role_id" :value="__('Role')" />
                            <select id="role_id" name="role_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">{{ __('Select Role') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                        </div>

                        <div>
                            <x-input-label for="organizational_unit_id" :value="__('Organizational Unit')" />
                            <select id="organizational_unit_id" name="organizational_unit_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">{{ __('Select Unit') }}</option>
                                @foreach ($organizationalUnits as $unit)
                                    <option value="{{ $unit->id }}" @selected(old('organizational_unit_id') == $unit->id)>
                                        {{ $unit->unit_name }}{{ $unit->unit_code ? ' ('.$unit->unit_code.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('organizational_unit_id')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="deped_id" :value="__('DepEd ID')" />
                                <x-text-input id="deped_id" name="deped_id" type="text" class="mt-1 block w-full" :value="old('deped_id')" />
                                <x-input-error class="mt-2" :messages="$errors->get('deped_id')" />
                            </div>
                            <div>
                                <x-input-label for="position_title" :value="__('Position Title')" />
                                <select id="position_title" name="position_title" class="mt-1 block w-full form-control-ui" required>
                                    <option value="">{{ __('Select Position Title') }}</option>
                                    @foreach ($positionTitles as $title)
                                        <option value="{{ $title }}" @selected(old('position_title') === $title)>{{ $title }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('position_title')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" />
                                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                            </div>
                            <div>
                                <x-input-label for="middle_name" :value="__('Middle Name')" />
                                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name')" />
                                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                            </div>
                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" />
                                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                            </div>
                            <div>
                                <x-input-label for="suffix" :value="__('Suffix')" />
                                <x-text-input id="suffix" name="suffix" type="text" class="mt-1 block w-full" :value="old('suffix')" />
                                <x-input-error class="mt-2" :messages="$errors->get('suffix')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>
                            <div>
                                <x-input-label for="contact_number" :value="__('Contact Number')" />
                                <x-text-input id="contact_number" name="contact_number" type="text" class="mt-1 block w-full" :value="old('contact_number')" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <x-primary-button>{{ __('Create') }}</x-primary-button>
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
