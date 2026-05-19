# 🛒 Procurement Management API

A robust **REST API** for managing company procurement workflows — built with **Laravel 12** using a **Service Layer architecture**, featuring role-based access control, multi-stage approval flows, vendor management, stock tracking, and analytical reporting.

> 📌 Built as a personal portfolio project by [Muhammad Hafizh Azzasafah]

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Architecture](#-architecture)
- [Getting Started](#-getting-started)
- [Environment Variables](#-environment-variables)
- [Running the Application](#-running-the-application)
- [API Documentation](#-api-documentation)
  - [Authentication](#authentication)
  - [Users](#users)
  - [Departments](#departments)
  - [Vendors](#vendors)
  - [Stocks](#stocks)
  - [Procurement Requests](#procurement-requests)
  - [Orders (Procures)](#orders-procures)
  - [Reports](#reports)
- [Role & Access Control](#-role--access-control)
- [Procurement Flow](#-procurement-flow)
- [Author](#-author)

---

## ✨ Features

- 🔐 **JWT-based Authentication** — Secure login, register, logout & token refresh
- 👥 **User & Role Management** — Admin and employee roles with granular access
- 🏢 **Department Management** — Organize users by department
- 🏭 **Vendor Management** — Maintain approved supplier/vendor list
- 📦 **Stock Management** — Track item inventory with minimum stock threshold alerts
- 📝 **Procurement Request** — Multi-item request creation with full approval lifecycle
- ✅ **Multi-Stage Approval Flow** — Draft → Submitted → Approved/Rejected → Procured → Completed
- 📊 **Analytics & Reporting** — Summary dashboard, category-per-month, average lead time
- ⚡ **Optimized Queries** — Efficient database queries to support complex multi-stage workflows
- 🧱 **Clean Code & Service Layer** — Maintainable, testable, and scalable architecture

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.2+ |
| Framework | Laravel 12 |
| Database | MySQL 8.0+ |
| Authentication | Laravel Sanctum (Bearer Token) |
| Architecture | Service Layer Pattern (MVC + Services) |
| API Style | RESTful API |
| ID Strategy | ULID (Universally Unique Lexicographically Sortable Identifier) |
| API Testing | Postman |

---

## 🏗 Architecture

```
procurement-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Request handling & response formatting
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── DepartmentController.php
│   │   │   ├── VendorController.php
│   │   │   ├── StockController.php
│   │   │   ├── ProcurementRequestController.php
│   │   │   ├── ProcureController.php
│   │   │   └── ReportController.php
│   │   ├── Middleware/         # Auth & role guard middleware
│   │   └── Requests/           # Form request validation
│   ├── Services/               # Business logic layer
│   │   ├── AuthService.php
│   │   ├── UserService.php
│   │   ├── DepartmentService.php
│   │   ├── VendorService.php
│   │   ├── StockService.php
│   │   ├── ProcurementRequestService.php
│   │   └── ReportService.php
│   ├── Models/                 # Eloquent models
│   └── Exceptions/             # Custom business exception handling
├── database/
│   ├── migrations/             # Database schema definitions
│   └── seeders/                # Initial data seeders
├── routes/
│   └── api.php                 # All API route definitions
└── ...
```

---

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL 8.0+
- Git

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/Azzasafah/procurement-api.git
cd procurement-api

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env (see section below)

# 6. Run database migrations
php artisan migrate

# 7. Seed initial data (admin user, sample departments, etc.)
php artisan db:seed
```

---

## ⚙️ Environment Variables

Key variables to configure in your `.env` file:

```env
APP_NAME=ProcurementAPI
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=procurement_db
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost
```

---

## ▶️ Running the Application

```bash
# Start the development server
php artisan serve

# The API will be available at:
# http://localhost:8000
```

---

## 📖 API Documentation

**Base URL:** `{{baseUrl}}/api/v1`

All protected endpoints require a Bearer Token in the Authorization header:
```
Authorization: Bearer <your_token>
```

---

### Authentication

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `POST` | `/auth/login` | Public | Login and get access token |
| `POST` | `/auth/register` | Public | Register a new user |
| `POST` | `/auth/logout` | 🔒 Protected | Revoke current token |
| `GET` | `/auth/me` | 🔒 Protected | Get authenticated user profile |

**Login Request:**
```json
{
  "email": "admin@procurement.app",
  "password": "password"
}
```

**Register Request:**
```json
{
  "name": "Azzasafah Procurement",
  "email": "azzasafah@procurement.app",
  "password": "password123",
  "password_confirmation": "password123",
  "department_id": "019e1304-e3c3-731d-b5c2-2a2c1be46252",
  "phone": "081234567890"
}
```

---

### Users

> 🔒 All endpoints require authentication. Admin-only endpoints are marked with `[Admin]`.

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/users` | 🔒 Admin | Get all users |
| `GET` | `/users/{id}` | 🔒 Admin | Get user by ID |
| `POST` | `/users` | 🔒 Admin | Create new user |
| `PUT` | `/users/{id}` | 🔒 Admin | Update user data |
| `PATCH` | `/users/{id}/role` | 🔒 Admin | Update user role only |
| `DELETE` | `/users/{id}` | 🔒 Admin | Delete user |

**Create User Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "department_id": "019e1304-e3c8-7270-afc8-88d9c648283d",
  "role": "employee",
  "phone": "081234567890",
  "is_active": true
}
```

**Available Roles:** `admin`, `manager`, `employee`

---

### Departments

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/departments` | 🔒 Protected | Get all departments |
| `GET` | `/departments/{id}` | 🔒 Protected | Get department by ID |
| `POST` | `/departments` | 🔒 Admin | Create department |
| `PUT` | `/departments/{id}` | 🔒 Admin | Update department |
| `DELETE` | `/departments/{id}` | 🔒 Admin | Delete department |

**Create Department Request:**
```json
{
  "name": "Research and Development",
  "code": "RND",
  "description": "Departemen Research and Development"
}
```

---

### Vendors

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/vendors` | 🔒 Protected | Get all vendors |
| `GET` | `/vendors/{id}` | 🔒 Protected | Get vendor by ID |
| `POST` | `/vendors` | 🔒 Admin | Create vendor |
| `PUT` | `/vendors/{id}` | 🔒 Admin | Update vendor |
| `DELETE` | `/vendors/{id}` | 🔒 Admin | Delete vendor |

**Create Vendor Request:**
```json
{
  "name": "PT Sumber Teknologi Nusantara",
  "code": "STN-001",
  "contact_person": "Andi Pratama",
  "email": "vendor@stn.co.id",
  "phone": "081234567890",
  "address": "Jl. Industri Raya No. 88, Surabaya",
  "category": "Electronics",
  "is_active": true,
  "notes": "Vendor pengadaan perangkat IT dan operasional kantor"
}
```

---

### Stocks

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/stocks` | 🔒 Protected | Get all stock items |
| `GET` | `/stocks/{id}` | 🔒 Protected | Get stock item by ID |
| `POST` | `/stocks` | 🔒 Admin | Create stock item |
| `PUT` | `/stocks/{id}` | 🔒 Admin | Update stock item |
| `POST` | `/stocks/check` | 🔒 Protected | Check stock availability |

**Create Stock Request:**
```json
{
  "item_name": "Laptop ASUS ExpertBook",
  "category": "Electronics",
  "quantity": 25,
  "unit": "pcs",
  "location": "Warehouse A - Rack 3",
  "minimum_stock": 5
}
```

**Check Stock Request:**
```json
{
  "item_name": "Laptop ASUS ExpertBook",
  "quantity": 5
}
```

---

### Procurement Requests

> Core module — manages the full lifecycle of a procurement request.

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/requests` | 🔒 Protected | Get all requests (role-filtered) |
| `POST` | `/requests` | 🔒 Protected | Create new procurement request |
| `PUT` | `/requests/{id}` | 🔒 Protected | Update request notes |
| `PUT` | `/requests/{id}/submit` | 🔒 Protected | Submit request for approval |
| `PUT` | `/requests/{id}/approve` | 🔒 Manager/Admin | Approve a request |
| `PUT` | `/requests/{id}/reject` | 🔒 Manager/Admin | Reject a request |
| `PUT` | `/requests/{id}/procure` | 🔒 Admin | Mark as procured (assign vendor) |
| `PUT` | `/requests/{id}/complete` | 🔒 Admin | Mark as completed |
| `DELETE` | `/requests/{id}` | 🔒 Protected | Delete draft request |

**Create Request Body:**
```json
{
  "notes": "Pengadaan perangkat untuk tim finansial",
  "items": [
    {
      "item_name": "Laptop ASUS ExpertBook",
      "category": "Electronics",
      "quantity": 4,
      "unit": "pcs",
      "estimated_price": 12500000,
      "notes": "Untuk staff finance dan accounting"
    },
    {
      "item_name": "Printer Epson EcoTank L5290",
      "category": "Office Equipment",
      "quantity": 2,
      "unit": "unit",
      "estimated_price": 3500000,
      "notes": "Printer kantor divisi keuangan"
    }
  ]
}
```

**Procure Request Body:**
```json
{
  "vendor_id": "019e1304-e7ba-73a2-820d-693700020831",
  "expected_delivery_date": "2026-05-25",
  "total_amount": 62300000,
  "notes": "Pengadaan perangkat operasional untuk tim finansial"
}
```

---

### Orders (Procures)

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/procures` | 🔒 Admin | Get all procurement orders |
| `PUT` | `/procures/{id}/deliver` | 🔒 Admin | Mark order as delivered |

---

### Reports

| Method | Endpoint | Access | Description |
|---|---|---|---|
| `GET` | `/reports/summary` | 🔒 Admin | Overall procurement summary (KPIs) |
| `GET` | `/reports/category-per-month` | 🔒 Admin | Requests breakdown by category per month |
| `GET` | `/reports/average-lead-time` | 🔒 Admin | Average lead time from request to delivery |

---

## 🔐 Role & Access Control

| Feature | Employee | Manager | Admin |
|---|:---:|:---:|:---:|
| Login / View Profile | ✅ | ✅ | ✅ |
| Create Request | ✅ | ✅ | ✅ |
| Submit Own Request | ✅ | ✅ | ✅ |
| Approve / Reject Request | ❌ | ✅ | ✅ |
| Manage Users | ❌ | ❌ | ✅ |
| Manage Vendors | ❌ | ❌ | ✅ |
| Manage Departments | ❌ | ❌ | ✅ |
| Manage Stocks | ❌ | ❌ | ✅ |
| Procure / Complete Request | ❌ | ❌ | ✅ |
| View Reports | ❌ | ❌ | ✅ |

---

## 🔄 Procurement Flow

```
[Employee]           [Manager/Admin]         [Admin]
    │                      │                    │
    ▼                      │                    │
 Create Request             │                    │
 (status: draft)            │                    │
    │                      │                    │
    ▼                      │                    │
 Submit Request             │                    │
 (status: submitted)        │                    │
    │                      │                    │
    └──────────────────────▶│                    │
                       Approve / Reject          │
                  (status: approved/rejected)    │
                            │                    │
                            └───────────────────▶│
                                           Procure
                                     (assign vendor,
                                   status: procured)
                                                 │
                                                 ▼
                                          Deliver / Complete
                                        (status: completed)
```

---

## 👤 Author

**Muhammad Hafizh Azzasafah**

- 📧 muhammad.hafizh0408@gmail.com

---