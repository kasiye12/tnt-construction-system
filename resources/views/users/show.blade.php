@extends('layouts.app')

@section('title', $user->full_name)

@section('content')
<div class="space-y-6">
    <!-- User Profile Card -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ $user->full_name }}</h2>
                        <p class="text-gray-600">{{ $user->position ?? 'No Position' }}</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <span class="px-3 py-1 rounded-full text-sm 
                        @if($user->status == 'active') bg-green-100 text-green-800
                        @elseif($user->status == 'inactive') bg-gray-100 text-gray-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($user->status) }}
                    </span>
                    <a href="{{ route('users.edit', $user) }}" 
                       class="px-3 py-1 bg-blue-500 text-white rounded text-sm">Edit</a>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-semibold">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Phone</p>
                <p class="font-semibold">{{ $user->phone_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Employee ID</p>
                <p class="font-semibold">{{ $user->employee_id ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Position</p>
                <p class="font-semibold">{{ $user->position ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Department</p>
                <p class="font-semibold">{{ $user->department ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Assigned Site</p>
                <p class="font-semibold">{{ $user->site->site_name ?? 'Not assigned' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Joined</p>
                <p class="font-semibold">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Last Seen</p>
                <p class="font-semibold">{{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Check-ins -->
    @if($user->checkins && $user->checkins->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold">Recent Check-ins</h3>
        </div>
        <div class="p-6">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Check In</th>
                        <th class="px-4 py-2">Check Out</th>
                        <th class="px-4 py-2">Site</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->checkins as $checkin)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $checkin->check_in_time->format('M d, Y') }}</td>
                        <td class="px-4 py-2">{{ $checkin->check_in_time->format('H:i A') }}</td>
                        <td class="px-4 py-2">
                            {{ $checkin->check_out_time ? $checkin->check_out_time->format('H:i A') : '---' }}
                        </td>
                        <td class="px-4 py-2">{{ $checkin->site->site_name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                {{ $checkin->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
