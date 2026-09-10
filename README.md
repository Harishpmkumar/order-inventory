# Store Order & Inventory Mini-System

A Laravel-based **Store Order & Inventory Mini-System** developed as a take-home assignment for a Laravel Developer role.

The application enables a store to create customer orders, validate and deduct inventory safely, calculate taxes and totals, reuse customers by email, display low-stock products, retrieve customer order history, and simulate order confirmation emails using Laravel queues.

---

# Features

- Customer creation and reuse by email
- Responsive Bootstrap billing interface
- Add multiple products to a single order
- Automatic subtotal, tax, and grand total calculation
- Historical storage of unit price and tax percentage
- Stock availability validation
- Atomic order creation using database transactions
- Row-level locking to prevent overselling
- Customer order history API
- Configurable low-stock threshold
- Simulated queued order confirmation
- RESTful JSON APIs
- Comprehensive feature tests
- Concurrency protection tests
- Responsive billing interface for desktop, tablet, and mobile

---

# Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL
- Bootstrap 5.3
- JavaScript (Vanilla)
- Eloquent ORM
- Laravel Form Requests
- Laravel Queues
- PHPUnit / Laravel Testing
- XAMPP

---

# Architecture

The application follows a simple layered architecture.

```text
Browser / API Client
        │
        ▼
Routes
        │
        ▼
Controllers
        │
        ▼
Form Request Validation
        │
        ▼
Order Service
        │
 ┌──────┴───────────────┐
 │ Database Transaction │
 │ Row-Level Locking    │
 │ Stock Validation     │
 │ Order Creation       │
 │ Queue Dispatch       │
 └──────┬───────────────┘
        │
        ▼
MySQL Database
```

---

# Database Design

## customers

| Column | Type |
|---------|------|
| id | bigint |
| name | string |
| email | string (unique) |
| timestamps | timestamps |

---

## products

| Column | Type |
|---------|------|
| id | bigint |
| name | string |
| code | string (unique) |
| price | decimal(10,2) |
| tax_percentage | decimal(5,2) |
| stock_on_hand | integer |
| timestamps | timestamps |

---

## orders

| Column | Type |
|---------|------|
| id | bigint |
| customer_id | foreign key |
| subtotal | decimal(10,2) |
| tax | decimal(10,2) |
| grand_total | decimal(10,2) |
| timestamps | timestamps |

---

## order_items

| Column | Type |
|---------|------|
| id | bigint |
| order_id | foreign key |
| product_id | foreign key |
| quantity | integer |
| unit_price | decimal(10,2) |
| tax_percentage | decimal(5,2) |
| subtotal | decimal(10,2) |
| tax_amount | decimal(10,2) |
| total | decimal(10,2) |
| timestamps | timestamps |

---

# Relationships

Customer

```
Customer
    └── hasMany Orders
```

Order

```
Order
    ├── belongsTo Customer
    └── hasMany OrderItems
```

Order Item

```
OrderItem
    ├── belongsTo Order
    └── belongsTo Product
```

Product

```
Product
    └── hasMany OrderItems
```

---

# Business Rules

- Customer email is unique.
- Product code is unique.
- Quantity must be greater than zero.
- Stock cannot become negative.
- One order may contain multiple products.
- Product price is stored historically.
- Product tax percentage is stored historically.
- Existing customers are reused by email.
- Orders are processed atomically.

---

# Concurrency Protection

To prevent overselling, the application uses:

- Database transactions
- `lockForUpdate()`
- Deterministic product locking order
- Stock validation before deduction
- Queue dispatch after commit

This ensures that concurrent requests cannot purchase the same inventory simultaneously.

---

# Order Workflow

```text
Customer
      │
      ▼
Validate Request
      │
      ▼
Find/Create Customer
      │
      ▼
Lock Product Rows
      │
      ▼
Validate Stock
      │
      ▼
Calculate Totals
      │
      ▼
Deduct Inventory
      │
      ▼
Create Order
      │
      ▼
Create Order Items
      │
      ▼
Dispatch Queue Job
      │
      ▼
Return JSON Response
```

