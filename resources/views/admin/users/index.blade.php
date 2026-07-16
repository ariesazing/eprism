<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Management') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.pending') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-400">
                    {{ __('Pending Requests') }}
                </a>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    {{ __('Create User') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded-md">{{ session('status') }}</div>
            @endif

            @if ($errors->has('status'))
                <div class="bg-red-100 text-red-800 px-4 py-3 rounded-md">{{ $errors->first('status') }}</div>
            @endif

            <div class="surface-card overflow-hidden">
                <div class="p-6 table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Name') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Email') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Role') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Unit') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Contact') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ $user->full_name }}</td>
                                    <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ $user->email }}</td>
                                    <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ $user->role?->role_name ?? '-' }}</td>
                                    <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ $user->organizationalUnit?->unit_name ?? '-' }}</td>
                                    <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ $user->contact_number ?: '-' }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.users.edit', ['user' => $user->id, 'return_to' => route('admin.users.index', absolute: false)]) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Deactivate this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">{{ __('Deactivate') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">{{ __('No active users found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
