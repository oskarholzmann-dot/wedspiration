{{-- Switch between the photo view and the folder view of the gallery --}}
<div class="mt-6 inline-flex rounded-lg bg-stone-200/60 p-1 text-sm">
    <a href="{{ route('gallery.index') }}"
       @class([
           'rounded-md px-4 py-1.5',
           'bg-white text-stone-900 shadow-sm' => request()->routeIs('gallery.index'),
           'text-stone-600 hover:text-stone-900' => ! request()->routeIs('gallery.index'),
       ])>
        Photos
    </a>
    <a href="{{ route('gallery.folders') }}"
       @class([
           'rounded-md px-4 py-1.5',
           'bg-white text-stone-900 shadow-sm' => request()->routeIs('gallery.folders'),
           'text-stone-600 hover:text-stone-900' => ! request()->routeIs('gallery.folders'),
       ])>
        Folders
    </a>
</div>
