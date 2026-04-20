# POS System - Setup Guide

## Prerequisites Installation

### 1. Install PHP 8.2+ (Recommended: 8.3)

- Download from: https://windows.php.net/download/
- Or use Windows Package Manager: `winget install PHP.PHP.8.3`
- Ensure PHP is added to system PATH

### 2. Install Composer

- Download from: https://getcomposer.org/download/
- Or use Windows Package Manager: `winget install Composer.Composer`

### 3. Install MySQL 8.0+

- Download from: https://dev.mysql.com/downloads/mysql/
- Or use: `winget install MySQL.Server`
- Start MySQL service

### 4. Install Node.js (Optional, for frontend assets)

- Download from: https://nodejs.org/
- Or use: `winget install OpenJS.NodeJS`

## Project Setup Steps

### Step 1: Create Laravel Project

```powershell
cd d:\Education\Projects\POS-system
composer create-project laravel/laravel . --prefer-dist
```

### Step 2: Install Laravel Sanctum

```powershell
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Step 3: Configure Environment

The `.env` file will be created automatically. Update it with:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4: Create Application Key

```powershell
php artisan key:generate
```

### Step 5: Run Migrations

```powershell
php artisan migrate
```

### Step 6: Create Initial API Resources

```powershell
# Models with migrations
php artisan make:model Product -m
php artisan make:model Category -m
php artisan make:model Customer -m
php artisan make:model Transaction -m
php artisan make:model TransactionItem -m

# Controllers
php artisan make:controller Api/AuthController
php artisan make:controller Api/ProductController --api
php artisan make:controller Api/CategoryController --api
php artisan make:controller Api/CustomerController --api
php artisan make:controller Api/TransactionController --api
```

### Step 7: Start Development Server

```powershell
php artisan serve
# Available at http://localhost:8000
```

## Database Creation

Before running migrations, create the database:

```sql
CREATE DATABASE pos_system;
```

Or use Laravel:

```powershell
php artisan migrate --step
```

## API Testing

Use Postman or Insomnia to test API endpoints. The base URL will be:

```
http://localhost:8000/api
```

## Next Steps (Already Configured)

- API routes are configured in `routes/api.php`
- Sanctum is set up for token-based authentication
- CORS is configured for API requests
- Database structure templates are ready for customization
