<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class QuoteRequestController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/quote-requests/Index', [
            'quoteRequests' => QuoteRequest::latest()
                ->get(['id', 'name', 'city', 'service', 'status', 'created_at']),
        ]);
    }

    public function show(string $currentTeam, QuoteRequest $quoteRequest): Response
    {
        $quote = $quoteRequest->quote()->with('items')->first();

        return Inertia::render('admin/quote-requests/Show', [
            'quoteRequest' => $quoteRequest,
            'quote' => $quote ? [
                'id' => $quote->id,
                'valid_until' => $quote->valid_until?->toDateString(),
                'notes' => $quote->notes,
                'items' => $quote->items,
                'subtotal' => $quote->subtotal(),
                'vat_amount' => $quote->vatAmount(),
                'total' => $quote->total(),
            ] : null,
        ]);
    }

    public function updateStatus(Request $request, string $currentTeam, QuoteRequest $quoteRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'quoted', 'accepted', 'completed'])],
        ]);

        $quoteRequest->status = $validated['status'];
        $quoteRequest->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Status bijgewerkt.']);

        return back();
    }

    public function saveQuote(Request $request, string $currentTeam, QuoteRequest $quoteRequest): RedirectResponse
    {
        $validated = $request->validate([
            'valid_until' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
            'items.*.vat_rate' => ['required', Rule::in([0, 9, 21])],
        ]);

        DB::transaction(function () use ($quoteRequest, $validated) {
            $quote = $quoteRequest->quote()->updateOrCreate([], [
                'valid_until' => $validated['valid_until'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $quote->items()->delete();
            $quote->items()->createMany($validated['items']);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Offerte opgeslagen.']);

        return back();
    }
}
