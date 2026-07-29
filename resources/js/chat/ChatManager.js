// File: resources/js/chat/ChatManager.js

export class ChatManager {
    constructor() {
        this.activeChannel = null;
        this.channels = new Map();
        this.setupEchoListeners();
    }

    setupEchoListeners() {
        // Listen for new messages on all user channels
        window.Echo.private('App.Models.User.' + window.App.user.id)
            .notification((notification) => {
                this.handleNotification(notification);
            });
    }

    joinChannel(channelId) {
        if (this.channels.has(channelId)) {
            this.activeChannel = channelId;
            return;
        }

        const channel = window.Echo.private(`chat.${channelId}`);

        channel
            .listen('.message.sent', (event) => {
                this.onMessageReceived(event);
            })
            .listen('.message.updated', (event) => {
                this.onMessageUpdated(event);
            })
            .listen('.message.deleted', (event) => {
                this.onMessageDeleted(event);
            })
            .listen('.user.typing', (event) => {
                this.onUserTyping(event);
            });

        this.channels.set(channelId, channel);
        this.activeChannel = channelId;
    }

    leaveChannel(channelId) {
        const channel = this.channels.get(channelId);
        if (channel) {
            window.Echo.leave(`chat.${channelId}`);
            this.channels.delete(channelId);
        }
    }

    async sendMessage(channelId, messageData) {
        try {
            const response = await fetch(`/api/v1/chat/channels/${channelId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify(messageData)
            });

            return await response.json();
        } catch (error) {
            console.error('Failed to send message:', error);
            throw error;
        }
    }

    async loadMessages(channelId, page = 1) {
        try {
            const response = await fetch(
                `/api/v1/chat/channels/${channelId}/messages?page=${page}`,
                {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                }
            );
            return await response.json();
        } catch (error) {
            console.error('Failed to load messages:', error);
            throw error;
        }
    }

    onMessageReceived(event) {
        // Handle new message
        const event = new CustomEvent('new-message', { detail: event.message });
        window.dispatchEvent(event);
    }

    onMessageUpdated(event) {
        const event = new CustomEvent('message-updated', { detail: event.message });
        window.dispatchEvent(event);
    }

    onMessageDeleted(event) {
        const event = new CustomEvent('message-deleted', { detail: event.messageId });
        window.dispatchEvent(event);
    }

    onUserTyping(event) {
        const event = new CustomEvent('user-typing', { detail: event });
        window.dispatchEvent(event);
    }

    handleNotification(notification) {
        // Handle push notifications
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.body,
                icon: '/logo.png'
            });
        }
    }
}