@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Search & Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm text-gray-600">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, email, phone..."
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm text-gray-600">Status</label>
                <select name="status" class="mt-1 block w-full rounded border-gray-300">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Search
                </button>
            </div>
            <div>
                <a href="{{ route('users.create') }}" 
                   class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 inline-block">
                    + Add User
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Site</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium">
                                    <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">
                                        {{ $user->full_name }}
                                    </a>
                                </div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ $user->position ?? 'No Position' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $user->site->site_name ?? 'Not assigned' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($user->status == 'active') bg-green-100 text-green-800
                            @elseif($user->status == 'inactive') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex space-x-3">
                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">View</a>
                            <a href="{{ route('users.edit', $user) }}" class="text-green-600 hover:underline">Edit</a>
                            <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-orange-600 hover:underline">
                                    {{ $user->status == 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
