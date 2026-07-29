@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold">Create New User</h2>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input type="text" name="full_name" required 
                       class="mt-1 block w-full rounded border-gray-300"
                       value="{{ old('full_name') }}">
                @error('full_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" required 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('email') }}">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number *</label>
                    <input type="text" name="phone_number" required 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('phone_number') }}" placeholder="+251...">
                    @error('phone_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" required 
                           class="mt-1 block w-full rounded border-gray-300"
                           placeholder="Minimum 8 characters">
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                    <input type="text" name="employee_id" 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('employee_id') }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role *</label>
                    <select name="role" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="project_manager">Project Manager</option>
                        <option value="site_engineer">Site Engineer</option>
                        <option value="worker">Worker</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assign to Site</label>
                    <select name="site_id" class="mt-1 block w-full rounded border-gray-300">
                        <option value="">No Site</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->site_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department</label>
                    <input type="text" name="department" 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('department') }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Position</label>
                <input type="text" name="position" 
                       class="mt-1 block w-full rounded border-gray-300"
                       value="{{ old('position') }}">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
