<?php

namespace App\Http\Controllers;

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
}