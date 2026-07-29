import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    wssPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

// Chat functionality
class ChatManager {
    constructor(channelId, userId) {
        this.channelId = channelId;
        this.userId = userId;
        this.messageContainer = document.getElementById('messages-container');
        this.messageForm = document.getElementById('message-form');
        this.messageInput = document.getElementById('message-input');
        this.typingIndicator = document.getElementById('typing-indicator');
        this.fileInput = document.getElementById('file-input');
        
        this.init();
    }

    init() {
        // Listen for new messages
        window.Echo.channel(`chat.${this.channelId}`)
            .listen('.message.sent', (e) => {
                if (e.sender.id !== this.userId) {
                    this.appendMessage(e);
                }
            })
            .listen('.user.typing', (e) => {
                if (e.user.id !== this.userId) {
                    this.showTyping(e.user.name, e.is_typing);
                }
            });

        // Form submission
        this.messageForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // Typing indicator
        let typingTimer;
        this.messageInput?.addEventListener('input', () => {
            clearTimeout(typingTimer);
            this.sendTypingStatus(true);
            typingTimer = setTimeout(() => this.sendTypingStatus(false), 1000);
        });

        // File upload
        this.fileInput?.addEventListener('change', () => {
            this.uploadFile();
        });

        // Scroll to bottom
        this.scrollToBottom();

        // Load more on scroll to top
        this.messageContainer?.addEventListener('scroll', () => {
            if (this.messageContainer.scrollTop === 0) {
                this.loadMoreMessages();
            }
        });
    }

    async sendMessage() {
        const body = this.messageInput.value.trim();
        if (!body) return;

        const formData = new FormData();
        formData.append('body', body);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const response = await fetch(`/chat/${this.channelId}/send`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();
            if (data.success) {
                this.appendMessage(data.data, true);
                this.messageInput.value = '';
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Failed to send message:', error);
        }
    }

    async uploadFile() {
        const file = this.fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const response = await fetch(`/chat/${this.channelId}/upload`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();
            if (data.success) {
                this.appendMessage(data.data, true);
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Failed to upload file:', error);
        }
    }

    async sendTypingStatus(isTyping) {
        try {
            await fetch(`/chat/${this.channelId}/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ is_typing: isTyping })
            });
        } catch (error) {
            console.error('Failed to send typing status:', error);
        }
    }

    appendMessage(messageData, isOwn = false) {
        const message = messageData.data || messageData;
        const sender = message.sender || messageData.sender;
        
        if (message.type === 'system') {
            const div = document.createElement('div');
            div.className = 'text-center text-sm text-gray-500 py-2';
            div.textContent = message.body;
            this.messageContainer.appendChild(div);
        } else {
            const template = document.getElementById('message-template');
            const clone = template.content.cloneNode(true);
            
            const messageDiv = clone.querySelector('.message-wrapper');
            messageDiv.classList.add(isOwn ? 'justify-end' : 'justify-start');
            
            if (!isOwn && sender) {
                const avatar = clone.querySelector('.avatar');
                avatar.textContent = sender.name.charAt(0).toUpperCase();
            }
            
            const bubble = clone.querySelector('.message-bubble');
            bubble.classList.add(isOwn ? 'bg-blue-500 text-white' : 'bg-gray-100');
            
            const nameEl = clone.querySelector('.sender-name');
            if (sender && !isOwn) {
                nameEl.textContent = sender.name;
            } else {
                nameEl.remove();
            }
            
            // Handle different message types
            const content = clone.querySelector('.message-content');
            if (message.type === 'image' && message.media_urls) {
                const urls = JSON.parse(message.media_urls);
                const img = document.createElement('img');
                img.src = urls.url;
                img.className = 'max-w-xs rounded mb-2 cursor-pointer';
                img.onclick = () => window.open(urls.url);
                content.appendChild(img);
            } else if (message.type === 'file' && message.media_urls) {
                const urls = JSON.parse(message.media_urls);
                const link = document.createElement('a');
                link.href = urls.url;
                link.className = 'flex items-center text-sm underline';
                link.download = urls.name;
                link.innerHTML = `📎 ${urls.name}`;
                content.appendChild(link);
            } else if (message.body) {
                content.textContent = message.body;
            }
            
            const time = clone.querySelector('.message-time');
            time.textContent = new Date(message.created_at).toLocaleTimeString([], { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            
            // Add reaction buttons
            const actions = clone.querySelector('.message-actions');
            if (!isOwn) {
                const reactBtn = document.createElement('button');
                reactBtn.innerHTML = '😀';
                reactBtn.className = 'text-xs hover:scale-125 transition';
                reactBtn.onclick = () => this.showEmojiPicker(message.id);
                actions.appendChild(reactBtn);
            }
            
            if (isOwn) {
                const editBtn = document.createElement('button');
                editBtn.textContent = 'Edit';
                editBtn.className = 'text-xs text-blue-200 hover:text-white';
                editBtn.onclick = () => this.editMessage(message.id);
                actions.appendChild(editBtn);
                
                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Delete';
                deleteBtn.className = 'text-xs text-red-200 hover:text-white ml-2';
                deleteBtn.onclick = () => this.deleteMessage(message.id);
                actions.appendChild(deleteBtn);
            }
            
            this.messageContainer.appendChild(clone);
        }
        
        this.scrollToBottom();
    }

    async editMessage(messageId) {
        const newBody = prompt('Edit message:');
        if (!newBody) return;

        try {
            await fetch(`/chat/message/${messageId}/edit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ body: newBody })
            });
        } catch (error) {
            console.error('Failed to edit message:', error);
        }
    }

    async deleteMessage(messageId) {
        if (!confirm('Delete this message?')) return;

        try {
            await fetch(`/chat/message/${messageId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        } catch (error) {
            console.error('Failed to delete message:', error);
        }
    }

    async loadMoreMessages() {
        const firstMessage = this.messageContainer.querySelector('.message-wrapper');
        if (!firstMessage) return;

        const beforeId = firstMessage.dataset.messageId;
        
        try {
            const response = await fetch(`/chat/${this.channelId}/messages?before_id=${beforeId}`);
            const data = await response.json();
            
            if (data.data.length > 0) {
                const scrollHeight = this.messageContainer.scrollHeight;
                data.data.reverse().forEach(msg => this.prependMessage(msg));
                this.messageContainer.scrollTop = this.messageContainer.scrollHeight - scrollHeight;
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
        }
    }

    showTyping(userName, isTyping) {
        if (isTyping) {
            this.typingIndicator.textContent = `${userName} is typing...`;
            this.typingIndicator.classList.remove('hidden');
        } else {
            this.typingIndicator.classList.add('hidden');
        }
    }

    scrollToBottom() {
        if (this.messageContainer) {
            this.messageContainer.scrollTop = this.messageContainer.scrollHeight;
        }
    }
}

// Initialize chat when page loads
document.addEventListener('DOMContentLoaded', () => {
    const channelId = document.getElementById('chat-app')?.dataset?.channelId;
    const userId = document.getElementById('chat-app')?.dataset?.userId;
    
    if (channelId && userId) {
        window.chatManager = new ChatManager(channelId, userId);
    }
});
