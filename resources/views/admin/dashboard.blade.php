<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ($counts as $label => $count)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-sm text-gray-500">{{ ucfirst($label) }}</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $count }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
