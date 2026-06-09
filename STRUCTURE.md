# Project Structure

## Overview

The application has been reorganized to separate framework core files from application code:

```
redil-api/
├── app/                          # Application code (edit these files)
│   ├── commands/                 # CLI commands
│   ├── config/                   # App configuration
│   ├── console/                  # Console kernel
│   ├── controllers/              # API controllers
│   ├── database/
│   │   ├── migrations/           # Database migrations
│   │   └── seeders/              # Database seeders
│   ├── facades/                  # Facade classes
│   ├── middleware/               # HTTP middleware
│   └── models/                   # Eloquent models
│
├── framework/                    # Framework & dependencies (rarely modified)
│   ├── vendor/                   # Composer packages
│   ├── bootstrap/                # Framework bootstrap files
│   └── Database/
│       └── MigrationManager.php  # Migration tracking system
│
├── config/                       # Application configuration files
│   ├── app.php
│   ├── cors.php
│   ├── db.php
│   └── security.php
│
├── routes/                       # API route definitions
│   └── api.php
│
├── public/                       # Public entry point
│   └── index.php                 # Application entry
│
├── composer.json                 # Project dependencies
├── composer.lock                 # Locked dependency versions
├── redil                         # CLI entry point
├── README.md                     # Project readme
├── MIGRATIONS.md                 # Migration guide
└── STRUCTURE.md                  # This file
```

## Directory Descriptions

### `/app` - Application Code
Where you build your application. Contains all the custom code for your project:
- **commands/** - Custom CLI commands for your application
- **config/** - App-specific configuration
- **console/** - Console command handling
- **controllers/** - Request handlers and business logic
- **database/** - Migrations and database seeders
- **facades/** - Simplified interfaces to complex subsystems
- **middleware/** - HTTP middleware for requests
- **models/** - Eloquent ORM models

### `/framework` - Framework & Dependencies
Contains all framework files that you rarely (if ever) need to modify:
- **vendor/** - Composer dependencies (auto-installed from composer.lock)
- **bootstrap/** - Framework initialization and bootstrapping
- **Database/** - Core database utilities like MigrationManager

### `/config` - Configuration
Application-wide configuration files for database, security, CORS, etc.

### `/routes` - Route Definitions
API route definitions and endpoint setup.

### `/public` - Public Entry Point
The web server points to this directory. Only `index.php` should be here.

## What Should You Edit?

✅ **Edit these directories:**
- `/app` - All application code
- `/config` - Configuration values
- `/routes` - Add or modify API routes
- `.env` - Environment variables

❌ **Don't edit these:**
- `/framework` - Framework internals (unless extending)
- `/composer.lock` - Auto-generated, don't commit changes

## Common Tasks

### Add a New Model
```bash
php redil make:model Post
```
Creates `/app/models/Post.php`

### Create a Migration
```bash
php redil make:migration create_posts_table
```
Creates `/app/database/migrations/YYYY_MM_DD_HHMMSS_create_posts_table.php`

### Create a Controller
```bash
php redil make:controller PostController
```
Creates `/app/controllers/PostController.php`

### Create a Command
```bash
php redil make:command SendEmails
```
Creates `/app/commands/SendEmailsCommand.php`

## Key Files

- **redil** - CLI entry point, loads commands and framework
- **public/index.php** - HTTP entry point for requests
- **.env** - Environment variables (don't commit to git)
- **composer.json** - Project dependencies definition
- **config/db.php** - Database configuration
- **routes/api.php** - API route definitions

## Extending the Framework

The framework files in `/framework` can be extended by:
1. Creating new files in `/app` that use framework classes
2. Registering custom service providers
3. Creating facades to simplify framework access

Most applications never need to modify framework files directly.
