<div id="userListModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)hideUserList()">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl" onclick="event.stopPropagation()">
        <div class="p-5 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">New Message</h3>
            <button onclick="hideUserList()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div class="p-4">
            <input type="text" placeholder="Search people..." 
                   class="w-full px-4 py-2.5 bg-gray-100 rounded-xl text-sm mb-3"
                   oninput="filterUsers(this.value)">
        </div>
        <div class="max-h-80 overflow-y-auto px-2 pb-4">
            @foreach($users as $user)
            <div onclick="startDirectChat({{ $user->id }})" 
                 class="user-item flex items-center p-3 hover:bg-gray-50 rounded-xl cursor-pointer transition">
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($user->full_name, 0, 1)) }}
                    </div>
                    @if($user->last_seen_at && $user->last_seen_at->diffInMinutes(now()) < 5)
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                    @endif
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold">{{ $user->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->position ?? 'Staff' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function filterUsers(query) {
    document.querySelectorAll('.user-item').forEach(item => {
        const name = item.querySelector('.text-sm').textContent.toLowerCase();
        item.style.display = name.includes(query.toLowerCase()) ? 'flex' : 'none';
    });
}
</script>
