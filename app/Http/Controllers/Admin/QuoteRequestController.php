<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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