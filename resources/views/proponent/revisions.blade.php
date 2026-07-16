<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Revisions') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="surface-card p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Revision Tracker') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Track returned submissions, required corrections, and update progress in this area.') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
