<?php

use App\Models\QuoteRequest;
use App\Models\User;

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
