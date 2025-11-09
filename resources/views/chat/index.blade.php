@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<div class="h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Laravel Chat</h1>
        <div class="flex items-center space-x-4">
            <span class="text-gray-700">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar - Users and Conversations List -->
        <div class="w-80 bg-white border-r flex flex-col">
            <!-- Tab Headers -->
            <div class="flex border-b">
                <button id="usersTab" class="flex-1 py-3 px-4 text-center font-semibold bg-blue-100 border-b-2 border-blue-500">
                    Users
                </button>
                <button id="conversationsTab" class="flex-1 py-3 px-4 text-center font-semibold hover:bg-gray-100">
                    Conversations
                </button>
            </div>

            <!-- Users Tab Content -->
            <div id="usersContent" class="flex-1 overflow-y-auto">
                <div class="p-4">
                    <input type="text" id="userSearch" placeholder="Search users..." 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div id="usersList" class="space-y-1"></div>
            </div>

            <!-- Conversations Tab Content -->
            <div id="conversationsContent" class="flex-1 overflow-y-auto hidden">
                <div id="conversationsList" class="space-y-1"></div>
            </div>

            <!-- Group Chat Button -->
            <div class="p-4 border-t">
                <button id="createGroupBtn" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Create Group Chat
                </button>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="flex-1 flex flex-col bg-gray-50">
            <!-- Chat Header -->
            <div id="chatHeader" class="bg-white px-6 py-4 shadow-sm border-b hidden">
                <h2 id="chatTitle" class="text-xl font-semibold"></h2>
                <p id="chatSubtitle" class="text-sm text-gray-500"></p>
            </div>

            <!-- Messages Area -->
            <div id="messagesArea" class="flex-1 overflow-y-auto p-6 space-y-4">
                <div class="flex items-center justify-center h-full text-gray-400">
                    Select a user or conversation to start chatting
                </div>
            </div>

            <!-- Message Input -->
            <div id="messageInput" class="bg-white px-6 py-4 border-t hidden">
                <form id="sendMessageForm" class="flex space-x-2">
                    <input type="text" id="messageText" placeholder="Type a message..." 
                        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Group Chat Modal -->
