<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\GuestCertificateController;
use App\Http\Controllers\OrderController;
use App\Models\Account;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Wovosoft\LaravelLetsencryptCore\LaravelClient;
use Wovosoft\LaravelLetsencryptCore\Ssl\ClientModes;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    AccountController::routes();
    DomainController::routes();
    CertificateController::routes();
    OrderController::routes();
});

GuestCertificateController::routes();

Route::get('/t', function () {
    $account = Account::first();

    $lc = new LaravelClient(
        mode: ClientModes::Staging,
        username: $account->email
    );

    $domain = $account->domains()->first()->pluck('domain')->toArray();
    $order = $lc->createOrder($domain);
    dd($lc->getOrder($order->getId()));

});


