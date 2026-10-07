<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin &middot; Edit photo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="aspect-[4/3] w-full rounded-md object-cover">
                <p class="mt-2 text-sm text-gray-500">Owner: {{ $photo->user->name }}</p>

                <form method="post" action="{{ route('admin.photos.update', $photo) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    @include('admin.photos._fields')

                    <div class="flex items-center gap-4">
                        <x-breeze.primary-button>Save</x-breeze.primary-button>
                        <a href="{{ route('admin.photos.show', $photo) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
