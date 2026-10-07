<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit photo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" class="aspect-[4/3] w-full rounded-md object-cover">

                <form method="post" action="{{ route('user.photos.update', $photo) }}" enctype="multipart/form-data" class="mt-6 space-y-6" data-compress-images>
                    @csrf
                    @method('patch')

                    <div>
                        <x-breeze.input-label for="title" value="Title" />
                        <x-breeze.text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $photo->title)" required autofocus />
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-breeze.input-label for="description" value="Description (optional)" />
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">{{ old('description', $photo->description) }}</textarea>
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div>
                        <x-breeze.input-label for="category" value="Category (optional)" />
                        <select id="category" name="category"
                                class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">
                            <option value="">- No category -</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->value }}" @selected(old('category', $photo->category?->value) === $category->value)>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('category')" />
                    </div>

                    <div>
                        <x-breeze.input-label for="image" value="Replace image (optional; large photos are made smaller automatically)" />
                        <input id="image" name="image" type="file" accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-white hover:file:bg-gray-700">
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('image')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-breeze.primary-button>Save</x-breeze.primary-button>
                        <a href="{{ route('gallery.show', $photo) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">Delete photo</h3>
                <p class="mt-1 text-sm text-gray-600">
                    The photo is removed from your folder and from the public gallery. This cannot be undone.
                </p>

                <form method="post" action="{{ route('user.photos.destroy', $photo) }}" class="mt-4"
                      onsubmit="return confirm('Do you really want to delete this photo?')">
                    @csrf
                    @method('delete')

                    <x-breeze.danger-button>Delete photo</x-breeze.danger-button>
                </form>
            </div>
        </div>
    </div>
    <x-script src="js/photo-upload.js" />
</x-app-layout>