<div id="groupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-96 max-h-[80vh] flex flex-col">
        <h2 class="text-2xl font-bold mb-4">Create Group Chat</h2>
        <input type="text" id="groupTitle" placeholder="Group name..." 
            class="w-full px-3 py-2 border rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="flex-1 overflow-y-auto mb-4">
            <p class="text-sm text-gray-600 mb-2">Select members:</p>
            <div id="groupUsersList" class="space-y-2"></div>
        </div>
        <div class="flex space-x-2">
            <button id="createGroupConfirm" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create
            </button>
            <button id="cancelGroup" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    const API_BASE = '/api/dbchat';
    let currentUser = null;
    let allUsers = [];
    let conversations = [];
    let currentConversation = null;
    let lastMessageId = 0;
    let isPolling = false;

    // CSRF Token for requests
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';
    
    if (!csrfToken) {
        console.error('CSRF token not found!');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Initializing chat application...');
        console.log('CSRF Token:', csrfToken);
        loadUsers();
        loadConversations();
        setupEventListeners();
    });

    function setupEventListeners() {
        console.log('Setting up event listeners...');
        // Tab switching
        document.getElementById('usersTab').addEventListener('click', () => {
            console.log('Users tab clicked');
            switchTab('users');
        });
        document.getElementById('conversationsTab').addEventListener('click', () => {
            console.log('Conversations tab clicked');
            switchTab('conversations');
        });

        // User search
        document.getElementById('userSearch').addEventListener('input', (e) => {
            filterUsers(e.target.value);
        });

        // Send message
        document.getElementById('sendMessageForm').addEventListener('submit', (e) => {
            e.preventDefault();
            sendMessage();
        });

        // Group modal
        document.getElementById('createGroupBtn').addEventListener('click', showGroupModal);
        document.getElementById('cancelGroup').addEventListener('click', hideGroupModal);
        document.getElementById('createGroupConfirm').addEventListener('click', createGroup);
    }

    function switchTab(tab) {
        const usersTab = document.getElementById('usersTab');
        const conversationsTab = document.getElementById('conversationsTab');
        const usersContent = document.getElementById('usersContent');
        const conversationsContent = document.getElementById('conversationsContent');

        if (tab === 'users') {
            usersTab.classList.add('bg-blue-100', 'border-b-2', 'border-blue-500');
            conversationsTab.classList.remove('bg-blue-100', 'border-b-2', 'border-blue-500');
            usersContent.classList.remove('hidden');
            conversationsContent.classList.add('hidden');
        } else {
            conversationsTab.classList.add('bg-blue-100', 'border-b-2', 'border-blue-500');
            usersTab.classList.remove('bg-blue-100', 'border-b-2', 'border-blue-500');
            conversationsContent.classList.remove('hidden');
            usersContent.classList.add('hidden');
        }
    }

    async function loadUsers() {
        try {
            const response = await fetch('/api/users', {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Users data:', data); // Debug log
            currentUser = data.current_user;
            allUsers = Array.isArray(data.users) ? data.users : [];
            renderUsers(allUsers);
        } catch (error) {
            console.error('Error loading users:', error);
            allUsers = []; // Ensure it's an array
            renderUsers([]);
        }
    }

    function renderUsers(users) {
        const usersList = document.getElementById('usersList');
        if (!Array.isArray(users) || users.length === 0) {
            usersList.innerHTML = '<div class="p-4 text-gray-500 text-center">No users found</div>';
            return;
        }
        
        usersList.innerHTML = users.map(user => `
            <div class="user-item px-4 py-3 hover:bg-gray-100 cursor-pointer flex items-center" data-user-id="${user.id}">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold mr-3">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div class="font-semibold">${user.name}</div>
                    <div class="text-sm text-gray-500">${user.email}</div>
                </div>
            </div>
        `).join('');

        // Add click handlers
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', () => {
                const userId = parseInt(item.dataset.userId);
                startDirectChat(userId);
            });
        });
    }

    function filterUsers(query) {
        const filtered = allUsers.filter(user => 
            user.name.toLowerCase().includes(query.toLowerCase()) ||
            user.email.toLowerCase().includes(query.toLowerCase())
        );
        renderUsers(filtered);
    }

    async function loadConversations() {
        try {
            const response = await fetch(`${API_BASE}/conversations`, {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            console.log('Conversations data:', data); // Debug log
            // Handle both array and object responses
            conversations = Array.isArray(data) ? data : (data.data || data.conversations || []);
            renderConversations();
        } catch (error) {
            console.error('Error loading conversations:', error);
            conversations = []; // Ensure it's always an array
        }
    }

    function renderConversations() {
        const conversationsList = document.getElementById('conversationsList');
        if (conversations.length === 0) {
            conversationsList.innerHTML = '<div class="p-4 text-gray-500 text-center">No conversations yet</div>';
            return;
        }

        conversationsList.innerHTML = conversations.map(conv => `
            <div class="conversation-item px-4 py-3 hover:bg-gray-100 cursor-pointer" data-conv-id="${conv.id}">
                <div class="font-semibold">${conv.title || 'Direct Chat'}</div>
                <div class="text-sm text-gray-500">${conv.type}</div>
            </div>
        `).join('');

        // Add click handlers
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', () => {
                const convId = parseInt(item.dataset.convId);
                openConversation(convId);
            });
        });
    }

    async function startDirectChat(userId) {
        const user = allUsers.find(u => u.id === userId);
        if (!user) return;

        // Check if conversation already exists
        const existingConv = conversations.find(c => 
            c.type === 'direct' && 
            c.participants && 
            Array.isArray(c.participants) && 
            c.participants.some(p => p.id === userId)
        );

        if (existingConv) {
            openConversation(existingConv.id);
            return;
        }

        // Create new conversation
        try {
            const response = await fetch(`${API_BASE}/conversations`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    type: 'direct',
                    participants: [userId]
                })
            });

            const data = await response.json();
            conversations.push(data.conversation);
            openConversation(data.conversation.id);
            renderConversations();
        } catch (error) {
            console.error('Error creating conversation:', error);
        }
    }

    async function openConversation(convId) {
        stopPolling();
        currentConversation = conversations.find(c => c.id === convId);
        if (!currentConversation) {
            try {
                const response = await fetch(`${API_BASE}/conversations/${convId}`, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                currentConversation = await response.json();
            } catch (error) {
                console.error('Error loading conversation:', error);
                return;
            }
        }

        // Update UI
        document.getElementById('chatHeader').classList.remove('hidden');
        document.getElementById('messageInput').classList.remove('hidden');
        document.getElementById('chatTitle').textContent = currentConversation.title || 'Direct Chat';
        document.getElementById('chatSubtitle').textContent = 
            currentConversation.type === 'group' && currentConversation.participants ? 
                `${currentConversation.participants.length} members` : '';

        // Load messages
        await loadMessages();
        startPolling();
    }

    async function loadMessages() {
        try {
            const response = await fetch(`${API_BASE}/conversations/${currentConversation.id}/messages?limit=50`, {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            console.log('Messages data:', data); // Debug log
            // Handle both array and object responses
            const messages = Array.isArray(data) ? data : (data.data || data.messages || []);
            
            if (messages.length > 0) {
                lastMessageId = messages[messages.length - 1].id;
            }
            
            renderMessages(messages);
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    function renderMessages(messages) {
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.innerHTML = messages.map(msg => {
            const isMine = msg.sender_id === currentUser.id;
            return `
                <div class="flex ${isMine ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-xs lg:max-w-md">
                        ${!isMine ? `<div class="text-xs text-gray-500 mb-1">${msg.sender?.name || 'Unknown'}</div>` : ''}
                        <div class="${isMine ? 'bg-blue-500 text-white' : 'bg-white'} rounded-lg px-4 py-2 shadow">
                            ${msg.body}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">${new Date(msg.created_at).toLocaleTimeString()}</div>
                    </div>
                </div>
            `;
        }).join('');
        
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    async function sendMessage() {
        const messageText = document.getElementById('messageText');
        const text = messageText.value.trim();
        if (!text || !currentConversation) return;

        try {
            const response = await fetch(`${API_BASE}/conversations/${currentConversation.id}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({ body: text })
            });

            const data = await response.json();
            messageText.value = '';
            
            // Immediately add the message to UI and update lastMessageId
            if (data.message) {
                lastMessageId = data.message.id;
                
                const messagesArea = document.getElementById('messagesArea');
                const isMine = data.message.sender_id === currentUser.id;
                const messageHtml = `
                    <div class="flex ${isMine ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-xs lg:max-w-md">
                            ${!isMine ? `<div class="text-xs text-gray-500 mb-1">${data.message.sender?.name || 'Unknown'}</div>` : ''}
                            <div class="${isMine ? 'bg-blue-500 text-white' : 'bg-white'} rounded-lg px-4 py-2 shadow">
                                ${data.message.body}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">${new Date(data.message.created_at).toLocaleTimeString()}</div>
                        </div>
                    </div>
                `;
                messagesArea.insertAdjacentHTML('beforeend', messageHtml);
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }

    async function poll() {
        if (!currentConversation || !isPolling) return;

        try {
            const response = await fetch(`${API_BASE}/poll?after_message_id=${lastMessageId}`, {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (response.status === 200) {
                const data = await response.json();
                lastMessageId = data.last_message_id;
                
                // Handle messages array
                const messages = Array.isArray(data.messages) ? data.messages : [];
                
                // Filter messages for current conversation
                const newMessages = messages.filter(m => m.conversation_id === currentConversation.id);
                if (newMessages.length > 0) {
                    const messagesArea = document.getElementById('messagesArea');
                    const currentMessages = Array.from(messagesArea.children);
                    
                    newMessages.forEach(msg => {
                        const isMine = msg.sender_id === currentUser.id;
                        const messageHtml = `
                            <div class="flex ${isMine ? 'justify-end' : 'justify-start'}">
                                <div class="max-w-xs lg:max-w-md">
                                    ${!isMine ? `<div class="text-xs text-gray-500 mb-1">${msg.sender?.name || 'Unknown'}</div>` : ''}
                                    <div class="${isMine ? 'bg-blue-500 text-white' : 'bg-white'} rounded-lg px-4 py-2 shadow">
                                        ${msg.body}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">${new Date(msg.created_at).toLocaleTimeString()}</div>
                                </div>
                            </div>
                        `;
                        messagesArea.insertAdjacentHTML('beforeend', messageHtml);
                    });
                    
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
            await new Promise(resolve => setTimeout(resolve, 5000));
        }

        if (isPolling) {
            setTimeout(poll, 100);
        }
    }

    function startPolling() {
        isPolling = true;
        poll();
    }

    function stopPolling() {
        isPolling = false;
    }

    function showGroupModal() {
        const modal = document.getElementById('groupModal');
        const groupUsersList = document.getElementById('groupUsersList');
        
        if (!Array.isArray(allUsers) || allUsers.length === 0) {
            alert('No users available. Please wait for users to load.');
            return;
        }
        
        groupUsersList.innerHTML = allUsers.map(user => `
            <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                <input type="checkbox" value="${user.id}" class="group-user-checkbox">
                <span>${user.name}</span>
            </label>
        `).join('');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideGroupModal() {
        const modal = document.getElementById('groupModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('groupTitle').value = '';
        // Uncheck all checkboxes
        document.querySelectorAll('.group-user-checkbox:checked').forEach(cb => cb.checked = false);
    }

    async function createGroup() {
        const title = document.getElementById('groupTitle').value.trim();
        const selectedUsers = Array.from(document.querySelectorAll('.group-user-checkbox:checked'))
            .map(cb => parseInt(cb.value));

        console.log('Creating group:', { title, selectedUsers, count: selectedUsers.length });

        if (!title) {
            alert('Please enter a group name');
            return;
        }

        if (selectedUsers.length < 2) {
            alert(`Please select at least 2 members. Currently selected: ${selectedUsers.length}`);
            return;
        }

        try {
            const response = await fetch(`${API_BASE}/conversations`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    type: 'group',
                    title: title,
                    participants: selectedUsers
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Group created:', data);
            
            if (data.conversation) {
                conversations.push(data.conversation);
                hideGroupModal();
                renderConversations();
                switchTab('conversations');
                openConversation(data.conversation.id);
            }
        } catch (error) {
            console.error('Error creating group:', error);
            alert('Failed to create group. Please try again.');
        }
    }
</script>
@endsection
