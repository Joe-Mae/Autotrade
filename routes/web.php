<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarqueeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/catalogue', [VehicleController::class, 'catalogue'])->name('catalogue');
Route::redirect('/vente', '/catalogue');
Route::get('/vehicule/{vehicle}', [VehicleController::class, 'show'])->name('vehicule.show');

Route::get('/demande-achat/{vehicle}', [PurchaseController::class, 'create'])->name('demande.achat');
Route::post('/demande-achat/{vehicle}', [PurchaseController::class, 'store'])->name('demande.achat.store');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Authentification admin
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// Zone admin protégée
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');

    Route::get('/parametres', [SettingsController::class, 'edit'])->name('admin.settings');
    Route::post('/parametres', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/parametres/test-mail', [SettingsController::class, 'sendTestMail'])->name('admin.settings.test-mail');

    Route::post('/marquee', [MarqueeController::class, 'store'])->name('admin.marquee.store');
    Route::delete('/marquee/{marquee}', [MarqueeController::class, 'destroy'])->name('admin.marquee.destroy');

    Route::get('/vehicules/creer', [AdminController::class, 'createVehicle'])->name('admin.vehicles.create');
    Route::get('/vehicules/export', [AdminController::class, 'exportVehicles'])->name('admin.vehicles.export');
    Route::post('/vehicules', [AdminController::class, 'storeVehicle'])->name('admin.vehicles.store');
    Route::get('/vehicules/{vehicle}/modifier', [AdminController::class, 'editVehicle'])->name('admin.vehicles.edit');
    Route::put('/vehicules/{vehicle}', [AdminController::class, 'updateVehicle'])->name('admin.vehicles.update');
    Route::delete('/vehicules/bulk', [AdminController::class, 'bulkDestroy'])->name('admin.vehicles.bulk');
    Route::delete('/vehicules/{vehicle}', [AdminController::class, 'destroyVehicle'])->name('admin.vehicles.destroy');
    Route::post('/vehicules/{vehicle}/disponibilite', [AdminController::class, 'toggleDisponible'])->name('admin.vehicles.toggle');

    Route::patch('/achats/{purchase}/statut', [AdminController::class, 'updatePurchaseStatus'])->name('admin.purchases.status');
    Route::post('/messages/{message}/lu', [AdminController::class, 'markMessageRead'])->name('admin.messages.read');

    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
});
