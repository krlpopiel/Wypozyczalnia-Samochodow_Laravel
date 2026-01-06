<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\EmployeeController;

// Strona główna
Route::get('/', function () {
    return redirect()->route('cars.index');
})->name('home');

// Publiczne trasy aut
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

// Autoryzacja
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Trasy dla Zalogowanych Klientów
Route::middleware(['auth'])->group(function () {
    // Rezerwacja
    Route::post('/rentals/{car}', [RentalController::class, 'store'])->name('rentals.store');
    // Moje rezerwacje
    Route::get('/my-rentals', [RentalController::class, 'index'])->name('rentals.index');
});

// Trasy dla Pracowników i Adminów
Route::middleware(['auth', 'role:admin,employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeController::class, 'index'])->name('dashboard');
    Route::patch('/rentals/{rental}/status', [EmployeeController::class, 'updateStatus'])->name('rentals.status');

    // Panel Zarządzania (Flota i Oddziały)
    Route::get('/management', [EmployeeController::class, 'management'])->name('management');
    
    // CRUD Oddziały
    Route::post('/branches', [EmployeeController::class, 'storeBranch'])->name('branches.store');
    Route::patch('/branches/{branch}', [EmployeeController::class, 'updateBranch'])->name('branches.update');
    Route::delete('/branches/{branch}', [EmployeeController::class, 'destroyBranch'])->name('branches.destroy');

    // CRUD Marki 
    Route::post('/brands', [EmployeeController::class, 'storeBrand'])->name('brands.store');
    Route::delete('/brands/{brand}', [EmployeeController::class, 'destroyBrand'])->name('brands.destroy');

    // CRUD Wyposażenie
    Route::post('/features', [EmployeeController::class, 'storeFeature'])->name('features.store');
    Route::delete('/features/{feature}', [EmployeeController::class, 'destroyFeature'])->name('features.destroy');
});

// Trasy CRUD Samochodów (dostępne dla pracowników - podpięte pod CarController)
Route::middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/cars/create/new', [CarController::class, 'create'])->name('cars.create'); // Zmieniona ścieżka by nie kolidowała z show
    Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
    Route::get('/cars/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
    Route::put('/cars/{car}', [CarController::class, 'update'])->name('cars.update');
    Route::delete('/cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
});