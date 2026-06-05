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
        <Head :title="`Log in | ${$page.props.app_settings?.app_name || 'UMNG Portal'}`" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold text-gray-900">Log into {{ $page.props.app_settings?.app_name || 'Portal' }}</h2>
        </div>

        <form @submit.prevent="submit" class="w-full">
            <!-- Profile Icon Top -->
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 bg-[#243460] rounded-full flex items-center justify-center text-white shadow-md overflow-hidden">
                    <img 
                        :src="$page.props.app_settings?.app_logo ? '/storage/' + $page.props.app_settings.app_logo : '/images/auth/image.png'" 
                        alt="Logo" 
                        class="w-full h-full object-contain bg-white" 
                    />
                </div>
            </div>

            <div class="space-y-4">
                <!-- Username / Email Field -->
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full border-2 border-gray-600 rounded-full focus:ring-[#243460] focus:border-[#243460] text-gray-900 pl-11 pr-4 py-2 text-xs font-bold uppercase tracking-wider placeholder-gray-400"
                            :class="{ 'border-red-500': form.errors.email }"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="USERNAME"
                        />
                    </div>
                    <InputError class="mt-1 text-red-500 text-[10px] ml-4" :message="form.errors.email" />
                </div>

                <!-- Password Field -->
                <div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full border-2 border-gray-600 rounded-full focus:ring-[#243460] focus:border-[#243460] text-gray-900 pl-11 pr-4 py-2 text-xs font-bold uppercase tracking-wider placeholder-gray-400 tracking-widest"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError class="mt-1 text-red-500 text-[10px] ml-4" :message="form.errors.password" />
                </div>

                <!-- Login Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-[#243460] hover:bg-[#1a264a] focus:outline-none transition-colors"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Login
                    </button>
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between pt-2 px-2">
                    <label class="flex items-center group cursor-pointer">
                        <Checkbox name="remember" v-model:checked="form.remember" class="w-3 h-3 text-[#243460] focus:ring-[#243460] rounded-full border-gray-400" />
                        <span class="ms-1.5 text-[9px] font-bold text-gray-700">Remember me</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[9px] font-bold text-gray-500 hover:text-[#243460]"
                    >
                        Forgot your password?
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
