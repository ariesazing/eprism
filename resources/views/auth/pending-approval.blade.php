<x-guest-layout>
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 sm:p-8 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-semibold text-amber-900">
            {{ __('Account Pending Administrative Verification') }}
        </h1>

        <p class="mt-3 text-sm sm:text-base text-amber-800 leading-relaxed">
            {{ __('Your registration was submitted successfully. Your account is waiting for administrator approval before full system access is granted.') }}
        </p>

        <p class="mt-2 text-sm text-amber-700">
            {{ __('You can stay on this page and try again later.') }}
        </p>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <a
                href="{{ route('account.pending') }}"
                class="inline-flex items-center rounded-md bg-amber-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-amber-700"
            >
                {{ __('Refresh Status') }}
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md border border-amber-700 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-800 transition hover:bg-amber-100"
                >
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
