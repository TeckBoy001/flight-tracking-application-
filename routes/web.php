<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FlightController as AdminFlightController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// ---- Public ----
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/destinations', [PageController::class, 'destinations'])->name('destinations');
Route::get('/travel-information', [PageController::class, 'travelInformation'])->name('travel-information');
Route::get('/flight-safety', [PageController::class, 'safety'])->name('flight-safety');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::get('/flights', [FlightController::class, 'search'])->name('flights.search');
Route::get('/flights/{flight}', [FlightController::class, 'show'])->name('flights.show');

Route::get('/track', [TrackController::class, 'index'])->name('track.index');
Route::post('/track', [TrackController::class, 'lookup'])->name('track.lookup');

// ---- Guest-only auth routes ----
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ---- Authenticated user routes ----
Route::middleware('auth')->group(function () {
    Route::post('/flights/{flight}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// ---- Admin panel ----
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/flights', [AdminFlightController::class, 'index'])->name('flights.index');
    Route::get('/flights/create', [AdminFlightController::class, 'create'])->name('flights.create');
    Route::post('/flights', [AdminFlightController::class, 'store'])->name('flights.store');
    Route::get('/flights/{flight}/edit', [AdminFlightController::class, 'edit'])->name('flights.edit');
    Route::put('/flights/{flight}', [AdminFlightController::class, 'update'])->name('flights.update');
    Route::delete('/flights/{flight}', [AdminFlightController::class, 'destroy'])->name('flights.destroy');
    Route::get('/flights/{flight}/bookings', [AdminFlightController::class, 'bookings'])->name('flights.bookings');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}/edit', [AdminBookingController::class, 'edit'])->name('bookings.edit');
    Route::patch('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');
});

// ---- Temporary Automated Admin Setup Hook ----
Route::get('/setup-admin', function () {
    $email = 'admin@example.com';
    $user = User::where('email', $email)->first();

    if (!$user) {
        $user = User::create([
            'name' => 'Admin User',
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);
    }

    // Forces assignment of the admin flag
    $user->update(['is_admin' => true]);

    return "Admin account is ready! Email: " . $user->email . " | Password: password123";
});
