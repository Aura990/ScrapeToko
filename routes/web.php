<?php

use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // USER MANAGEMENT
    Route::resource('users', UserManagementController::class)->except(['show']);
    Route::get('/users/statuses', [UserManagementController::class, 'statuses'])->name('users.statuses');

    // SHOP MANAGEMENT
    Route::resource('shops', ShopController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/comparison', [ComparisonController::class, 'index'])->name('comparison.index');
    Route::post('/comparison/compare', [ComparisonController::class, 'compare'])->name('comparison.compare');
    Route::get('/comparison/history', [ComparisonController::class, 'history'])->name('comparison.history');
    Route::post('/comparison/export-pdf', [ComparisonController::class, 'exportPdf'])->name('comparison.export-pdf');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

require __DIR__.'/auth.php';

use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrations', function () {
    if (request('key') === 'supersecretkey') {
        try {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            return '<pre>' . Artisan::output() . '</pre>';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    return 'Unauthorized';
});
