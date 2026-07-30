import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'tntreverb',
    wsHost: window.location.hostname,
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

// Log connection
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('✅ Real-time connected!');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('❌ Real-time disconnected');
});

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.log('⚠️ Real-time error:', err);
});
