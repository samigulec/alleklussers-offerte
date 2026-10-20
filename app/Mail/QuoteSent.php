<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;

class QuoteSent extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uw offerte van AlleKlussers',
        );
    }

    public function content(): Content
    {
        $expiresAt = $this->quote->valid_until?->endOfDay() ?? now()->addDays(30);

        return new Content(
            markdown: 'mail.quote-sent',
            with: [
                'url' => URL::temporarySignedRoute('quotes.show', $expiresAt, ['quote' => $this->quote]),
                'total' => Number::currency($this->quote->total() / 100, in: 'EUR', locale: 'nl'),
            ],
        );
    }
}
