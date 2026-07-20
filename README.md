# ReSaaS — Restaurant Management SaaS

A multi-tenant restaurant management system built with Laravel, Livewire, and Tailwind CSS.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Livewire v4, Alpine.js, Tailwind CSS v3, Vite
- **Multi-Tenancy:** stancl/tenancy ^3.x (database-per-tenant)
- **RBAC:** spatie/laravel-permission
- **Database:** MySQL (central + per-tenant databases)

## Multi-Tenancy

- **Strategy:** Database-per-tenant
- **Identification:** Domain/subdomain (`{slug}.resaas.test`)
- **Central DB:** Tenants, domains, central admin users
- **Tenant DB:** All restaurant-specific tables (menu, orders, inventory, etc.)

### Tenant creation flow

1. Admin creates tenant with name, slug, plan, domains
2. `TenantCreated` event fires → pipeline creates database, runs migrations, seeds data
3. Default roles, branches, menu categories, and tables are seeded

## Roles & Permissions

| Role               | Description             | Dashboard                                                                                     |
| ------------------ | ----------------------- | --------------------------------------------------------------------------------------------- |
| **owner**    | Full access             | Revenue KPIs, 7-day chart, orders, top items, low stock, reservations                         |
| **admin**    | Full operational access | Same as owner                                                                                 |
| **manager**  | Day-to-day operations   | Same KPIs + orders, staff shifts, floor plan, reports                                         |
| **chef**     | Kitchen operations      | Pending/preparing/ready counts, active orders (color-coded by elapsed time), low stock alerts |
| **waiter**   | Floor service           | My active tables, my orders, pending orders with elapsed time                                 |
| **cashier**  | Payment handling        | Today's sales, shift summary, transactions, payment methods                                   |
| **customer** | Self-service portal     | My orders, loyalty points, upcoming reservations (redirected to`/customer/menu`)            |

## Layouts

| Layout               | Audience        | Sidebar                                                   |
| -------------------- | --------------- | --------------------------------------------------------- |
| `layouts.admin`    | Owner/Admin     | Admin sidebar (all modules)                               |
| `layouts.manager`  | Manager         | Manager sidebar (operations + reports)                    |
| `layouts.kitchen`  | Chef            | Kitchen sidebar (orders, prep list, alerts)               |
| `layouts.waiter`   | Waiter          | Waiter sidebar (tables, orders)                           |
| `layouts.cashier`  | Cashier         | Cashier sidebar (POS, invoices, shifts)                   |
| `layouts.customer` | Customer        | Top navbar (menu, orders, reservations, loyalty, profile) |
| `layouts.public`   | Public visitors | None                                                      |
| `layouts.guest`    | Login/Register  | None                                                      |

All layouts support RTL for Arabic (`dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"`).

## Features

### Menu Management (`/admin/menu`)

- Categories, items, modifiers with CRUD
- Dietary labels, allergens, pricing, availability scheduling
- Drag-and-drop reordering

### Table & Floor Management (`/admin/tables`, `/admin/floor-plan`)

- Visual floor plan designer with SVG canvas
- Table CRUD with section/capacity/status
- QR code generation per table

### Order Management

- **Waiter:** Table-side ordering with modifiers, split bills, transfer tables
- **KDS (Kitchen):** Real-time order display with elapsed time coloring
- **Status workflow:** pending → confirmed → preparing → ready → served → completed
- Order types: dine-in, takeaway, delivery, online

### POS & Billing (`/cashier/pos`)

- Quick item selection by category
- Split bill, multiple payment methods (cash, card, Stripe, PayPal)
- Tax calculation, service charge, tips
- Receipt printing (thermal, 80mm format)
- Shift open/close with cash reconciliation

### Customer Portal (Public + Authenticated)

- **Public:** Full menu browsing, cart with modifiers, checkout, order tracking, reservations
- **Authenticated:** Order history, loyalty points, reservations, profile management
- QR code → `/menu?table=X` for table-side ordering

### Inventory & Supply Chain

- Inventory items with SKU, stock tracking, reorder points
- Supplier management
- Purchase orders (draft → ordered → received → cancelled)
- Recipes linking menu items to ingredients
- Low stock alerts

### Staff Management

- Staff profiles with roles
- Shift scheduling, clock in/out, break tracking
- Sales per employee tracking

### Loyalty Program

- Points per spend, visit-based rewards
- Birthday rewards, tiered programs
- Customer-facing points balance and transaction history

### Reservations

- Date/time picker with auto table assignment
- Walk-in management
- Customer notifications

### Reports & Analytics (`/admin/reports`)

- Sales by day/week/month/hour
- Top-selling items, peak hours, average order value
- Table turnover rate, staff performance
- Tax reports, inventory usage vs sales
- Export to PDF/Excel

### Design Config (`/admin/design`)

- Customizable colors, logo, favicon
- Receipt header/footer customization
- Restaurant branding for online ordering portal

### Multi-Language

- English + Arabic (RTL) support
- Language switcher in sidebar
- Translation file at `lang/ar.json`

## Project Structure

```
app/
├── Livewire/          # Role-based components (Admin/, Manager/, Kitchen/, Waiter/, Cashier/, Customer/)
├── Models/Tenant/     # Per-tenant models (Order, MenuItem, Table, etc.)
├── Mail/              # OrderConfirmation, InvoiceMail
├── Http/Middleware/   # SetLocale, SecurityHeaders, ApplySmtpSettings
├── Console/Commands/  # CreateTenant, GenerateReports
└── Providers/         # TenancyServiceProvider, AppServiceProvider

database/
├── migrations/        # Central DB (tenants, domains, users)
└── migrations/tenant/ # Tenant DB (all restaurant tables)

resources/
├── views/layouts/     # Role-specific layouts
├── views/livewire/    # Component views (mirrors Livewire structure)
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
# Configure DB connections (central + tenant template)

composer install
npm install && npm run build

php artisan key:generate
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan tenants:migrate --tenant=1
php artisan tenants:seed --tenant=1
```

## Design System

- **Primary:** Warm amber/orange (rgb 232 89 12)
- **Typography:** Inter (English), Cairo (Arabic)
- **Components:** `btn-primary`, `btn-secondary`, `card`, `input` utility classes
- **Colors:** Material Design 3-inspired surface colors via CSS custom properties
- Restaurants can customize colors/logo via Design Config at runtime
