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

See [MIGRATIONS.md](./framework/docs/MIGRATIONS.md) for detailed documentation.

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

See [CLI_COMMANDS.md](./framework/docs/CLI_COMMANDS.md) for more details.

## Project Structure

Everything you edit daily is outside `framework/`; the engine lives inside it.

```
redil-api/
├── app/
│   ├── controllers/          # API controllers
│   ├── models/               # Eloquent models
│   ├── middleware/           # Your custom middleware
│   └── database/migrations/  # Migrations
├── config/                   # app, cors, db, security
├── routes/api.php            # Routes
├── public/index.php          # Web root
├── redil                     # CLI
├── .env.example
└── framework/                # Engine (rarely edited)
    ├── Bootstrap/  Config/  Console/  Database/  Facades/
    ├── Http/ (Controller + Middleware)  Logger/  Support/ (helpers)
    ├── docs/                 # STRUCTURE, CLI_COMMANDS, MIGRATIONS, ...
    └── vendor/               # Composer packages
```

See [framework/docs/STRUCTURE.md](./framework/docs/STRUCTURE.md) for details and [framework/docs/DEPLOYMENT.md](./framework/docs/DEPLOYMENT.md) for Apache/nginx setup and the production checklist.
