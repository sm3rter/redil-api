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

## Available Commands

- `php redil migrate` - Run database migrations
- `php redil make:migration create_table_name_table` - Create a new migration
- `php redil make:model ModelName` - Create a new model
- `php redil make:controller ControllerName` - Create a new controller

## Project Structure

```
redil-api/
├── app/
│   ├── controllers/    # Application controllers
│   ├── models/        # Database models
│   ├── middleware/    # Application middleware
│   ├── config/        # Configuration files
│   └── database/      # Database migrations
├── public/            # Public directory
├── vendor/            # Composer dependencies
├── .env               # Environment configuration
├── .env.example       # Example environment configuration
├── composer.json      # Composer configuration
└── redil             # Command-line tool
```

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

## License

MIT License 