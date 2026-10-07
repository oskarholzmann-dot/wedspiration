<x-public-layout>
    <!-- Hero -->
    <section class="bg-white border-b border-stone-200">
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

    <!-- Latest photos -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-baseline justify-between">
            <h2 class="font-serif text-2xl text-stone-900">Latest inspiration</h2>
            <a href="{{ route('gallery.index') }}" class="text-sm text-rose-700 hover:underline">See all photos &rarr;</a>
        </div>

        @if ($latestPhotos->isEmpty())
            <p class="mt-8 text-stone-500">No photos yet - be the first to share your inspiration.</p>
        @else
            <div class="mt-8 columns-2 gap-2 sm:columns-3 lg:columns-4" data-masonry data-masonry-max="4">
                @foreach ($latestPhotos as $photo)
                    <x-photo-card :photo="$photo" />
                @endforeach
            </div>
        @endif
    </section>
</x-public-layout>
