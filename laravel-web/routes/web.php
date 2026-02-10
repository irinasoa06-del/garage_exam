<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Garage Auto API',
        'version' => '1.0',
        'status' => 'online',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Nouvelle route pour tester la base PostgreSQL avec la vue Blade
Route::get('/test-db', function () {
    return view('test'); // resources/views/test.blade.php
});
