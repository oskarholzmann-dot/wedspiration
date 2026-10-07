<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin &middot; Folders
            </h2>
            <a href="{{ route('admin.folders.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                New folder
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        {{-- Alpine keeps track of the ticked folders; the checkboxes belong to the bulk form via form="bulk-delete" --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4"
             x-data="{ selected: [], all: @js($folders->pluck('id')->map(fn ($id) => (string) $id)) }">

            <form id="bulk-delete" method="post" action="{{ route('admin.folders.bulk-destroy') }}"
                  class="flex flex-wrap items-center gap-4 bg-white px-4 py-3 shadow-sm sm:rounded-lg"
                  x-on:submit="if (! confirm(`Delete ${selected.length} folder(s)?`)) $event.preventDefault()">
                @csrf
                @method('delete')

                <p class="text-sm text-gray-700">
                    <span x-text="selected.length">0</span> selected
                </p>

                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="with_photos" value="1" class="rounded border-gray-400">
                    Also delete the photos the owners uploaded into them
                </label>

                <button type="submit" x-bind:disabled="selected.length === 0"
                        class="ms-auto inline-flex items-center px-4 py-2 bg-red-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 disabled:opacity-40 disabled:cursor-not-allowed">
                    Delete selected
                </button>
            </form>

            <x-breeze.input-error :messages="$errors->get('folders')" class="px-4 sm:px-0" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
                            <th class="w-10 px-4 py-3">
                                <input type="checkbox" aria-label="Select all folders" class="rounded border-gray-400"
                                       x-bind:checked="all.length > 0 && selected.length === all.length"
                                       x-on:change="selected = $event.target.checked ? [...all] : []">
                            </th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Owner</th>
                            <th class="px-4 py-3 font-medium">Wedding date</th>
                            <th class="px-4 py-3 font-medium">Photos</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($folders as $folder)
                            <tr x-bind:class="selected.includes('{{ $folder->id }}') && 'bg-red-50'">
                                <td class="px-4 py-2">
                                    <input type="checkbox" name="folders[]" value="{{ $folder->id }}" form="bulk-delete"
                                           aria-label="Select {{ $folder->name }}" class="rounded border-gray-400"
                                           x-model="selected">
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $folder->name }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $folder->user->name }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $folder->wedding_date?->format('d.m.Y') ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $folder->photos_count }}</td>
                                <td class="px-4 py-2 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('admin.folders.show', $folder) }}" class="text-indigo-600 hover:underline">View</a>
                                        <a href="{{ route('admin.folders.edit', $folder) }}" class="text-indigo-600 hover:underline">Edit</a>
                                        <form method="post" action="{{ route('admin.folders.destroy', $folder) }}"
                                              onsubmit="return confirm('Delete the folder &quot;{{ $folder->name }}&quot;? Its photos stay in the gallery.')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No folders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $folders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
