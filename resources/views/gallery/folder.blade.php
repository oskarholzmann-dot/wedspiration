<x-public-layout>
    <x-slot name="title">{{ $folder->name }}</x-slot>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('gallery.folders') }}" class="text-sm text-stone-500 hover:text-stone-800">&larr; All folders</a>

        <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-serif text-3xl text-stone-900">{{ $folder->name }}</h1>
                <p class="mt-2 text-stone-600">
                    {{ $photos->total() }} {{ Str::plural('photo', $photos->total()) }} collected by {{ $folder->user->name }}
                </p>
            </div>

            @can('update', $folder)
                <a href="{{ route('user.folders.edit', $folder) }}"
                   class="rounded-md bg-stone-900 px-4 py-2 text-sm text-white hover:bg-stone-700">
                    Edit folder
                </a>
            @endcan
        </div>

        @if ($photos->isEmpty())
            <p class="mt-8 text-stone-500">This folder has no photos yet.</p>
        @else
            <div class="mt-8 columns-2 gap-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6" data-masonry data-infinite data-next="{{ $photos->nextPageUrl() }}">
                @foreach ($photos as $photo)
                    <x-photo-card :photo="$photo" />
                @endforeach
            </div>

            <div class="mt-10" data-pager>
                {{ $photos->links() }}
            </div>
        @endif
    </section>
</x-public-layout>
