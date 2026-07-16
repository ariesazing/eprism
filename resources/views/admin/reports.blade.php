<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="surface-card p-6">
                <h3 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Analytics Workspace') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Use this page for reporting dashboards, trend analysis, and admin metrics.') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
