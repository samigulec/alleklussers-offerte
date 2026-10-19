<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const services = [
    'Storing of spoedhulp',
    'Groepenkast',
    'Verlichting',
    'Stopcontacten en schakelaars',
    'Bedrading en aansluitingen',
    'Keuring en veiligheid',
    'Laadpaal en slimme elektra',
];

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    postal_code: '',
    city: '',
    service: '',
    description: '',
});

function submit() {
    form.post('/offerte', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Offerte aanvragen" />

    <div class="min-h-screen bg-background px-4 py-12">
        <div class="mx-auto max-w-xl">
            <h1 class="text-3xl font-semibold">Offerte aanvragen</h1>
            <p class="mt-2 text-muted-foreground">
                Vertel ons wat u nodig heeft. U ontvangt binnen 24 uur een
                offerte op maat.
            </p>

            <div v-if="form.wasSuccessful" class="mt-8 rounded-lg border p-6">
                <h2 class="text-xl font-semibold">Bedankt voor uw aanvraag!</h2>
                <p class="mt-2 text-muted-foreground">
                    We nemen binnen 24 uur contact met u op.
                </p>
            </div>

            <form v-else class="mt-8 space-y-5" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="name">Naam</Label>
                    <Input id="name" v-model="form.name" autocomplete="name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">E-mailadres</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Telefoonnummer (optioneel)</Label>
                    <Input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        autocomplete="tel"
                    />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="address">Adres</Label>
                    <Input
                        id="address"
                        v-model="form.address"
                        autocomplete="street-address"
                    />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="postal_code">Postcode</Label>
                        <Input
                            id="postal_code"
                            v-model="form.postal_code"
                            placeholder="3011 AD"
                            autocomplete="postal-code"
                        />
                        <InputError :message="form.errors.postal_code" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="city">Plaats</Label>
                        <Input
                            id="city"
                            v-model="form.city"
                            autocomplete="address-level2"
                        />
                        <InputError :message="form.errors.city" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="service">Soort werk</Label>
                    <select
                        id="service"
                        v-model="form.service"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                    >
                        <option value="" disabled>Kies een dienst</option>
                        <option
                            v-for="service in services"
                            :key="service"
                            :value="service"
                        >
                            {{ service }}
                        </option>
                    </select>
                    <InputError :message="form.errors.service" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Omschrijving</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="5"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                        placeholder="Beschrijf kort wat er moet gebeuren"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    Offerte aanvragen
                </Button>
            </form>
        </div>
    </div>
</template>
