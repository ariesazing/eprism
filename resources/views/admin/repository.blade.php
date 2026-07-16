<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Repository') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="surface-card p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Research Storage') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('This section is reserved for categorized research storage and filtering tools.') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
