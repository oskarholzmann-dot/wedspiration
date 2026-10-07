<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin &middot; {{ $folder->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('admin.folders.index') }}" class="inline-block px-4 sm:px-0 text-sm text-gray-500 hover:text-gray-800">
                &larr; All folders
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <dl class="p-6 grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Name</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $folder->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Owner</dt>
                        <dd class="mt-1 text-gray-900">
                            <a href="{{ route('admin.users.show', $folder->user) }}" class="text-indigo-600 hover:underline">{{ $folder->user->name }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Wedding date</dt>
                        <dd class="mt-1 text-gray-900">{{ $folder->wedding_date?->format('d.m.Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Photos</dt>
                        <dd class="mt-1 text-gray-900">{{ $folder->photos->count() }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Notes</dt>
                        <dd class="mt-1 text-gray-900">{{ $folder->notes ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            @if ($folder->photos->isNotEmpty())
                <div class="grid gap-4 grid-cols-2 sm:grid-cols-4">
                    @foreach ($folder->photos as $photo)
                        <a href="{{ route('admin.photos.show', $photo) }}" class="block">
                            <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="aspect-[4/3] w-full rounded-md object-cover">
                            <p class="mt-1 text-xs text-gray-600 truncate">{{ $photo->title }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('admin.folders.edit', $folder) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Edit
                </a>

                <form method="post" action="{{ route('admin.folders.destroy', $folder) }}"
                      onsubmit="return confirm('Delete this folder? Its photos stay in the gallery.')">
                    @csrf
                    @method('delete')

                    <x-breeze.danger-button>Delete</x-breeze.danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
