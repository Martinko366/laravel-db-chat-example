# Quick Start Guide

## 🚀 Getting Started in 3 Steps

### 1. Start the Server
The server should already be running at `http://127.0.0.1:8000`

If not, run:
```bash
php artisan serve
```

### 2. Login with Test Account
Open your browser to: `http://127.0.0.1:8000`

Use any of these test accounts:
- **Email**: alice@example.com | **Password**: password
- **Email**: bob@example.com | **Password**: password
- **Email**: charlie@example.com | **Password**: password
- **Email**: diana@example.com | **Password**: password
- **Email**: eve@example.com | **Password**: password

### 3. Start Chatting!

**To chat with a user:**
1. Click on any user in the "Users" tab
2. Type a message and hit "Send"

**To create a group:**
1. Click "Create Group Chat" button
2. Enter a group name
3. Select 2 or more users
4. Click "Create"

**To test real-time messaging:**
1. Open the app in two different browsers
2. Login as different users (e.g., Alice in Chrome, Bob in Firefox)
3. Start a chat between them
4. Messages will appear in real-time via long polling!

## 🎯 Demo Scenario

Try this to see all features:

1. **Login as Alice** (alice@example.com)
2. **Start a direct chat with Bob** - Click on Bob Smith in the Users tab
3. **Send a message** - Type "Hi Bob!" and send
4. **Create a group chat** - Click "Create Group Chat"
   - Name it "Team Meeting"
   - Select Bob, Charlie, and Diana
   - Click Create
5. **Send a group message** - Type "Hello everyone!" in the group chat
6. **Open in another browser as Bob** - See the messages appear!

## 📱 Interface Overview

```
┌─────────────────────────────────────────────────────────┐
│  Laravel Chat                    [Your Name] [Logout]   │
├──────────────┬──────────────────────────────────────────┤
│              │  Chat with: Bob Smith                    │
│ [Users]      ├──────────────────────────────────────────┤
│ [Conversations] │                                       │
│              │  Messages appear here...                 │
│ 👤 Alice     │                                          │
│ 👤 Bob       │  Bob: Hi there!         10:30 AM        │
│ 👤 Charlie   │                                          │
│ 👤 Diana     │  You: Hello!            10:31 AM        │
│ 👤 Eve       │                                          │
│              │                                          │
│ [Search...]  │                                          │
│              ├──────────────────────────────────────────┤
│              │ [Type a message...] [Send]              │
│ [Create Group] │                                         │
└──────────────┴──────────────────────────────────────────┘
```

## 🔧 Troubleshooting

**Can't see messages updating?**
- Open browser console (F12) - check for errors
- Make sure you're logged in
- Check the Network tab - you should see regular polling requests

**Login not working?**
- Make sure the database was seeded: `php artisan db:seed`
- Password is `password` (lowercase) for all test users

**Want to start fresh?**
```bash
php artisan migrate:fresh --seed
```

This will reset the database and recreate all test users.

## 🎨 Customization

**Change the polling interval:**
Edit `config/dbchat.php`:
```php
'polling' => [
    'timeout' => 25,  // Seconds to wait for new messages
    'check_interval' => 500,  // Milliseconds between checks
],
```

**Change the app name:**
Edit `.env`:
```
APP_NAME="My Awesome Chat"
```

## 📚 More Information

See `CHAT_README.md` for complete documentation including:
- Architecture details
- API endpoints
- Development notes
- Scaling considerations

Enjoy your new chat application! 🎉
