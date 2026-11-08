# Deployment

## 1. Point the web server at `public/`

Best practice: the document root is `public/`, so `.env`, `app/`, `framework/` etc. are never reachable.

### Apache
`public/.htaccess` is included (front controller rewrite + `Authorization` header pass-through).
Requires `mod_rewrite` and `AllowOverride All` for the document root.

If your host forces the document root to be the **project root** (typical cPanel), the root
`.htaccess` is already set up: it sends requests to `public/` and returns 403 for dotfiles,
`app/`, `config/`, `routes/`, `framework/`, `composer.*`, `redil` and `README.md`.
If the document root is `public/`, the root `.htaccess` is not needed and can be deleted.

### nginx
```nginx
server {
    listen 80;
    server_name api.example.com;
    root /var/www/redil-api/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_AUTHORIZATION $http_authorization;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known) { deny all; }
}
```

## 2. Checklist

- `composer install --no-dev --optimize-autoloader` (vendor goes to `framework/vendor`)
- `.env`: `APP_ENV=production`, `APP_DEBUG=false`, a fresh `APP_KEY` (`php redil key:generate`)
- Set `CORS_ALLOW_ORIGINS` to your real frontend origin(s), not `*`
- Enable `apcu` (or set `RATE_LIMIT_STORE=redis`), otherwise rate limiting does nothing
- `framework/logs/` must be writable by the web user
- `php redil migrate`
- Remove the demo controllers/routes (`UserController`, `ProductController`, `/api/login`) before going live
