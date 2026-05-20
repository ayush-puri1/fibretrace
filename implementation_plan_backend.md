# FibreTrace B2B Marketplace — Backend Implementation Plan

## Goal
Build the complete production backend for FibreTrace: database schemas, authentication, role-based access, CRUD controllers, real-time bidding engine, settlement logic, and admin/super-admin management — all wired to the existing Blade frontend, replacing every placeholder with live data.

---

## User Review Required

> [!IMPORTANT]
> **Pricing & Business Constants Needed Before Phase 1 Execution:**
> I need you to confirm or adjust these default values that will be seeded into the `system_settings` table:
>
> | Setting | Default Value | Notes |
> |---|---|---|
> | Platform Commission | ₹1.50/kg | Per PRD |
> | Escrow Release Delay | 48 hours | After "Delivered" status |
> | Purity Premium Coefficient | 1.15× | For >95% primary fiber |
> | Color Sort Premium Coefficient | 1.08× | If color-sorted |
> | Min Lot Weight | 100 kg | Per PRD |
> | Max Lot Weight | 25,000 kg | Current UI default |
> | Auction Duration | 24 hours | Default lot lifetime |
> | Weight Variance Tolerance | ±10% | Before renegotiation |

> [!IMPORTANT]
> **Initial Market Index Prices (Week 1 Seed Data):**
>
> | Fiber Category | Price (₹/kg) |
> |---|---|
> | 100% Cotton (White/Raw) | 46.00 |
> | Cotton (Mixed Color) | 38.00 |
> | Poly-Blend (Mixed) | 24.00 |
> | Denim Offcuts | 31.80 |
> | 100% Acrylic | 28.50 |
> | Yarn Ends (Cotton) | 42.00 |
>
> Are these realistic for the Ludhiana-Panipat corridor? Please adjust.

> [!WARNING]
> **TRD Update:** The TRD specifies Laravel 11 but your project runs **Laravel 12**. I will use Laravel 12 as-is. The TRD also mentions "PostgreSQL/MySQL" — I recommend **MySQL 8** for simplicity on Windows dev, with the option to switch to PostgreSQL later. The current `.env` uses SQLite which is insufficient for production features (JSON columns, full-text search, concurrent writes during bidding).

---

## Open Questions

1. **Payment Gateway:** PRD says "simulated placeholder." Should I build a full simulation flow (fake Razorpay-style modal → success/fail callback) or just a button that toggles status?
2. **Image Storage:** Local disk (`storage/app/public`) is fine for MVP? Or do you want Cloudinary/S3?
3. **Email Notifications:** Should registration approval/bid notifications send real emails (via Mailtrap for dev) or just in-app?
4. **WebSocket Provider:** Laravel Reverb (first-party) is the plan per TRD. Confirm this vs. Pusher/Soketi?

---

## Technology Decisions

| Layer | Technology | Rationale |
|---|---|---|
| **Framework** | Laravel 12 (existing) | Already installed, monolithic Blade app |
| **Database** | **MySQL 8.0** | JSON columns, full-text indexing, robust concurrent writes for bidding. Easy Windows setup via XAMPP/Laragon |
| **Auth** | Laravel Breeze (Blade stack) | Provides login/register/password scaffolding that integrates with existing Blade views |
| **Real-time** | Laravel Reverb + Echo | Native WebSocket for bid updates, zero third-party cost |
| **File Storage** | Local disk (`public`) | Symlinked via `php artisan storage:link` |
| **Queue** | Database driver | Already configured in `.env`, handles bid notifications & email |
| **Cache** | Database driver | Already configured, used for rate-limiting bids |

---

## Database Schema (10 Tables)

