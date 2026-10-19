<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type QuoteItem = {
    description: string;
    quantity: number;
    unit_price: number;
    vat_rate: number;
};

type ItemRow = {
    description: string;
    quantity: number;
    price: string;
    vat_rate: number;
};

const props = defineProps<{
    action: string;
    quote: {
        valid_until: string | null;
        notes: string | null;
        items: QuoteItem[];
    } | null;
}>();

const emit = defineEmits<{
    saved: [];
    cancel: [];
}>();

function emptyRow(): ItemRow {
    return { description: '', quantity: 1, price: '', vat_rate: 21 };
}

function toRow(item: QuoteItem): ItemRow {
    return {
        description: item.description,
        quantity: item.quantity,
        price: (item.unit_price / 100).toFixed(2).replace('.', ','),
        vat_rate: item.vat_rate,
    };
}

// "450,50" of "1.250,00" -> centen. Ongeldige invoer wordt -1, zodat de server een fout geeft.
function toCents(price: string): number {
    if (price.trim() === '') {
        return -1;
    }

    const normalized = price.includes(',')
        ? price.replace(/\./g, '').replace(',', '.')
        : price;
    const value = Number(normalized);

    return Number.isFinite(value) ? Math.round(value * 100) : -1;
}

const form = useForm({
    valid_until: props.quote?.valid_until ?? '',
    notes: props.quote?.notes ?? '',
    items: props.quote?.items.length
        ? props.quote.items.map(toRow)
        : [emptyRow()],
});

function addItem() {
    form.items.push(emptyRow());
}

function removeItem(index: number) {
    form.items.splice(index, 1);
}

function itemError(index: number, field: string): string | undefined {
    return (form.errors as Record<string, string>)[`items.${index}.${field}`];
}

const money = new Intl.NumberFormat('nl-NL', {
    style: 'currency',
    currency: 'EUR',
});

const totals = computed(() => {
    let subtotal = 0;
    let vat = 0;

    for (const item of form.items) {
        const lineTotal =
            Math.max(toCents(item.price), 0) * Number(item.quantity);
        subtotal += lineTotal;
        vat += Math.round((lineTotal * item.vat_rate) / 100);
    }

    return { subtotal, vat, total: subtotal + vat };
});

function submit() {
    form.transform((data) => ({
        valid_until: data.valid_until || null,
        notes: data.notes || null,
        items: data.items.map((item) => ({
            description: item.description,
            quantity: Number(item.quantity),
            unit_price: toCents(item.price),
            vat_rate: Number(item.vat_rate),
        })),
    })).put(props.action, {
        preserveScroll: true,
        onSuccess: () => emit('saved'),
    });
}
</script>

<template>
    <form class="mt-4 flex flex-col gap-4" @submit.prevent="submit">
        <div class="flex flex-col gap-3">
            <div
                v-for="(item, index) in form.items"
                :key="index"
                class="grid grid-cols-[1fr_5rem_7rem_5rem_auto] items-start gap-2"
            >
                <div>
                    <Input
                        v-model="item.description"
                        placeholder="Omschrijving"
                    />
                    <InputError :message="itemError(index, 'description')" />
                </div>
                <div>
                    <Input v-model="item.quantity" type="number" min="1" />
                    <InputError :message="itemError(index, 'quantity')" />
                </div>
                <div>
                    <Input
                        v-model="item.price"
                        placeholder="0,00"
                        inputmode="decimal"
                    />
                    <InputError :message="itemError(index, 'unit_price')" />
                </div>
                <select
                    v-model="item.vat_rate"
                    class="h-9 rounded-md border border-input bg-transparent px-2 text-sm"
                >
                    <option :value="21">21%</option>
                    <option :value="9">9%</option>
                    <option :value="0">0%</option>
                </select>
                <Button
                    type="button"
                    variant="ghost"
                    :disabled="form.items.length === 1"
                    aria-label="Regel verwijderen"
                    @click="removeItem(index)"
                >
                    ✕
                </Button>
            </div>
            <InputError :message="form.errors.items" />

            <Button
                type="button"
                variant="outline"
                class="self-start"
                @click="addItem"
            >
                + Regel toevoegen
            </Button>
        </div>

        <div class="ml-auto w-64 space-y-1 text-sm">
            <div class="flex justify-between text-muted-foreground">
                <span>Subtotaal</span
                ><span>{{ money.format(totals.subtotal / 100) }}</span>
            </div>
            <div class="flex justify-between text-muted-foreground">
                <span>BTW</span
                ><span>{{ money.format(totals.vat / 100) }}</span>
            </div>
            <div class="flex justify-between text-base font-semibold">
                <span>Totaal</span
                ><span>{{ money.format(totals.total / 100) }}</span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="valid_until">Geldig tot</Label>
                <Input
                    id="valid_until"
                    v-model="form.valid_until"
                    type="date"
                />
                <InputError :message="form.errors.valid_until" />
            </div>
            <div class="grid gap-2 sm:col-span-2">
                <Label for="notes">Opmerkingen (optioneel)</Label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                />
                <InputError :message="form.errors.notes" />
            </div>
        </div>

        <div class="flex gap-2">
            <Button type="submit" :disabled="form.processing"
                >Offerte opslaan</Button
            >
            <Button type="button" variant="ghost" @click="emit('cancel')"
                >Annuleren</Button
            >
        </div>
    </form>
</template>
