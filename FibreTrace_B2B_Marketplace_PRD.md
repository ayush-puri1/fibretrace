# Product Requirement Document (PRD)

## Project Title: FibreTrace B2B Marketplace
**Document Version:** 1.0  
**Target Launch:** Q3 2026  
**Author:** Ayush Puri (Product Lead)  

---

## 1. Executive Summary & Objective

### 1.1 Objective
FibreTrace is a specialized B2B digital marketplace designed to bridge the data gap between textile manufacturers in Ludhiana and mechanical/shoddy yarn recyclers (primarily in Panipat). The platform optimizes pre-consumer textile waste monetization by enabling accurate fiber-composition profiling, real-time price discovery, and structured lot-bidding.

### 1.2 The Core Shift
Instead of attempting to eliminate traditional scrap aggregators, FibreTrace digitizes and upgrades the value chain. It converts an opaque, unstandardized cash-and-carry trade into an organized, quality-indexed digital commodity exchange.

```
[Traditional Flow]  Factory ──(Opaque Price)──> Middlemen/Aggregators ──(Unsorted Bales)──> Recycler
                                                                                
[FibreTrace Flow]   Factory ──(Sorted/Tagged)──> Verified Aggregator/Recycler ──(High-Yield Feedstock)──> High-Quality Yarn
```

---

## 2. Problem Statement & User Persona Analysis

### 2.1 The Core Problem
* **Information Asymmetry:** Ludhiana hosiery units generate 50–200 kg of clean, pre-consumer cutting scraps daily. Because they lack data on current recycler demand and exact fiber blends (e.g., 80/20 Cotton/Polyester vs. 100% Acrylic), they sell mixed waste at low baseline rates.
* **The Sorting Yield Loss:** Recyclers in Panipat experience a ~40% material loss during processing due to unexpected synthetic contamination in unsorted bales. If waste is accurately sorted and profiled at the source, it easily commands top-tier rates (~₹73/kg).
* **Regulatory Impending Doom:** With India’s upcoming Textile EPR rules (2026–2027) and the EU ban on destroying unsold apparel (July 2026), factories must digitally log and audit their waste streams to maintain compliance.

### 2.2 User Personas

| Attribute | Persona A: The Ludhiana Factory Owner / Manager | Persona B: The Panipat Recycler / Large Aggregator |
| :--- | :--- | :--- |
| **Profile** | Runs an SME hosiery unit producing sweaters, jackets, or t-shirts. | Operates a shoddy yarn spinning mill or a massive scrap aggregation facility. |
| **Core Need** | Clear factory floor space daily; maximize revenue from production scrap; comply with upcoming EPR tracking. | Source predictable, clean, high-cotton or single-fiber feedstock to reduce the 40% processing yield loss. |
| **Pain Point** | No idea what Panipat is paying this week; treats scrap as a low-value byproduct. | Receives contaminated loads; lacks direct data on which factories produce high-purity cotton vs. synthetics. |

---

## 3. Scope & Feature Specifications

### 3.1 Epic 1: Digital Waste Profiling & Lot Creation (Factory App)
Factories must be able to list a waste lot in under 60 seconds.

* **F.1.1 Smart Lot Creation:** * Field inputs: Waste Category (Cutting Scraps, Yarn Ends, Rejected Batches, Selvedge).
  * Fiber Composition Picker: Dropdown/slider for estimated percentages (e.g., 100% Cotton, 80/20 Cotton-Poly, 100% Acrylic, Polyester Blend).
  * Color Sorting Status: Binary toggle (Sorted by Color / Mixed Colors).
  * Weight Input: Estimated weight in kilograms (minimum threshold: 100 kg).
* **F.1.2 Visual Verification:** Mandatory multi-photo attachment (minimum 2 clear photos of the heap/bags) to verify cleanliness and sorting quality.
* **F.1.3 Digital Waste Ledger:** Historical dashboard showing total tonnage listed, average price realized per fiber type, and digital certificates showing clean disposal for future EPR compliance audits.

### 3.2 Epic 2: Real-Time Price Discovery & Market Indexing
Removes the guesswork by publishing data-driven commodity rates.

* **F.2.1 Panipat Live Index:** A weekly updated price chart showing baseline market prices for categorized textile waste (e.g., *"Sorted Clean Cotton Scraps: ₹70–₹75/kg"*).
* **F.2.2 Smart Pricing Suggestion Engine:** When a factory inputs a lot (e.g., 500 kg of 100% Sorted Cotton), the system utilizes recent platform transaction data to auto-suggest an optimal starting bid price.

### 3.3 Epic 3: Private Bidding & Matching Engine
* **F.3.1 Blind/Open Auction Rooms:** Recyclers and verified aggregators view active listings filtered by fiber profile, location, and volume. They can place counter-offers or direct bids per kg.
* **F.3.2 Bid Acceptance Logic:** Factories receive real-time push notifications of bids. Accepting a bid locks the contract and closes the auction, generating a secure digital trade token.

