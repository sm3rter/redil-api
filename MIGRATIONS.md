# Database Migrations Guide

This application now includes a complete migration system similar to Laravel, with automatic tracking of executed migrations.

## How It Works

The migration system automatically:
1. Creates a `migrations` table on first run to track migration history
2. Records which migrations have been executed and in which batch
3. Prevents running the same migration twice
4. Supports rollback to any previous batch

## Migration Commands

### Running Migrations

```bash
php redil migrate
```

This command:
- Creates the `migrations` table if it doesn't exist
- Discovers all migration files in `app/database/migrations/`
- Runs only migrations that haven't been executed yet
- Records each migration in the `migrations` table
- Shows which migrations were skipped (already executed)

### Checking Migration Status

```bash
php redil migrate:status
```

This shows a table with:
- Migration name
- Execution status (✓ Ran or ○ Pending)
- Batch number (when it was executed)

### Rolling Back Migrations

```bash
# Rollback the last batch of migrations
php redil migrate:rollback

# Rollback the last 2 batches
php redil migrate:rollback --steps=2
```

This command:
- Calls the `down()` method of migrations in reverse order
- Removes migration records from the tracking table
- Shows progress for each rollback

## Creating New Migrations

```bash
php redil make:migration create_posts_table
```

This generates a migration file in `app/database/migrations/` with the format:
```
YYYY_MM_DD_HHMMSS_create_posts_table.php
```

### Migration File Structure

```php
<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return new class {
    public function up()
    {
        if (!Capsule::schema()->hasTable('posts')) {
            Capsule::schema()->create('posts', function ($table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Capsule::schema()->hasTable('posts')) {
            Capsule::schema()->drop('posts');
        }
    }
};
```

## Migrations Table Schema

The `migrations` table stores:
- `id` - Auto-incrementing primary key
- `migration` - Migration name (filename without .php)
- `batch` - Batch number (migrations run together have the same batch)
- `executed_at` - Timestamp of execution

## Best Practices

1. **Always create a down() method** - Make sure you can rollback your changes
2. **Use idempotent checks** - Use `hasTable()` and similar checks to prevent errors
3. **One change per migration** - Keep migrations focused and reversible
4. **Name migrations clearly** - Use descriptive names like `create_users_table`, `add_email_to_users`
5. **Test rollbacks** - Always test that your migration can be rolled back properly

## Example Workflow

```bash
# Create a new migration
php redil make:migration create_comments_table

# Check what migrations are pending
php redil migrate:status

# Run all pending migrations
php redil migrate

# Verify migrations were executed
php redil migrate:status

# If something is wrong, rollback
php redil migrate:rollback

# Check status again
php redil migrate:status

# Try again with fixes
php redil migrate
```

## Troubleshooting

### Migration fails to run
- Check the error message in the console
- Verify the migration file is valid PHP
- Ensure the up() method is properly defined

### Can't rollback
- Make sure the down() method is defined
- Check that the migration record exists in the migrations table
- Verify the migration file still exists

### Duplicate migration names
- Use `php redil migrate:status` to see all migrations
- Rename conflicting migrations
- Never modify a migration that's already been executed (in production)

## Framework Folder Structure

All core framework files are now organized in the `/framework` folder:

```
framework/
├── vendor/          # Composer dependencies
├── bootstrap/       # Application bootstrap files
└── Database/
    └── MigrationManager.php  # Migration tracking system
```

This keeps your project root clean and clearly separates framework code from application code.
