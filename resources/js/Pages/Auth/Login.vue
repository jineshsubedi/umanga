<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <div class="flex flex-col items-center text-center mb-10">
            <!-- Centralized Welcome Back Pill -->
            <div class="mb-6">
                <Link href="/">
                    <div class="inline-block bg-purple-600 text-white px-5 py-2 rounded-full text-sm font-semibold shadow-md hover:bg-purple-700 transition-colors">
                        Welcome back
                    </div>
                </Link>
            </div>
            
            <h2 class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">Login your account</h2>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email / Username" class="text-gray-600 dark:text-gray-300 font-medium" />
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
                <InputLabel for="password" value="Password" class="text-gray-600 dark:text-gray-300 font-medium" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-800 dark:border-gray-700 dark:text-white px-4 py-3 transition-colors"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center group cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-[#7c3aed] focus:ring-[#7c3aed] dark:bg-gray-800 dark:border-gray-700" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Remember me</span>
                </label>
            </div>

            <div class="pt-4">
                <button
                    type="submit"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-[#b026ff] hover:bg-[#9a1ce6] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#b026ff] transition-all transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Login
                </button>
            </div>

            <div class="flex flex-col items-center space-y-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-gray-500 hover:text-[#7c3aed] dark:text-gray-400 dark:hover:text-[#a78bfa] transition-colors"
                >
                    Forgot Password?
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
