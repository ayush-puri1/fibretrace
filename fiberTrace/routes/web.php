<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\{
    PendingController, DashboardController,
    AuctionController, WalletController, LotController, BidController,
    SettlementController, DispatchController, AdminDashboardController,
    VerificationController, ModerationController, AuditController,
    SuperAdminController, MarketPriceController, SystemSettingController
};

use App\Http\Controllers\PageController;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', [PageController::class, 'welcome'])->name('welcome');
Route::get('/market', [MarketPriceController::class, 'publicIndex'])->name('market');

// ─── Guest-Only Auth Routes ───────────────────────────────────────────────────
// Breeze's RegisteredUserController & AuthenticatedSessionController already exist.
// We'll extend RegisteredUserController to capture our custom fields.
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// ─── Authenticated (any verification status) ──────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/pending', [PendingController::class, 'show'])->name('pending');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ─── Verified Client Portal (seller + buyer) ─────────────────────────────────
Route::middleware(['auth', 'verified.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');

    // Seller routes
    Route::get('/seller/create', [LotController::class, 'create'])->name('seller.create');
    Route::get('/seller/price-suggest', [LotController::class, 'suggestPrice'])->name('seller.price.suggest'); // AJAX
    Route::post('/seller/lots', [LotController::class, 'store'])->name('seller.lots.store');
    Route::get('/seller/ledger', [LotController::class, 'ledger'])->name('seller.ledger');
    Route::get('/seller/lot/{lot}', [LotController::class, 'show'])->name('seller.lot.show');
    Route::post('/seller/lot/{lot}/accept', [LotController::class, 'acceptBid'])->name('seller.lot.accept');
    Route::post('/seller/lot/{lot}/cancel', [LotController::class, 'cancel'])->name('seller.lot.cancel');

    // Buyer routes
    Route::get('/buyer/room/{lot}', [BidController::class, 'room'])->name('buyer.room');
    Route::post('/buyer/room/{lot}/bid', [BidController::class, 'place'])->name('buyer.bid.place');
    Route::get('/buyer/bids', [BidController::class, 'ledger'])->name('buyer.bids');
    Route::post('/buyer/bid/{bid}/cancel', [BidController::class, 'cancelBid'])->name('buyer.bid.cancel');

    // Settlement routes
    Route::get('/settlement/{transaction}', [SettlementController::class, 'show'])->name('settlement.show');
    Route::post('/settlement/{transaction}/pay', [SettlementController::class, 'simulatePayment'])->name('settlement.pay');
    Route::get('/dispatch/{transaction}', [DispatchController::class, 'show'])->name('dispatch.show');
    Route::post('/dispatch/{transaction}/update', [DispatchController::class, 'updateStatus'])->name('dispatch.update');
});

// ─── Admin Portal ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications');
    Route::post('/verifications/{user}/approve', [VerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{user}/reject', [VerificationController::class, 'reject'])->name('verifications.reject');
    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation');
    Route::post('/moderation/{lot}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
    Route::post('/moderation/{lot}/suspend', [ModerationController::class, 'suspend'])->name('moderation.suspend');
    Route::post('/moderation/{lot}/restore', [ModerationController::class, 'restore'])->name('moderation.restore');
    Route::get('/audit', [AuditController::class, 'index'])->name('audit');
});

// ─── Super-Admin Portal ───────────────────────────────────────────────────────
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/admins', [SuperAdminController::class, 'admins'])->name('admins');
    Route::post('/admins', [SuperAdminController::class, 'createAdmin'])->name('admins.create');
    Route::post('/admins/{user}/toggle', [SuperAdminController::class, 'toggleAdmin'])->name('admins.toggle');
    Route::get('/market-index', [MarketPriceController::class, 'manage'])->name('market-index');
    Route::post('/market-index', [MarketPriceController::class, 'update'])->name('market-index.update');
    Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
});

// ─── Breeze password reset routes (forgot-password / reset-password) ──────────
require __DIR__.'/auth.php';
