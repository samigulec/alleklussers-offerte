<?php

namespace Database\Seeders;

use App\Models\QuoteRequest;
use Illuminate\Database\Seeder;

/**
 * Fills the database with realistic, fictional demo data for screenshots and the live demo.
 *
 * Run with: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->demoRequests() as $demo) {
            $quoteRequest = QuoteRequest::create($demo['request']);
            $quoteRequest->status = $demo['status'];
            $quoteRequest->setCreatedAt(now()->subDays($demo['days_ago'])->setTime(9, 30));
            $quoteRequest->save();

            if ($demo['items'] === []) {
                continue;
            }

            $quote = $quoteRequest->quote()->create([
                'valid_until' => now()->addDays(30)->toDateString(),
                'notes' => $demo['notes'],
            ]);

            $quote->items()->createMany($demo['items']);

            $quote->sent_at = $demo['sent_days_ago'] !== null ? now()->subDays($demo['sent_days_ago'])->setTime(14, 5) : null;
            $quote->accepted_at = $demo['accepted_days_ago'] !== null ? now()->subDays($demo['accepted_days_ago'])->setTime(19, 42) : null;
            $quote->save();
        }
    }

    /**
     * @return list<array{
     *     request: array<string, string|null>,
     *     status: string,
     *     days_ago: int,
     *     notes: string|null,
     *     items: list<array{description: string, quantity: int, unit_price: int, vat_rate: int}>,
     *     sent_days_ago: int|null,
     *     accepted_days_ago: int|null,
     * }>
     */
    private function demoRequests(): array
    {
        return [
            [
                'request' => [
                    'name' => 'Fatima El Amrani',
                    'email' => 'fatima.elamrani@example.com',
                    'phone' => '06 12 34 56 78',
                    'address' => 'Mathenesserlaan 214',
                    'postal_code' => '3021 HE',
                    'city' => 'Rotterdam',
                    'service' => 'Groepenkast',
                    'description' => "De groepenkast is nog van 1985 en de stoppen slaan regelmatig door als de wasmachine en de oven tegelijk aan staan.\nGraag een nieuwe kast met aardlekschakelaars.",
                ],
                'status' => 'accepted',
                'days_ago' => 9,
                'notes' => 'Inclusief afvoer van de oude groepenkast. Werkzaamheden duren ongeveer één dag.',
                'items' => [
                    ['description' => 'Groepenkast 3-fase, 12 groepen', 'quantity' => 1, 'unit_price' => 64500, 'vat_rate' => 21],
                    ['description' => 'Aardlekautomaat', 'quantity' => 4, 'unit_price' => 3850, 'vat_rate' => 21],
                    ['description' => 'Arbeid elektricien (per uur)', 'quantity' => 6, 'unit_price' => 6500, 'vat_rate' => 21],
                ],
                'sent_days_ago' => 8,
                'accepted_days_ago' => 6,
            ],
            [
                'request' => [
                    'name' => 'Mark de Vries',
                    'email' => 'mark.devries@example.com',
                    'phone' => '06 98 76 54 32',
                    'address' => 'Lange Haven 17',
                    'postal_code' => '3111 CA',
                    'city' => 'Schiedam',
                    'service' => 'Laadpaal en slimme elektra',
                    'description' => 'Ik krijg volgende maand een elektrische auto en wil een laadpaal op de oprit, ongeveer 8 meter van de meterkast.',
                ],
                'status' => 'quoted',
                'days_ago' => 4,
                'notes' => 'Prijs is inclusief aanmelding bij de netbeheerder.',
                'items' => [
                    ['description' => 'Laadpaal 11 kW met app', 'quantity' => 1, 'unit_price' => 89900, 'vat_rate' => 21],
                    ['description' => 'Bekabeling en graafwerk oprit (per meter)', 'quantity' => 8, 'unit_price' => 2450, 'vat_rate' => 21],
                    ['description' => 'Installatie en inbedrijfstelling', 'quantity' => 1, 'unit_price' => 35000, 'vat_rate' => 21],
                ],
                'sent_days_ago' => 3,
                'accepted_days_ago' => null,
            ],
            [
                'request' => [
                    'name' => 'Yusuf Kaya',
                    'email' => 'yusuf.kaya@example.com',
                    'phone' => '06 55 44 33 22',
                    'address' => 'Beijerlandselaan 88',
                    'postal_code' => '3074 EK',
                    'city' => 'Rotterdam',
                    'service' => 'Storing of spoedhulp',
                    'description' => 'Sinds vanochtend is de stroom in de keuken uitgevallen en de groep blijft eruit springen.',
                ],
                'status' => 'completed',
                'days_ago' => 14,
                'notes' => null,
                'items' => [
                    ['description' => 'Spoedinzet storing', 'quantity' => 1, 'unit_price' => 9500, 'vat_rate' => 21],
                    ['description' => 'Arbeid elektricien (per uur)', 'quantity' => 2, 'unit_price' => 6500, 'vat_rate' => 21],
                    ['description' => 'Nieuwe wandcontactdoos keuken', 'quantity' => 2, 'unit_price' => 2450, 'vat_rate' => 21],
                ],
                'sent_days_ago' => 14,
                'accepted_days_ago' => 13,
            ],
            [
                'request' => [
                    'name' => 'Linda Bakker',
                    'email' => 'linda.bakker@example.com',
                    'phone' => null,
                    'address' => 'Kerkplein 5',
                    'postal_code' => '2901 AT',
                    'city' => 'Capelle aan den IJssel',
                    'service' => 'Verlichting',
                    'description' => 'Wij willen inbouwspots in de woonkamer (ongeveer 10 stuks) en dimbare verlichting boven de eettafel.',
                ],
                'status' => 'new',
                'days_ago' => 1,
                'notes' => null,
                'items' => [
                    ['description' => 'LED-inbouwspot dimbaar', 'quantity' => 10, 'unit_price' => 3200, 'vat_rate' => 21],
                    ['description' => 'Dimmer en hanglamp eettafel', 'quantity' => 1, 'unit_price' => 18500, 'vat_rate' => 21],
                    ['description' => 'Arbeid elektricien (per uur)', 'quantity' => 5, 'unit_price' => 6500, 'vat_rate' => 21],
                ],
                'sent_days_ago' => null,
                'accepted_days_ago' => null,
            ],
            [
                'request' => [
                    'name' => 'Sophie Jansen',
                    'email' => 'sophie.jansen@example.com',
                    'phone' => '06 11 22 33 44',
                    'address' => 'Westhavenkade 41',
                    'postal_code' => '3131 AG',
                    'city' => 'Vlaardingen',
                    'service' => 'Stopcontacten en schakelaars',
                    'description' => 'Graag extra stopcontacten in de werkkamer en een buitenstopcontact in de tuin.',
                ],
                'status' => 'new',
                'days_ago' => 0,
                'notes' => null,
                'items' => [],
                'sent_days_ago' => null,
                'accepted_days_ago' => null,
            ],
            [
                'request' => [
                    'name' => 'Ahmed Bouzid',
                    'email' => 'ahmed.bouzid@example.com',
                    'phone' => '06 77 88 99 00',
                    'address' => 'Middenbaan Noord 120',
                    'postal_code' => '2991 CD',
                    'city' => 'Barendrecht',
                    'service' => 'Keuring en veiligheid',
                    'description' => 'Voor de verkoop van ons huis hebben we een keuring van de elektrische installatie nodig.',
                ],
                'status' => 'new',
                'days_ago' => 0,
                'notes' => null,
                'items' => [],
                'sent_days_ago' => null,
                'accepted_days_ago' => null,
            ],
        ];
    }
}
