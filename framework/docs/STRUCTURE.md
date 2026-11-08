# Project Structure

The project is split into two parts: **what you edit every day** (outside `framework/`)
and **the engine** (inside `framework/`) that you rarely touch.

```
redil-api/
├── app/                      # YOUR code
│   ├── controllers/          # API controllers
│   ├── models/               # Eloquent models
│   ├── middleware/           # Your custom middleware
│   └── database/migrations/  # Database migrations
├── config/                   # app.php, cors.php, db.php, security.php
├── routes/api.php            # Route definitions
├── public/index.php          # Web entry point
├── redil                     # CLI entry point
├── .env / .env.example       # Environment variables
├── composer.json / .lock
│
└── framework/                # The engine (rarely edited)
    ├── Bootstrap/            # App boot, error handler, facade registration
    ├── Config/               # Config loader
    ├── Console/              # CLI kernel, Output, Command base
    │   └── Commands/         # migrate, make:*, key:generate, start, help
    ├── Database/             # Capsule init, MigrationManager, base Model
    ├── Facades/              # Request, Response, Auth, DB facades
    ├── Http/
    │   ├── Controller.php    # Base controller (extend it)
    │   └── Middleware/       # CORS, rate limit, security headers, ...
    ├── Logger/               # File logger
    ├── Support/              # helpers.php, api_helpers.php (global functions)
    ├── logs/                 # redil.log
    ├── docs/                 # These docs
    └── vendor/               # Composer packages
```

## What you edit

| Task | Where |
|------|-------|
| New endpoint | `routes/api.php` + `app/controllers/` |
| New table | `php redil make:migration create_x_table` -> `app/database/migrations/` |
| New model | `php redil make:model X` -> `app/models/` |
| Settings | `.env` and `config/*.php` |
| Custom middleware | `app/middleware/` (namespace `App\Middleware`) |

## Namespaces

- Framework: `Framework\...` -> `framework/` (e.g. `Framework\Http\Controller`)
- Your code: `App\Controllers`, `App\Models`, `App\Middleware` -> `app/controllers`, `app/models`, `app/middleware`
- Any other `App\Xyz` namespace maps to `app/Xyz/` (use the exact folder case, Linux is case-sensitive)

## Notes

- Composer is configured with `vendor-dir = framework/vendor`, so `composer install`
  and `composer dump-autoload` write there. Entry points load `framework/vendor/autoload.php`.
- Docs for the CLI and migrations: `CLI_COMMANDS.md`, `MIGRATIONS.md` (same folder).
