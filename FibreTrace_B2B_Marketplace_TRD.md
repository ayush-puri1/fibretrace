# FibreTrace B2B Marketplace - Technical Requirements Document (TRD)

**Document Version:** 1.0 (MVP Phase)  
**Primary Technology:** Laravel  

---

## 1. Project Overview & Scope
The FibreTrace MVP is a web-based B2B digital marketplace designed to connect textile manufacturers (sellers) with recyclers (buyers). The MVP focuses on a web-only experience to validate the core market mechanics: lot creation, real-time bidding, manual verification, and secure (placeholder) payments.

## 2. Technology Stack
Given the MVP scope and web-only requirement, the architecture optimizes for development speed, real-time capabilities, and monolithic simplicity.

* **Backend & Core Framework:** Laravel 11 (PHP)
* **Frontend Architecture:** Laravel Blade + Livewire + Tailwind CSS (This eliminates the need to manage a separate frontend SPA/API layer while providing a highly reactive, app-like feel).
* **Database:** MySQL or PostgreSQL.
* **Real-time WebSockets:** Laravel Reverb (Provides native, first-party WebSocket support for real-time bid updates).
* **File Storage:** Local Server Storage (Laravel's `public` disk for storing lot images).
* **Localization:** Single Language (English).

## 3. Platform Portals
The application will be divided into three distinct web portals:

1. **Marketplace Portal (Buy & Sell):** A unified frontend where authenticated business entities can switch between selling (creating waste lots) and buying (bidding on active lots).
2. **Admin Portal:** Used by platform staff to moderate listings, manually verify user GSTINs, monitor transactions, and oversee platform health.
3. **Super-Admin Portal:** Used by system owners to manage Admin accounts, view global platform analytics, and configure core system settings.

## 4. Core Workflows & Business Logic

### 4.1 Registration & Manual Verification
* Businesses register and must provide their GSTIN.
* Upon registration, the account is placed in a **Pending Verification** state.
* Admins or Super-Admins must manually verify the GSTIN through the Admin Portal and approve the account before the user can list lots or place bids.

### 4.2 Lot Creation & Image Storage
* Sellers list waste lots with required details (fiber type, weight, base price).
* Sellers upload photos of the waste lot. These images are processed and stored directly on the local server.
* The system publishes the lot to the live bidding floor.

### 4.3 Real-Time Bidding Engine
* The platform utilizes WebSockets to display the current highest bid in real-time.
* **Fallback Logic:** The system records the complete bid history. If the current highest bidder cancels their bid, the system automatically falls back to the **2nd highest bid**, making it the new active highest bid.
* Sellers can view incoming bids and choose to accept the active highest bid to close the auction.

### 4.4 Payments & Strict Data Privacy
* **Payments:** For the MVP, payment gateway integration is a simulated placeholder API. It simulates the transaction flow and the deduction of the platform commission.
* **Data Privacy (PII Masking):** The platform acts as a strict blind proxy. Buyers and Sellers will **never** see each other's GSTIN, phone number, or exact address, even after a payment is successfully simulated.
* Only Admins and Super-Admins have permission to view the PII of transacting parties.

### 4.5 Logistics (Placeholder)
* Logistics tracking is simulated via static UI statuses (e.g., "Ready for Pickup", "In Transit", "Delivered"). There will be no integration with external freight APIs for the MVP.

## 5. High-Level Database Schema (Core Models)

* **`User`**: `id`, `name`, `email`, `phone`, `gstin`, `role` (user, admin, super-admin), `status` (pending, verified).
* **`Lot`**: `id`, `seller_id`, `category`, `fiber_composition`, `weight`, `base_price`, `status` (active, sold, cancelled).
* **`Media`**: (Polymorphic table for local image paths attached to Lots).
* **`Bid`**: `id`, `lot_id`, `buyer_id`, `amount`, `status` (active, cancelled).
* **`Transaction`**: `id`, `lot_id`, `buyer_id`, `seller_id`, `amount`, `commission_fee`, `payment_status` (simulated).
