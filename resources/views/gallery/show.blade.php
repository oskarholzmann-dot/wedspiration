<x-public-layout>
    <x-slot name="title">{{ $photo->title }}</x-slot>

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('gallery.index') }}" class="text-sm text-stone-500 hover:text-stone-800">&larr; Back to the gallery</a>

        <div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-stone-200">
            <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="w-full max-h-[70vh] object-cover">

            <div class="p-6 sm:p-8">
                @if ($photo->category)
                    <p class="text-xs uppercase tracking-wide text-rose-700">{{ $photo->category->label() }}</p>
                @endif

                <h1 class="mt-1 font-serif text-3xl text-stone-900">{{ $photo->title }}</h1>

                @if ($photo->description)
                    <p class="mt-4 text-stone-700">{{ $photo->description }}</p>
                @endif

                <p class="mt-6 text-sm text-stone-500">
                    Shared by {{ $photo->user->name }} on {{ $photo->created_at->format('d.m.Y') }}
                </p>

                @can('update', $photo)
                    <a href="{{ route('user.photos.edit', $photo) }}"
                       class="mt-6 inline-block rounded-md bg-stone-900 px-4 py-2 text-sm text-white hover:bg-stone-700">
                        Edit or delete this photo
                    </a>
                @endcan
            </div>
        </div>
    </section>
</x-public-layout>
