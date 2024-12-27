<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FornecedorController; /* CONTROLLER FORNECEDOR */

Route::get('/fornecedor', [FornecedorController::class, 'index'])->name('fornecedor.index');
Route::get('/fornecedor/{id}', [FornecedorController::class, 'show'])->name('fornecedor.show');
Route::post('/fornecedor', [FornecedorController::class, 'store'])->name('fornecedor.store');
Route::put('/fornecedor/{id}', [FornecedorController::class, 'update'])->name('fornecedor.update');
Route::delete('/fornecedor/{id}', [FornecedorController::class, 'destroy'])->name('fornecedor.destroy');
