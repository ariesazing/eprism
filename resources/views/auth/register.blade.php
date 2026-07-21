<x-guest-layout>
    <div
        x-data="{ showVerificationModal: @js((bool) session('registration_verification_sent')) }"
        x-cloak
    >
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

        <!-- Middle Initial -->
        <div class="mt-4">
            <x-input-label for="middle_name" :value="__('Middle Initial')" />
            <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" maxlength="1" required />
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
            <select id="position_title" name="position_title" class="form-control-ui mt-1 block w-full" required>
                <option value="">{{ __('Select Position Title') }}</option>
                @foreach ($positionTitles as $title)
                    <option value="{{ $title }}" @selected(old('position_title') === $title)>{{ $title }}</option>
                @endforeach
            </select>
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
            <select id="organizational_unit_id" name="organizational_unit_id" class="form-control-ui mt-1 block w-full" required>
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
        <div class="mt-4" x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-10"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                required autocomplete="new-password" />
                <button
                    type="button"
                    class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-gray-500 hover:text-gray-700"
                    @click="showPassword = !showPassword"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                >
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                    </svg>
                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.292-3.95M9.88 9.88a3 3 0 104.24 4.24" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.1 6.1A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.98 9.98 0 01-4.132 5.145M3 3l18 18" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4" x-data="{ showConfirmPassword: false }">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-10"
                                x-bind:type="showConfirmPassword ? 'text' : 'password'"
                                name="password_confirmation" required autocomplete="new-password" />
                <button
                    type="button"
                    class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-gray-500 hover:text-gray-700"
                    @click="showConfirmPassword = !showConfirmPassword"
                    :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                >
                    <svg x-show="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                    </svg>
                    <svg x-show="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.292-3.95M9.88 9.88a3 3 0 104.24 4.24" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.1 6.1A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.98 9.98 0 01-4.132 5.145M3 3l18 18" />
                    </svg>
                </button>
            </div>

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

    <div
        x-show="showVerificationModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        style="display: none;"
        @keydown.escape.window="showVerificationModal = false"
        role="dialog"
        aria-modal="true"
        aria-labelledby="registration-verification-title"
    >
        <div
            x-show="showVerificationModal"
            x-transition
            class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-800"
            @click.away="showVerificationModal = false"
        >
            <h2 id="registration-verification-title" class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Check your email') }}
            </h2>

            <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                {{ session('registration_verification_message') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-primary-button type="button" x-on:click="showVerificationModal = false">
                    {{ __('OK') }}
                </x-primary-button>
            </div>
        </div>
    </div>
    </div>
</x-guest-layout>
