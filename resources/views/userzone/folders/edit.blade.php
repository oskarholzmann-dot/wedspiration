{{-- AI-GENERATED (beyond course scope): users editing / deleting their own folder — written with Claude Code --}}
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

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">Delete folder</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Deletes this folder and the photos you uploaded into it, also from the public gallery.
                    Photos you saved from other couples stay with them. This cannot be undone.
                </p>

                <form method="post" action="{{ route('user.folders.destroy', $folder) }}" class="mt-4"
                      onsubmit="return confirm('Delete this folder and all photos you uploaded into it?')">
                    @csrf
                    @method('delete')

                    <x-breeze.danger-button>Delete folder</x-breeze.danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
