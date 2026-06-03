<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">Verify your email</h2>
        </div>

        <div class="mb-6 text-sm text-gray-600 dark:text-gray-300 leading-relaxed text-center px-2">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 dark:bg-green-900/30 p-4 rounded-xl text-center" v-if="verificationLinkSent">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-[#b026ff] hover:bg-[#9a1ce6] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#b026ff] transition-all transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </button>
            </div>

            <div class="flex justify-center mt-6">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm font-medium text-gray-500 hover:text-[#7c3aed] dark:text-gray-400 dark:hover:text-[#a78bfa] transition-colors"
                >
                    Log Out
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
