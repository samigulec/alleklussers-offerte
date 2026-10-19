<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
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
}