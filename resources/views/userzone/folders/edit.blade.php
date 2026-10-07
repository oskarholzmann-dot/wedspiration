<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit folder
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="post" action="{{ route('user.folders.update', $folder) }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    @include('partials.folder-fields')

                    <div class="flex items-center gap-4">
                        <x-breeze.primary-button>Save</x-breeze.primary-button>
                        <a href="{{ route('user.folders.show', $folder) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
