<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin &middot; {{ $photo->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('admin.photos.index') }}" class="inline-block px-4 sm:px-0 text-sm text-gray-500 hover:text-gray-800">
                &larr; All photos
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="w-full max-h-[60vh] object-cover">

                <dl class="p-6 grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Title</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $photo->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Category</dt>
                        <dd class="mt-1 text-gray-900">{{ $photo->category?->label() ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Owner</dt>
                        <dd class="mt-1 text-gray-900">{{ $photo->user->name }} ({{ $photo->user->email }})</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">In folders</dt>
                        <dd class="mt-1 text-gray-900">{{ $photo->folders->pluck('name')->join(', ') ?: '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-900">{{ $photo->description ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('admin.photos.edit', $photo) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Edit
                </a>

                <form method="post" action="{{ route('admin.photos.destroy', $photo) }}"
                      onsubmit="return confirm('Do you really want to delete this photo?')">
                    @csrf
                    @method('delete')

                    <x-breeze.danger-button>Delete</x-breeze.danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
