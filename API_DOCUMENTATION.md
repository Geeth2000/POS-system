# POS System - API Documentation

## Base URL

```
http://localhost:8000/api
```

## Authentication

All endpoints marked with 🔒 require authentication.

Include the token in the Authorization header:

```
Authorization: Bearer {your_token_here}
```

---

## Public Endpoints

### User Registration

**POST** `/auth/register`

Request:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "123456789",
  "role": "cashier"
}
```

Response:

```json
{
  "success": true,
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "123456789",
    "role": "cashier",
    "is_active": true,
    "created_at": "2024-04-20T12:00:00Z"
  },
  "token": "auth_token_here"
}
```

### User Login

**POST** `/auth/login`

Request:

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

Response: (Same as registration)

---

## Protected Endpoints (Require 🔒 Authentication)

### Authentication

**GET** 🔒 `/auth/me` - Get current authenticated user

Response:

```json
{
  "success": true,
  "user": {
    /* user object */
  }
}
```

**POST** 🔒 `/auth/logout` - Logout user

Response:

```json
{
  "success": true,
  "message": "Logout successful"
}
```

**PUT** 🔒 `/auth/profile` - Update profile

Request:

```json
{
  "name": "Jane Doe",
  "phone": "987654321",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

### Categories

**GET** 🔒 `/categories?page=1&per_page=20&is_active=true`

Query Parameters:

- `page` (optional): Page number
- `per_page` (optional): Items per page (default: 20)
- `is_active` (optional): Filter by active status (true/false)

Response:

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Electronics",
        "description": "Electronic products",
        "is_active": true,
        "created_at": "2024-04-20T12:00:00Z",
        "updated_at": "2024-04-20T12:00:00Z"
      }
    ],
    "current_page": 1,
    "total": 10,
    "per_page": 20,
    "last_page": 1
  }
}
```

**POST** 🔒 `/categories`

Request:

```json
{
  "name": "Books",
  "description": "All types of books"
}
```

**GET** 🔒 `/categories/{id}`

**PUT** 🔒 `/categories/{id}`

Request: (Same as POST, with optional fields)

**DELETE** 🔒 `/categories/{id}`

---

### Products

**GET** 🔒 `/products?page=1&per_page=15&category_id=1&search=laptop&is_active=true`

Query Parameters:

- `category_id` (optional): Filter by category
- `search` (optional): Search by name or SKU
- `is_active` (optional): Filter by active status
- `page` (optional): Page number
- `per_page` (optional): Items per page (default: 15)

Response:

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "sku": "PROD-001",
        "name": "Laptop Dell",
        "description": "High-performance laptop",
        "category_id": 1,
        "price": "999.99",
        "cost": "700.00",
        "quantity": 25,
        "is_active": true,
        "category": {
          "id": 1,
          "name": "Electronics"
        },
        "created_at": "2024-04-20T12:00:00Z"
      }
    ],
    "current_page": 1,
    "total": 50,
    "per_page": 15,
    "last_page": 4
  }
}
```

**POST** 🔒 `/products`

Request:

```json
{
  "sku": "PROD-001",
  "name": "Laptop Dell",
  "description": "High-performance laptop",
  "category_id": 1,
  "price": 999.99,
  "cost": 700.0,
  "quantity": 25
}
```

**GET** 🔒 `/products/{id}`

**PUT** 🔒 `/products/{id}`

Request: (Same as POST, with optional fields)

**DELETE** 🔒 `/products/{id}`

**GET** 🔒 `/products/low-stock?threshold=10`

Query Parameters:

- `threshold` (optional): Stock level threshold (default: 10)

Response:

```json
{
  "success": true,
  "threshold": 10,
  "count": 5,
  "data": [
    /* products with quantity <= threshold */
  ]
}
```

---

### Customers

**GET** 🔒 `/customers?page=1&search=john&is_active=true`

Query Parameters:

- `search` (optional): Search by name, email, or phone
- `is_active` (optional): Filter by active status
- `page` (optional): Page number
- `per_page` (optional): Items per page (default: 15)

