<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ status: { type: String } });
const form = useForm({});
const submit = () => { form.post(route('verification.send')); };
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <!-- Heading -->
        <div class="ve-heading">
            <div class="ve-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="ve-title">Verify Your Email</h2>
            <p class="ve-sub">
                Thanks for signing up! Before getting started, please verify your email address
                by clicking the link we just sent you.
            </p>
        </div>

        <!-- Success notice -->
        <div v-if="verificationLinkSent" class="ve-notice">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ve-notice-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            A new verification link has been sent to your email address.
        </div>

        <form @submit.prevent="submit" class="ve-form">
            <!-- Resend -->
            <button type="submit" class="ve-btn" :disabled="form.processing">
                <span v-if="!form.processing">Resend Verification Email</span>
                <svg v-else class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
            </button>

            <!-- Logout -->
            <div class="ve-logout">
                <Link :href="route('logout')" method="post" as="button" class="ve-logout-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.ve-heading { text-align:center; margin-bottom:1.75rem; }
.ve-icon { width:60px; height:60px; margin:0 auto 1rem; border-radius:16px; background:linear-gradient(135deg,#e0f2fe,#bae6fd); border:1.5px solid #7dd3fc; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(14,165,233,0.15); }
.ve-icon svg { width:26px; height:26px; color:#0284c7; }
.ve-title { font-size:1.35rem; font-weight:800; color:#1e3a5f; margin-bottom:0.5rem; letter-spacing:-0.01em; }
.ve-sub { font-size:0.78rem; color:#6b7280; line-height:1.6; padding:0 0.25rem; }
.ve-notice { display:flex; align-items:flex-start; gap:0.6rem; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:0.75rem 1rem; font-size:0.78rem; color:#15803d; margin-bottom:1.25rem; line-height:1.5; }
.ve-notice-icon { width:16px; height:16px; flex-shrink:0; margin-top:1px; }
.ve-form { display:flex; flex-direction:column; gap:0.85rem; }
.ve-btn { width:100%; padding:0.75rem; border-radius:8px; border:none; background:linear-gradient(135deg,#4f46e5 0%,#6d28d9 100%); color:#fff; font-size:0.82rem; font-weight:700; letter-spacing:0.06em; cursor:pointer; transition:transform 0.15s,box-shadow 0.15s,filter 0.15s; box-shadow:0 4px 18px rgba(79,70,229,0.35); display:flex; align-items:center; justify-content:center; min-height:42px; }
.ve-btn:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 6px 24px rgba(79,70,229,0.45); filter:brightness(1.06); }
.ve-btn:disabled { opacity:0.65; cursor:not-allowed; }
.spin { width:18px; height:18px; animation:spin 1s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
.ve-logout { display:flex; justify-content:center; }
.ve-logout-btn { display:inline-flex; align-items:center; gap:0.4rem; font-size:0.78rem; font-weight:600; color:#6b7280; background:none; border:none; cursor:pointer; text-decoration:none; transition:color 0.2s; padding:0; }
.ve-logout-btn svg { width:14px; height:14px; }
.ve-logout-btn:hover { color:#ef4444; }
</style>
