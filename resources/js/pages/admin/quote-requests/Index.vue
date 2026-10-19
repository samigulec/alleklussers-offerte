<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { Team } from '@/types';

type QuoteRequest = {
    id: number;
    name: string;
    city: string;
    service: string;
    status: string;
    created_at: string;
};

defineProps<{
    quoteRequests: QuoteRequest[];
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Offerteaanvragen',
                href: props.currentTeam ? `/${props.currentTeam.slug}/quote-requests` : '/',
            },
        ],
    }),
});

const statusLabels: Record<string, string> = {
    new: 'Nieuw',
    quoted: 'Offerte verstuurd',
    accepted: 'Geaccepteerd',
    completed: 'Afgerond',
};

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('nl-NL', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Offerteaanvragen" />

    <div class="flex flex-col gap-4 p-4">
        <h1 class="text-2xl font-semibold">Offerteaanvragen</h1>

        <p v-if="quoteRequests.length === 0" class="text-muted-foreground">
            Er zijn nog geen aanvragen.
        </p>

        <div v-else class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="border-b bg-muted/50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Datum</th>
                        <th class="px-4 py-3 font-medium">Naam</th>
                        <th class="px-4 py-3 font-medium">Plaats</th>
                        <th class="px-4 py-3 font-medium">Soort werk</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in quoteRequests"
                        :key="item.id"
                        class="border-b last:border-0"
                    >
                        <td class="px-4 py-3 text-muted-foreground">{{ formatDate(item.created_at) }}</td>
                        <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.city }}</td>
                        <td class="px-4 py-3">{{ item.service }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-muted px-2.5 py-1 text-xs font-medium">
                                {{ statusLabels[item.status] ?? item.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>