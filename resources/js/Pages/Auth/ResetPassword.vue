<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="flex flex-col items-center text-center mb-8">
            <div class="mb-5">
                <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">Create New Password</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Choose a strong password for your account</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" class="text-gray-600 dark:text-gray-300 font-medium" />
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

            <div>
                <InputLabel for="password" value="New Password" class="text-gray-600 dark:text-gray-300 font-medium" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-gray-600 dark:text-gray-300 font-medium" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-2 block w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-[#b026ff] hover:bg-[#9a1ce6] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#b026ff] transition-all transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
