<nav x-data="{ open: false }" class="bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <a href="{{ route('welcome') }}" class="shrink-0 flex items-center font-serif text-2xl text-stone-900">
                    Wedspiration
                </a>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-breeze.nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                        Home
                    </x-breeze.nav-link>
                    <x-breeze.nav-link :href="route('gallery.index')" :active="request()->routeIs('gallery.*')">
                        Gallery
                    </x-breeze.nav-link>
                    <x-breeze.nav-link :href="route('about')" :active="request()->routeIs('about')">
                        About
                    </x-breeze.nav-link>
                    <x-breeze.nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                        Contact
                    </x-breeze.nav-link>
                </div>
            </div>

            <!-- Login / Register, or a link to the userzone when logged in -->
            <div class="hidden sm:flex sm:items-center sm:gap-4 text-sm">
                @auth
                    <a href="{{ route('user.dashboard') }}" class="rounded-md bg-stone-900 px-4 py-2 text-white hover:bg-stone-700">
                        My folder
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-stone-600 hover:text-stone-900">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-stone-900 px-4 py-2 text-white hover:bg-stone-700">
                        Register
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-stone-400 hover:text-stone-500 hover:bg-stone-100 focus:outline-none focus:bg-stone-100 focus:text-stone-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-breeze.responsive-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                Home
            </x-breeze.responsive-nav-link>
            <x-breeze.responsive-nav-link :href="route('gallery.index')" :active="request()->routeIs('gallery.*')">
                Gallery
            </x-breeze.responsive-nav-link>
            <x-breeze.responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                About
            </x-breeze.responsive-nav-link>
            <x-breeze.responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                Contact
            </x-breeze.responsive-nav-link>
        </div>

        <div class="pt-4 pb-3 border-t border-stone-200 space-y-1">
            @auth
                <x-breeze.responsive-nav-link :href="route('user.dashboard')">
                    My folder
                </x-breeze.responsive-nav-link>
            @else
                <x-breeze.responsive-nav-link :href="route('login')">
                    Log in
                </x-breeze.responsive-nav-link>
                <x-breeze.responsive-nav-link :href="route('register')">
                    Register
                </x-breeze.responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>
