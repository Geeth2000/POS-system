# POS System - Project Structure Guide

## Folder Structure Overview

### 📁 Root Level

- **app/** - Application core logic
- **config/** - Configuration files
- **database/** - Migrations and seeders
- **public/** - Public assets and entry point
- **resources/** - Views and assets
- **routes/** - API and web routes
- **storage/** - Application storage (logs, sessions, etc.)
- **tests/** - Unit and feature tests
- **bootstrap/** - Framework bootstrap files
- **.env** - Environment variables (create from .env.example)
- **artisan** - Laravel command-line interface
- **composer.json** - PHP dependencies

---

## Detailed Structure

### 📂 **app/**

Main application namespace with all core business logic.

#### `app/Models/`

Eloquent ORM models representing database entities:

- **User.php** - Authenticated user (staff/cashiers)
  - Roles: admin, cashier, manager
  - Has many transactions (as cashier)
  - Uses Sanctum for API authentication
- **Category.php** - Product categories
  - Has many products
  - Is active flag for soft categorization
- **Product.php** - Inventory items
  - Belongs to category
  - Has many transaction items
  - Tracks SKU, price, cost, quantity
  - Calculates profit margin
- **Customer.php** - Store customers
  - Has many transactions
  - Loyalty points tracking
  - Contact information
- **Transaction.php** - Sales transactions
  - Belongs to customer (nullable for walk-ins)
  - Belongs to cashier (user)
  - Has many transaction items
  - Generates unique transaction numbers
- **TransactionItem.php** - Individual items in a transaction
  - Belongs to transaction
  - Belongs to product
  - Stores unit price and quantity

#### `app/Http/Controllers/Api/`

RESTful API controllers handling business logic:

- **AuthController.php**
  - `POST /api/auth/register` - User registration
  - `POST /api/auth/login` - User login
  - `GET /api/auth/me` - Get current user
  - `POST /api/auth/logout` - User logout
  - `PUT /api/auth/profile` - Update profile

- **CategoryController.php**
  - `GET /api/categories` - List categories
  - `POST /api/categories` - Create category
  - `GET /api/categories/{id}` - Get category details
  - `PUT /api/categories/{id}` - Update category
  - `DELETE /api/categories/{id}` - Delete category

- **ProductController.php**
  - `GET /api/products` - List products (with filters)
  - `POST /api/products` - Create product
  - `GET /api/products/{id}` - Get product details
  - `PUT /api/products/{id}` - Update product
  - `DELETE /api/products/{id}` - Delete product
  - `GET /api/products/low-stock` - Get low stock products

- **CustomerController.php**
  - `GET /api/customers` - List customers
  - `POST /api/customers` - Create customer
  - `GET /api/customers/{id}` - Get customer details
  - `PUT /api/customers/{id}` - Update customer
  - `DELETE /api/customers/{id}` - Delete customer
  - `POST /api/customers/{id}/loyalty-points` - Add loyalty points

- **TransactionController.php**
  - `GET /api/transactions` - List transactions
  - `POST /api/transactions` - Create transaction (process sale)
  - `GET /api/transactions/{id}` - Get transaction details
  - `GET /api/transactions/reports/daily` - Daily sales report
  - `GET /api/transactions/reports/period` - Period sales report

#### `app/Http/Requests/`

Form request validation classes (add as needed for complex validations)

#### `app/Http/Resources/`

API resource transformers for response formatting (optional, for consistent formatting)

---

### 📂 **config/**

Configuration files for Laravel services:

- **database.php** - Database connection settings (MySQL configured)
- **cors.php** - CORS configuration for frontend integration
- **sanctum.php** - Laravel Sanctum authentication settings
- **Other configs** - Mail, queue, cache, etc. (auto-generated)

---

### 📂 **database/**

#### `database/migrations/`

Database schema files (auto-generated):

- **2024_01_01_000001_create_users_table.php**
- **2024_01_01_000002_create_categories_table.php**
- **2024_01_01_000003_create_products_table.php**
- **2024_01_01_000004_create_customers_table.php**
- **2024_01_01_000005_create_transactions_table.php**
- **2024_01_01_000006_create_transaction_items_table.php**
- **2024_01_01_000007_create_personal_access_tokens_table.php** (Sanctum)

#### `database/seeders/`

Database seeders for test data (create as needed)

---

### 📂 **routes/**

- **api.php** - All API routes with Sanctum middleware protection
  - Public routes: registration, login, health check
  - Protected routes: all other resources (require token)

---

### 📂 **public/**

Publicly accessible files (index.php entry point)

---

### 📂 **storage/logs/**

Application log files (auto-generated)

---

## Database Schema

### users

```
id, name, email, password, phone, role, is_active, created_at, updated_at
```

### categories

```
id, name, description, is_active, created_at, updated_at
```

### products

```
id, sku, name, description, category_id, price, cost, quantity, is_active, created_at, updated_at
```

### customers

```
id, name, email, phone, address, loyalty_points, is_active, created_at, updated_at
```

### transactions

```
id, transaction_number, customer_id, cashier_id, subtotal, tax_amount, discount_amount, total_amount, payment_method, notes, created_at, updated_at
```

### transaction_items

```
id, transaction_id, product_id, quantity, unit_price, line_total, created_at, updated_at
```

### personal_access_tokens (Sanctum)

```
id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at
```

---

## Key Features

### ✅ Authentication

- User registration with role assignment
- Login with credentials
- Token-based API authentication (Sanctum)
- Profile update capability
- Secure logout

### ✅ Product Management

- CRUD operations for products
- Category organization
- Inventory tracking
- SKU management
- Low stock alerts

### ✅ Sales Operations

- Create transactions (checkouts)
- Add multiple items per transaction
- Automatic inventory deduction
- Customer tracking
- Tax and discount handling
- Payment method recording

### ✅ Reporting

- Daily sales reports
- Period-based analytics
- Sales aggregation by payment method
- Transaction history filtering

### ✅ Customer Management

- Customer profiles
- Loyalty points system
- Purchase history
- Contact information

---

## API Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    /* resource data */
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error description"
}
```

---

## Authentication

All protected routes require the `Authorization` header:

```
Authorization: Bearer {token}
```

Obtain token by:

1. Register: `POST /api/auth/register`
2. Login: `POST /api/auth/login`
3. Use returned token in request headers

---

## Next Steps After Setup

1. **Run migrations**: `php artisan migrate`
2. **Create seeders** for test data
3. **Set up Telescope** (optional debugging): `php artisan telescope:install`
4. **Create frontend** (React, Vue, etc.) to consume API
5. **Add request validation** classes as needed
6. **Implement pagination** for large datasets
7. **Add caching** for frequently accessed data
8. **Set up payment gateway** integration
9. **Configure email** notifications
10. **Add tests** (Unit & Feature tests)

---

## Development Tips

- Use `php artisan tinker` for interactive testing
- Check logs in `storage/logs/`
- Use API tools (Postman, Insomnia) for endpoint testing
- Keep controllers thin, move logic to services if needed
- Use authorization gates/policies for fine-grained access control
- Leverage Eloquent relationships for clean queries