---

# REST API

## Create Order

```
POST /api/orders
```

Example Request

```json
{
    "customer": {
        "name": "John Doe",
        "email": "john@example.com"
    },
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        }
    ]
}
```

---

## Customer Order History

```
GET /api/customers/orders?email=john@example.com
```

---

## Low Stock Products

```
GET /api/products/low-stock
```

Optional threshold

```
GET /api/products/low-stock?threshold=10
```

---

# Web Routes

| Route | Description |
|---------|-------------|
| / | Home |
| /billing | Billing interface |
| /customers/lookup | Customer lookup |

---

# Billing Interface

The application includes a responsive Bootstrap billing interface.

Features include:

- Customer email lookup
- Automatic customer reuse
- Product selection
- Quantity selection
- Automatic pricing
- Tax calculation
- Grand total calculation
- Amount received
- Balance calculation
- Currency denomination breakdown
- Low stock alert
- Generated bill preview

---

# Queue

Order confirmations are simulated using Laravel queues.

The queue job is dispatched only after a successful database transaction.

```php
SendOrderConfirmation::dispatch($order->id)->afterCommit();
```

---

# Validation

The application validates:

- Customer name
- Customer email
- Product existence
- Quantity
- Duplicate products
- Stock availability

Invalid requests return JSON validation errors.

---

# Installation

Clone the repository

```bash
git clone https://github.com/Harishpmkumar/order-inventory.git
```

Go into the project

```bash
cd order-inventory
```

Install dependencies

```bash
composer install
```

Copy environment

```bash
cp .env.example .env
```

Generate key

```bash
php artisan key:generate
```

Configure database inside `.env`

```env
DB_DATABASE=order_inventory
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations

```bash
php artisan migrate
```

Seed sample data

```bash
php artisan db:seed
```

Start development server

```bash
php artisan serve
```

Run queue worker

```bash
php artisan queue:work
```

---

# Running Tests

Execute all feature tests.

```bash
php artisan test
```

Current Result

```
PASS 13 Tests
60 Assertions
```

Tests include:

- Successful order creation
- Validation failures
- Existing customer reuse
- Insufficient stock
- Multiple products
- Transaction rollback
- Low stock endpoint
- Customer order history
- Concurrency protection

---

# Design Decisions

- Service layer used for business logic.
- Controllers remain thin.
- Transactions ensure atomicity.
- Historical prices are preserved.
- Historical tax percentages are preserved.
- Queue dispatch occurs only after successful commit.
- Row-level locking prevents race conditions.
- Responsive Bootstrap UI improves usability.

---

# Assumptions

- One store location.
- Single currency (INR).
- Tax is percentage based.
- Inventory is managed per product.
- Queue simulates confirmation processing.
- Authentication is outside the scope of this assignment.

---

# AI-Assisted Development

AI was used as a development assistant for:

- Requirement clarification
- Database design review
- API design review
- UI improvements
- Responsive layout improvements
- Code review
- README preparation

Development prompts and screenshots are included in the `prompts/` directory as required by the assignment.

---

# Project Structure

```text
app/
    Http/
    Models/
    Services/
    Jobs/

database/
    migrations/
    seeders/

resources/
    views/billing/

routes/

tests/
    Feature/
    Support/

prompts/

README.md
```

---

# Future Improvements

- Authentication
- Invoice PDF generation
- Email delivery integration
- Product search
- Barcode support
- Discount support
- Pagination
- Dashboard analytics
- Inventory reports

---

# Verification Checklist

- Responsive Bootstrap UI
- REST API implemented
- Transactions implemented
- Row-level locking implemented
- Queue implemented
- Feature tests passing
- Concurrency test passing
- Low-stock API implemented
- Customer lookup implemented
- Order history implemented
- Prompt screenshots included
- README included

---

# Author

**Harishkumar M**

Laravel Developer Take-Home Assignment
