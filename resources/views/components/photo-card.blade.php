@props(['photo'])

@php
    // Logged-in users can save any photo into their folder (button, or drag onto the drop zone)
    $canSave = auth()->check();
    $saved = $canSave && in_array($photo->id, auth()->user()->savedPhotoIds());
@endphp

{{-- One photo as a square tile. A click opens it large in the lightbox (public/js/lightbox.js);
     without JavaScript the link simply opens the photo page. --}}
<div class="group relative"
     data-photo-card
     data-lightbox-src="{{ $photo->image_url }}"
     data-title="{{ $photo->title }}"
     data-category="{{ $photo->category?->label() }}"
     data-meta="Shared by {{ $photo->user->name }}"
     data-page-url="{{ route('gallery.show', $photo) }}"
     @if (auth()->user()?->is_admin) data-delete-url="{{ route('admin.photos.destroy', $photo) }}" @endif
     @if ($canSave) draggable="true" data-save-url="{{ route('user.saved-photos.store', $photo) }}" @endif>
    <a href="{{ route('gallery.show', $photo) }}" draggable="false" data-lightbox-open
       class="block aspect-square overflow-hidden rounded-md bg-stone-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-700 focus-visible:ring-offset-2">
        <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy" draggable="false"
             class="h-full w-full object-cover transition duration-300 group-hover:scale-105">

        <span class="pointer-events-none absolute inset-x-0 bottom-0 rounded-b-md bg-gradient-to-t from-black/70 to-transparent p-3 pt-10 text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
            @if ($photo->category)
                <span class="block text-[11px] uppercase tracking-wide text-white/80">{{ $photo->category->label() }}</span>
            @endif
            <span class="block text-sm font-medium leading-snug">{{ $photo->title }}</span>
        </span>
    </a>

    @if (auth()->user()?->is_admin)
        {{-- Admins can delete any photo right here; without JavaScript this is a normal form --}}
        <form method="post" action="{{ route('admin.photos.destroy', $photo) }}" data-admin-delete
              class="absolute top-2 left-2 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100"
              onsubmit="return confirm(@js('Delete "'.$photo->title.'" for everyone? This cannot be undone.'))">
            @csrf
            @method('delete')
            <button type="submit" title="Delete this photo (admin)"
                    class="rounded-full bg-white/90 px-3 py-1.5 text-xs font-medium text-red-700 shadow-sm ring-1 ring-red-200 hover:bg-red-600 hover:text-white hover:ring-red-600">
                Delete
            </button>
        </form>
    @endif

    @if ($canSave)
        <x-save-photo-button :photo="$photo" :saved="$saved"
                             class="absolute top-2 right-2 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100 sm:has-[button[data-saved=true]]:opacity-100" />
    @endif
</div>
