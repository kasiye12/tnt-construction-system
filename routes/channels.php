<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{channelId}', function ($user, $channelId) {
    return true; // Authenticated users can listen to any chat
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('reports', function ($user) {
    return true; // All authenticated users can see reports
});

Broadcast::channel('checkins', function ($user) {
    return true; // All authenticated users can see check-ins
});

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    return true; // All authenticated users
});
