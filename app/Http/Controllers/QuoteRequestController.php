<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuoteRequestController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('quote-requests/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
            'city' => ['required', 'string', 'max:255'],
            'service' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        QuoteRequest::create($validated);

        return back();
    }
        public function show(string $currentTeam, QuoteRequest $quoteRequest): Response
    {
        return Inertia::render('admin/quote-requests/Show', [
            'quoteRequest' => $quoteRequest,
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
}