### Table: `users` (Modified)
```
id                  BIGINT UNSIGNED PK AUTO_INCREMENT
name                VARCHAR(255)          -- Contact person name
company_name        VARCHAR(255)          -- Legal company name
email               VARCHAR(255) UNIQUE
phone               VARCHAR(20)
password            VARCHAR(255)          -- bcrypt hashed
gstin               VARCHAR(15) UNIQUE    -- 15-digit GST number
role                ENUM('seller','buyer','admin','super_admin')
status              ENUM('pending','verified','suspended','rejected')
rejection_reason    TEXT NULLABLE
city                VARCHAR(100)
state               VARCHAR(50)
address             TEXT NULLABLE         -- Full address (PII, masked)
verified_at         TIMESTAMP NULLABLE
verified_by         BIGINT UNSIGNED FK→users NULLABLE
remember_token      VARCHAR(100) NULLABLE
email_verified_at   TIMESTAMP NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Table: `lots`
```
id                  BIGINT UNSIGNED PK
seller_id           BIGINT UNSIGNED FK→users
lot_number          VARCHAR(20) UNIQUE    -- "FT-1001" auto-generated
category            ENUM('cutting_scraps','yarn_ends','rejected_batches','selvedge')
fiber_type          VARCHAR(100)          -- "100% Cotton", "Poly-Blend 65/35"
fiber_purity_pct    TINYINT UNSIGNED      -- 0-100
color_sorted        BOOLEAN DEFAULT false
color_description   VARCHAR(100)          -- "White/Raw", "Mixed Colors"
weight_kg           DECIMAL(10,2)
base_price          DECIMAL(8,2)          -- Auto-suggested ₹/kg
status              ENUM('draft','pending_review','active','awarded','settled','cancelled','suspended')
highest_bid_id      BIGINT UNSIGNED FK→bids NULLABLE
auction_ends_at     TIMESTAMP NULLABLE
flagged             BOOLEAN DEFAULT false
flag_count          SMALLINT DEFAULT 0
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_lots_status (status)
INDEX idx_lots_seller (seller_id)
INDEX idx_lots_fiber (fiber_type)
```

### Table: `lot_images`
```
id                  BIGINT UNSIGNED PK
lot_id              BIGINT UNSIGNED FK→lots ON DELETE CASCADE
file_path           VARCHAR(500)          -- "lot-images/FT-1042/img_001.webp"
file_name           VARCHAR(255)
file_size           INT UNSIGNED          -- bytes
sort_order          TINYINT DEFAULT 0
created_at          TIMESTAMP
```

### Table: `bids`
```
id                  BIGINT UNSIGNED PK
lot_id              BIGINT UNSIGNED FK→lots
buyer_id            BIGINT UNSIGNED FK→users
amount              DECIMAL(8,2)          -- ₹/kg
status              ENUM('active','outbid','won','cancelled','rejected')
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_bids_lot (lot_id, amount DESC)
INDEX idx_bids_buyer (buyer_id)
UNIQUE idx_no_duplicate (lot_id, buyer_id, amount)
```

### Table: `transactions`
```
id                  BIGINT UNSIGNED PK
transaction_number  VARCHAR(20) UNIQUE    -- "TRX-99824"
lot_id              BIGINT UNSIGNED FK→lots
bid_id              BIGINT UNSIGNED FK→bids
buyer_id            BIGINT UNSIGNED FK→users
seller_id           BIGINT UNSIGNED FK→users
agreed_price        DECIMAL(8,2)          -- ₹/kg
actual_weight_kg    DECIMAL(10,2) NULLABLE
subtotal            DECIMAL(12,2)
commission_amount   DECIMAL(10,2)
total_amount        DECIMAL(12,2)
payment_status      ENUM('pending','paid','released','disputed','refunded')
dispute_reason      TEXT NULLABLE
logistics_status    ENUM('ready_for_pickup','in_transit','delivered','confirmed')
escrow_released_at  TIMESTAMP NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Table: `market_prices`
```
id                  BIGINT UNSIGNED PK
fiber_category      VARCHAR(100)          -- "100% Cotton (White/Raw)"
sub_label           VARCHAR(50)           -- "High Purity", "Standard"
price_per_kg        DECIMAL(8,2)
previous_price      DECIMAL(8,2) NULLABLE
week_start          DATE
published_by        BIGINT UNSIGNED FK→users
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX idx_market_week (week_start, fiber_category)
```

