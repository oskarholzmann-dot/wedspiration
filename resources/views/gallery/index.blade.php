<x-public-layout>
    <x-slot name="title">Gallery</x-slot>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-serif text-3xl text-stone-900">Gallery</h1>
        <p class="mt-2 text-stone-600">Every photo our couples have shared as inspiration.</p>

        @include('gallery._tabs')

        <!-- Category filter -->
        <nav class="mt-6 flex flex-wrap gap-2 text-sm">
            <a href="{{ route('gallery.index') }}"
               @class([
                   'rounded-full px-4 py-1.5 ring-1',
                   'bg-stone-900 text-white ring-stone-900' => ! $activeCategory,
                   'bg-white text-stone-700 ring-stone-300 hover:bg-stone-100' => $activeCategory,
               ])>
                All
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('gallery.index', ['category' => $category->value]) }}"
                   @class([
                       'rounded-full px-4 py-1.5 ring-1',
                       'bg-stone-900 text-white ring-stone-900' => $activeCategory === $category,
                       'bg-white text-stone-700 ring-stone-300 hover:bg-stone-100' => $activeCategory !== $category,
                   ])>
                    {{ $category->label() }}
                </a>
            @endforeach
        </nav>

        @if ($photos->isEmpty())
            <p class="mt-8 text-stone-500">
                @if ($activeCategory)
                    No photos in {{ $activeCategory->label() }} yet.
                @else
                    No photos yet - be the first to share your inspiration.
                @endif
            </p>
        @else
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4" data-infinite data-next="{{ $photos->nextPageUrl() }}">
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
