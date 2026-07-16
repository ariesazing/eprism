<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <p>{{ __("You're logged in!") }}</p>

                    @if (auth()->user()?->role?->role_name === 'Administrator')
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('researches.index') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-500">
                                {{ __('Research') }}
                            </a>
                            <a href="{{ route('researches.create') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-500">
                                {{ __('Submit Research') }}
                            </a>
                        </div>
                    @elseif (auth()->user()?->role?->role_name === 'Proponent')
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('researches.index') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-500">
                                {{ __('My Research') }}
                            </a>
                            <a href="{{ route('researches.create') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-500">
                                {{ __('Submit Research') }}
                            </a>
                            <a href="{{ route('proponent.revisions') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-500">
                                {{ __('Revisions') }}
                            </a>
                            <a href="{{ route('proponent.announcements') }}" class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-500">
                                {{ __('Announcements') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">
                                {{ __('Profile') }}
                            </a>
                        </div>
                    @else
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('researches.index') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-500">
                                {{ __('Research') }}
                            </a>
                        </div>
                    @endif

                    @if (auth()->user()?->role?->role_name === 'Administrator')
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                {{ __('User Management') }}
                            </a>
                            <a href="{{ route('admin.repository') }}" class="inline-flex items-center px-4 py-2 bg-slate-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-500">
                                {{ __('Repository') }}
                            </a>
                            <a href="{{ route('admin.reports') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500">
                                {{ __('Reports & Analytics') }}
                            </a>
                            <a href="{{ route('admin.templates') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-500">
                                {{ __('Templates') }}
                            </a>
                            <a href="{{ route('admin.announcements') }}" class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-500">
                                {{ __('Announcements') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
