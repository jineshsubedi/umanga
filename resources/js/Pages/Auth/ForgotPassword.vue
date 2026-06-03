<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="flex flex-col items-center text-center mb-8">
            <div class="mb-5">
                <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">Forgot Password?</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed px-2">
                No worries! Enter your email and we'll send you a reset link.
            </p>
        </div>

        <div v-if="status" class="mb-6 font-medium text-sm text-green-600 bg-green-50 dark:bg-green-900/30 p-4 rounded-xl text-center border border-green-100 dark:border-green-800/30">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email Address" class="text-gray-600 dark:text-gray-300 font-medium" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-[#b026ff] hover:bg-[#9a1ce6] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#b026ff] transition-all transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Send Reset Link
                </button>
            </div>

            <div class="text-center pt-4 border-t border-gray-100 dark:border-gray-800">
                <Link :href="route('login')" class="text-sm font-semibold text-[#7c3aed] hover:text-[#6d28d9] dark:text-[#a78bfa] dark:hover:text-[#c4b5fd] transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
