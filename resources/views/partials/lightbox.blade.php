{{-- AI-GENERATED (beyond course scope): lightbox — written with Claude Code --}}
{{-- Large view of one photo over the page. Filled and opened by public/js/lightbox.js --}}
<div data-lightbox hidden role="dialog" aria-modal="true" aria-labelledby="lightbox-title"
     class="fixed inset-0 z-40 flex flex-col bg-stone-950/95 text-white">
    <div class="flex items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <p class="text-sm text-white/60" data-lightbox-position></p>
        <button type="button" data-lightbox-close aria-label="Close"
                class="rounded-full p-2 text-white/80 hover:bg-white/10 hover:text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    <div class="relative flex min-h-0 flex-1 items-center justify-center px-4 sm:px-16">
        <button type="button" data-lightbox-prev aria-label="Previous photo"
                class="absolute left-2 z-10 rounded-full bg-black/30 p-3 text-white/80 hover:bg-white/10 hover:text-white disabled:opacity-20 sm:left-4">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5l-7 7 7 7"/></svg>
        </button>

        {{-- The large photo can be dragged onto the drop zone like a tile --}}
        <div data-lightbox-drag class="flex h-full max-h-full items-center justify-center">
            <img data-lightbox-image src="" alt="" draggable="false" class="max-h-full max-w-full rounded object-contain">
        </div>

        <button type="button" data-lightbox-next aria-label="Next photo"
                class="absolute right-2 z-10 rounded-full bg-black/30 p-3 text-white/80 hover:bg-white/10 hover:text-white disabled:opacity-20 sm:right-4">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <div class="flex flex-wrap items-end justify-between gap-4 px-4 py-4 sm:px-6 pb-[calc(1rem+env(safe-area-inset-bottom))]">
        <div class="min-w-0">
            <p class="text-xs uppercase tracking-wide text-rose-300" data-lightbox-category></p>
            <h2 id="lightbox-title" class="font-serif text-xl" data-lightbox-title></h2>
            <p class="mt-1 text-sm text-white/60">
                <span data-lightbox-meta></span>
                &middot; <a href="#" data-lightbox-page class="underline hover:text-white">Open photo page</a>
            </p>
        </div>

        @auth
          <div class="flex items-center gap-3">
            {{-- Only shown for photos the viewer may delete (their own, or any photo for admins) --}}
            <button type="button" data-lightbox-delete hidden
                    class="rounded-full px-5 py-2 text-sm font-medium text-red-300 ring-1 ring-red-400/60 hover:bg-red-600 hover:text-white hover:ring-red-600">
                Delete
            </button>

            <form method="post" action="" data-save-form data-lightbox-save>
                @csrf
                <button type="submit" data-saved="false"
                        class="rounded-full px-5 py-2 text-sm font-medium ring-1 transition
                               data-[saved=true]:bg-rose-700 data-[saved=true]:text-white data-[saved=true]:ring-rose-700
                               data-[saved=false]:bg-white data-[saved=false]:text-stone-900 data-[saved=false]:ring-white">
                    Save
                </button>
            </form>
          </div>
        @endauth
    </div>
</div>
