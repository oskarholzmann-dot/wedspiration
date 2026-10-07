<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $folder->name }}
            </h2>
            <a href="{{ route('user.folders.edit', $folder) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Edit folder
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('user.dashboard') }}" class="inline-block px-4 sm:px-0 text-sm text-gray-500 hover:text-gray-800">
                &larr; Back to my folders
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2">
                @if ($folder->wedding_date)
                    <p class="text-sm text-gray-500">Wedding on {{ $folder->wedding_date->format('d.m.Y') }}</p>
                @endif

                @if ($folder->notes)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Notes for the photographer</h3>
                        <p class="mt-1 text-gray-700">{{ $folder->notes }}</p>
                    </div>
                @endif

                <p class="text-sm text-gray-500">
                    {{ $photos->count() }} {{ Str::plural('photo', $photos->count()) }} in this folder
                </p>
            </div>

            @if ($photos->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-500">
                    No photos yet - upload your first inspiration.
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($photos as $photo)
                        <x-photo-card :photo="$photo" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
