<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PendaftaranViewController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pendaftaran/perorangan', [PendaftaranViewController::class, 'tampilPerorangan'])->name('pendaftaran.perorangan');
