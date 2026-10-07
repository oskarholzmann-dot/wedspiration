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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
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
                            <tr>
                                <td class="px-4 py-2">
                                    <img src="{{ $photo->image_url }}" alt="" class="h-12 w-16 rounded object-cover">
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $photo->title }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $photo->category?->label() ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $photo->user->name }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $photo->created_at->format('d.m.Y') }}</td>
                                <td class="px-4 py-2 text-right whitespace-nowrap space-x-3">
                                    <a href="{{ route('admin.photos.show', $photo) }}" class="text-indigo-600 hover:underline">View</a>
                                    <a href="{{ route('admin.photos.edit', $photo) }}" class="text-indigo-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No photos yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $photos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
