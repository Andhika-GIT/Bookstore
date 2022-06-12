<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Payment\TripayCallbackController;
use App\Http\Controllers\Payment\TripayController;
use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// TRIPAY - LARAVEL PERT 5 -> BUAT MODEL DAN MIGRASI TRANSAKSI, UNTUK MEMBUAT DAFTAR TRANSAKSI

// TRIPAY - LARAVEL PERT 6 -> BUAT CONTROLLER TripayCallbackController untuk menghandel callback tripay (mengatur response apakah user sudah membayar atau belom), lalu gunakan website 'NGROK' untuk membuat url public ( karena callback tripay hanya menerima url public )

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('books/{type}', [BookController::class, 'index'])->name('books');
Route::get('book/{book}', [BookController::class, 'show'])->name('book.show');

Route::group(['middleware' => 'auth'], function () {
    // route metode pembayaran
    Route::get('book/{book}/checkout', [BookController::class, 'checkout'])->name('book.checkout');

    // route untuk request transaksi dengan method get
    Route::post('transaction', [TransactionController::class, 'store'])->name('transaction.store');
    
    Route::get('transaction/{reference}', [TransactionController::class, 'show'])->name('transaction.show');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// mengatur route callback untuk response apakah pengguna sudah membayar
// request callback tripay harus dalam bentuk method post
// jalankan program ngrok terlebih dahulu
// lalu copy public url yang sudah di olah ngrok pada cmd, kemudian test url pada tripay->api developer->callback->copy link url untuk simulator console callback
// cth : http://63bc-180-251-177-99.ngrok.io/callback -> tambahkan /callback dibelakang url
// karena tidak ada form pada url untuk menghandel method 'post', maka akan muncul page expired karena tidak ada @csrf(wajib untuk method post), oleh karena itu pergi ke verifyCsrfToken.php untuk membuat pengecualian csrf, lalu copy url callback cth diatas pada $except
// pada simulator console, isi callbackurl, reference, total amount, dan status
Route::post('callback', [TripayCallbackController::class, 'handle']);

require __DIR__.'/auth.php';
