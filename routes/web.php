<?php

use App\Http\Controllers\Admin\QuoteRequestController as AdminQuoteRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::get('/offerte', [QuoteRequestController::class, 'create'])->name('quote-requests.create');
Route::post('/offerte', [QuoteRequestController::class, 'store'])->name('quote-requests.store');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
                Route::get('quote-requests', [AdminQuoteRequestController::class, 'index'])->name('admin.quote-requests.index');
                Route::get('quote-requests/{quoteRequest}', [AdminQuoteRequestController::class, 'show'])->name('admin.quote-requests.show');
                Route::patch('quote-requests/{quoteRequest}/status', [AdminQuoteRequestController::class, 'updateStatus'])->name('admin.quote-requests.update-status');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
