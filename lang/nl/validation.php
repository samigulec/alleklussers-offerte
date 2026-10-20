<?php

return [
    'array' => 'Het veld :attribute moet een lijst zijn.',
    'date' => 'Het veld :attribute moet een geldige datum zijn.',
    'email' => 'Vul een geldig e-mailadres in.',
    'in' => 'De gekozen waarde voor :attribute is ongeldig.',
    'integer' => 'Het veld :attribute moet een geheel getal zijn.',
    'max' => [
        'numeric' => 'Het veld :attribute mag niet groter zijn dan :max.',
        'string' => 'Het veld :attribute mag maximaal :max tekens bevatten.',
    ],
    'min' => [
        'array' => 'Voeg minimaal :min regel toe.',
        'numeric' => 'Het veld :attribute moet minimaal :min zijn.',
    ],
    'regex' => 'Het formaat van :attribute is ongeldig.',
    'required' => 'Het veld :attribute is verplicht.',
    'string' => 'Het veld :attribute moet tekst zijn.',

    'custom' => [
        'postal_code' => [
            'regex' => 'Vul een geldige postcode in, bijvoorbeeld 3011 AD.',
        ],
    ],

    'attributes' => [
        'name' => 'naam',
        'email' => 'e-mailadres',
        'phone' => 'telefoonnummer',
        'address' => 'adres',
        'postal_code' => 'postcode',
        'city' => 'plaats',
        'service' => 'soort werk',
        'description' => 'omschrijving',
        'status' => 'status',
        'valid_until' => 'geldig tot',
        'notes' => 'opmerkingen',
        'items' => 'regels',
        'items.*.description' => 'omschrijving',
        'items.*.quantity' => 'aantal',
        'items.*.unit_price' => 'prijs',
        'items.*.vat_rate' => 'BTW',
    ],
];
