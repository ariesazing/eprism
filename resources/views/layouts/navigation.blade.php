<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-violet-100/70 bg-white/80 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-violet-700" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if (Auth::user()?->role?->role_name === 'Administrator')
                        <x-nav-link :href="route('researches.index')" :active="request()->routeIs('researches.*')">
                            {{ __('Research') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.repository')" :active="request()->routeIs('admin.repository')">
                            {{ __('Repository') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('User Management') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                            {{ __('Reports & Analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.templates')" :active="request()->routeIs('admin.templates')">
                            {{ __('Templates') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.announcements')" :active="request()->routeIs('admin.announcements')">
                            {{ __('Announcements') }}
                        </x-nav-link>
                    @elseif (Auth::user()?->role?->role_name === 'Proponent')
                        <x-nav-link :href="route('researches.index')" :active="request()->routeIs('researches.index') || request()->routeIs('researches.show')">
                            {{ __('My Research') }}
                        </x-nav-link>
                        <x-nav-link :href="route('researches.create')" :active="request()->routeIs('researches.create')">
                            {{ __('Submit Research') }}
                        </x-nav-link>
                        <x-nav-link :href="route('proponent.revisions')" :active="request()->routeIs('proponent.revisions')">
                            {{ __('Revisions') }}
                        </x-nav-link>
                        <x-nav-link :href="route('proponent.announcements')" :active="request()->routeIs('proponent.announcements')">
                            {{ __('Announcements') }}
                        </x-nav-link>
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                            {{ __('Profile') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('researches.index')" :active="request()->routeIs('researches.*')">
                            {{ __('Research') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-violet-100 text-sm leading-4 font-medium rounded-xl text-gray-700 bg-white hover:bg-violet-50 focus:outline-none transition ease-in-out duration-150">
                            <span class="grid h-8 w-8 place-items-center rounded-full bg-violet-100 text-violet-700 text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name ?? '', 0, 1)) }}
                            </span>
                            <div class="text-left">
                                <div>{{ Auth::user()->full_name }}</div>
                                <div class="text-[11px] text-violet-700">{{ Auth::user()->role?->role_name ?? 'User' }}</div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-black hover:bg-violet-50 focus:bg-violet-50">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                                <x-dropdown-link :href="route('logout')" class="text-black hover:bg-violet-50 focus:bg-violet-50"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-violet-600 hover:text-violet-700 hover:bg-violet-50 focus:outline-none focus:bg-violet-100 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 border-t border-violet-100">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (Auth::user()?->role?->role_name === 'Administrator')
                <x-responsive-nav-link :href="route('researches.index')" :active="request()->routeIs('researches.*')">
                    {{ __('Research') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.repository')" :active="request()->routeIs('admin.repository')">
                    {{ __('Repository') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('User Management') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                    {{ __('Reports & Analytics') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.templates')" :active="request()->routeIs('admin.templates')">
                    {{ __('Templates') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.announcements')" :active="request()->routeIs('admin.announcements')">
                    {{ __('Announcements') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()?->role?->role_name === 'Proponent')
                <x-responsive-nav-link :href="route('researches.index')" :active="request()->routeIs('researches.index') || request()->routeIs('researches.show')">
                    {{ __('My Research') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('researches.create')" :active="request()->routeIs('researches.create')">
                    {{ __('Submit Research') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('proponent.revisions')" :active="request()->routeIs('proponent.revisions')">
                    {{ __('Revisions') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('proponent.announcements')" :active="request()->routeIs('proponent.announcements')">
                    {{ __('Announcements') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('researches.index')" :active="request()->routeIs('researches.*')">
                    {{ __('Research') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-violet-100">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->full_name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="mt-1 inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-700">{{ Auth::user()->role?->role_name ?? 'User' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
