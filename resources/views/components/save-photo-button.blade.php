@props(['photo', 'saved' => false])

{{-- Works without JavaScript as a normal form; public/js/save-photos.js turns it into an instant toggle --}}
<form method="post" action="{{ route('user.saved-photos.store', $photo) }}" data-save-form {{ $attributes }}>
    @csrf
    @if ($saved)
        <input type="hidden" name="_method" value="DELETE">
    @endif

    <button type="submit" data-saved="{{ $saved ? 'true' : 'false' }}"
            title="{{ $saved ? 'Remove from your folder' : 'Save to your folder' }}"
            class="rounded-full px-3 py-1.5 text-xs font-medium shadow-sm ring-1 transition
                   data-[saved=true]:bg-rose-700 data-[saved=true]:text-white data-[saved=true]:ring-rose-700
                   data-[saved=false]:bg-white/90 data-[saved=false]:text-stone-800 data-[saved=false]:ring-stone-300 data-[saved=false]:hover:bg-white">
        {{ $saved ? 'Saved' : 'Save' }}
    </button>
</form>
