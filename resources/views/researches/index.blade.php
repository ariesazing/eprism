<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Research Record Management') }}
            </h2>
            <a href="{{ route('researches.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                {{ __('Submit New Research') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Code</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Title</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Category</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Unit</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Submitted</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($researches as $research)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $research->research_code }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $research->title }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $research->category?->category_name }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $research->organizationalUnit?->unit_name }}</td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                            {{ $research->status?->status_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-gray-700">{{ optional($research->submitted_at)?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('researches.show', $research) }}" class="text-indigo-600 hover:text-indigo-700">View</a>
                                            <a href="{{ route('researches.edit', $research) }}" class="text-amber-600 hover:text-amber-700">Edit</a>
                                            <form method="POST" action="{{ route('researches.destroy', $research) }}" onsubmit="return confirm('Archive this research record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-700">Archive</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                                        No research submissions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $researches->links() }}
        </div>
    </div>
</x-app-layout>
