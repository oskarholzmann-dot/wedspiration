<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin &middot; New photo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="post" action="{{ route('admin.photos.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-breeze.input-label for="user_id" value="Owner" />
                        <select id="user_id" name="user_id" required
                                class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">
                            <option value="">- Choose a user -</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">The photo is added to this user's wedding folder.</p>
                        <x-breeze.input-error class="mt-2" :messages="$errors->get('user_id')" />
                    </div>

                    @include('admin.photos._fields')

                    <div class="flex items-center gap-4">
                        <x-breeze.primary-button>Create</x-breeze.primary-button>
                        <a href="{{ route('admin.photos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
