# 🏛️ FibreTrace B2B Marketplace

FibreTrace is a specialized, enterprise-grade B2B digital commodity exchange designed to optimize pre-consumer textile waste monetization and traceability. It bridges the industrial data gap between **garment manufacturers (sellers in Ludhiana)** and **mechanical/shoddy yarn recyclers (buyers in Panipat)**.

Built on **Laravel 12**, this monolithic web application leverages modern web technologies to replace traditional opaque waste aggregations with a premium, glassmorphism-themed, and real-time digital commodity floor.

---

## 📌 1. The Problem Statement

The pre-consumer textile waste value chain in the Ludhiana-Panipat industrial corridor face three core challenges:

1. **Information Asymmetry**: Ludhiana hosiery units generate 50–200 kg of clean textile scraps daily. Lacking visibility into recycler demand and exact fiber blend values (e.g., 100% Cotton vs. 80/20 Cotton/Polyester), they dump mixed waste at low baseline scrap rates.
2. **Processing Yield Losses**: Spinning mills in Panipat receive highly contaminated, unsorted waste bales from middlemen. This leads to a **~40% material processing loss** during mechanical recycling. Accurately sorted feedstock can easily double processing yields and command premium rates (~₹73/kg).
3. **Upcoming Regulatory Compliance**: With India’s upcoming Textile Extended Producer Responsibility (EPR) regulations (2026–2027) and the EU ban on destroying unsold apparel (July 2026), factories must digitally track and audit their waste disposal streams to maintain compliance.

---

## 💡 2. The Solution

FibreTrace upgrades and digitizes this value chain through a secure digital matching framework:

- **Sort & Profile (Factory App)**: Sellers list waste lots with precise categories (cutting scraps, yarn ends, selvedge), fiber compositions, color-sorting states, and photo verification.
- **Automated Pricing Engine**: Suggests starting base valuations based on weekly market index trends combined with purity and sorting premiums.
- **Blind Bidding (Recycler Floor)**: Buyers place bids per kilogram in a real-time, competitive, and anonymous bidding room to prevent collusion and bypassing of the platform.
- **Strict PII Masking**: Real identities, GSTINs, phone numbers, and addresses of transacting parties are dynamically masked. Complete unmasked details are restricted to platform staff for dispute auditing.
- **Escrow-Style Settlement & Simulated Logistics**: A simulated payment gate holds commissions and initiates logistics tracking status updates ("Ready for Pickup" ➔ "In Transit" ➔ "Delivered") to coordinate physical lots transfer.

---

## 🛠️ 3. Technology Stack & Key Aspects

- **Framework**: Laravel 12 (monolithic MVC architecture serving Blade views)
- **Frontend Interactivity**: Tailwind CSS (CDN-based HSL tokens) + Alpine.js + Google Fonts (Inter/Outfit)
- **Database**: SQLite (for local development/testing) or MySQL 8.0 (Production)
- **Real-Time WebSockets**: Laravel Reverb + Laravel Echo for real-time live bid propagation
- **Background Jobs**: Laravel Database Queue Worker for processing background events (e.g., email notifications)
- **Visual Design**: Premium dark-mode glassmorphism cards (`.glass-card`), dynamic mesh backdrops, responsive grid layouts, and active interactive charts.

---

## 📁 4. Project File Map & Structure