### Table: `system_settings`
```
id                  BIGINT UNSIGNED PK
key                 VARCHAR(100) UNIQUE
value               TEXT
type                ENUM('number','string','boolean','json')
description         TEXT
updated_by          BIGINT UNSIGNED FK→users NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Table: `activity_logs`
```
id                  BIGINT UNSIGNED PK
user_id             BIGINT UNSIGNED FK→users NULLABLE
action              VARCHAR(100)          -- "bid.placed", "lot.created", "user.approved"
subject_type        VARCHAR(100)          -- "App\Models\Lot"
subject_id          BIGINT UNSIGNED
description         TEXT
metadata            JSON NULLABLE
ip_address          VARCHAR(45) NULLABLE
created_at          TIMESTAMP

INDEX idx_activity_user (user_id)
INDEX idx_activity_subject (subject_type, subject_id)
```

### Table: `lot_reports` (User-submitted flags)
```
id                  BIGINT UNSIGNED PK
lot_id              BIGINT UNSIGNED FK→lots
reporter_id         BIGINT UNSIGNED FK→users
reason              TEXT
created_at          TIMESTAMP
```

### Table: `notifications` (Laravel default)
```
-- Standard Laravel notifications table (UUID PK, type, notifiable, data, read_at)
```

---

## Proposed Changes

### Phase 1: Database Foundation
> Install MySQL, configure `.env`, create all migrations & models.

#### [MODIFY] `.env`
- Switch `DB_CONNECTION` from `sqlite` to `mysql`
- Add `DB_HOST`, `DB_PORT`, `DB_DATABASE=fibretrace`, `DB_USERNAME`, `DB_PASSWORD`

#### [MODIFY] `database/migrations/0001_01_01_000000_create_users_table.php`
- Extend users table with `company_name`, `phone`, `gstin`, `role`, `status`, `city`, `state`, `address`, `rejection_reason`, `verified_at`, `verified_by`

#### [NEW] `database/migrations/xxxx_create_lots_table.php`
#### [NEW] `database/migrations/xxxx_create_lot_images_table.php`
#### [NEW] `database/migrations/xxxx_create_bids_table.php`
#### [NEW] `database/migrations/xxxx_create_transactions_table.php`
#### [NEW] `database/migrations/xxxx_create_market_prices_table.php`
#### [NEW] `database/migrations/xxxx_create_system_settings_table.php`
#### [NEW] `database/migrations/xxxx_create_activity_logs_table.php`
#### [NEW] `database/migrations/xxxx_create_lot_reports_table.php`
#### [NEW] `database/migrations/xxxx_create_notifications_table.php`

---

### Phase 2: Models & Relationships

#### [MODIFY] `app/Models/User.php`
- Add fillable fields, relationships: `lots()`, `bids()`, `buyTransactions()`, `sellTransactions()`
- Add scopes: `scopePending()`, `scopeVerified()`, `scopeSellers()`, `scopeBuyers()`
- Add role helpers: `isSeller()`, `isBuyer()`, `isAdmin()`, `isSuperAdmin()`
- Add PII masking accessor: `getMaskedPhoneAttribute()`, `getMaskedGstinAttribute()`

#### [NEW] `app/Models/Lot.php`
- Relationships: `seller()`, `bids()`, `images()`, `highestBid()`, `transaction()`, `reports()`
- Scopes: `scopeActive()`, `scopeByFiber()`, `scopeExpired()`
- Auto lot_number generation in `boot()` method

#### [NEW] `app/Models/Bid.php`
- Relationships: `lot()`, `buyer()`

#### [NEW] `app/Models/Transaction.php`
- Relationships: `lot()`, `bid()`, `buyer()`, `seller()`
- Commission calculation method

#### [NEW] `app/Models/LotImage.php`
#### [NEW] `app/Models/MarketPrice.php`
#### [NEW] `app/Models/SystemSetting.php` (with static helper `SystemSetting::get('key')`)
#### [NEW] `app/Models/ActivityLog.php`
#### [NEW] `app/Models/LotReport.php`

---

### Phase 3: Authentication & Middleware

#### Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```
Then **override** its generated views with our existing glassmorphism Blade files.

