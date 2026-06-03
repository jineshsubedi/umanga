<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    company_name: '',
    company_email: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.post(route('register'));
</script>

<template>
    <GuestLayout>
        <Head title="Register Company" />

        <div class="flex flex-col items-center text-center mb-8">
            <div class="mb-5">
                <div class="inline-block bg-purple-600 text-white px-5 py-2 rounded-full text-sm font-semibold shadow-md">
                    Create Account
                </div>
            </div>
            <h2 class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">Register Your Company</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Set up your workspace and get started</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Company Section -->
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-2xl p-4 space-y-4 border border-purple-100 dark:border-purple-800/30">
                <p class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                    Company Information
                </p>

                <div>
                    <InputLabel for="company_name" value="Company Name" class="text-gray-600 dark:text-gray-300 font-medium" />
                    <TextInput id="company_name" type="text"
                        class="mt-2 block w-full bg-white border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                        v-model="form.company_name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.company_name" />
                </div>

                <div>
                    <InputLabel for="company_email" value="Company Email" class="text-gray-600 dark:text-gray-300 font-medium" />
                    <TextInput id="company_email" type="email"
                        class="mt-2 block w-full bg-white border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                        v-model="form.company_email" required />
                    <InputError class="mt-2" :message="form.errors.company_email" />
                </div>
            </div>

            <!-- Admin Section -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 space-y-4 border border-gray-100 dark:border-gray-700/50">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Admin Account
                </p>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <InputLabel for="name" value="Your Name" class="text-gray-600 dark:text-gray-300 font-medium" />
                        <TextInput id="name" type="text"
                            class="mt-2 block w-full bg-white border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                            v-model="form.name" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email Address" class="text-gray-600 dark:text-gray-300 font-medium" />
                        <TextInput id="email" type="email"
                            class="mt-2 block w-full bg-white border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                            v-model="form.email" required />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <InputLabel for="password" value="Password" class="text-gray-600 dark:text-gray-300 font-medium" />
                        <TextInput id="password" type="password"
                            class="mt-2 block w-full bg-white border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                            v-model="form.password" required autocomplete="new-password" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" class="text-gray-600 dark:text-gray-300 font-medium" />
                        <TextInput id="password_confirmation" type="password"
                            class="mt-2 block w-full bg-white border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                            v-model="form.password_confirmation" required />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-[#b026ff] hover:bg-[#9a1ce6] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#b026ff] transition-all transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Register Company
                </button>
            </div>

            <div class="text-center pt-4 border-t border-gray-100 dark:border-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Already registered?
                    <Link :href="route('login')" class="font-semibold text-[#7c3aed] hover:text-[#6d28d9] dark:text-[#a78bfa] dark:hover:text-[#c4b5fd] transition-colors">
                        Log in
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
