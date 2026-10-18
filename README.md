# MotoShowroom SaaS 🏍️
**Production Multi-Tenant Bike Showroom Management Platform**

MotoShowroom SaaS is a modern, enterprise-grade multi-tenant platform built with **Laravel 13**, **PHP 8.5**, **MySQL 8.0**, **Livewire 4**, and **Tailwind CSS 4**. It empowers independent motorcycle dealerships, franchise networks, and multi-brand showrooms to manage inventory, customer enquiries, online service bookings, test rides, career applications, promotional scratch-card marketing, and interactive 360-degree virtual vehicle showcases.

---

## 🚀 Key Features

* **Multi-Tenant Architecture**: Shared MySQL database with strict `tenant_id` column isolation, Eloquent Global Scopes, Form Requests, and Policies.
* **Tiered Subscription System**: Configurable plans, dynamic feature entitlements, trial management, and automated expiry enforcement.
* **Razorpay Payment Gateway**: Server-to-server HMAC signature verification, idempotent webhooks, and audit-logged transactions.
* **Interactive 360° Motorcycle Viewer**: Three.js WebGL canvas supporting both 3D GLTF models and 36-frame turntable photo sequences with graceful fallback.
* **Service Booking Engine**: Concurrency-safe service appointment scheduler with daily capacity validation and upfront deposit processing.
* **Promotional Scratch Cards**: Server-side prize generation, anti-tampering token verification, and single-participation enforcement.
* **Careers & Job Portal**: Branded showroom job boards with private resume storage on non-public disks.
* **AI Virtual Showroom Assistant**: Tenant-grounded AI assistant responding strictly with active showroom data without cross-tenant leaks.
* **Dual Notification Engine**: Transactional emails dispatched via Laravel Queues with paired customer and showroom alerts.

---

## 🛠️ Technology Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 13.37.0 |
| **Language** | PHP 8.5.10 |
| **Database** | MySQL 8.0 (InnoDB, strict mode, decimal arithmetic) |
| **Frontend Reactive UI** | Laravel Blade + Livewire 4.4 |
| **Styling** | Tailwind CSS 4.0 |
| **3D / 360 Engine** | Three.js (WebGL) |
| **Payments** | Razorpay SDK (Server-Side Signature & Webhooks) |
| **Authentication** | Laravel Session Auth + Laravel Socialite (Google OAuth) |
| **Queue & Cache** | Database / Redis |

---

## 📖 Documentation Suite

* [Architecture Overview](docs/ARCHITECTURE.md)
* [Database Schema & ERD](docs/DATABASE_SCHEMA.md)
* [Security & Tenant Isolation](docs/SECURITY.md)
* [Subscriptions & Entitlements](docs/SUBSCRIPTIONS.md)
* [Payment Integration (Razorpay)](docs/PAYMENTS.md)
* [Email & Notifications](docs/EMAILS.md)
* [Testing & QA Strategy](docs/TESTING.md)
* [Production Deployment (Ubuntu VPS)](docs/DEPLOYMENT.md)
* [Backup & Recovery Runbook](docs/BACKUPS.md)
* [Known Limitations](docs/KNOWN_LIMITATIONS.md)

---

## ⚡ Quick Setup & Development

```bash
# 1. Clone repository & install dependencies
composer install
npm install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Database configuration & migrations
php artisan migrate

# 4. Compile frontend assets
npm run build

# 5. Start development servers
php artisan serve
npm run dev
```

---

## 🧪 Testing

```bash
php artisan test --compact
```
