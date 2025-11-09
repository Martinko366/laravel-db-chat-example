# Laravel DB Chat - Example Application

A **Teams-like** chat application demonstrating the integration of [Laravel DB Chat](https://packagist.org/packages/martinko366/laravel-db-chat) package with Laravel Sanctum authentication. This example shows how to build a complete real-time messaging system using only database-driven long polling - **no WebSockets, Redis, or Pusher required!**

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## 🎯 What This Example Demonstrates

This application showcases:

- ✅ **User Authentication** - Complete login/register system using Laravel Sanctum
- ✅ **Direct Messaging** - One-on-one conversations with any registered user
- ✅ **Group Chats** - Create and manage group conversations with multiple participants
- ✅ **Real-time Updates** - Near real-time message delivery using long polling
- ✅ **User Directory** - Browse all registered users and start conversations instantly
- ✅ **Pure Database Solution** - No external services (Redis, Pusher, etc.) required

## 🚀 What You Can Do

### As a User:
1. **Register/Login** - Create an account or use test credentials
2. **Browse Users** - See all registered users in the left sidebar
3. **Start Direct Chats** - Click any user to begin a private conversation
4. **Create Groups** - Click "Create Group Chat" to make a group with multiple users
5. **Send Messages** - Type and send messages that appear in real-time for other users
6. **Switch Conversations** - Toggle between the "Users" and "Conversations" tabs

### As a Developer:
- **Study the Code** - See how laravel-db-chat integrates with a Laravel application
- **Understand Long Polling** - Learn how database-driven real-time works without WebSockets
- **Authentication Patterns** - Explore session-based auth with Sanctum
- **API Integration** - See how to consume the chat package's REST API
- **UI/UX Implementation** - Review the Tailwind CSS chat interface design

## 📦 Technology Stack

- **[Laravel 12](https://laravel.com)** - PHP web application framework
- **[Laravel Sanctum](https://laravel.com/docs/sanctum)** - Authentication system
- **[Laravel DB Chat](https://packagist.org/packages/martinko366/laravel-db-chat)** - Database-driven chat package
- **SQLite** - Lightweight database (easily switchable to MySQL/PostgreSQL)
- **[Tailwind CSS](https://tailwindcss.com)** - Utility-first CSS framework (via CDN)
- **Vanilla JavaScript** - No frontend framework required

## ⚡ Quick Start

### 1. Clone & Install
```bash
git clone <repository-url>
cd laravel-db-chat-test
composer install
```

### 2. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database
```bash
php artisan migrate --seed
```
This creates the SQLite database and seeds 5 test users.

### 4. Start Development Server
```bash
php artisan serve
```

### 5. Access the Application
Open your browser to: `http://127.0.0.1:8000`

## 👥 Test Accounts

The application comes with 5 pre-seeded test users (all with password: `password`):

| Name | Email | Password |
|------|-------|----------|
| Alice Johnson | alice@example.com | password |
| Bob Smith | bob@example.com | password |
| Charlie Brown | charlie@example.com | password |
| Diana Prince | diana@example.com | password |
| Eve Wilson | eve@example.com | password |

## 🎮 Try It Out

### Demo Scenario: Real-time Chat
1. **Open two browsers** (e.g., Chrome and Firefox)
2. **Login as Alice** in the first browser
3. **Login as Bob** in the second browser
4. In Alice's browser:
   - Click on "Bob Smith" in the Users tab
   - Send a message: "Hi Bob!"
5. In Bob's browser:
   - The message appears automatically via long polling
   - Reply: "Hello Alice!"
6. Watch messages appear in both browsers in real-time!

### Demo Scenario: Group Chat
1. **Login as any user**
2. **Click "Create Group Chat"**
3. **Enter a group name** (e.g., "Team Meeting")
4. **Select 2 or more users** from the list
5. **Click "Create"**
6. **Send messages** to the group - all members can see them!

## 📱 Interface Overview

```
┌──────────────────────────────────────────────────────────────┐
│  Laravel Chat                       Alice Johnson  [Logout]  │
├─────────────────┬────────────────────────────────────────────┤
│ [Users] [Chats] │  💬 Chat with: Bob Smith                   │
├─────────────────┼────────────────────────────────────────────┤
│ 🔍 Search...    │                                            │
│                 │  Bob: Hey, how are you?      10:30 AM     │
│ 👤 Bob Smith    │                                            │
│ 👤 Charlie      │  You: I'm good, thanks!     10:31 AM     │
│ 👤 Diana        │                                            │
│ 👤 Eve Wilson   │                                            │
│                 │                                            │
│                 │                                            │
│ [Create Group]  ├────────────────────────────────────────────┤
│                 │  [Type a message...]           [Send]     │
└─────────────────┴────────────────────────────────────────────┘
```

## 🏗️ Architecture

### How It Works
- **Long Polling**: Frontend continuously polls `/api/dbchat/poll` for new messages
- **Database Storage**: All messages, conversations, and participants stored in SQLite
- **Session Auth**: Laravel's session-based authentication (no API tokens in localStorage)
- **CSRF Protection**: All POST requests include CSRF tokens
- **No Broadcasting**: Pure HTTP requests - no WebSocket connections

### API Endpoints
The laravel-db-chat package automatically registers these routes under `/api/dbchat`:

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/conversations` | Create a new direct or group conversation |
| GET | `/conversations` | List all user's conversations |
| GET | `/conversations/{id}` | Get conversation details |
| POST | `/conversations/{id}/messages` | Send a message |
| GET | `/conversations/{id}/messages` | Fetch message history |
| GET | `/poll` | Long poll for new messages |
| POST | `/messages/{id}/read` | Mark message as read |

## 📚 Documentation Files

- **`README.md`** (this file) - Example overview and quick start
- **`QUICKSTART.md`** - 3-step getting started guide
- **`CHAT_README.md`** - Complete technical documentation
- **`SETUP_SUMMARY.md`** - Detailed setup and architecture info

## 🔧 Configuration

### Chat Settings
Edit `config/dbchat.php`:
```php
'polling' => [
    'timeout' => 25,  // Seconds to wait for new messages
    'check_interval' => 500,  // Milliseconds between checks
],
```

### Database
To switch from SQLite to MySQL, update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 🎓 Learning Resources

### Understanding Long Polling
This example uses **long polling** instead of WebSockets:
- Server holds the request open for up to 25 seconds
- Returns immediately if new messages arrive
- Returns 204 (No Content) if timeout expires
- Client immediately makes a new poll request

**Advantages:**
- Works through firewalls and proxies
- No special server configuration needed
- Compatible with all hosting environments
- Simple to debug and understand

**Trade-offs:**
- Slightly higher latency than WebSockets (~1-2 seconds)
- More HTTP overhead
- Less scalable for thousands of concurrent users

### When to Use This Approach
✅ **Good for:**
- Small to medium applications (< 1000 concurrent users)
- Corporate environments with restrictive firewalls
- Shared hosting without WebSocket support
- Learning/prototyping real-time features
- Internal tools and admin panels

❌ **Consider WebSockets for:**
- Large-scale public applications
- Sub-second latency requirements
- Thousands of concurrent users
- Mobile apps needing battery efficiency

## 🚀 Next Steps

### Extend This Example
- [ ] Add file upload support for images/documents
- [ ] Implement read receipts in the UI
- [ ] Add typing indicators
- [ ] Show user online/offline status
- [ ] Add message search
- [ ] Implement message reactions (emoji)
- [ ] Add user profile pages
- [ ] Enable message editing/deletion

### Production Checklist
- [ ] Switch to MySQL/PostgreSQL
- [ ] Configure proper caching (Redis)
- [ ] Set up queue workers for background jobs
- [ ] Add rate limiting
- [ ] Implement logging and monitoring
- [ ] Set up automated backups
- [ ] Consider switching to WebSockets (Laravel Reverb)

## 📄 License

This example application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Credits

- Built with [Laravel](https://laravel.com)
- Uses [martinko366/laravel-db-chat](https://packagist.org/packages/martinko366/laravel-db-chat)
- Styled with [Tailwind CSS](https://tailwindcss.com)

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
