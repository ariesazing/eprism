<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Research\ResearchController;
use App\Http\Controllers\Research\ResearchDocumentController;
use App\Http\Controllers\Research\ResearchProponentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'approved', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/account/pending-approval', function () {
        if (request()->user()?->status?->status_name === 'Active') {
            return redirect()->route('dashboard');
        }

        return view('auth.pending-approval');
    })->name('account.pending');
});

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'approved', 'verified'])->group(function () {
    Route::resource('researches', ResearchController::class)->except(['edit']);
    Route::post('/researches/{research}/submit', [ResearchController::class, 'submit'])
        ->name('researches.submit');

    Route::post('/researches/{research}/documents', [ResearchDocumentController::class, 'store'])
        ->name('researches.documents.store');
    Route::get('/researches/{research}/documents/{document}/download', [ResearchDocumentController::class, 'download'])
        ->name('researches.documents.download');
    Route::delete('/researches/{research}/documents/{document}', [ResearchDocumentController::class, 'destroy'])
        ->name('researches.documents.destroy');

    Route::post('/researches/{research}/proponents', [ResearchProponentController::class, 'store'])
        ->name('researches.proponents.store');
    Route::get('/researches/{research}/proponents/{proponent}/photo', [ResearchProponentController::class, 'photo'])
        ->name('researches.proponents.photo');
    Route::put('/researches/{research}/proponents/{proponent}', [ResearchProponentController::class, 'update'])
        ->name('researches.proponents.update');
    Route::delete('/researches/{research}/proponents/{proponent}', [ResearchProponentController::class, 'destroy'])
        ->name('researches.proponents.destroy');
});

Route::middleware(['auth', 'approved', 'verified', 'admin'])->prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/pending', [UserManagementController::class, 'pending'])->name('pending');
    Route::patch('/{user}/approve', [UserManagementController::class, 'approve'])->name('approve');
    Route::patch('/{user}/reject', [UserManagementController::class, 'reject'])->name('reject');

    Route::get('/', [UserManagementController::class, 'index'])->name('index');
    Route::get('/create', [UserManagementController::class, 'create'])->name('create');
    Route::post('/', [UserManagementController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
