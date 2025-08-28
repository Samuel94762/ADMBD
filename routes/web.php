<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\OtpLoginController; // 👈 Importamos nuestro controlador
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 🔐 Rutas para Login con OTP
// =============================
Route::post('/login-otp', [OtpLoginController::class, 'login'])->name('otp.login'); // procesa email+password
Route::get('/otp', [OtpLoginController::class, 'showOtpForm'])->name('otp.form');   // muestra formulario OTP
Route::post('/otp', [OtpLoginController::class, 'validateOtp'])->name('otp.validate'); // valida OTP

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
