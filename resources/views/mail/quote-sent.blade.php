<x-mail::message>
# Uw offerte

Beste {{ $quote->quoteRequest->name }},

Bedankt voor uw aanvraag. Wij hebben een offerte voor u klaargezet.

<x-mail::table>
| Omschrijving | Aantal | Totaal |
| :--- | ---: | ---: |
@foreach ($quote->items as $item)
| {{ $item->description }} | {{ $item->quantity }} | {{ \Illuminate\Support\Number::currency($item->total() / 100, in: 'EUR', locale: 'nl') }} |
@endforeach
</x-mail::table>

**Totaal (incl. BTW): {{ $total }}**

<x-mail::button :url="$url">
Bekijk en accepteer de offerte
</x-mail::button>

@if ($quote->valid_until)
Deze offerte is geldig tot {{ $quote->valid_until->format('d-m-Y') }}.
@endif

Met vriendelijke groet,<br>
AlleKlussers
</x-mail::message>