<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
})->name('root');

Route::get('/dashboard', function () {
    if(auth()->user()->type!=2){
        return redirect()->route('root');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::match(
        array('GET', 'POST'),
        'travel-info', 
        [
            \App\Http\Controllers\TravelController::class, 
            'store'
        ]
    )->name('travel-info.store');
    Route::post('/update-passed-route', 
        [
            App\Http\Controllers\TravelController::class, 
            'update_passed_route'
        ]
    )
    ->name('travel-info.update_passed_route');
    

});

Route::prefix('admin')->group(function () {
    Route::match(
        array('GET', 'POST'),
        'login', 
        [
            \App\Http\Controllers\AdminController::class, 
            'login'
        ]
    )->name('admin.login');
    Route::match(
        array('GET', 'POST'),
        'register', 
        [
            \App\Http\Controllers\AdminController::class, 
            'register'
        ]
    )->name('admin.register');
    Route::match(
        array('GET', 'POST'),
        'dashboard', 
        [
            \App\Http\Controllers\AdminController::class, 
            'dashboard'
        ]
    )->name('admin.dashboard');
    Route::match(
        array('GET', 'POST'),
        'notice', 
        [
            \App\Http\Controllers\AdminController::class, 
            'notice'
        ]
    )->name('admin.notice');
    Route::match(
        array('GET', 'POST'),
        'get-notices', 
        [
            \App\Http\Controllers\AdminController::class, 
            'get_notices'
        ]
    )->name('admin.get_notices');
    
    
    
});
Route::get('/running-vehicle-summary', 
    [
        App\Http\Controllers\TravelController::class, 
        'running_vehicle_summary'
    ]
)
->name('travel-info.running_vehicle_summary');


Route::post('/broadcast', [App\Http\Controllers\PusherController::class, 'broadcast'])
->name('pusher.broadcast');
Route::post('/receive', [App\Http\Controllers\PusherController::class, 'broadcast'])
->name('pusher.receive');