#### [NEW] `app/Http/Middleware/EnsureUserIsVerified.php`
- Redirects `pending` users to `/pending` screen
- Blocks `suspended`/`rejected` users with error

#### [NEW] `app/Http/Middleware/EnsureUserRole.php`
- Accepts role parameter: `role:admin`, `role:super_admin`
- Returns 403 for unauthorized role access

#### [MODIFY] `bootstrap/app.php`
- Register middleware aliases: `verified.user`, `role`

#### [MODIFY] `routes/web.php` — Complete rewrite:
```php
// Public
Route::get('/', [PageController::class, 'welcome']);
Route::get('/market', [MarketPriceController::class, 'publicIndex']);

// Auth (Breeze handles POST routes)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create']);
    Route::post('/login', [LoginController::class, 'store']);
});

// Authenticated but NOT verified
Route::middleware('auth')->group(function () {
    Route::get('/pending', [PendingController::class, 'show']);
    Route::post('/logout', [LoginController::class, 'destroy']);
});

// Verified Client Portal
Route::middleware(['auth', 'verified.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/auctions', [AuctionController::class, 'index']);
    Route::get('/wallet', [WalletController::class, 'index']);

    // Seller
    Route::get('/seller/create', [LotController::class, 'create']);
    Route::post('/seller/lots', [LotController::class, 'store']);
    Route::get('/seller/ledger', [LotController::class, 'ledger']);
    Route::get('/seller/lot/{lot}', [LotController::class, 'show']);
    Route::post('/seller/lot/{lot}/accept', [LotController::class, 'acceptBid']);
    Route::post('/seller/lot/{lot}/cancel', [LotController::class, 'cancel']);

    // Buyer
    Route::get('/buyer/room/{lot}', [BidController::class, 'room']);
    Route::post('/buyer/room/{lot}/bid', [BidController::class, 'place']);
    Route::get('/buyer/bids', [BidController::class, 'ledger']);
    Route::post('/buyer/bid/{bid}/cancel', [BidController::class, 'cancelBid']);

    // Settlement
    Route::get('/settlement/{transaction}', [SettlementController::class, 'show']);
    Route::post('/settlement/{transaction}/pay', [SettlementController::class, 'simulatePayment']);
    Route::get('/dispatch/{transaction}', [DispatchController::class, 'show']);
    Route::post('/dispatch/{transaction}/update', [DispatchController::class, 'updateStatus']);
});

// Admin Portal
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index']);
    Route::get('/verifications', [VerificationController::class, 'index']);
    Route::post('/verifications/{user}/approve', [VerificationController::class, 'approve']);
    Route::post('/verifications/{user}/reject', [VerificationController::class, 'reject']);
    Route::get('/moderation', [ModerationController::class, 'index']);
    Route::post('/moderation/{lot}/suspend', [ModerationController::class, 'suspend']);
    Route::post('/moderation/{lot}/restore', [ModerationController::class, 'restore']);
    Route::get('/audit', [AuditController::class, 'index']);
});

// Super-Admin Portal
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard']);
    Route::get('/admins', [SuperAdminController::class, 'admins']);
    Route::post('/admins', [SuperAdminController::class, 'createAdmin']);
    Route::post('/admins/{user}/toggle', [SuperAdminController::class, 'toggleAdmin']);
    Route::get('/market-index', [MarketPriceController::class, 'manage']);
    Route::post('/market-index', [MarketPriceController::class, 'update']);
    Route::get('/settings', [SystemSettingController::class, 'index']);
    Route::post('/settings', [SystemSettingController::class, 'update']);
});
```

---

### Phase 4: Controllers (12 Total)

#### [NEW] Controllers in `app/Http/Controllers/`:

