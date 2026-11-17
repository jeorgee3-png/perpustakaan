<?php

use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoanController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/my-loans', [LoanController::class, 'myLoans'])->name('loans.my');
    Route::post('/books/{book}/borrow', [LoanController::class, 'borrow'])->name('books.borrow');
    Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('admin')->prefix('kelola')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/buku', [AdminBookController::class, 'index'])->name('books.index');
        Route::get('/buku/tambah', [AdminBookController::class, 'create'])->name('books.create');
        Route::post('/buku', [AdminBookController::class, 'store'])->name('books.store');
        Route::get('/buku/{book}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
        Route::put('/buku/{book}', [AdminBookController::class, 'update'])->name('books.update');
        Route::delete('/buku/{book}', [AdminBookController::class, 'destroy'])->name('books.destroy');

        Route::get('/peminjaman', [AdminLoanController::class, 'index'])->name('loans.index');
        Route::get('/peminjaman/tambah', [AdminLoanController::class, 'create'])->name('loans.create');
        Route::post('/peminjaman', [AdminLoanController::class, 'store'])->name('loans.store');
        Route::post('/peminjaman/{loan}/kembalikan', [AdminLoanController::class, 'returnBook'])->name('loans.return');

        Route::get('/anggota', [AdminUserController::class, 'index'])->name('users.index');
    });
});
