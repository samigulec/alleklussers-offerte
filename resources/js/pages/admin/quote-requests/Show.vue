<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import QuoteForm from '@/components/QuoteForm.vue';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

type QuoteRequest = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    address: string;
    postal_code: string;
    city: string;
    service: string;
    description: string;
    status: string;
    created_at: string;
};

type QuoteItem = {
    id: number;
    description: string;
    quantity: number;
    unit_price: number;
    vat_rate: number;
};

type Quote = {
    id: number;
    valid_until: string | null;
    notes: string | null;
    items: QuoteItem[];
    subtotal: number;
    vat_amount: number;
    total: number;
};

const props = defineProps<{
    quoteRequest: QuoteRequest;
    quote: Quote | null;
}>();

defineOptions({
    layout: (props: {
        currentTeam?: Team | null;
        quoteRequest?: QuoteRequest;
    }) => ({
        breadcrumbs: [
            {
                title: 'Offerteaanvragen',
                href: props.currentTeam
                    ? `/${props.currentTeam.slug}/quote-requests`
                    : '/',
            },
            {
                title: props.quoteRequest?.name ?? 'Aanvraag',
                href: '#',
            },
        ],
    }),
});

const page = usePage();
const listUrl = computed(
    () => `/${page.props.currentTeam?.slug}/quote-requests`,
);
const editing = ref(false);

const statusOptions = [
    { value: 'new', label: 'Nieuw' },
    { value: 'quoted', label: 'Offerte verstuurd' },
    { value: 'accepted', label: 'Geaccepteerd' },
    { value: 'completed', label: 'Afgerond' },
];

const form = useForm({
    status: props.quoteRequest.status,
});

function updateStatus() {
    form.patch(`${listUrl.value}/${props.quoteRequest.id}/status`, {
        preserveScroll: true,
    });
}

const createdAt = new Date(props.quoteRequest.created_at).toLocaleString(
    'nl-NL',
    {
        dateStyle: 'long',
        timeStyle: 'short',
    },
);

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
    <Head :title="`Aanvraag van ${quoteRequest.name}`" />

    <div class="flex max-w-3xl flex-col gap-6 p-4">
        <div>
            <Link
                :href="listUrl"
                class="text-sm text-muted-foreground hover:underline"
            >
                ← Terug naar overzicht
            </Link>
            <h1 class="mt-2 text-2xl font-semibold">{{ quoteRequest.name }}</h1>
            <p class="text-sm text-muted-foreground">
                Ontvangen op {{ createdAt }}
            </p>
        </div>

        <div class="grid gap-6 rounded-xl border p-6 sm:grid-cols-2">
            <div>
                <p class="text-sm text-muted-foreground">E-mailadres</p>
                <a
                    :href="`mailto:${quoteRequest.email}`"
                    class="hover:underline"
                    >{{ quoteRequest.email }}</a
                >
            </div>
            <div>
                <p class="text-sm text-muted-foreground">Telefoonnummer</p>
                <a
                    v-if="quoteRequest.phone"
                    :href="`tel:${quoteRequest.phone}`"
                    class="hover:underline"
                >
                    {{ quoteRequest.phone }}
                </a>
                <span v-else class="text-muted-foreground">Niet opgegeven</span>
            </div>
            <div>
                <p class="text-sm text-muted-foreground">Adres</p>
                <p>{{ quoteRequest.address }}</p>
                <p>{{ quoteRequest.postal_code }} {{ quoteRequest.city }}</p>
            </div>
            <div>
                <p class="text-sm text-muted-foreground">Soort werk</p>
                <p>{{ quoteRequest.service }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-sm text-muted-foreground">Omschrijving</p>
                <p class="whitespace-pre-line">
                    {{ quoteRequest.description }}
                </p>
            </div>
        </div>

        <div class="rounded-xl border p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Offerte</h2>
                <Button
                    v-if="!editing"
                    variant="outline"
                    size="sm"
                    @click="editing = true"
                >
                    {{ quote ? 'Bewerken' : 'Offerte maken' }}
                </Button>
            </div>

            <QuoteForm
                v-if="editing"
                :action="`${listUrl}/${quoteRequest.id}/quote`"
                :quote="quote"
                @saved="editing = false"
                @cancel="editing = false"
            />

            <template v-else>
                <p v-if="!quote" class="mt-2 text-muted-foreground">
                    Er is nog geen offerte gemaakt.
                </p>

                <template v-else>
                    <p
                        v-if="quote.valid_until"
                        class="mt-1 text-sm text-muted-foreground"
                    >
                        Geldig tot {{ formatDate(quote.valid_until) }}
                    </p>

                    <table class="mt-4 w-full text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr>
                                <th class="py-2 font-medium">Omschrijving</th>
                                <th class="py-2 text-right font-medium">
                                    Aantal
                                </th>
                                <th class="py-2 text-right font-medium">
                                    Prijs
                                </th>
                                <th class="py-2 text-right font-medium">
                                    Totaal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in quote.items"
                                :key="item.id"
                                class="border-b"
                            >
                                <td class="py-2">{{ item.description }}</td>
                                <td class="py-2 text-right">
                                    {{ item.quantity }}
                                </td>
                                <td class="py-2 text-right">
                                    {{ formatMoney(item.unit_price) }}
                                </td>
                                <td class="py-2 text-right">
                                    {{
                                        formatMoney(
                                            item.quantity * item.unit_price,
                                        )
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
                                <td colspan="3" class="pt-2 text-right">
                                    Totaal
                                </td>
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
                </template>
            </template>
        </div>

        <form
            class="flex flex-col gap-3 rounded-xl border p-6"
            @submit.prevent="updateStatus"
        >
            <label for="status" class="font-medium">Status</label>
            <div class="flex gap-3">
                <select
                    id="status"
                    v-model="form.status"
                    class="h-9 flex-1 rounded-md border border-input bg-transparent px-3 text-sm"
                >
                    <option
                        v-for="option in statusOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
                <Button
                    type="submit"
                    :disabled="form.processing || !form.isDirty"
                    >Opslaan</Button
                >
            </div>
            <InputError :message="form.errors.status" />
        </form>
    </div>
</template>
