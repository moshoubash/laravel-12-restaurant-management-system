<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', \App\Livewire\Forms\LoginForm::class)->name('tenant.login');
Route::get('/register', function () {
    return view('tenant.auth.register');
})->name('tenant.register');
