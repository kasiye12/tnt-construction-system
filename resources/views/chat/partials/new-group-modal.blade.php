<div id="newGroupModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)hideNewGroupModal()">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl" onclick="event.stopPropagation()">
        <div class="p-5 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">Create Group</h3>
            <button onclick="hideNewGroupModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                <input type="text" id="groupName" placeholder="Enter group name..." 
                       class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Members</label>
                <div class="max-h-48 overflow-y-auto border rounded-xl">
                    @foreach($users as $user)
                    <label class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b last:border-0">
                        <input type="checkbox" value="{{ $user->id }}" class="member-checkbox rounded text-sky-500 focus:ring-sky-500">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm ml-3">
                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                        </div>
                        <span class="ml-3 text-sm font-medium">{{ $user->full_name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="p-5 border-t">
            <button onclick="createGroup()" class="w-full bg-sky-500 text-white py-3 rounded-xl font-medium hover:bg-sky-600 transition">
                Create Group
            </button>
        </div>
    </div>
</div>
