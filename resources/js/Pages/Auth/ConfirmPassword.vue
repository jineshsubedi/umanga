<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ password: '' });
const submit = () => { form.post(route('password.confirm'), { onFinish: () => form.reset() }); };
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />
        <div class="cp-heading">
            <div class="cp-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h2 class="cp-title">Confirm Your Identity</h2>
            <p class="cp-sub">This is a secure area. Please confirm your password before continuing.</p>
        </div>
        <form @submit.prevent="submit" class="cp-form">
            <div class="field">
                <label class="field-label">Current Password</label>
                <div class="field-inner">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <TextInput id="password" type="password" v-model="form.password" required autofocus
                        autocomplete="current-password" placeholder="Enter your password"
                        class="f-input" :class="{ 'f-input--error': form.errors.password }" />
                </div>
                <InputError :message="form.errors.password" class="field-err" />
            </div>
            <button type="submit" class="cp-btn" :disabled="form.processing">
                <span v-if="!form.processing">Confirm &amp; Continue</span>
                <svg v-else class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
            </button>
        </form>
    </GuestLayout>
</template>

<style scoped>
.cp-heading { text-align:center; margin-bottom:1.75rem; }
.cp-icon { width:60px; height:60px; margin:0 auto 1rem; border-radius:16px; background:linear-gradient(135deg,#fef9c3,#fde68a); border:1.5px solid #fcd34d; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(234,179,8,0.15); }
.cp-icon svg { width:26px; height:26px; color:#b45309; }
.cp-title { font-size:1.35rem; font-weight:800; color:#1e3a5f; margin-bottom:0.4rem; letter-spacing:-0.01em; }
.cp-sub { font-size:0.78rem; color:#6b7280; line-height:1.55; padding:0 0.5rem; }
.cp-form { display:flex; flex-direction:column; gap:1rem; }
.field { display:flex; flex-direction:column; gap:0.3rem; }
.field-label { font-size:0.72rem; font-weight:600; color:#374151; letter-spacing:0.04em; text-transform:uppercase; }
.field-inner { position:relative; display:flex; align-items:center; }
.field-icon { position:absolute; left:0.9rem; display:flex; align-items:center; color:#9ca3af; pointer-events:none; }
.field-icon svg { width:16px; height:16px; }
.f-input { width:100%; background:#f8faff !important; border:1.5px solid #dde3f0 !important; border-radius:8px !important; color:#1e293b !important; padding:0.65rem 1rem 0.65rem 2.6rem !important; font-size:0.85rem !important; outline:none; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
.f-input::placeholder { color:#b0bac8 !important; }
.f-input:focus { border-color:#6366f1 !important; background:#fff !important; box-shadow:0 0 0 3px rgba(99,102,241,0.10) !important; }
.f-input--error { border-color:#f87171 !important; }
.field-err { font-size:0.7rem; color:#ef4444 !important; padding-left:0.25rem; }
.cp-btn { width:100%; padding:0.75rem; border-radius:8px; border:none; background:linear-gradient(135deg,#4f46e5 0%,#6d28d9 100%); color:#fff; font-size:0.82rem; font-weight:700; letter-spacing:0.06em; cursor:pointer; transition:transform 0.15s,box-shadow 0.15s,filter 0.15s; box-shadow:0 4px 18px rgba(79,70,229,0.35); display:flex; align-items:center; justify-content:center; min-height:42px; }
.cp-btn:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 6px 24px rgba(79,70,229,0.45); filter:brightness(1.06); }
.cp-btn:disabled { opacity:0.65; cursor:not-allowed; }
.spin { width:18px; height:18px; animation:spin 1s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>
