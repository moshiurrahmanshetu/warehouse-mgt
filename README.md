# Warehouse Management System (WMS Pro)

A professional, enterprise-grade **Warehouse Management System** built with:

- **PHP 8+** (Raw, no framework)
- **MySQL 8** + **PDO** with prepared statements
- **Bootstrap 5** + **Vanilla JavaScript**
- Clean **MVC architecture**

---

## ⚡ Quick Start

### 1. Requirements
- XAMPP (Apache + MySQL + PHP 8+)
- `mod_rewrite` enabled

### 2. Place Project
The project must be located at:
```
C:\xampp\htdocs\warehouse-mgt\
```

### 3. Database Setup

**Option A — Automated (Recommended)**

Navigate to:
```
http://localhost/warehouse-mgt/setup.php
```
This creates the database, all tables, seeds roles/permissions, and creates the admin user with a proper bcrypt hash.

> ⚠️ **Delete `setup.php` after running it!**

**Option B — Manual**

Import `database/schema.sql` via phpMyAdmin or MySQL CLI:
```bash
mysql -u root -p < database/schema.sql
```
Then update the admin password via phpMyAdmin since the SQL file contains a placeholder.

### 4. Configure

Edit `config/config.php`:
- Set `APP_URL` to match your local URL.
- Adjust `APP_TIMEZONE` if needed.

Edit `config/database.php`:
- Set `DB_USER` and `DB_PASS` to match your MySQL credentials.

### 5. Access

| URL | Page |
|-----|------|
| `http://localhost/warehouse-mgt/` | Redirects to login or dashboard |
| `http://localhost/warehouse-mgt/login.php` | Login page |
| `http://localhost/warehouse-mgt/dashboard.php` | Dashboard (requires auth) |

**Default Admin Account:**
- Email: `admin@example.com`
- Password: `admin123`

---

## 📁 Project Structure

```
warehouse-mgt/
├── assets/
│   ├── css/style.css          # Main stylesheet (dark theme)
│   ├── js/main.js             # Vanilla JavaScript
│   └── images/                # Static images
├── config/
│   ├── config.php             # App configuration & constants
│   └── database.php           # PDO Database class (Singleton)
├── controllers/
│   ├── AuthController.php     # Login / Logout logic
│   └── DashboardController.php
├── models/
│   └── UserModel.php          # User data access
├── views/
│   ├── auth/login.php         # Login page view
│   ├── dashboard/index.php    # Dashboard view
│   └── errors/403.php         # Access denied page
├── includes/
│   ├── bootstrap.php          # Core dependency loader
│   ├── sidebar.php            # Sidebar component
│   ├── navbar.php             # Top navbar component
│   └── footer.php             # Footer component
├── middleware/
│   ├── AuthMiddleware.php     # Session auth check
│   └── RoleMiddleware.php     # Role / permission check
├── helpers/
│   └── functions.php          # Global utility functions
├── database/
│   └── schema.sql             # DB schema reference
├── uploads/                   # File uploads (writable)
├── logs/                      # App logs (writable)
├── index.php                  # Entry point
├── login.php                  # Login route
├── logout.php                 # Logout route
├── dashboard.php              # Dashboard route
├── setup.php                  # One-time DB installer
├── 404.php                    # Not Found page
├── 500.php                    # Server Error page
├── .htaccess                  # Apache config & security
└── README.md                  # This file
```

---

## 🗄️ Database Schema

| Table | Description |
|-------|-------------|
| `roles` | System roles (admin, manager, staff) |
| `permissions` | Granular permission definitions |
| `role_permissions` | Many-to-many: roles ↔ permissions |
| `users` | Authenticated system users |
| `user_roles` | Many-to-many: users ↔ roles |
| `activity_logs` | Full user activity audit trail |
| `system_settings` | Key-value application settings |

---

## 🔐 Security Features

- **PDO Prepared Statements** — All DB queries parameterized
- **CSRF Protection** — Token-per-session, verified on every POST
- **XSS Prevention** — `htmlspecialchars()` on all output via `e()`
- **Bcrypt Password Hashing** — cost=12 via `password_hash()`
- **Session Security** — Regenerated ID on login, httpOnly cookies, SameSite=Lax
- **Session Timeout** — Configurable inactivity expiry (default 1 hour)
- **Login Rate Limiting** — Lockout after 5 failed attempts
- **Security Headers** — X-Frame-Options, X-Content-Type-Options, etc.
- **Directory Protection** — `.htaccess` blocks direct PHP access to non-public dirs

---

## 🔄 Phase Roadmap

- **Phase 01 ✅** — Foundation (Auth, Dashboard, RBAC, Security)
- **Phase 02** — User & Role Management
- **Phase 03** — Warehouse & Location Management
- **Phase 04** — Product & Category Management
- **Phase 05** — Inventory Management
- **Phase 06** — Purchase & Sales Modules
- **Phase 07** — Reports & Analytics

---

## 📄 License

Proprietary — All rights reserved.