Response:

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "John Customer",
        "email": "john@example.com",
        "phone": "123456789",
        "address": "123 Main St",
        "loyalty_points": 150,
        "is_active": true,
        "transactions_count": 5,
        "created_at": "2024-04-20T12:00:00Z"
      }
    ],
    "current_page": 1,
    "total": 20,
    "per_page": 15,
    "last_page": 2
  }
}
```

**POST** 🔒 `/customers`

Request:

```json
{
  "name": "Jane Customer",
  "email": "jane@example.com",
  "phone": "987654321",
  "address": "456 Oak Ave"
}
```

**GET** 🔒 `/customers/{id}`

**PUT** 🔒 `/customers/{id}`

Request: (Same as POST, with optional fields)

**DELETE** 🔒 `/customers/{id}`

**POST** 🔒 `/customers/{id}/loyalty-points`

Request:

```json
{
  "points": 50
}
```

---

### Transactions (Sales)

**GET** 🔒 `/transactions?page=1&customer_id=1&cashier_id=1&payment_method=cash&from_date=2024-01-01&to_date=2024-04-30`

Query Parameters:

- `customer_id` (optional): Filter by customer
- `cashier_id` (optional): Filter by cashier
- `payment_method` (optional): Filter by payment method (cash, card, check, transfer)
- `from_date` (optional): Start date (YYYY-MM-DD)
- `to_date` (optional): End date (YYYY-MM-DD)
- `page` (optional): Page number
- `per_page` (optional): Items per page (default: 15)

Response:

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "transaction_number": "TRX-20240420-0001",
        "customer_id": 1,
        "cashier_id": 1,
        "subtotal": "1999.98",
        "tax_amount": "199.99",
        "discount_amount": "100.00",
        "total_amount": "2099.97",
        "payment_method": "card",
        "notes": "Customer paid in full",
        "customer": {
          "id": 1,
          "name": "John Customer"
        },
        "cashier": {
          "id": 1,
          "name": "John Cashier"
        },
        "items": [
          {
            "id": 1,
            "product_id": 1,
            "quantity": 2,
            "unit_price": "999.99",
            "line_total": "1999.98",
            "product": {
              "id": 1,
              "sku": "PROD-001",
              "name": "Laptop Dell"
            }
          }
        ],
        "created_at": "2024-04-20T15:30:00Z"
      }
    ],
    "current_page": 1,
    "total": 100,
    "per_page": 15,
    "last_page": 7
  }
}
```

**POST** 🔒 `/transactions` - Create new transaction

Request:

```json
{
  "customer_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 3,
      "quantity": 1
    }
  ],
  "tax_amount": 199.99,
  "discount_amount": 50.0,
  "payment_method": "card",
  "notes": "Regular customer"
}
```

Response:

```json
{
  "success": true,
  "message": "Transaction created successfully",
  "data": {
    /* transaction object */
  }
}
```

**GET** 🔒 `/transactions/{id}`

Response: (Single transaction object)

**GET** 🔒 `/transactions/reports/daily?date=2024-04-20`

Query Parameters:

- `date` (optional): Date (YYYY-MM-DD, default: today)

Response:

```json
{
  "success": true,
  "data": {
    "date": "2024-04-20",
    "total_transactions": 25,
    "total_sales": "12500.00",
    "total_tax": "1250.00",
    "total_discount": "500.00",
    "by_payment_method": {
      "cash": "5000.00",
      "card": "7000.00",
      "check": "500.00"
    }
  }
}
```

**GET** 🔒 `/transactions/reports/period?from_date=2024-04-01&to_date=2024-04-30`

Query Parameters:

- `from_date` (required): Start date (YYYY-MM-DD)
- `to_date` (required): End date (YYYY-MM-DD)

Response:

```json
{
  "success": true,
  "data": {
    "period": "2024-04-01 to 2024-04-30",
    "total_transactions": 500,
    "total_sales": "250000.00",
    "total_tax": "25000.00",
    "total_discount": "5000.00",
    "average_transaction": "500.00"
  }
}
```

---

## Status Codes

- **200** - OK (successful GET, PUT)
- **201** - Created (successful POST)
- **400** - Bad Request (validation error)
- **401** - Unauthorized (missing or invalid token)
- **403** - Forbidden (insufficient permissions)
- **404** - Not Found (resource doesn't exist)
- **422** - Unprocessable Entity (validation failed)
- **500** - Internal Server Error

---

## Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

---

## Rate Limiting

Currently not implemented. Consider adding in production:

- 60 requests per minute per IP for public endpoints
- 300 requests per minute per authenticated user

---

## Testing the API

### Using cURL

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com","password":"password123","password_confirmation":"password123","role":"cashier"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get categories
curl -X GET http://localhost:8000/api/categories \
  -H "Authorization: Bearer {token}"
```

### Using Postman

1. Set base URL to `http://localhost:8000/api`
2. Create Auth collection with JWT Bearer token type
3. Import this documentation as OpenAPI specification
4. Test all endpoints with provided examples

### Using Insomnia

Similar setup to Postman - import API documentation or manually create requests.
