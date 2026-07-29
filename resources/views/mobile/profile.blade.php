@extends('layouts.mobile')

@section('title', 'Profile')

@section('content')
<div class="space-y-4">
    <div class="card text-center">
        <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-3">
            {{ strtoupper(substr($user->full_name, 0, 1)) }}
        </div>
        <h2 class="text-xl font-bold">{{ $user->full_name }}</h2>
        <p class="text-gray-600">{{ $user->position ?? 'Worker' }}</p>
    </div>

    <div class="card">
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Email</span>
                <span>{{ $user->email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Phone</span>
                <span>{{ $user->phone_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Employee ID</span>
                <span>{{ $user->employee_id ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Site</span>
                <span>{{ $user->site->site_name ?? 'Not assigned' }}</span>
            </div>
        </div>
    </div>

    <form action="/logout" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">🚪 Logout</button>
    </form>
</div>
@endsection
