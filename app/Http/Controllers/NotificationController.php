<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        DB::table('notifications')->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back()->with('success', 'All marked read');
    }

    public function getUnreadCount()
    {
        $count = DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', 'App\\Models\\User')
            ->whereNull('read_at')
            ->count();
        return response()->json(['count' => $count]);
    }

    public function getLatest()
    {
        $notifications = DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', 'App\\Models\\User')
            ->latest()
            ->take(20)
            ->get()
            ->map(function($n) {
                $n->data = is_string($n->data) ? json_decode($n->data, true) : ($n->data ?? []);
                return $n;
            });

        $unreadCount = DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', 'App\\Models\\User')
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}
