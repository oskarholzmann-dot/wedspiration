@props(['photo'])

<a href="{{ route('gallery.show', $photo) }}" class="group block overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-stone-200 hover:shadow-md transition">
    <div class="aspect-[4/3] overflow-hidden bg-stone-100">
        <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy"
             class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
    </div>
    <div class="p-4">
        @if ($photo->category)
            <p class="text-xs uppercase tracking-wide text-rose-700">{{ $photo->category->label() }}</p>
        @endif
        <h3 class="mt-1 font-medium text-stone-900">{{ $photo->title }}</h3>
    </div>
</a>
