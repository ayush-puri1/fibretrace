# 🏛️ FibreTrace

FibreTrace is a specialized B2B digital commodity exchange designed to optimize pre-consumer textile waste monetization and traceability. The platform bridges the industrial data gap between **garment manufacturers (sellers in Ludhiana)** and **yarn recyclers/spinners (buyers in Panipat)**.

Built on **Laravel**, FibreTrace replaces opaque traditional scrap aggregation with an organized, quality-indexed digital marketplace featuring real-time price discovery, blind bidding, and secure settlement workflows.

---

## 📌 Features & Business Workflows

- **Smart Lot Creation**: Textile factories list pre-consumer cutting scraps, yarn ends, and selvedges with composition profiling, weight estimations, and visual validation.
- **Dynamic Pricing Engine**: Algorithmic suggested base pricing based on verified market indices combined with composition purity and sorting premiums.
- **Blind Bidding Floor**: Verified recyclers and aggregators participate in competitive, anonymous bidding rooms to prevent collusion and ensure fair market pricing.
- **Anonymity & Privacy Layer**: Automatic masking of phone numbers, GSTINs, and addresses on the marketplace floor to protect transaction integrity.
- **Escrow-Style Settlement**: Complete workflow from bid acceptance to simulated payment clearing and logistics milestones tracking.
- **Management Portals**: Admin and Super-Admin roles for business entity verification, catalog moderation, and market price configuration.

---

## 🛠️ Technology Stack

- **Backend**: Laravel
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Database**: SQLite (Development) / MySQL (Production)
- **Real-Time Layer**: Laravel Reverb (WebSockets)
- **Queues**: Database Queue Driver for background processing

---

## 🚀 Getting Started & Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite or MySQL 8.0

### Local Development Setup

1. **Clone the repository and enter the directory**:
   ```bash
   cd fiberTrace
   ```

2. **Install project dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment Settings**:
   Create your local `.env` configuration:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize Database**:
   Run database migrations and seed system indexes/constants:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Generate Storage Symlink**:
   ```bash
   php artisan storage:link
   ```

6. **Start Frontend Compilers**:
   ```bash
   npm run dev
   ```

7. **Start Application Server**:
   ```bash
   php artisan serve
   ```
   The application will be accessible locally at `http://127.0.0.1:8000`.

8. **(Optional) Run WebSocket & Queue Workers**:
   ```bash
   php artisan reverb:start
   php artisan queue:work
   ```
