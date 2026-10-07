<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin &middot; {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('admin.users.index') }}" class="inline-block px-4 sm:px-0 text-sm text-gray-500 hover:text-gray-800">
                &larr; All users
            </a>

            <x-breeze.input-error :messages="$errors->get('user')" class="px-4 sm:px-0" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <dl class="p-6 grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Name</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Role</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->is_admin ? 'Admin' : 'User' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Registered</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->created_at->format('d.m.Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Photos</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->photos_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Folders</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->folders->pluck('name')->join(', ') ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Edit
                </a>

                @unless ($user->is(Auth::user()))
                    <form method="post" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('Delete this user with all their folders and photos?')">
                        @csrf
                        @method('delete')

                        <x-breeze.danger-button>Delete</x-breeze.danger-button>
                    </form>
                @endunless
            </div>
        </div>
    </div>
</x-app-layout>
