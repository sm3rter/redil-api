# Redil CLI Commands

## Migration Commands

```bash
# Run all pending migrations
php redil migrate

# Check migration status
php redil migrate:status

# Rollback last batch
php redil migrate:rollback

# Rollback last 2 batches
php redil migrate:rollback --steps=2

# Create a new migration
php redil make:migration create_users_table
```

## Generator Commands

```bash
# Create a new model
php redil make:model Post

# Create a new controller
php redil make:controller PostController

# Create a new command
php redil make:command SendEmails
```

## Other Commands

```bash
# Generate a new APP_KEY
php redil key:generate

# Show this help
php redil help

# Start development server
php redil start
```

## Migration Examples

### Create Table
```bash
php redil make:migration create_posts_table
```

### Modify Table
```bash
php redil make:migration add_category_to_posts
```

### Drop Table
```bash
php redil make:migration drop_old_table
```

## Workflow

1. **Create migration:**
   ```bash
   php redil make:migration create_posts_table
   ```

2. **Edit the migration file** in `app/database/migrations/`

3. **Run migrations:**
   ```bash
   php redil migrate
   ```

4. **Check status:**
   ```bash
   php redil migrate:status
   ```

5. **Rollback if needed:**
   ```bash
   php redil migrate:rollback
   ```

## Options

Commands that support options:

```bash
# Rollback specific number of batches
php redil migrate:rollback --steps=3
```

## Help

Get help for any command:
```bash
php redil help command_name
```
