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

    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\Tenant\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole('customer');

        // Check if customer already exists or create new one
        \App\Models\Tenant\Customer::firstOrCreate(
            ['email' => $request->email],
            [
                'branch_id' => \App\Models\Tenant\Branch::first()?->id ?? 1,
                'name' => $request->name,
                'phone' => null,
                'is_active' => true,
            ]
        );

        auth()->guard('tenant')->login($user);

        return redirect()->route('tenant.customer.menu');
    })->name('tenant.register.submit');
});
