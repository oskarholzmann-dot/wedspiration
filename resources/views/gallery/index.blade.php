<x-public-layout>
    <x-slot name="title">Gallery</x-slot>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-serif text-3xl text-stone-900">Gallery</h1>
        <p class="mt-2 text-stone-600">Every photo our couples have shared as inspiration.</p>

        @if ($photos->isEmpty())
            <p class="mt-8 text-stone-500">No photos yet - be the first to share your inspiration.</p>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($photos as $photo)
                    <x-photo-card :photo="$photo" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $photos->links() }}
            </div>
        @endif
    </section>
</x-public-layout>
