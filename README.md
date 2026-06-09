# Redil API Framework

A lightweight PHP API framework built on top of Slim Framework.

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/redil-api.git
cd redil-api
```

2. Install dependencies:
```bash
composer install
```

3. Create `.env` file:
```bash
cp .env.example .env
```

4. Configure your database in `.env` file:
```
DB_HOST=localhost
DB_NAME=redil_api
DB_USER=your_username
DB_PASS=your_password
```

5. Run migrations:
```bash
php redil migrate
```

6. Start the development server:
```bash
php -S localhost:8000 -t public
```

## Database Migrations

This framework includes a complete migration system with automatic tracking:

```bash
# Run all pending migrations
php redil migrate

# Check migration status
php redil migrate:status

# Rollback last batch
php redil migrate:rollback

# Create a new migration
php redil make:migration create_users_table
```

✨ **Features:**
- Automatically creates a `migrations` tracking table
- Prevents running the same migration twice
- Supports batching and rollback
- Works just like Laravel's migration system

See [MIGRATIONS.md](./MIGRATIONS.md) for detailed documentation.

## Available Commands

**Migrations:**
- `php redil migrate` - Run database migrations
- `php redil migrate:status` - Check migration status
- `php redil migrate:rollback` - Rollback migrations

**Generators:**
- `php redil make:migration create_table_name_table` - Create a new migration
- `php redil make:model ModelName` - Create a new model
- `php redil make:controller ControllerName` - Create a new controller

**Other:**
- `php redil key:generate` - Generate APP_KEY
- `php redil start` - Start development server
- `php redil help` - Show help

See [CLI_COMMANDS.md](./CLI_COMMANDS.md) for more details.

## Project Structure

```
redil-api/
├── app/                          # Application code (edit these)
│   ├── commands/                 # CLI commands
│   ├── controllers/              # API controllers
│   ├── models/                   # Database models
│   ├── middleware/               # Request middleware
│   ├── config/                   # App configuration
│   ├── database/
│   │   ├── migrations/           # Database migrations
│   │   └── seeders/              # Database seeders
│   └── facades/                  # Facade classes
│
├── framework/                    # Framework core (rarely edited)
│   ├── vendor/                   # Composer dependencies
│   ├── bootstrap/                # Framework bootstrap
│   └── Database/                 # Framework utilities
│       └── MigrationManager.php  # Migration tracking
│
├── config/                       # Configuration files
├── routes/                       # API routes
├── public/                       # Web root (index.php)
├── composer.json                 # Dependencies
├── redil                         # CLI entry point
├── MIGRATIONS.md                 # Migration guide
└── STRUCTURE.md                  # Structure documentation
```

**Key Points:**
- `/app` contains your application code - edit these files
- `/framework` contains framework internals - rarely modified
- Framework files are organized separately to keep your project clean

See [STRUCTURE.md](./STRUCTURE.md) for detailed structure information.

## API Documentation

The API follows RESTful principles and returns JSON responses. All endpoints are prefixed with `/api`.

### Response Format

Success Response:
```json
{
    "status": "success",
    "data": {
        // Response data
    }
}
```

Error Response:
```json
{
    "status": "error",
    "message": "Error message"
}
```

## Getting Started

1. **Create your first migration:**
   ```bash
   php redil make:migration create_posts_table
   ```

2. **Edit the migration** in `app/database/migrations/`

3. **Run migrations:**
   ```bash
   php redil migrate
   ```

4. **Create a model:**
   ```bash
   php redil make:model Post
   ```

5. **Create a controller:**
   ```bash
   php redil make:controller PostController
   ```

6. **Define routes** in `routes/api.php`

## Documentation

- [Migrations Guide](./MIGRATIONS.md) - Complete migration system documentation
- [Project Structure](./STRUCTURE.md) - Project structure and organization
- [CLI Commands](./CLI_COMMANDS.md) - All available CLI commands

## License

MIT License