<?php

use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Support\Facades\URL;

function validQuoteRequestData(array $overrides = []): array
{
    return array_merge([
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'phone' => '0612345678',
        'address' => 'Coolsingel 40',
        'postal_code' => '3011 AD',
        'city' => 'Rotterdam',
        'service' => 'Groepenkast',
        'description' => 'Groepenkast vervangen',
    ], $overrides);
}

function quoteWithItems(): Quote
{
    $quoteRequest = QuoteRequest::create(validQuoteRequestData());
    $quote = $quoteRequest->quote()->create(['valid_until' => now()->addDays(30)]);
    $quote->items()->create(['description' => 'Groepenkast vervangen', 'quantity' => 1, 'unit_price' => 45000]);

    return $quote;
}

test('a customer can submit a quote request', function () {
    $this->post('/offerte', validQuoteRequestData())->assertRedirect();

    $this->assertDatabaseHas('quote_requests', [
        'email' => 'jan@example.com',
        'status' => 'new',
    ]);
});

test('an invalid postal code is rejected', function () {
    $this->post('/offerte', validQuoteRequestData(['postal_code' => '12345']))
        ->assertSessionHasErrors('postal_code');

    expect(QuoteRequest::count())->toBe(0);
});

test('a customer cannot set the status', function () {
    $this->post('/offerte', validQuoteRequestData(['status' => 'accepted']));

    expect(QuoteRequest::first()->status)->toBe('new');
});

test('guests cannot see the admin overview', function () {
    $user = User::factory()->create();

    $this->get("/{$user->currentTeam->slug}/quote-requests")
        ->assertRedirect(route('login'));
});

test('an admin can save a quote with items', function () {
    $user = User::factory()->create();
    $quoteRequest = QuoteRequest::create(validQuoteRequestData());

    $this->actingAs($user)
        ->put("/{$user->currentTeam->slug}/quote-requests/{$quoteRequest->id}/quote", [
            'items' => [
                ['description' => 'Groepenkast vervangen', 'quantity' => 1, 'unit_price' => 45000, 'vat_rate' => 21],
                ['description' => 'Stopcontact plaatsen', 'quantity' => 3, 'unit_price' => 3500, 'vat_rate' => 21],
            ],
        ])
        ->assertRedirect();

    $quote = $quoteRequest->quote()->with('items')->first();

    expect($quote->items)->toHaveCount(2)
        ->and($quote->subtotal())->toBe(55500)
        ->and($quote->vatAmount())->toBe(11655)
        ->and($quote->total())->toBe(67155);
});

test('a customer can view a quote with a signed link', function () {
    $quote = quoteWithItems();

    $this->get(URL::temporarySignedRoute('quotes.show', now()->addDay(), ['quote' => $quote]))
        ->assertOk();
});

test('a quote cannot be viewed without a valid signature', function () {
    $quote = quoteWithItems();

    $this->get("/offerte/{$quote->id}")->assertForbidden();
});

test('a customer can accept a quote', function () {
    $quote = quoteWithItems();

    $this->post(URL::temporarySignedRoute('quotes.accept', now()->addDay(), ['quote' => $quote]))
        ->assertRedirect();

    expect($quote->fresh()->accepted_at)->not->toBeNull()
        ->and($quote->quoteRequest->fresh()->status)->toBe('accepted');
});

test('an expired quote cannot be accepted', function () {
    $quote = quoteWithItems();
    $quote->update(['valid_until' => now()->subDay()]);

    $this->post(URL::temporarySignedRoute('quotes.accept', now()->addDay(), ['quote' => $quote]))
        ->assertForbidden();

    expect($quote->fresh()->accepted_at)->toBeNull();
});
