<?php

use App\Livewire\Customer\FieldDirectory;
use App\Livewire\Customer\FieldDetail;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/login', function () {
    return view('pages::auth.login');
})->name('login');

Route::get('/register', function () {
    return view('pages::auth.register');
})->name('register');

Route::get('/fields', FieldDirectory::class)->name('fields.index');
Route::get('/fields/{field}', FieldDetail::class)->name('fields.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', \App\Livewire\Customer\Dashboard::class)->name('dashboard');
    Route::get('/checkout/{booking}', \App\Livewire\Customer\Checkout::class)->name('checkout');
    Route::get('/bookings', \App\Livewire\Customer\BookingHistory::class)->name('bookings.history');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('fields', \App\Livewire\Admin\Fields::class)->name('fields');
    Route::get('bookings', \App\Livewire\Admin\Bookings::class)->name('bookings');
});

require __DIR__.'/settings.php';
