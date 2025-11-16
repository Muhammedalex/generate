<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\FormResponseController;
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

// Protected Routes - Admin Only
Route::middleware(['auth', 'admin'])->group(function () {
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

    // Form Management Routes
    Route::resource('forms', FormController::class);
    Route::post('/forms/{form}/duplicate', [FormController::class, 'duplicate'])->name('forms.duplicate');
    Route::post('/forms/{form}/publish', [FormController::class, 'publish'])->name('forms.publish');
    Route::post('/forms/{form}/unpublish', [FormController::class, 'unpublish'])->name('forms.unpublish');

    // Form Builder Routes
    Route::prefix('forms/{form}')->name('forms.')->group(function () {
        Route::get('/builder', [FormBuilderController::class, 'builder'])->name('builder');
        
        // Sections
        Route::post('/sections', [FormBuilderController::class, 'addSection'])->name('sections.store');
        Route::put('/sections/{section}', [FormBuilderController::class, 'updateSection'])->name('sections.update');
        Route::delete('/sections/{section}', [FormBuilderController::class, 'deleteSection'])->name('sections.destroy');
        Route::post('/sections/reorder', [FormBuilderController::class, 'reorderSections'])->name('sections.reorder');
        
        // Questions
        Route::post('/questions', [FormBuilderController::class, 'addQuestion'])->name('questions.store');
        Route::put('/questions/{question}', [FormBuilderController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{question}', [FormBuilderController::class, 'deleteQuestion'])->name('questions.destroy');
        Route::post('/questions/reorder', [FormBuilderController::class, 'reorderQuestions'])->name('questions.reorder');
        Route::post('/questions/{question}/duplicate', [FormBuilderController::class, 'duplicateQuestion'])->name('questions.duplicate');
        
        // Responses
        Route::get('/responses', [FormResponseController::class, 'index'])->name('responses');
        Route::get('/responses/{response}', [FormResponseController::class, 'show'])->name('responses.show');
        Route::delete('/responses/{response}', [FormResponseController::class, 'destroy'])->name('responses.destroy');
        Route::get('/responses/export/csv', [FormResponseController::class, 'export'])->name('responses.export');
    });
});

// Public Form Routes
Route::get('/forms/{form:slug}', [FormController::class, 'show'])->name('forms.show');
Route::post('/forms/{form:slug}/submit', [FormResponseController::class, 'submit'])->name('forms.submit');
