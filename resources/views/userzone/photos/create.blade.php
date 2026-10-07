<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload photos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">
                    Your photos are added to your wedding folder and shown in the public gallery.
                    You can select several at once; large photos are made smaller automatically.
                </p>

                <form method="post" action="{{ route('user.photos.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6" data-multi-upload>
                    @csrf

                    <div>
                        <x-breeze.input-label value="Photos" />

                        {{-- Drop photos or whole folders here, or use one of the two buttons --}}
                        <div data-upload-drop data-over="false"
                             class="mt-1 rounded-lg border-2 border-dashed border-gray-300 px-6 py-8 text-center transition data-[over=true]:border-gray-800 data-[over=true]:bg-gray-50">
                            <p class="text-sm text-gray-600">Drag photos or whole folders here</p>
                            <p class="mt-1 text-xs text-gray-500">or</p>

                            <div class="mt-3 flex flex-wrap justify-center gap-3">
                                <label class="cursor-pointer rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2">
                                    Choose photos
                                    <input id="image" name="image" type="file" accept="image/*" multiple class="sr-only">
                                </label>
                                <label class="cursor-pointer rounded-md border border-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-800 hover:bg-gray-100 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2">
                                    Choose a folder
                                    <input type="file" webkitdirectory multiple class="sr-only">
                                </label>
                            </div>

                            <p data-upload-summary hidden class="mt-4 text-sm font-medium text-gray-800" aria-live="polite"></p>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">Only photos are uploaded; other files in a folder are skipped. Large photos are made smaller automatically.</p>
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('image')" />
                    </div>

                    <div>
                        <x-breeze.input-label for="title" value="Title (optional - otherwise taken from the file name)" />
                        <x-breeze.text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-breeze.input-label for="description" value="Description (optional, for all selected photos)" />
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">{{ old('description') }}</textarea>
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div>
                        <x-breeze.input-label for="category" value="Category (optional, for all selected photos)" />
                        <select id="category" name="category"
                                class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">
                            <option value="">- No category -</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->value }}" @selected(old('category') === $category->value)>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('category')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-breeze.primary-button>Upload</x-breeze.primary-button>
                        <a href="{{ route('user.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>

                    <div data-upload-status hidden class="text-sm text-gray-700" aria-live="polite"></div>
                </form>
            </div>
        </div>
    </div>

    <x-script src="js/photo-upload.js" />
</x-app-layout>
