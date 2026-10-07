<x-public-layout>
    <x-slot name="title">Gallery folders</x-slot>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-serif text-3xl text-stone-900">Gallery</h1>
        <p class="mt-2 text-stone-600">Every couple's collection of inspiration, one folder each.</p>

        @include('gallery._tabs')

        @if ($folders->isEmpty())
            <p class="mt-8 text-stone-500">No folders with photos yet.</p>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($folders as $folder)
                    <a href="{{ route('gallery.folders.show', $folder) }}"
                       class="group block overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-stone-200 hover:shadow-md transition">
                        <div class="aspect-[4/3] overflow-hidden bg-stone-100">
                            <img src="{{ $folder->photos->first()->image_url }}" alt="{{ $folder->name }}" loading="lazy"
                                 class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-stone-900">{{ $folder->name }}</h3>
                            <p class="mt-1 text-sm text-stone-500">
                                {{ $folder->photos_count }} {{ Str::plural('photo', $folder->photos_count) }}
                                &middot; by {{ $folder->user->name }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $folders->links() }}
            </div>
        @endif
    </section>
</x-public-layout>
