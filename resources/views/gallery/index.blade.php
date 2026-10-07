<x-public-layout>
    <x-slot name="title">Gallery</x-slot>

    @php
        $isAdmin = (bool) auth()->user()?->is_admin;
        // Filters combine: changing one row keeps the choice in the other
        $categoryUrl = fn ($category) => route('gallery.index', array_filter(['category' => $category?->value, 'subcategory' => $activeSubcategory?->id]));
        $subcategoryUrl = fn ($subcategory) => route('gallery.index', array_filter(['category' => $activeCategory?->value, 'subcategory' => $subcategory?->id]));
        $pill = 'rounded-full px-4 py-1.5 ring-1';
        $pillActive = 'bg-stone-900 text-white ring-stone-900';
        $pillIdle = 'bg-white text-stone-700 ring-stone-300 hover:bg-stone-100';
    @endphp

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-serif text-3xl text-stone-900">Gallery</h1>
        <p class="mt-2 text-stone-600">Every photo our couples have shared as inspiration.</p>

        @include('gallery._tabs')

        {{-- Sticky: stays visible while scrolling, so photos can be dragged onto a subcategory from anywhere --}}
        <div class="sticky top-0 z-30 -mx-4 mt-6 space-y-3 bg-stone-50/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <!-- Category filter -->
            <nav class="flex flex-wrap gap-2 text-sm" aria-label="Categories">
                <a href="{{ $categoryUrl(null) }}" @class([$pill, $pillActive => ! $activeCategory, $pillIdle => $activeCategory])>All</a>
                @foreach ($categories as $category)
                    <a href="{{ $categoryUrl($category) }}"
                       @if ($isAdmin) data-category-drop="{{ $category->value }}" @endif
                       @class([$pill, 'transition data-[over=true]:scale-110 data-[over=true]:ring-2 data-[over=true]:ring-rose-700', $pillActive => $activeCategory === $category, $pillIdle => $activeCategory !== $category])>
                        {{ $category->label() }}
                    </a>
                @endforeach
            </nav>

            <!-- Subcategory filter; for admins every subcategory is also a drop target -->
            @if ($subcategories->isNotEmpty() || $isAdmin)
                <nav class="flex flex-wrap items-center gap-2 text-sm" aria-label="Subcategories">
                    <span class="me-1 text-xs uppercase tracking-wide text-stone-500">Subcategories</span>

                    {{-- Admins: dropping photos on "All" takes them out of their subcategory --}}
                    <a href="{{ $subcategoryUrl(null) }}"
                       @if ($isAdmin) data-subcategory-clear title="Drop photos here to take them out of their subcategory" @endif
                       @class(['rounded-full px-3 py-1 ring-1 transition data-[over=true]:scale-110 data-[over=true]:ring-2 data-[over=true]:ring-rose-700', $pillActive => ! $activeSubcategory, $pillIdle => $activeSubcategory])>
                        All
                    </a>

                    @foreach ($subcategories as $subcategory)
                        <a href="{{ $subcategoryUrl($subcategory) }}"
                           @if ($isAdmin) data-subcategory-drop="{{ $subcategory->id }}" @endif
                           @class([
                               'rounded-full px-3 py-1 ring-1 transition data-[over=true]:scale-110 data-[over=true]:ring-2 data-[over=true]:ring-rose-700',
                               $pillActive => $activeSubcategory?->is($subcategory),
                               $pillIdle => ! $activeSubcategory?->is($subcategory),
                           ])>
                            {{ $subcategory->name }}
                            <span class="ms-1 opacity-60" data-subcategory-count="{{ $subcategory->id }}">{{ $subcategory->photos_count }}</span>
                        </a>
                    @endforeach

                    @if ($isAdmin)
                        <form method="post" action="{{ route('admin.subcategories.store') }}" class="flex items-center gap-1">
                            @csrf
                            <input type="text" name="name" placeholder="New subcategory" maxlength="50" required
                                   aria-label="Name of the new subcategory"
                                   class="w-40 rounded-full border border-stone-300 bg-white px-3 py-1 text-sm focus:border-stone-800 focus:ring-0">
                            <button type="submit" class="rounded-full bg-stone-900 px-3 py-1 text-white hover:bg-stone-700" aria-label="Create subcategory">+</button>
                        </form>

                        @if ($activeSubcategory)
                            <form method="post" action="{{ route('admin.subcategories.destroy', $activeSubcategory) }}"
                                  onsubmit="return confirm(@js('Delete the subcategory "'.$activeSubcategory->name.'"? Its photos stay in the gallery.'))">
                                @csrf
                                @method('delete')
                                <button type="submit" class="rounded-full px-3 py-1 text-red-700 ring-1 ring-red-200 hover:bg-red-600 hover:text-white">
                                    Delete "{{ $activeSubcategory->name }}"
                                </button>
                            </form>
                        @endif
                    @endif
                </nav>

                @if ($isAdmin)
                    <x-breeze.input-error :messages="$errors->get('name')" />
                @endif
            @endif

            @if ($isAdmin)
                <!-- Admin: select several photos, then drag them together onto a category or subcategory -->
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <button type="button" data-select-toggle aria-pressed="false" data-sort-url="{{ route('admin.photos.sort') }}"
                            class="rounded-full px-3 py-1 ring-1 ring-stone-300 bg-white text-stone-700 hover:bg-stone-100 aria-pressed:bg-rose-700 aria-pressed:text-white aria-pressed:ring-rose-700">
                        Select photos
                    </button>
                    <span data-selection-info hidden class="text-stone-700">
                        <span data-selection-count>0</span> selected
                        &middot; <button type="button" data-selection-clear class="underline hover:text-stone-900">Clear</button>
                    </span>
                    <span class="text-xs text-stone-500">
                        Drag photos onto a category or subcategory to sort them. Shift-click selects several.
                    </span>
                </div>
            @endif
        </div>

        @if ($photos->isEmpty())
            <p class="mt-8 text-stone-500">
                @if ($activeSubcategory)
                    No photos in {{ $activeSubcategory->name }} yet.
                @elseif ($activeCategory)
                    No photos in {{ $activeCategory->label() }} yet.
                @else
                    No photos yet - be the first to share your inspiration.
                @endif
            </p>
        @else
            <div class="mt-6 columns-2 gap-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6" data-masonry
                 data-infinite data-next="{{ $photos->nextPageUrl() }}"
                 @if ($activeSubcategory) data-active-subcategory="{{ $activeSubcategory->id }}" @endif
                 @if ($activeCategory) data-active-category="{{ $activeCategory->value }}" @endif>
                @foreach ($photos as $photo)
                    <x-photo-card :photo="$photo" />
                @endforeach
            </div>

            <div class="mt-10" data-pager>
                {{ $photos->links() }}
            </div>
        @endif
    </section>

    @if ($isAdmin)
        <x-script src="js/admin-sorting.js" />
    @endif
</x-public-layout>