| Controller | Key Methods | Data Passed to View |
|---|---|---|
| `RegisterController` | `create()`, `store()` | Validation + create user with `pending` status |
| `LoginController` | `create()`, `store()`, `destroy()` | Auth::attempt, redirect by role |
| `PendingController` | `show()` | Auth user status check |
| `DashboardController` | `index()` | User's lots, bids, stats, market prices, recent activity |
| `LotController` | `create()`, `store()`, `ledger()`, `show()`, `acceptBid()`, `cancel()` | Lots with bids, images; auto-price suggestion |
| `BidController` | `room()`, `place()`, `ledger()`, `cancelBid()` | Lot details, bid history, current highest |
| `AuctionController` | `index()` | Active lots with filters (fiber, weight, location) |
| `SettlementController` | `show()`, `simulatePayment()` | Transaction invoice, masked PII |
| `AdminDashboardController` | `index()` | KPIs (lot count, bid count, escrow volume, trader count), activity log |
| `VerificationController` | `index()`, `approve()`, `reject()` | Pending users with full PII |
| `ModerationController` | `index()`, `suspend()`, `restore()` | Active/flagged lots |
| `AuditController` | `index()` | Unmasked transaction records |
| `SuperAdminController` | `dashboard()`, `admins()`, `createAdmin()`, `toggleAdmin()` | Revenue, GMV, user counts, admin list |
| `MarketPriceController` | `publicIndex()`, `manage()`, `update()` | Weekly prices with trends |
| `SystemSettingController` | `index()`, `update()` | All system settings |

---

### Phase 5: Price Auto-Suggest Algorithm

```php
// app/Services/PriceSuggestionService.php
class PriceSuggestionService
{
    public function suggest(string $fiberType, int $purityPct, bool $colorSorted): float
    {
        $basePrice = MarketPrice::latestForFiber($fiberType)->price_per_kg;
        $purityCoeff = $purityPct >= 95
            ? (float) SystemSetting::get('purity_premium_coefficient', 1.15)
            : 1.0;
        $colorCoeff = $colorSorted
            ? (float) SystemSetting::get('color_sort_premium_coefficient', 1.08)
            : 1.0;
        return round($basePrice * $purityCoeff * $colorCoeff, 2);
    }
}
```

---

### Phase 6: Seeders (Real Data)

#### [NEW] `database/seeders/SystemSettingSeeder.php`
Seeds all 8 system constants from the table above.

#### [NEW] `database/seeders/MarketPriceSeeder.php`
Seeds 6 fiber categories with 4 weeks of historical price data.

#### [NEW] `database/seeders/UserSeeder.php`
```
Super Admin:  superadmin@fibretrace.in / password123
Admin 1:      amit.s@fibretrace.in / password123
Admin 2:      priya.singh@fibretrace.in / password123
Seller 1:     admin@globaltextiles.com (GSTIN: 03AAAAA0000A1Z5) / password123
Seller 2:     sunrisegarments@gmail.com (GSTIN: 03CCCCC2222C3X7) / password123
Buyer 1:      contact@panipatspinners.in (GSTIN: 06BBBBB1111B2Y6) / password123
Buyer 2:      info@haryanathreads.com (GSTIN: 06DDDDD3333D4W8) / password123
+ 3 pending users for verification queue testing
```

#### [NEW] `database/seeders/LotSeeder.php`
Seeds 5 lots with various statuses (active, awarded, cancelled) with images.

#### [NEW] `database/seeders/BidSeeder.php`
Seeds bid history for active lots.

#### [NEW] `database/seeders/TransactionSeeder.php`
Seeds 2 completed transactions for audit ledger testing.

---

### Phase 7: Frontend Integration (Blade Changes)

Every view needs to replace hardcoded HTML with Blade variables. Summary of changes per view:

