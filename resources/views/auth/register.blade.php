<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- DepEd ID -->
        <div>
            <x-input-label for="deped_id" :value="__('DepEd ID (Optional)')" />
            <x-text-input id="deped_id" class="block mt-1 w-full" type="text" name="deped_id" :value="old('deped_id')" autofocus />
            <x-input-error :messages="$errors->get('deped_id')" class="mt-2" />
        </div>

        <!-- First Name -->
        <div class="mt-4">
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Middle Name -->
        <div class="mt-4">
            <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
            <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" />
            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Suffix -->
        <div class="mt-4">
            <x-input-label for="suffix" :value="__('Suffix (Optional)')" />
            <x-text-input id="suffix" class="block mt-1 w-full" type="text" name="suffix" :value="old('suffix')" />
            <x-input-error :messages="$errors->get('suffix')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Position Title -->
        <div class="mt-4">
            <x-input-label for="position_title" :value="__('Position Title')" />
            <x-text-input id="position_title" class="block mt-1 w-full" type="text" name="position_title" :value="old('position_title')" required />
            <x-input-error :messages="$errors->get('position_title')" class="mt-2" />
        </div>

        <!-- Contact Number -->
        <div class="mt-4">
            <x-input-label for="contact_number" :value="__('Contact Number (Optional)')" />
            <x-text-input id="contact_number" class="block mt-1 w-full" type="text" name="contact_number" :value="old('contact_number')" />
            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
        </div>

        <!-- Organizational Unit -->
        <div class="mt-4">
            <x-input-label for="organizational_unit_id" :value="__('Organizational Unit')" />
            <select id="organizational_unit_id" name="organizational_unit_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                <option value="">{{ __('Select Unit') }}</option>
                @foreach ($organizationalUnits as $unit)
                    <option value="{{ $unit->id }}" @selected(old('organizational_unit_id') == $unit->id)>
                        {{ $unit->unit_name }}{{ $unit->unit_code ? ' ('.$unit->unit_code.')' : '' }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('organizational_unit_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
