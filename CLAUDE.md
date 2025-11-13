# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development
```bash
# Start all development services (server, queue, logs, vite)
composer dev

# Alternatively, run services individually:
php artisan serve              # Development server
php artisan queue:listen --tries=1  # Queue worker
php artisan pail --timeout=0   # Real-time logs
npm run dev                    # Vite dev server for assets
```

### Build & Production
```bash
npm run build                  # Build frontend assets for production
```

### Testing
```bash
vendor/bin/phpunit             # Run all tests
vendor/bin/phpunit --filter=TestName  # Run specific test
vendor/bin/phpunit tests/Unit  # Run unit tests only
vendor/bin/phpunit tests/Feature  # Run feature tests only
```

### Code Quality
```bash
./vendor/bin/pint              # Auto-fix code style (Laravel Pint)
./vendor/bin/pint --test       # Check code style without fixing
```

### Database
```bash
php artisan migrate            # Run migrations
php artisan db:seed            # Run database seeders
php artisan migrate:fresh --seed  # Fresh migration with seeding
```

## Architecture Overview

This is a **Laravel 12 + Livewire** application for market/vendor management ("Pasar 2025"). The architecture prioritizes rapid development and real-time interactivity over strict abstraction patterns.

### Key Architectural Decisions

**Raw Query Builder Pattern (No Eloquent ORM)**
- Despite having `app/Models/` directory, the codebase uses `DB::table()` for all database operations
- No Eloquent models, relationships, or query scopes
- When adding features, follow this pattern: use raw query builder with `DB::table()`

**Livewire-First Frontend**
- All interactive pages are Livewire components in `app/Livewire/`
- Components make HTTP calls to internal API routes (`/api/*`) for data fetching
- Uses `jantinnerezo/livewire-alert` for user feedback notifications
- Livewire SPA mode enabled via `wire:navigate`

**Custom Session-Based Authentication**
- No Laravel's default `Auth` facade usage
- Login validates against `admin` table directly via raw query
- Session stores: `id`, `nama`, `nama_pasar`, `id_pasar`, `username`
- `CheckLogin` middleware validates `session()->has('id')`
- **Security Note**: Passwords stored in plain text in `admin` and `petugas` tables (not hashed)

**Market-Aware Multi-Tenancy**
- All queries filter by `nama_pasar` (market name) stored in session
- Critical for data isolation between different markets
- When writing queries, always include market filtering where applicable

### Database Tables

Core tables (exist in database but not all have migrations):
- `admin` - Admin users (username, password - plain text, nama, nama_pasar)
- `pedagang` - Merchants/vendors (id, nama, kode_kios, id_kios, tarif, email, jenis_dagangan, alamat, nama_pasar)
- `tagihan` - Invoices (id, id_kios, pedagang_id, tanggal_tagihan, status, merchant_id, transaction_id)
- `transaksi` - Transactions (id, nominal_transaksi, tanggal_transaksi, metode_pembayaran, id_petugas, status)
- `petugas` - Officers (username, password)
- `users` - Laravel default (mostly unused)

### API Routes Pattern

All API routes in `routes/api.php` map to methods in single controller: `App\Http\Controllers\Api\ApiController`

Key endpoints:
- `POST /api/login` - Admin authentication
- `POST /api/pedagang` - Get merchants list
- `POST /api/tagihan` - Get invoices
- `POST /api/bayar` - Process payment (creates transaction, updates invoice, sends email)
- `POST /api/dashboard`, `/api/home` - Dashboard statistics
- `POST /api/revenue_chart` - 30-day revenue data

When adding new features, add methods to `ApiController` and route them in `routes/api.php`.

## Frontend Stack

**Build System**: Vite 6.0 with Laravel Vite Plugin
- Input files: `resources/css/app.css`, `resources/js/app.js`
- Tailwind CSS 4.0 configured but **Bootstrap vendor theme actually used**
- Vite config: `vite.config.js`

**CSS Framework Confusion**
- `package.json` includes Tailwind 4.0 with `@tailwindcss/vite`
- `resources/css/app.css` has Tailwind directives
- **However**: Actual UI uses Bootstrap-based vendor theme from `public/vendor/`
- Livewire config sets `pagination_theme: 'tailwind'` but may not apply

**JavaScript Dependencies** (included in layout)
- jQuery
- DataTables 2.3.1
- Chart.js (with custom rounded bar charts)
- Flatpickr (date picker)
- SweetAlert2
- PDFMake

**Layout**: `resources/views/components/layouts/app.blade.php`

## Livewire Components

Located in `app/Livewire/`, views in `resources/views/livewire/`

**Interactive Pages**:
- `Login` - Admin authentication
- `Home` - Dashboard with revenue charts and statistics
- `Pedagang` - Merchant CRUD with modals
- `Tagihan` - Invoice management
- `Transaksi` - Transaction history
- `NavBar`, `Sidebar` - Navigation

**Component Patterns**:
- Use `WithPagination` trait for lists
- Form validation with `$rules` property
- Modal-based editing (`isOpen`, `isEdit` flags)
- Flash messages: `session()->flash('message', 'Success')`
- LivewireAlert: `$this->alert('success', 'Message')`
- Data fetching via `Http::post(env('API_BASE_URL') . '/api/endpoint')`

## Environment Variables

Critical `.env` variables beyond standard Laravel:

```bash
API_BASE_URL=http://localhost:8000  # Base URL for internal API calls (required!)
MAIL_*=...                          # Email config for invoice delivery
QUEUE_CONNECTION=database           # Queue driver (invoice emails are queued)
```

**Important**: Livewire components depend on `API_BASE_URL` being set correctly. Without it, data fetching will fail.

## Key Features

**Invoice Email System** (`app/Mail/InvoiceMail.php`)
- Triggered after successful payment in `ApiController@bayar`
- Queued dispatch: `Mail::to($pedagang->email)->queue(new InvoiceMail(...))`
- Requires proper `MAIL_*` configuration

**Payment Processing**
- Wrapped in database transactions
- Generates transaction IDs: `now()->format('YmdHis') . rand(1000, 9999)`
- Updates invoice status and creates transaction record
- Logs errors on failure

**Dashboard Statistics**
- Revenue tracking: 30-day aggregated totals by date
- Invoice statistics: counts by status
- User performance metrics

## Code Conventions

- **Session Access**: Use `session()->get('nama_pasar')` for market filtering
- **Database Queries**: Always use `DB::table()`, never create Eloquent models
- **Error Handling**: Wrap critical operations in try-catch, use `Log::error()`
- **Date Formatting**: Uses Carbon with Indonesian locale (`AppServiceProvider`)
- **Validation**: Define in Livewire `$rules` or controller validation
- **API Responses**: Return `response()->json(['data' => [...], 'message' => '...'])`

## Notes

- This codebase prioritizes pragmatic development over architectural purity
- When refactoring, maintain the raw query builder pattern unless explicitly asked to migrate to Eloquent
- The `admin` table uses plain text passwords - be aware when working with authentication
- Market filtering (`nama_pasar`) is critical for data isolation - never skip this filter
