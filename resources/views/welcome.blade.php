<x-public-layout>
    <!-- Hero: fills the screen between navigation and footer -->
    <section class="flex min-h-[70vh] items-center bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h1 class="font-serif text-4xl sm:text-5xl text-stone-900">
                Collect the moments you dream of
            </h1>
            <p class="mt-6 text-lg text-stone-600">
                Wedspiration is a visual inspiration archive for couples planning their wedding.
                Upload the photos that inspire you, keep them in your personal wedding folder
                and share it with your photographer as a visual brief.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('gallery.index') }}" class="rounded-md bg-stone-900 px-6 py-3 text-white hover:bg-stone-700">
                    Browse the gallery
                </a>
                @guest
                    <a href="{{ route('register') }}" class="rounded-md border border-stone-300 px-6 py-3 text-stone-800 hover:bg-stone-100">
                        Start your folder
                    </a>
                @endguest
            </div>
        </div>
    </section>
</x-public-layout>