| View File | Key Changes |
|---|---|
| `auth/register.blade.php` | Wire `<form>` to `POST /register`, add `@csrf`, `name` attributes, `@error` blocks |
| `auth/login.blade.php` | Wire to `POST /login`, add `@csrf`, error display |
| `auth/pending.blade.php` | Show `{{ Auth::user()->status }}`, dynamic review info |
| `dashboard.blade.php` | Replace all hardcoded stats with `{{ $activeLots }}`, `{{ $activeBids }}`, `{{ $totalYield }}`. Market prices from `$marketPrices` |
| `auctions.blade.php` | `@foreach($lots as $lot)` loop, filter form with GET params |
| `seller/create-lot.blade.php` | Wire form to `POST /seller/lots`, file upload, AJAX price suggestion |
| `seller/ledger.blade.php` | `@foreach($lots as $lot)` with status badges, pagination |
| `seller/lot-details.blade.php` | `{{ $lot->lot_number }}`, `{{ $lot->highestBid->amount }}`, bid feed from `$lot->bids` |
| `buyer/bidding-room.blade.php` | Dynamic lot data, bid form to `POST /buyer/room/{lot}/bid` |
| `buyer/bids-ledger.blade.php` | `@foreach($myBids as $bid)` with winning/outbid status |
| `settlement.blade.php` | `{{ $transaction }}` data, masked buyer/seller PII |
| `admin/dashboard.blade.php` | `{{ $stats->activeListings }}`, `{{ $stats->pendingVerifications }}`, activity log from DB |
| `admin/verifications.blade.php` | `@foreach($pendingUsers as $user)` with approve/reject forms |
| `admin/moderation.blade.php` | `@foreach($lots as $lot)` with suspend forms |
| `admin/audit.blade.php` | `@foreach($transactions as $txn)` with UNMASKED PII |
| `super-admin/dashboard.blade.php` | `{{ $totalRevenue }}`, `{{ $gmv }}`, `{{ $userCounts }}`, admin list from DB |
| `super-admin/admins.blade.php` | `@foreach($admins as $admin)` with role edit/suspend forms |
| `super-admin/market-index.blade.php` | `@foreach($prices as $price)` with editable inputs, POST form |
| `super-admin/settings.blade.php` | `@foreach($settings as $setting)` with editable inputs, POST form |
| `layouts/dashboard.blade.php` | Dynamic nav: show seller/buyer links based on `Auth::user()->role` |
| `layouts/admin.blade.php` | Show `{{ Auth::user()->name }}` in sidebar |
| `layouts/superadmin.blade.php` | Show `{{ Auth::user()->name }}` in sidebar |

---

### Phase 8: Real-Time Bidding (Laravel Reverb)

#### [NEW] Packages
```bash
composer require laravel/reverb
php artisan reverb:install
npm install --save-dev laravel-echo pusher-js
```

#### [NEW] `app/Events/BidPlaced.php`
Broadcasts on `lot.{lotId}` channel with bid amount, buyer ID (anonymized), timestamp.

#### [NEW] `app/Events/AuctionClosed.php`
Broadcasts when seller accepts bid.

#### Frontend: Add Echo listener in `buyer/bidding-room.blade.php`:
```js
Echo.channel('lot.' + lotId)
    .listen('BidPlaced', (e) => { /* update bid feed */ })
    .listen('AuctionClosed', (e) => { /* redirect to settlement */ });
```

---

## Verification Plan

### Automated Tests
```bash
php artisan test                          # Run full suite
php artisan migrate:fresh --seed          # Verify schema + seeders
php artisan route:list                    # Verify all routes registered
```

#### Unit Tests to Write:
- `PriceSuggestionServiceTest` — verify coefficient math
- `LotCreationTest` — verify validation (min weight, required photos)
- `BidPlacementTest` — verify bid must exceed current highest
- `UserVerificationTest` — verify admin can approve/reject
- `SettlementTest` — verify commission calculation

### Manual Verification
1. Register new seller → should land on `/pending`
2. Log in as admin → approve user → user can now access `/dashboard`
3. Create lot as seller → appears on `/auctions`
4. Place bid as buyer → bid appears in seller's lot details
5. Accept bid → transaction created → settlement page shows invoice
6. Super-admin updates market prices → reflected on client dashboard
7. Super-admin changes commission rate → next settlement uses new rate

### Browser Testing
- Navigate all 21 pages and verify no placeholder text remains
- Test responsive layout on mobile/tablet viewports
- Verify WebSocket bid updates in real-time (two browser windows)
