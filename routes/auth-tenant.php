<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(['guest:tenant'])->group(function () {
    Route::get('/login', function () {
        return view('tenant.auth.login-form');
    })->name('tenant.login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('tenant')->attempt($credentials)) {
            return redirect()->intended(route('tenant.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    })->name('tenant.login.submit');

    Route::get('/register', function () {
        return view('tenant.auth.register');
    })->name('tenant.register');
});
