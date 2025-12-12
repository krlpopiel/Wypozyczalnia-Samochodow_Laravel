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
    // Zmiana statusu
    Route::patch('/rentals/{rental}/status', [EmployeeController::class, 'updateStatus'])->name('rentals.status');
});