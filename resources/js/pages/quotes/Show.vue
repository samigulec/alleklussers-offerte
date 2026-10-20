<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type QuoteItem = {
    id: number;
    description: string;
    quantity: number;
    unit_price: number;
    vat_rate: number;
};

const props = defineProps<{
    quote: {
        customer_name: string;
        valid_until: string | null;
        notes: string | null;
        accepted_at: string | null;
        is_expired: boolean;
        items: QuoteItem[];
        subtotal: number;
        vat_amount: number;
        total: number;
    };
    acceptUrl: string;
}>();

const form = useForm({});

function accept() {
    form.post(props.acceptUrl, { preserveScroll: true });
}

const money = new Intl.NumberFormat('nl-NL', {
    style: 'currency',
    currency: 'EUR',
});

function formatMoney(cents: number): string {
    return money.format(cents / 100);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('nl-NL', { dateStyle: 'long' });
}
</script>

<template>
    <Head title="Uw offerte" />

    <div class="min-h-screen bg-background px-4 py-12">
        <div class="mx-auto max-w-2xl">
            <p class="text-sm text-muted-foreground">AlleKlussers</p>
            <h1 class="mt-1 text-3xl font-semibold">
                Offerte voor {{ quote.customer_name }}
            </h1>
            <p v-if="quote.valid_until" class="mt-2 text-muted-foreground">
                Geldig tot {{ formatDate(quote.valid_until) }}
            </p>

            <div class="mt-8 rounded-xl border p-6">
                <table class="w-full text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="py-2 font-medium">Omschrijving</th>
                            <th class="py-2 text-right font-medium">Aantal</th>
                            <th class="py-2 text-right font-medium">Prijs</th>
                            <th class="py-2 text-right font-medium">Totaal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in quote.items"
                            :key="item.id"
                            class="border-b"
                        >
                            <td class="py-2">{{ item.description }}</td>
                            <td class="py-2 text-right">{{ item.quantity }}</td>
                            <td class="py-2 text-right">
                                {{ formatMoney(item.unit_price) }}
                            </td>
                            <td class="py-2 text-right">
                                {{
                                    formatMoney(item.quantity * item.unit_price)
                                }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td
                                colspan="3"
                                class="pt-3 text-right text-muted-foreground"
                            >
                                Subtotaal
                            </td>
                            <td class="pt-3 text-right">
                                {{ formatMoney(quote.subtotal) }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                colspan="3"
                                class="py-1 text-right text-muted-foreground"
                            >
                                BTW
                            </td>
                            <td class="py-1 text-right">
                                {{ formatMoney(quote.vat_amount) }}
                            </td>
                        </tr>
                        <tr class="text-base font-semibold">
                            <td colspan="3" class="pt-2 text-right">Totaal</td>
                            <td class="pt-2 text-right">
                                {{ formatMoney(quote.total) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <p
                    v-if="quote.notes"
                    class="mt-4 text-sm whitespace-pre-line text-muted-foreground"
                >
                    {{ quote.notes }}
                </p>
            </div>

            <div v-if="quote.accepted_at" class="mt-6 rounded-xl border p-6">
                <h2 class="text-lg font-semibold">
                    Bedankt, de offerte is geaccepteerd!
                </h2>
                <p class="mt-1 text-muted-foreground">
                    Geaccepteerd op {{ formatDate(quote.accepted_at) }}. We
                    nemen contact met u op om een afspraak te plannen.
                </p>
            </div>

            <p v-else-if="quote.is_expired" class="mt-6 text-muted-foreground">
                Deze offerte is verlopen. Neem contact met ons op voor een
                nieuwe offerte.
            </p>

            <Button
                v-else
                class="mt-6 w-full"
                size="lg"
                :disabled="form.processing"
                @click="accept"
            >
                Akkoord met deze offerte
            </Button>
        </div>
    </div>
</template>
QuoteRequestTest.php
