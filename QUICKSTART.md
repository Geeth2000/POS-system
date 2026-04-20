# POS System - Quick Start Guide

## ⚡ 5-Minute Setup

### Prerequisites

- PHP 8.2+ installed
- Composer installed
- MySQL 8.0+ running
- Node.js (optional, for frontend)

### Step 1: Install Dependencies

```powershell
cd d:\Education\Projects\POS-system
composer install
```

### Step 2: Setup Environment

```powershell
copy .env.example .env
php artisan key:generate
```

Update `.env`:

```env
DB_DATABASE=pos_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Create Database

```sql
CREATE DATABASE pos_system;
```

### Step 4: Run Migrations

```powershell
php artisan migrate
```

### Step 5: Start Server

```powershell
php artisan serve
```

API is now available at `http://localhost:8000/api`

---

## 🔐 First Login

### 1. Register a User

**POST** `http://localhost:8000/api/auth/register`

```json
{
  "name": "Admin User",
  "email": "admin@posystem.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "123456789",
  "role": "admin"
}
```

### 2. Login

**POST** `http://localhost:8000/api/auth/login`

```json
{
  "email": "admin@posystem.com",
  "password": "password123"
}
```

Copy the returned `token` - you'll use this for API requests.

### 3. Test Authentication

**GET** `http://localhost:8000/api/auth/me`

Header: `Authorization: Bearer {your_token}`

---

## 📝 Common Tasks

### Create a Product Category

**POST** `http://localhost:8000/api/categories`

Header: `Authorization: Bearer {token}`

```json
{
  "name": "Electronics",
  "description": "Electronic devices and accessories"
}
```

### Add Products

**POST** `http://localhost:8000/api/products`

Header: `Authorization: Bearer {token}`

```json
{
  "sku": "LAPTOP-001",
  "name": "Dell Laptop",
  "description": "High-performance laptop",
  "category_id": 1,
  "price": 1200,
  "cost": 800,
  "quantity": 50
}
```

### Create a Customer

**POST** `http://localhost:8000/api/customers`

Header: `Authorization: Bearer {token}`

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "123456789",
  "address": "123 Main Street"
}
```

### Process a Sale

**POST** `http://localhost:8000/api/transactions`

Header: `Authorization: Bearer {token}`

```json
{
  "customer_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "tax_amount": 10.0,
  "discount_amount": 0,
  "payment_method": "card",
  "notes": "Customer paid in full"
}
```

### Get Sales Report

**GET** `http://localhost:8000/api/transactions/reports/daily?date=2024-04-20`

Header: `Authorization: Bearer {token}`

---

## 🛠️ Development Commands

```powershell
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Refresh database (reset and re-migrate)
php artisan migrate:refresh

# Create a new model with migration
php artisan make:model ModelName -m

# Create a new controller
php artisan make:controller ControllerName

# Generate application key
php artisan key:generate

# Interactive shell
php artisan tinker

# Clear cache
php artisan cache:clear

# View routes
php artisan route:list

# Run tests
php artisan test

# Start server with reload on file changes
php artisan serve --watch
```

---

## 📚 Documentation Files

- **SETUP_GUIDE.md** - Detailed installation instructions
- **PROJECT_STRUCTURE.md** - Complete folder and database structure
- **API_DOCUMENTATION.md** - Full API endpoint reference
- **README.md** - Project overview

---

## 🐛 Troubleshooting

### "SQLSTATE[HY000]: General error: 1030 Got error from storage engine"

- Check MySQL is running
- Verify database name in .env matches created database
- Run `php artisan migrate:refresh`

### "Class 'App\Http\Controllers\Api\ProductController' not found"

- Check controller file exists in correct namespace
- Run `composer dump-autoload`

### "Unauthenticated" error on protected routes

- Verify Authorization header is included
- Check token is not expired
- Ensure token format is: `Bearer {token}`

### "The Application Has Encountered an Error"

- Check `storage/logs/laravel.log` for details
- Ensure .env is properly configured
- Run `php artisan key:generate` if needed

---

## 🚀 Next Steps

1. **Set up frontend** - React, Vue, or Next.js consuming this API
2. **Add validation** - Create Form Request classes for complex validations
3. **Implement caching** - Cache frequently accessed data
4. **Add logging** - Track user actions and system events
5. **Email notifications** - Send receipts and alerts
6. **Payment gateway** - Integrate Stripe, PayPal, etc.
7. **Reporting** - Advanced analytics and business intelligence
8. **Testing** - Write unit and feature tests
9. **CI/CD** - Setup automated deployment

---

## 📞 Support Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Sanctum Authentication](https://laravel.com/docs/sanctum)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [API Resources](https://laravel.com/docs/eloquent-resources)
- [Error Handling](https://laravel.com/docs/errors)

---

## 📄 Project Info

- **Framework**: Laravel 11.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL
- **API Style**: RESTful
- **Language**: PHP 8.2+

**Created**: April 20, 2024
**Status**: Development Ready
