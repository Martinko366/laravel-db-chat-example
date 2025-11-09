# Laravel Chat Application

A Teams-like chat application built with Laravel, featuring direct messaging and group chats.

## Features

- ✅ **User Authentication** - Login and registration using Laravel Sanctum
- ✅ **Direct Messaging** - Chat one-on-one with any registered user
- ✅ **Group Chats** - Create group conversations with multiple users
- ✅ **Real-time Updates** - Long polling for near real-time message delivery
- ✅ **User Directory** - View all registered users and start conversations
- ✅ **Conversation Management** - Manage all your direct and group chats

## Technology Stack

- **Laravel 12** - PHP framework
- **Laravel Sanctum** - Authentication
- **laravel-db-chat** - Database-driven chat package with long polling
- **SQLite** - Database (can be changed to MySQL/PostgreSQL)
- **Tailwind CSS** - Styling via CDN

## Installation

The application is already set up! Here's what was done:

1. Installed Laravel Sanctum for authentication
2. Installed martinko366/laravel-db-chat package
3. Published configurations and ran migrations
4. Created authentication system (login/register)
5. Created chat interface with user list and conversation management
6. Seeded database with test users

## Running the Application

1. Start the Laravel development server:
```bash
php artisan serve
```

2. Open your browser and navigate to:
```
http://localhost:8000
```

## Test Users

The following test users have been created (password: `password`):

- Alice Johnson - alice@example.com
- Bob Smith - bob@example.com
- Charlie Brown - charlie@example.com
- Diana Prince - diana@example.com
- Eve Wilson - eve@example.com

## How to Use

### 1. Login/Register
- Navigate to the login page
- Use one of the test accounts or register a new account
- Password for all test accounts: `password`

### 2. Start a Direct Chat
- Click on the "Users" tab in the sidebar
- Click on any user to start a direct conversation
- Type your message and click "Send"

### 3. Create a Group Chat
- Click the "Create Group Chat" button
- Enter a group name
- Select at least 2 members
- Click "Create"

### 4. Switch Between Conversations
- Click on the "Conversations" tab to see all your chats
- Click on any conversation to open it

## Features Overview

### Chat Interface
- **Left Sidebar**: Switch between Users and Conversations tabs
- **Users Tab**: Browse all registered users and start new conversations
- **Conversations Tab**: View all your existing direct and group chats
- **Main Area**: Chat messages with automatic updates via long polling
- **Message Input**: Send text messages to the current conversation

### Real-time Updates
The application uses long polling (not WebSockets) to provide near real-time updates:
- New messages appear automatically
- No page refresh needed
- Efficient database-driven approach
- No external services required (no Redis, Pusher, etc.)

## Architecture

### Authentication Flow
1. Session-based authentication using Laravel's built-in auth
2. CSRF protection for all API requests
3. Middleware protection for chat routes

### Chat System
- **Conversations**: Support for direct (1:1) and group chats
- **Messages**: Stored in database with sender info and timestamps
- **Participants**: Many-to-many relationship between users and conversations
- **Long Polling**: Clients poll for new messages every ~25 seconds

### Database Schema
- `users` - User accounts
- `chat_conversations` - Conversation metadata
- `chat_participants` - User-conversation relationships
- `chat_messages` - Chat messages
- `chat_message_reads` - Read receipts (available but not implemented in UI)

## Configuration

### Chat Settings
Edit `config/dbchat.php` to customize:
- Polling timeout and intervals
- Message length limits
- Rate limiting
- Database table names

### Environment Variables
Add to `.env` to override defaults:
```env
DBCHAT_POLL_TIMEOUT=25
DBCHAT_POLL_CHECK_INTERVAL=500
DBCHAT_MESSAGE_MAX_LENGTH=5000
```

## API Endpoints

The chat package provides these API endpoints (all under `/api/dbchat`):

- `POST /conversations` - Create a new conversation
- `GET /conversations` - List all conversations
- `GET /conversations/{id}` - Get conversation details
- `POST /conversations/{id}/messages` - Send a message
- `GET /conversations/{id}/messages` - Get message history
- `GET /poll` - Long polling for new messages

## Development Notes

### Adding More Features

**Read Receipts**: The database already supports read receipts. To implement:
```javascript
await fetch(`${API_BASE}/messages/${messageId}/read`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken
    }
});
```

**File Attachments**: The messages table supports attachments in JSON format:
```javascript
{
    body: "Check this out!",
    attachments: [
        { type: "image", url: "https://..." }
    ]
}
```

**Typing Indicators**: Can be implemented using a separate polling endpoint or database table.

### Scaling Considerations

For production use:
- Switch from SQLite to MySQL/PostgreSQL
- Add indexes on frequently queried columns
- Consider switching to WebSockets (Reverb, Pusher) for better real-time performance
- Implement message pagination for large conversations
- Add caching for user lists and conversation metadata

## Troubleshooting

### Messages not updating
- Check browser console for JavaScript errors
- Verify you're logged in
- Check that polling is active (should see network requests in browser dev tools)

### Can't send messages
- Verify CSRF token is being sent
- Check session is active
- Ensure conversation is properly loaded

### Database errors
- Make sure migrations ran successfully: `php artisan migrate:status`
- Check database file exists: `database/database.sqlite`

## License

This application uses:
- Laravel (MIT License)
- Laravel Sanctum (MIT License)
- martinko366/laravel-db-chat (MIT License)
