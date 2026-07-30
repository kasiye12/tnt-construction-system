@extends('layouts.mobile')

@section('title', 'Check In')

@section('content')
<div class="animate-in">
    <!-- Status Card -->
    <div class="card text-center">
        <div style="font-size:64px;margin-bottom:12px;">
            {{ (isset($todayCheckin) && $todayCheckin && !$todayCheckin->check_out_time) ? '🟢' : '🔴' }}
        </div>
        <h2 style="font-size:20px;font-weight:700;color:#1e293b;">
            {{ (isset($todayCheckin) && $todayCheckin) ? ($todayCheckin->check_out_time ? 'Checked Out' : 'On Site') : 'Not Checked In' }}
        </h2>
        
        @if(isset($todayCheckin) && $todayCheckin)
        <div style="margin-top:16px;text-align:left;">
            <div class="list-item">
                <span style="color:#64748b;">Check In Time</span>
                <span style="font-weight:600;">{{ $todayCheckin->check_in_time->format('H:i A') }}</span>
            </div>
            @if($todayCheckin->check_out_time)
            <div class="list-item">
                <span style="color:#64748b;">Check Out Time</span>
                <span style="font-weight:600;">{{ $todayCheckin->check_out_time->format('H:i A') }}</span>
            </div>
            <div class="list-item">
                <span style="color:#64748b;">Hours Worked</span>
                <span style="font-weight:600;">{{ $todayCheckin->hours_worked ?? 'N/A' }} hrs</span>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Action Buttons -->
    @if(!isset($todayCheckin) || !$todayCheckin || $todayCheckin->check_out_time)
    <button onclick="doCheckIn()" class="btn btn-primary" style="margin-bottom:10px;">
        📍 Check In Now
    </button>
    @endif
    
    @if(isset($todayCheckin) && $todayCheckin && !$todayCheckin->check_out_time)
    <button onclick="doCheckOut()" class="btn btn-danger">
        🚪 Check Out
    </button>
    @endif

    <!-- Recent History -->
    @if(isset($recentCheckins) && $recentCheckins->count() > 0)
    <div class="card" style="margin-top:16px;">
        <div class="card-header">📅 Recent History</div>
        @foreach($recentCheckins as $c)
        <div class="list-item">
            <div>
                <div style="font-size:14px;font-weight:500;">{{ $c->created_at->format('M d, Y') }}</div>
                <div style="font-size:12px;color:#64748b;">{{ $c->check_in_time->format('H:i') }} - {{ $c->check_out_time ? $c->check_out_time->format('H:i') : 'Active' }}</div>
            </div>
            <span class="tag {{ $c->check_out_time ? 'tag-success' : 'tag-info' }}">
                {{ $c->check_out_time ? 'Done' : 'Active' }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
function getPosition() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) { reject('GPS not available'); return; }
        navigator.geolocation.getCurrentPosition(
            pos => resolve({ latitude: pos.coords.latitude, longitude: pos.coords.longitude }),
            err => reject('Location access denied. Please enable GPS.')
        );
    });
}

async function doCheckIn() {
    try {
        const pos = await getPosition();
        const res = await fetch('/mobile/checkin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(pos)
        });
        const data = await res.json();
        alert(data.message || 'Checked in!');
        location.reload();
    } catch(e) { alert('Error: ' + e); }
}

async function doCheckOut() {
    if (!confirm('Check out now?')) return;
    const res = await fetch('/mobile/checkout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    const data = await res.json();
    alert(data.message || 'Checked out!');
    location.reload();
}
</script>
@endsection
