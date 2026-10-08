<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin &middot; Photos
            </h2>
            <a href="{{ route('admin.photos.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                New photo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        {{-- AI-GENERATED (beyond course scope): bulk delete with checkboxes — written with Claude Code --}}
        {{-- Alpine keeps track of the ticked photos; the checkboxes belong to the bulk form via form="bulk-delete" --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4"
             x-data="{ selected: [], all: @js($photos->pluck('id')->map(fn ($id) => (string) $id)) }">

            <form id="bulk-delete" method="post" action="{{ route('admin.photos.bulk-destroy') }}"
                  class="flex flex-wrap items-center gap-4 bg-white px-4 py-3 shadow-sm sm:rounded-lg"
                  x-on:submit="if (! confirm(`Delete ${selected.length} photo(s)? This cannot be undone.`)) $event.preventDefault()">
                @csrf
                @method('delete')

                <p class="text-sm text-gray-700">
                    <span x-text="selected.length">0</span> selected
                </p>

                <button type="submit" x-bind:disabled="selected.length === 0"
                        class="ms-auto inline-flex items-center px-4 py-2 bg-red-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 disabled:opacity-40 disabled:cursor-not-allowed">
                    Delete selected
                </button>
            </form>

            <x-breeze.input-error :messages="$errors->get('photos')" class="px-4 sm:px-0" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
                            <th class="w-10 px-4 py-3">
                                <input type="checkbox" aria-label="Select all photos" class="rounded border-gray-400"
                                       x-bind:checked="all.length > 0 && selected.length === all.length"
                                       x-on:change="selected = $event.target.checked ? [...all] : []">
                            </th>
                            <th class="px-4 py-3 font-medium">Photo</th>
                            <th class="px-4 py-3 font-medium">Title</th>
                            <th class="px-4 py-3 font-medium">Category</th>
                            <th class="px-4 py-3 font-medium">Owner</th>
                            <th class="px-4 py-3 font-medium">Uploaded</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($photos as $photo)
                            <tr x-bind:class="selected.includes('{{ $photo->id }}') && 'bg-red-50'">
                                <td class="px-4 py-2">
                                    <input type="checkbox" name="photos[]" value="{{ $photo->id }}" form="bulk-delete"
                                           aria-label="Select {{ $photo->title }}" class="rounded border-gray-400"
                                           x-model="selected">
                                </td>
                                <td class="px-4 py-2">
                                    <img src="{{ $photo->image_url }}" alt="" class="h-12 w-16 rounded object-cover">
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $photo->title }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $photo->category?->label() ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $photo->user->name }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $photo->created_at->format('d.m.Y') }}</td>
                                <td class="px-4 py-2 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('admin.photos.show', $photo) }}" class="text-indigo-600 hover:underline">View</a>
                                        <a href="{{ route('admin.photos.edit', $photo) }}" class="text-indigo-600 hover:underline">Edit</a>
                                        <form method="post" action="{{ route('admin.photos.destroy', $photo) }}"
                                              onsubmit="return confirm(@js('Delete the photo "'.$photo->title.'"? This cannot be undone.'))">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">No photos yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $photos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
