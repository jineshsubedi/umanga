<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Register Your Company</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create an account to get started</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Company Section -->
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 space-y-4 border border-indigo-100 dark:border-indigo-800/30">
                <p class="text-xs font-semibold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider">Company Information</p>

                <div>
                    <InputLabel for="company_name" value="Company Name" class="dark:text-gray-300" />
                    <TextInput id="company_name" type="text" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white" v-model="form.company_name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.company_name" />
                </div>

                <div>
                    <InputLabel for="company_email" value="Company Email" class="dark:text-gray-300" />
                    <TextInput id="company_email" type="email" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white" v-model="form.company_email" required />
                    <InputError class="mt-2" :message="form.errors.company_email" />
                </div>
            </div>

            <!-- Admin Section -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 space-y-4 border border-gray-100 dark:border-gray-700/50">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Admin Account</p>

                <div>
                    <InputLabel for="name" value="Your Name" class="dark:text-gray-300" />
                    <TextInput id="name" type="text" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white" v-model="form.name" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email Address" class="dark:text-gray-300" />
                    <TextInput id="email" type="email" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white" v-model="form.email" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" class="dark:text-gray-300" />
                    <TextInput id="password" type="password" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white" v-model="form.password" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" class="dark:text-gray-300" />
                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full dark:bg-gray-800 dark:border-gray-700 dark:text-white" v-model="form.password_confirmation" required />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-2">
                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register Company
                </PrimaryButton>
            </div>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-4">
                Already registered? 
                <Link :href="route('login')" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    Log in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
