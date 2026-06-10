<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ status: { type: String } });

const form = useForm({ email: '' });
const submit = () => { form.post(route('password.email')); };
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <!-- Icon + Heading -->
        <div class="fp-heading">
            <div class="fp-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="fp-title">Forgot Password?</h2>
            <p class="fp-sub">Enter your email and we'll send you a password reset link.</p>
        </div>

        <!-- Status -->
        <div v-if="status" class="fp-status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="fp-status-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="fp-form">

            <!-- Email -->
            <div class="field">
                <label class="field-label">Email Address</label>
                <div class="field-inner">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="your@email.com"
                        class="f-input"
                        :class="{ 'f-input--error': form.errors.email }"
                    />
                </div>
                <InputError :message="form.errors.email" class="field-err" />
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="fp-btn"
                :disabled="form.processing"
            >
                <span v-if="!form.processing">Send Reset Link</span>
                <svg v-else class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
            </button>

            <!-- Back to login -->
            <div class="fp-back">
                <Link :href="route('login')" class="fp-back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Login
                </Link>
            </div>

        </form>
    </GuestLayout>
</template>

<style scoped>
/* ── Heading ── */
.fp-heading {
    text-align: center;
    margin-bottom: 1.75rem;
}
.fp-icon {
    width: 60px; height: 60px;
    margin: 0 auto 1rem;
    border-radius: 16px;
    background: linear-gradient(135deg, #ede9fe, #ddd6fe);
    border: 1.5px solid #c4b5fd;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(109,40,217,0.12);
}
.fp-icon svg { width: 26px; height: 26px; color: #6d28d9; }
.fp-title {
    font-size: 1.35rem; font-weight: 800; color: #1e3a5f;
    margin-bottom: 0.4rem; letter-spacing: -0.01em;
}
.fp-sub {
    font-size: 0.78rem; color: #6b7280; line-height: 1.55;
    padding: 0 0.5rem;
}

/* ── Status ── */
.fp-status {
    display: flex; align-items: flex-start; gap: 0.6rem;
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
    padding: 0.75rem 1rem; font-size: 0.78rem; color: #15803d;
    margin-bottom: 1.25rem; line-height: 1.5;
}
.fp-status-icon { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

/* ── Form ── */
.fp-form { display: flex; flex-direction: column; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.35rem; }
.field-label {
    font-size: 0.72rem; font-weight: 600; color: #374151;
    letter-spacing: 0.04em; text-transform: uppercase;
}
.field-inner { position: relative; display: flex; align-items: center; }
.field-icon {
    position: absolute; left: 0.9rem;
    display: flex; align-items: center;
    color: #9ca3af; pointer-events: none;
}
.field-icon svg { width: 16px; height: 16px; }
.f-input {
    width: 100%;
    background: #f8faff !important;
    border: 1.5px solid #dde3f0 !important;
    border-radius: 8px !important;
    color: #1e293b !important;
    padding: 0.65rem 1rem 0.65rem 2.6rem !important;
    font-size: 0.85rem !important;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.f-input::placeholder { color: #b0bac8 !important; }
.f-input:focus {
    border-color: #6366f1 !important;
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.10) !important;
}
.f-input--error { border-color: #f87171 !important; }
.field-err { font-size: 0.7rem; color: #ef4444 !important; padding-left: 0.25rem; }

/* ── Submit ── */
.fp-btn {
    width: 100%; padding: 0.75rem; border-radius: 8px; border: none;
    background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
    color: #fff; font-size: 0.82rem; font-weight: 700;
    letter-spacing: 0.06em; cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
    box-shadow: 0 4px 18px rgba(79,70,229,0.35);
    display: flex; align-items: center; justify-content: center; min-height: 42px;
}
.fp-btn:hover:not(:disabled) {
    transform: translateY(-1px); box-shadow: 0 6px 24px rgba(79,70,229,0.45);
    filter: brightness(1.06);
}
.fp-btn:disabled { opacity: 0.65; cursor: not-allowed; }
.spin { width: 18px; height: 18px; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Back ── */
.fp-back { display: flex; justify-content: center; }
.fp-back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    font-size: 0.78rem; font-weight: 600; color: #6b7280;
    text-decoration: none; transition: color 0.2s;
}
.fp-back-link svg { width: 14px; height: 14px; }
.fp-back-link:hover { color: #4f46e5; }
</style>
