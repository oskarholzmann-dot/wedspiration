@props(['photo'])

{{-- AI-GENERATED (beyond course scope): photo tiles with lightbox, drag and drop, save/delete buttons and masonry sizes — written with Claude Code --}}

@php
    // Logged-in users can save any photo into their folder (button, or drag onto the drop zone)
    $canSave = auth()->check();
    $saved = $canSave && in_array($photo->id, auth()->user()->savedPhotoIds());
    $size = $photo->image_size;
    $isAdmin = (bool) auth()->user()?->is_admin;

    // Admins may delete any photo (admin route); everyone else only their own photos (PhotoPolicy)
    $deleteUrl = match (true) {
        $isAdmin => route('admin.photos.destroy', $photo),
        (bool) auth()->user()?->can('delete', $photo) => route('user.photos.destroy', $photo),
        default => null,
    };
@endphp

{{-- One photo as a tile in its own shape (no cropping). A click opens it large in the lightbox (public/js/lightbox.js);
     without JavaScript the link simply opens the photo page. --}}
<div class="group relative mb-2 break-inside-avoid rounded-md data-[selected=true]:outline-4 data-[selected=true]:outline-offset-2 data-[selected=true]:outline-rose-600"
     data-photo-card
     data-ratio="{{ round($size['height'] / $size['width'], 4) }}"
     data-lightbox-src="{{ $photo->image_url }}"
     data-title="{{ $photo->title }}"
     data-category="{{ $photo->category?->label() }}"
     data-meta="Shared by {{ $photo->user->name }}"
     data-page-url="{{ route('gallery.show', $photo) }}"
     @if ($deleteUrl) data-delete-url="{{ $deleteUrl }}" @endif
     @if ($isAdmin) data-assign-url="{{ route('admin.photos.subcategory', $photo) }}" data-photo-id="{{ $photo->id }}" @endif
     @if ($canSave) draggable="true" data-save-url="{{ route('user.saved-photos.store', $photo) }}" @endif>
    <a href="{{ route('gallery.show', $photo) }}" draggable="false" data-lightbox-open
       class="block overflow-hidden rounded-md bg-stone-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-700 focus-visible:ring-offset-2">
        {{-- width/height let the browser reserve the right space before the image has loaded --}}
        <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy" draggable="false"
             width="{{ $size['width'] }}" height="{{ $size['height'] }}"
             class="block h-auto w-full transition duration-300 group-hover:scale-105">

        <span class="pointer-events-none absolute inset-x-0 bottom-0 rounded-b-md bg-gradient-to-t from-black/70 to-transparent p-3 pt-10 text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
            @if ($photo->category)
                <span class="block text-[11px] uppercase tracking-wide text-white/80">{{ $photo->category->label() }}</span>
            @endif
            <span class="block text-sm font-medium leading-snug">{{ $photo->title }}</span>
        </span>
    </a>

    {{-- Shown while the photo is selected (admins sort several photos at once) --}}
    <span class="pointer-events-none absolute bottom-2 left-2 hidden h-7 w-7 items-center justify-center rounded-full bg-rose-600 text-white shadow group-data-[selected=true]:flex" aria-hidden="true">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </span>

    @if ($deleteUrl)
        {{-- Owners (and admins) can delete the photo right here, without asking first;
             without JavaScript this is a normal form --}}
        <form method="post" action="{{ $deleteUrl }}" data-delete-form
              class="absolute top-2 left-2 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
            @csrf
            @method('delete')
            {{-- Without JavaScript: come back to this page instead of another list --}}
            <input type="hidden" name="return_to_previous" value="1">
            <button type="submit" title="Delete this photo"
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