### 3.4 Epic 4: Logistics Routing & Settlement Workflows
* **F.4.1 Shared Logistics Module:** Integration with regional freight networks (e.g., Tata Ace / Pickup drivers operating the Ludhiana-Panipat corridor).
* **F.4.2 Digital Weight & Quality Match:** Upon physical collection, actual weight is verified against the digital listing. The platform allows a ±10% weight variance tolerance before triggering a renegotiation flow.

---

## 4. User Experience & Wireframe Concept

The interface must be clean, highly visual, and optimized for fast-paced factory floors.

### 4.1 Factory App Home & Lot Creation
```
+-----------------------------------------------------+
|  FibreTrace [Ludhiana]                [Notifications] |
+-----------------------------------------------------+
| LIVE MARKET INDEX (This Week)                       |
| - Sorted Cotton:  ₹72-75/kg  [▲ 2%]                 |
| - Polyester Blend:  ₹45-50/kg  [▼ 1%]               |
+-----------------------------------------------------+
|                                                     |
|  [ + LIST A NEW WASTE LOT ]                         |
|                                                     |
|  1. Waste Type: [ Cutting Scraps | ▼ ]              |
|  2. Primary Fiber: [ 100% Cotton  | ▼ ]              |
|  3. Sorting:    (X) Sorted by Color  ( ) Mixed      |
|  4. Est. Weight: [ 250 ] kg                         |
|  5. Upload Photos: [ [📷 Photo 1] [📷 Photo 2] ]    |
|                                                     |
|  Suggested Base Price: ₹73/kg                       |
|                                                     |
|  [ PUBLISH TO ECOSYSTEM ]                           |
+-----------------------------------------------------+
```

### 4.2 Recycler/Aggregator Dashboard
```
+-----------------------------------------------------+
|  FibreTrace Recycler Console            [Panipat]   |
+-----------------------------------------------------+
| ACTIVE LOTS NEAR YOU                                |
+-----------------------------------------------------+
| Lot #4029 - 500kg Sorted Cotton Scraps              |
| Location: Focal Point, Ludhiana                     |
| Current Highest Bid: ₹74.00/kg                      |
| [ PLACE BID PER KG: [  ] ]  [BID NOW]               |
+-----------------------------------------------------+
| Lot #4030 - 1,200kg Mixed Acrylic Ends               |
| Location: Bahadur Ke Road, Ludhiana                 |
| Current Highest Bid: ₹58.50/kg                      |
| [ PLACE BID PER KG: [  ] ]  [BID NOW]               |
+-----------------------------------------------------+
```

---

## 5. Non-Functional Requirements (NFRs)

### 5.1 Performance & Reliability
* **Latency:** Real-time bid updating via WebSockets; bid propagation time must be less than 500ms to avoid concurrency conflicts on high-demand lots.
* **Availability:** 99.9% uptime. The application must perform reliably under low-bandwidth conditions typical of inner-city industrial areas (Gill Road, Industrial Area B).

### 5.2 Security & Data Privacy
* **Verification Protocol:** All participating recyclers, aggregators, and factories must upload their GSTIN (GST Number) during registration to prevent fraudulent listings.
* **Anonymity Layer:** To preserve business networks, exact factory names and addresses remain hidden during the bidding phase. They are only revealed to the winning bidder once the lot is officially accepted.

### 5.3 Localization
* **Language Support:** The system interface must be fully toggleable between **English, Punjabi, and Hindi** to ensure smooth adoption by floor managers and weighing supervisors.

---

## 6. Monetization Strategy

FibreTrace uses a two-pronged monetization framework designed to scale cleanly:

* **Transaction Commission Fee:** A flat fee of **₹1.50 per kg** processed through the platform, automatically settled during the final invoice clearing between the buyer and the factory.
* **Premium SaaS Subscription Tier (FibreTrace Pro):** Priced at **₹1,999/month** for factories. Features include:
  * Automated digital compliance ledger exports for upcoming EPR audits.
  * Direct priority matching to top-rated Panipat buyers.
  * Monthly waste-generation analytics reports detailing potential yield optimization strategies.

---

## 7. Key Performance Indicators (KPIs) & Success Metrics

To monitor the platform's health and market penetration, the following metrics will be tracked continuously:

* **Liquidity Metric (Match Rate):** Percentage of listed waste lots successfully matched with a buyer within 48 hours of posting (Target: >85%).
* **Volume Metric (Gross Tonnage Transacted):** Total metric tonnes of profiled textile waste routed through the platform per month.
* **Value Metric (Premium Recovery Rate):** The average price-per-kg increase realized by factories using the sorting verification tools compared to baseline unprofiled mixed scrap values.
* **Retention Metric:** Number of recurring monthly listings per factory onboarded.