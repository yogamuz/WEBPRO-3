<?php

use App\Models\Layanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\DaftarAntrianController;
use App\Http\Controllers\Dashboard\DashboardAntrianController;
use App\Http\Controllers\Dashboard\DashboardAntrianMasukController;
use App\Http\Controllers\Dashboard\DashboardLayananController;
use App\Http\Controllers\DisplayPanggilanController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Admin\UserController; // TAMBAHAN: Import UserController
use App\Http\Controllers\Dashboard\DashboardPesanController; // TAMBAHAN: Import PesanController
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route Halaman Utama/Home Depan
Route::get('/', function () {
    return view('index');
});

// Route Halaman /home akan otomatis redirect ke view index
Route::get('/home', function () {
    return view('index');
});

// Route Halaman view contact
Route::get('/contact', function () {
    return view('contact');
});

// Route untuk menyimpan pesan contact
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Route untuk menampilkan chatbot
Route::post('/chatbot/send-message', [ChatbotController::class, 'processMessage'])->name('chatbot.send');

// Route untuk mendapatkan data antrian real-time
Route::get('/chatbot/queue-data', [ChatbotController::class, 'getQueueData'])->name('chatbot.queue');

// Optional: Route untuk testing chatbot
Route::get('/chatbot/test', function () {
    return response()->json([
        'status' => 'Chatbot service is running',
        'timestamp' => now()
    ]);
});

Route::get('/display-panggilan/get-nomor-antrian', [DisplayPanggilanController::class, 'getNomorAntrianDipanggil']);
Route::resource('/display-panggilan', DisplayPanggilanController::class);

// Route halaman antrian untuk masyarakat/pengambil antrian
Route::get('/daftar-antrian', [DaftarAntrianController::class, 'index']);
Route::get('/daftar-antrian/{antrian:slug}', [DaftarAntrianController::class, 'show']);
Route::get('/antrian', [AntrianController::class, 'index']);

Auth::routes();

// INTEGRASI: Route untuk admin - Menambahkan User Management
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')
        ->middleware('can:admin');
    // Dashboard Admin
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Manajemen Antrian (existing)
    Route::get('/dashboard/antrian/checkSlug', [DashboardAntrianController::class, 'checkSlug']);
    Route::get('/api/dashboard/antrian', [DashboardAntrianController::class, 'getAutoCompleteData']);
    Route::resource('/dashboard/antrian', DashboardAntrianController::class);
    Route::resource('/dashboard/layanan', DashboardLayananController::class);
    Route::get('/dashboard/antrian-masuk/{antrian:slug}', [DashboardAntrianMasukController::class, 'index']);
    Route::DELETE('/dashboard/antrian-masuk/{antrian:id}', [DashboardAntrianMasukController::class, 'destroy'])->name('antrian.destroy');
    Route::PUT('/dashboard/antrian-masuk/{antrian:id}/skip', [DashboardAntrianMasukController::class, 'skip'])->name('antrian.skip');
    Route::DELETE('/dashboard/antrian-masuk/{slug}/reset', [DashboardAntrianMasukController::class, 'reset']);

    // INTEGRASI BARU: User Management untuk Admin
    Route::prefix('dashboard/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
    });

    // TAMBAHAN BARU: Route untuk halaman pesan - DIPERBAIKI
    Route::prefix('dashboard/pesan')->name('dashboard.pesan.')->group(function () {
        Route::get('/', [DashboardPesanController::class, 'index'])->name('index');
        Route::get('/create', [DashboardPesanController::class, 'create'])->name('create');
        Route::post('/', [DashboardPesanController::class, 'store'])->name('store');
        Route::get('/{id}', [DashboardPesanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DashboardPesanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DashboardPesanController::class, 'update'])->name('update');
        Route::delete('/{id}', [DashboardPesanController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reply', [DashboardPesanController::class, 'reply'])->name('reply');
        Route::put('/{id}/mark-read', [DashboardPesanController::class, 'markAsRead'])->name('mark-read');
    });
});

// INTEGRASI: Route untuk petugas - Akses terbatas untuk operasional
Route::middleware(['auth', 'role:admin'])->group(function () {
    // admin hanya bisa akses dashboard antrian masuk dan operasional
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // Buat view khusus admin
    })->name('admin.dashboard');

    Route::get('/admin/antrian-masuk/{antrian:slug}', [DashboardAntrianMasukController::class, 'index'])->name('admin.antrian.index');
    Route::PUT('/admin/antrian-masuk/{antrian:id}/skip', [DashboardAntrianMasukController::class, 'skip'])->name('admin.antrian.skip');
});

// Ubah bagian route untuk masyarakat menjadi:
Route::middleware(['auth'])->group(function () {
    // Route untuk form create antrian
    Route::get('/antrian/{antrian}', [AntrianController::class, 'create'])->name('antrian.create');

    // Route store antrian
    Route::post('/antrian', [AntrianController::class, 'store'])->name('store.antrian');

    // Route detail antrian user
    // Di web.php
    // Menampilkan halaman detail antrian milik user saat ini
    Route::get('/antrian/detail', [AntrianController::class, 'detailUser'])
        ->name('antrian.detail')
        ->middleware('auth');

    // Route untuk operasi CRUD antrian user
    Route::delete('/antrian/detail/{id}', [AntrianController::class, 'destroy'])->name('antrian.user.destroy');
    Route::get('/antrian/kode-antrian/{id}', [AntrianController::class, 'cetakKodeAntrian'])->name('antrian.cetak');
    Route::get('/antrian/{id}/pdf', [AntrianController::class, 'generatePDF'])->name('antrian.pdf');
    Route::get('/antrian/{id}/edit', [AntrianController::class, 'edit'])->name('antrian.edit');
    Route::put('/antrian/{id}/update', [AntrianController::class, 'update'])->name('antrian.update');
});