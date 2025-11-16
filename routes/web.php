<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;

// Language Switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Home
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Company Profile
Route::get('/company/{company:slug}', [CompanyController::class, 'show'])->name('company.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Company Management Routes
    Route::resource('companies', CompanyController::class)->except(['show']);
    
    // Company Details Route (for authenticated users with full details)
    Route::get('/companies/{company}', [CompanyController::class, 'details'])->name('companies.details');
    
    // QR Code Routes
    Route::prefix('companies/{company}')->name('companies.')->group(function () {
        Route::get('/qrcode', [QrCodeController::class, 'show'])->name('qrcode.show');
        Route::get('/qrcode/download', [QrCodeController::class, 'download'])->name('qrcode.download');
        Route::get('/qrcode/base64', [QrCodeController::class, 'base64'])->name('qrcode.base64');
    });
});
