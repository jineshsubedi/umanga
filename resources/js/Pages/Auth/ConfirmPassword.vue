<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="flex flex-col items-center text-center mb-8">
            <div class="mb-5">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">Confirm Your Identity</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed px-2">
                This is a secure area. Please confirm your password before continuing.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="password" value="Current Password" class="text-gray-600 dark:text-gray-300 font-medium" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-[#b026ff] hover:bg-[#9a1ce6] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#b026ff] transition-all transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Confirm & Continue
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
