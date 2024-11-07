<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return redirect()->route('inventory.index');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');         
    Route::get('/create', [InventoryController::class, 'create'])->name('create'); 
    Route::post('/', [InventoryController::class, 'store'])->name('store');        
    Route::get('/{id}', [InventoryController::class, 'show'])->name('show');       
    Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');  
    Route::put('/{id}', [InventoryController::class, 'update'])->name('update');   
    Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy'); 
});

require __DIR__.'/auth.php';