```text
fiberTrace/
├── app/
│   ├── Http/
│   │   ├── Controllers/             # MVC controllers implementing portal logic
│   │   │   ├── Auth/                # Laravel Breeze authentication overrides
│   │   │   ├── LotController.php    # Listings CRUD + PriceSuggestion integration
│   │   │   ├── BidController.php    # Bidding rooms + blind-bid validation
│   │   │   └── ...                  # Dashboard, Wallet, Admin & Super-Admin portals
│   │   └── Middleware/              # Route guards
│   │       ├── EnsureUserIsVerified.php  # Redirects unverified accounts to pending KYC page
│   │       └── EnsureUserRole.php        # Gated access by roles (seller, buyer, admin, super_admin)
│   ├── Models/                      # Eloquent models with relationships, scopes, and PII masking
│   │   ├── User.php, Lot.php, Bid.php, Transaction.php, LotImage.php, MarketPrice.php, etc.
│   └── Services/
│       └── PriceSuggestionService.php    # Algorithmic suggeted base pricing
├── database/
│   ├── database.sqlite              # Local SQLite database
│   ├── migrations/                  # 11 database table schema migrations
│   └── seeders/                     # Seeders populating users, lots, bids, system settings
├── resources/
│   ├── css/app.css                  # Custom Tailwind directives and typography settings
│   └── views/                       # Blade templates arranged by portal hierarchy
│       ├── layouts/                 # Structural base templates (app, dashboard, admin, superadmin)
│       ├── auth/                    # Glassmorphism login & multi-step registration
│       ├── seller/                  # Lot creation forms & warehouse waste ledger
│       ├── buyer/                   # Live auction floor & blind bidding room
│       └── welcome.blade.php        # Landing page with active database statistics ticker
└── routes/
    ├── web.php                      # 54 custom web routes gated by middleware
    └── auth.php                     # Authentication endpoints
```

---

## 🚀 5. Getting Started & Local Installation

### Prerequisites
Make sure your development machine has the following installed:
- **PHP** >= 8.2 (with SQLite / MySQL extensions)
- **Composer** (PHP Package Manager)
- **Node.js** & **NPM** (Frontend Asset Compiler)
- **MySQL 8.0** (optional, SQLite is set up by default)

### Step-by-Step Installation

1. **Clone the repository and enter the directory**:
   ```bash
   cd fiberTrace
   ```

2. **Install Composer dependencies**:
   ```bash
   composer install
   ```

3. **Install NPM dependencies**:
   ```bash
   npm install
   ```

4. **Set up the Environment variables**:
   Create a copy of `.env.example` named `.env`:
   ```bash
   cp .env.example .env
   ```
   Generate the application key:
   ```bash
   php artisan key:generate
   ```

5. **Configure the Database**:
   FibreTrace supports both SQLite (default local) and MySQL.
   - **For SQLite (Default)**: Ensure the file `database/database.sqlite` exists (it is initialized in the repo). Make sure `.env` specifies:
     ```env
     DB_CONNECTION=sqlite
     ```
   - **For MySQL**: Create a database named `fibretrace` on your MySQL server, then update `.env`:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=fibretrace
     DB_USERNAME=root
     DB_PASSWORD=yourpassword
     ```

6. **Run Database Migrations and Seeders**:
   This compiles the database structure and seeds it with demo accounts, lots, bidding history, and index pricing:
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Create Storage Symlink**:
   Link the public storage folder to allow local uploaded lot images to render in browser templates:
   ```bash
   php artisan storage:link
   ```

8. **Start the Vite development server**:
   ```bash
   npm run dev
   ```

9. **Start the local PHP server**:
   In a separate terminal tab, run:
   ```bash
   php artisan serve
   ```
   Access the application at: **`http://127.0.0.1:8000`**

10. **(Optional) Run Queue Workers & WebSockets**:
    To test email processing and background notifications:
    ```bash
    php artisan queue:work
    ```
    To enable real-time bidding updates via Laravel Reverb:
    ```bash
    php artisan reverb:start
    ```

---

## 🧪 6. Testing

Run the PHPUnit suite to verify key features such as the `PriceSuggestionService` formulas and KYC gatekeepers:
```bash
php artisan test
```

---

## 🔑 7. Seeded Demo Accounts (Passward: `password123`)

The following accounts are seeded by default to allow grading and evaluation across all portal levels:

| Role | Email | Use Case |
|---|---|---|
| **Super Admin** | `superadmin@fibretrace.in` | Global system configurations, commission rates, and pricing indexes. |
| **Admin** | `amit.s@fibretrace.in` | KYC GSTIN approval queue, moderation reports, and unmasked PII audits. |
| **Seller (Ludhiana Factory)** | `admin@globaltextiles.com` | Create waste lots, use smart price pricing suggestions, and accept bids. |
| **Buyer (Panipat Recycler)** | `contact@panipatspinners.in` | Browse marketplace, join live bidding rooms, and simulate payment settlements. |
