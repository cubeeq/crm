<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;

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

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::prefix('task')->group(function () {
    Route::match(['get', 'post'], '/', [TaskController::class, 'index'])->name('task.index');
    Route::get('/create', [TaskController::class, 'create'])->name('task.create');
    Route::post('/store', [TaskController::class, 'store'])->name('task.store');
    Route::get('/edit/{id}', [TaskController::class, 'edit'])->name('task.edit');
    Route::post('/update/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::get('/delete/{id}', [TaskController::class, 'destroy'])->name('task.delete');
    Route::get('/restore/{id}', [TaskController::class, 'restore'])->name('task.restore');
    Route::post('/share', [TaskController::class, 'share'])->name('task.share');
    // Javascript
    Route::post('/changeStatus', [TaskController::class, 'changeStatus']);
});


Route::get('/send', [TaskController::class, 'send'])->name('task.send');