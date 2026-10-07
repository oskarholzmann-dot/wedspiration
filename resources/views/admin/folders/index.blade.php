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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Owner</th>
                            <th class="px-4 py-3 font-medium">Wedding date</th>
                            <th class="px-4 py-3 font-medium">Photos</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($folders as $folder)
                            <tr>
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
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">No folders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $folders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
