<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('web.dashboard');

require __DIR__ . '/auth.php';

Route::get('lang/{lang}', function ($lang) {
    $available = ['en', 'ar'];
    if (! in_array($lang, $available)) {
        abort(404);
    }
    session(['locale' => $lang]);
    return redirect()->back();
})->name('lang.switch');
