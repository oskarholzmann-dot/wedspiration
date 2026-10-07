<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin &middot; Users
            </h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                New user
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Folders</th>
                            <th class="px-4 py-3 font-medium">Photos</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $user->folders_count }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $user->photos_count }}</td>
                                <td class="px-4 py-2 text-right whitespace-nowrap space-x-3">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:underline">View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
