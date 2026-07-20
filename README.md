# ReSaaS — Restaurant Management SaaS

A multi-tenant restaurant management system built with Laravel, Livewire, and Tailwind CSS.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Livewire v4, Alpine.js, Tailwind CSS v3, Vite
- **Multi-Tenancy:** stancl/tenancy (database-per-tenant)
- **RBAC:** spatie/laravel-permission
- **Database:** MySQL (central + per-tenant databases)

## Roles

| Role | Description |
|------|-------------|
| **owner** | Full access |
| **admin** | Full operational access |
| **manager** | Day-to-day operations |
| **chef** | Kitchen operations |
| **waiter** | Floor service |
| **cashier** | Payment handling |
| **customer** | Self-service portal |

## Features

- **Menu Management** — Categories, items, modifiers with CRUD, dietary labels, allergens, drag-and-drop reordering
- **Table & Floor Plan** — Visual SVG floor plan with sections, auto-arrange grid, click to edit/view details, QR code generation per table
- **Order Management** — Table-side ordering via waiter, online ordering with cart, KDS real-time display with elapsed time coloring, status workflow (pending → confirmed → preparing → ready → served → completed), order types (dine-in, takeaway, delivery, online)
- **POS & Billing** — Quick category-based item selection, split bills, multiple payment methods (cash, card, Stripe, PayPal), tax calculation, thermal receipt printing, shift open/close with cash reconciliation
- **Customer Portal** — Public menu browsing with cart + modifiers, order tracking, online reservations, authenticated order history + loyalty points + profile management, QR code table-side ordering
- **Inventory & Supply Chain** — Full CRUD with stock tracking, SKU, reorder points, category/unit filtering, low-stock alerts; stock adjustment with reason; automatic ingredient deduction on order completion; supplier management; purchase orders (draft → ordered → received)
- **Staff Management** — Profiles with roles, shift scheduling, clock in/out, sales per employee tracking
- **Loyalty Program** — Points per spend, visit-based and birthday rewards, tiered programs
- **Reservations** — Date/time picker with auto table assignment, walk-in management, notifications on new/updated/cancelled reservations
- **Notifications** — Real-time in-app notifications for orders (new, ready, served), reservations, low stock, payments; role-targeted delivery; polling badge in all layouts
- **Reports & Analytics** — Sales by time period, top-selling items, peak hours, average order value, table turnover, staff performance, inventory usage, PDF/Excel export with compact single-page layout
- **Design Config** — Customizable colors, logo, favicon, receipt header/footer
- **Multi-Language** — English + Arabic (RTL), language switcher
- **Auto-Arrange Floor Plan** — One-click grid layout for tables within sections, smart defaults for new tables

## Project Structure

```
app/
├── Livewire/          # Role-based components (Admin/, Manager/, Kitchen/, Waiter/, Cashier/, Customer/)
├── Models/Tenant/     # Per-tenant models (Order, MenuItem, Table, InventoryItem, etc.)
├── Support/           # NotificationHelper, InventoryHelper, ReportHelper
├── Exports/           # ReportExport (Excel via Laravel Excel)
├── Mail/              # OrderConfirmation, InvoiceMail
├── Http/Middleware/   # SetLocale, SecurityHeaders, ApplySmtpSettings
├── Console/Commands/  # CreateTenant, GenerateReports
└── Providers/         # TenancyServiceProvider, AppServiceProvider

database/
├── migrations/        # Central DB (tenants, domains, users)
└── migrations/tenant/ # Tenant DB (all restaurant tables)

resources/
├── views/layouts/     # Role-specific layouts (admin, manager, kitchen, waiter, cashier, customer, public, guest)
├── views/livewire/    # Component views (mirrors Livewire structure)
├── views/exports/     # PDF template for reports
└── views/components/  # Sidebars, navbar, shared components

routes/
├── tenant.php         # All tenant-specific routes
├── auth-tenant.php    # Tenant login/register
├── web.php            # Central app routes
└── api.php            # Sanctum API routes
```

## Setup

```bash
git clone <repo>
cd resaas
cp .env.example .env
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan tenants:migrate --tenant=1
php artisan tenants:seed --tenant=1
```

## Design System

- **Primary:** Warm amber/orange
- **Typography:** Inter (English), Cairo (Arabic)
- **Components:** `btn-primary`, `btn-secondary`, `card`, `input` utility classes
- **Colors:** Material Design 3-inspired surface colors via CSS custom properties, runtime customization via Design Config
