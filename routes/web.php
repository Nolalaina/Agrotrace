<?php

use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\IntrantController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\ParcelleController;
use App\Http\Controllers\RecolteController;
use App\Http\Controllers\TracabilitePublicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Module personnel : traçabilité publique (QR code), sans authentification
Route::get('/tracabilite/{code}', [TracabilitePublicController::class, 'show'])->name('tracabilite.public');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('parcelles', ParcelleController::class)->except(['show']);
    Route::resource('campagnes', CampagneController::class)->except(['show']);
    Route::resource('activites', ActiviteController::class)->only(['index', 'create', 'store']);
    Route::resource('recoltes', RecolteController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('/intrants', [IntrantController::class, 'index'])->name('intrants.index');
    Route::post('/intrants', [IntrantController::class, 'store'])->name('intrants.store');
    Route::post('/intrants/{intrant}/mouvements', [IntrantController::class, 'mouvement'])->name('intrants.mouvement');

    Route::resource('lots', LotController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/lots/{lot}/cloturer', [LotController::class, 'cloturer'])->name('lots.cloturer');
    Route::post('/lots/{lot}/vendre', [LotController::class, 'vendre'])->name('lots.vendre');
    Route::post('/lots/{lot}/fiche', [LotController::class, 'genererFiche'])->name('lots.fiche.generer');
    Route::get('/lots/{lot}/fiche', [LotController::class, 'telechargerFiche'])->name('lots.fiche.telecharger');

    Route::get('/finances', [FinanceController::class, 'index'])->middleware('role:admin,responsable')->name('finances.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
