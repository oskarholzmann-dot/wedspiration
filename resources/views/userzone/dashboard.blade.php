<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Welcome, {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="px-4 sm:px-0 flex flex-wrap items-center justify-between gap-4">
                <p class="text-gray-600">
                    Your wedding folders. Every photo you upload is added to your folder and to the public gallery.
                </p>
                <a href="{{ route('user.photos.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Upload photo
                </a>
            </div>

            @forelse ($folders as $folder)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <a href="{{ route('user.folders.show', $folder) }}" class="hover:underline">{{ $folder->name }}</a>
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $folder->photos_count }} {{ Str::plural('photo', $folder->photos_count) }}
                            @if ($folder->wedding_date)
                                &middot; Wedding on {{ $folder->wedding_date->format('d.m.Y') }}
                            @endif
                        </p>
                    </div>

                    @if ($folder->photos->isEmpty())
                        <p class="mt-4 text-gray-500">No photos yet - upload your first inspiration.</p>
                    @else
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($folder->photos as $photo)
                                <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}"
                                     class="aspect-[4/3] w-full rounded-md object-cover">
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-500">
                    You don't have a wedding folder yet. Upload a photo or save one from the gallery, and a new folder is created for you.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
