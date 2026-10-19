<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
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

const props = defineProps<{
    quoteRequest: QuoteRequest;
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null; quoteRequest?: QuoteRequest }) => ({
        breadcrumbs: [
            {
                title: 'Offerteaanvragen',
                href: props.currentTeam ? `/${props.currentTeam.slug}/quote-requests` : '/',
            },
            {
                title: props.quoteRequest?.name ?? 'Aanvraag',
                href: '#',
            },
        ],
    }),
});

const page = usePage();
const listUrl = computed(() => `/${page.props.currentTeam?.slug}/quote-requests`);

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

const createdAt = new Date(props.quoteRequest.created_at).toLocaleString('nl-NL', {
    dateStyle: 'long',
    timeStyle: 'short',
});
</script>

<template>
    <Head :title="`Aanvraag van ${quoteRequest.name}`" />

    <div class="flex max-w-3xl flex-col gap-6 p-4">
        <div>
            <Link :href="listUrl" class="text-sm text-muted-foreground hover:underline">
                ← Terug naar overzicht
            </Link>
            <h1 class="mt-2 text-2xl font-semibold">{{ quoteRequest.name }}</h1>
            <p class="text-sm text-muted-foreground">Ontvangen op {{ createdAt }}</p>
        </div>

        <div class="grid gap-6 rounded-xl border p-6 sm:grid-cols-2">
            <div>
                <p class="text-sm text-muted-foreground">E-mailadres</p>
                <a :href="`mailto:${quoteRequest.email}`" class="hover:underline">{{ quoteRequest.email }}</a>
            </div>
            <div>
                <p class="text-sm text-muted-foreground">Telefoonnummer</p>
                <a v-if="quoteRequest.phone" :href="`tel:${quoteRequest.phone}`" class="hover:underline">
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
                <p class="whitespace-pre-line">{{ quoteRequest.description }}</p>
            </div>
        </div>

        <form class="flex flex-col gap-3 rounded-xl border p-6" @submit.prevent="updateStatus">
            <label for="status" class="font-medium">Status</label>
            <div class="flex gap-3">
                <select
                    id="status"
                    v-model="form.status"
                    class="h-9 flex-1 rounded-md border border-input bg-transparent px-3 text-sm"
                >
                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
                <Button type="submit" :disabled="form.processing || !form.isDirty">Opslaan</Button>
            </div>
            <InputError :message="form.errors.status" />
        </form>
    </div>
</template>