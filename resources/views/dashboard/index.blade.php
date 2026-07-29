@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-sky-500 to-blue-600 rounded-2xl p-8 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Welcome back, {{ Auth::user()->full_name }}!</h1>
            <p class="text-sky-100 mt-2">{{ date('l, F d, Y') }} • {{ $stats['workers']['checked_in_today'] }} workers on site today</p>
        </div>
        <div class="hidden md:flex space-x-3">
            <a href="{{ route('reports.create') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                📝 New Report
            </a>
            <a href="{{ route('projects.create') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                🏗️ New Project
            </a>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Active Projects</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['projects']['active'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">🏗️</div>
        </div>
        <div class="mt-3 flex items-center text-sm">
            <span class="text-green-500 font-medium">{{ $stats['projects']['completed'] }} completed</span>
            <span class="text-gray-300 mx-2">•</span>
            <span class="text-gray-500">{{ $stats['projects']['total'] }} total</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">On Site Now</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['workers']['checked_in_today'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">📍</div>
        </div>
        <div class="mt-3 flex items-center text-sm">
            <span class="text-green-500 font-medium">{{ $stats['workers']['total_checkins_today'] }} check-ins</span>
            <span class="text-gray-300 mx-2">•</span>
            <span class="text-gray-500">{{ $stats['workers']['active'] }} active workers</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Reports Today</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['reports']['today'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-2xl">📝</div>
        </div>
        <div class="mt-3 flex items-center text-sm">
            <span class="text-orange-500 font-medium">{{ $stats['reports']['pending'] }} pending</span>
            <span class="text-gray-300 mx-2">•</span>
            <span class="text-gray-500">{{ $stats['reports']['this_week'] }} this week</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Safety Status</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['safety']['open'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-2xl">🦺</div>
        </div>
        <div class="mt-3 flex items-center text-sm">
            <span class="text-red-500 font-medium">{{ $stats['safety']['open'] }} open cases</span>
            <span class="text-gray-300 mx-2">•</span>
            <span class="text-gray-500">{{ $stats['safety']['this_month'] }} this month</span>
        </div>
    </div>
</div>

<!-- Weekly Chart & Active Projects -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Weekly Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Weekly Activity Overview</h3>
        <div class="flex items-end space-x-3 h-48">
            @foreach($weeklyChart as $day)
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full flex flex-col items-center space-y-1">
                    <span class="text-xs font-bold text-gray-600">{{ $day['count'] }}</span>
                    <div class="w-full bg-sky-500 rounded-t-lg transition-all hover:bg-sky-600" 
                         style="height: {{ $day['count'] > 0 ? max(($day['count'] / max(collect($weeklyChart)->max('count'), 1)) * 120, 8) : 4 }}px;"></div>
                </div>
                <span class="text-xs text-gray-500 mt-2">{{ $day['day'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex justify-between mt-4 text-xs text-gray-400">
            <span>Reports</span>
            <span>{{ $stats['reports']['this_week'] }} this week</span>
        </div>
    </div>

    <!-- Active Projects -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Active Projects</h3>
        <div class="space-y-3">
            @foreach($activeProjects as $project)
            <a href="{{ route('projects.show', $project) }}" class="block p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500">{{ $project->sites->count() }} sites • {{ $project->manager->full_name ?? 'N/A' }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Active</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Reports & Check-ins -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Reports -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-5 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Recent Reports</h3>
            <a href="{{ route('reports.index') }}" class="text-sm text-sky-500 hover:text-sky-600">View All →</a>
        </div>
        <div class="p-5">
            @foreach($recentReports as $report)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-lg">📝</div>
                    <div>
                        <p class="text-sm font-semibold">{{ $report->site->site_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $report->submittedBy->full_name ?? 'N/A' }} • {{ $report->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full 
                    @if($report->status == 'approved') bg-green-100 text-green-700
                    @elseif($report->status == 'submitted') bg-yellow-100 text-yellow-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ ucfirst($report->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Check-ins -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-5 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Today's Check-ins</h3>
            <a href="{{ route('users.index') }}" class="text-sm text-sky-500 hover:text-sky-600">View All →</a>
        </div>
        <div class="p-5">
            @foreach($recentCheckins as $checkin)
            <div class="flex items-center justify-between py-3 border-b last:border-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($checkin->user->full_name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ $checkin->user->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $checkin->site->site_name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium">{{ $checkin->check_in_time->format('H:i') }}</p>
                    <p class="text-xs text-green-500">{{ $checkin->status == 'checked_in' ? 'On Site' : 'Checked Out' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <a href="{{ route('reports.create') }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition text-center">
        <div class="text-3xl mb-2">📝</div>
        <p class="text-sm font-semibold text-gray-700">Submit Report</p>
    </a>
    <a href="{{ route('projects.create') }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition text-center">
        <div class="text-3xl mb-2">🏗️</div>
        <p class="text-sm font-semibold text-gray-700">New Project</p>
    </a>
    <a href="{{ route('users.create') }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition text-center">
        <div class="text-3xl mb-2">👤</div>
        <p class="text-sm font-semibold text-gray-700">Add User</p>
    </a>
    <a href="{{ route('chat.index') }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition text-center">
        <div class="text-3xl mb-2">💬</div>
        <p class="text-sm font-semibold text-gray-700">Messages</p>
    </a>
    <a href="{{ route('reports.export') }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition text-center">
        <div class="text-3xl mb-2">📥</div>
        <p class="text-sm font-semibold text-gray-700">Export</p>
    </a>
</div>
@endsection
