@extends('layouts.mobile')

@section('title', 'Profile')

@section('content')
<div class="animate-in">
    <div class="card text-center">
        <div class="avatar" style="width:72px;height:72px;font-size:28px;margin:0 auto 12px;background:linear-gradient(135deg,#0ea5e9,#8b5cf6);">
            {{ strtoupper(substr($user->full_name ?? 'U', 0, 1)) }}
        </div>
        <h2 style="font-size:20px;font-weight:700;">{{ $user->full_name ?? 'Worker' }}</h2>
        <p style="color:#64748b;font-size:13px;">{{ $user->position ?? 'Staff' }}</p>
    </div>

    <div class="card">
        <div class="list-item"><span style="color:#64748b;">Email</span><span style="font-weight:500;">{{ $user->email }}</span></div>
        <div class="list-item"><span style="color:#64748b;">Phone</span><span style="font-weight:500;">{{ $user->phone_number }}</span></div>
        <div class="list-item"><span style="color:#64748b;">Employee ID</span><span style="font-weight:500;">{{ $user->employee_id ?? 'N/A' }}</span></div>
        <div class="list-item"><span style="color:#64748b;">Site</span><span style="font-weight:500;">{{ $user->site->site_name ?? 'Not assigned' }}</span></div>
        <div class="list-item"><span style="color:#64748b;">Joined</span><span style="font-weight:500;">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span></div>
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">🚪 Sign Out</button>
    </form>
</div>
@endsection
