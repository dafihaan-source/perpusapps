<?php 
 
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\HomeController; 
 
Route::get('/', [HomeController::class, 'index']);
Route::get('/tentang', [HomeController::class, 'tentang']); 
Route::get('/kontak', [HomeController::class, 'kontak']); 