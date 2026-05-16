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

        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900">Register Your Company</h1>
            <p class="text-sm text-gray-500 mt-1">Create an account to get started</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Company Section -->
            <div class="bg-indigo-50 rounded-lg p-4 space-y-4">
                <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Company Information</p>

                <div>
                    <InputLabel for="company_name" value="Company Name" />
                    <TextInput id="company_name" type="text" class="mt-1 block w-full" v-model="form.company_name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.company_name" />
                </div>

                <div>
                    <InputLabel for="company_email" value="Company Email" />
                    <TextInput id="company_email" type="email" class="mt-1 block w-full" v-model="form.company_email" required />
                    <InputError class="mt-2" :message="form.errors.company_email" />
                </div>
            </div>

            <!-- Admin Section -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin Account</p>

                <div>
                    <InputLabel for="name" value="Your Name" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email Address" />
                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <Link :href="route('login')" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Already registered?
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register Company
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
