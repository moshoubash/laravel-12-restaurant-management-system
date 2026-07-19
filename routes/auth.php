<?php

use Illuminate\Support\Facades\Route;

$isCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains'));

if ($isCentralDomain) {
    Route::prefix('/central')->group(function () {
        Route::get('/login', function () {
            return view('auth.login');
        })->name('central.login');

        Route::get('/register', function () {
            return view('auth.register');
        })->name('central.register');
    });

    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        auth('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('central.login');
    })->name('central.logout');
} else {
    Route::any('/central/{any?}', function () {
        return redirect('/login');
    })->where('any', '.*');
}
