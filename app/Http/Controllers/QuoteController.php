<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class QuoteController extends Controller
{
    public function show(Quote $quote): Response
    {
        $quote->load(['items', 'quoteRequest']);

        return Inertia::render('quotes/Show', [
            'quote' => [
                'customer_name' => $quote->quoteRequest->name,
                'valid_until' => $quote->valid_until?->toDateString(),
                'notes' => $quote->notes,
                'accepted_at' => $quote->accepted_at?->toIso8601String(),
                'is_expired' => $quote->isExpired(),
                'items' => $quote->items->map(fn (QuoteItem $item): array => $item->only([
                    'id', 'description', 'quantity', 'unit_price', 'vat_rate',
                ])),
                'subtotal' => $quote->subtotal(),
                'vat_amount' => $quote->vatAmount(),
                'total' => $quote->total(),
            ],
            'acceptUrl' => URL::temporarySignedRoute('quotes.accept', now()->addDays(30), ['quote' => $quote]),
        ]);
    }

    public function accept(Quote $quote): RedirectResponse
    {
        abort_if($quote->isExpired(), 403, 'Deze offerte is verlopen.');

        if ($quote->accepted_at === null) {
            $quote->accepted_at = now();
            $quote->save();

            $quote->quoteRequest->status = 'accepted';
            $quote->quoteRequest->save();
        }

        return back();
    }
}
