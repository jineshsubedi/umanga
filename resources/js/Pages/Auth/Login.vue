<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: { type: Boolean },
    status:           { type: String },
});

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

const showPwd = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="`Log in | ${$page.props.app_settings?.app_name || 'UMNG Portal'}`" />

        <!-- Heading -->
        <div class="login-heading">
            <p class="login-sub">Log into</p>
            <h2 class="login-title">{{ $page.props.app_settings?.app_name || 'HUMAN RESOURCE INFORMATION SYSTEM' }}</h2>
        </div>

        <!-- Logo -->
        <div class="login-logo-wrap">
            <div class="login-logo-ring">
                <img
                    :src="$page.props.app_settings?.app_logo
                        ? '/storage/' + $page.props.app_settings.app_logo
                        : '/images/auth/login_illustration.png'"
                    alt="Logo"
                    class="login-logo-img"
                />
            </div>
        </div>

        <!-- Status alert -->
        <div v-if="status" class="login-alert login-alert--success">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="login-form">

            <!-- Email / Username -->
            <div class="field">
                <div class="field-inner">
                    <span class="field-icon field-icon--left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="USERNAME"
                        class="f-input"
                        :class="{ 'f-input--error': form.errors.email }"
                    />
                </div>
                <InputError :message="form.errors.email" class="field-err" />
            </div>

            <!-- Password -->
            <div class="field">
                <div class="field-inner">
                    <span class="field-icon field-icon--left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <TextInput
                        id="password"
                        :type="showPwd ? 'text' : 'password'"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="PASSWORD"
                        class="f-input"
                        :class="{ 'f-input--error': form.errors.password }"
                    />
                    <button type="button" class="field-icon field-icon--right" @click="showPwd = !showPwd" tabindex="-1">
                        <svg v-if="!showPwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <InputError :message="form.errors.password" class="field-err" />
            </div>

            <!-- Login button -->
            <button
                type="submit"
                class="login-btn"
                :disabled="form.processing"
            >
                <span v-if="!form.processing">LOGIN</span>
                <svg v-else class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
            </button>

            <!-- Remember + Forgot -->
            <div class="login-options">
                <label class="remember">
                    <Checkbox name="remember" v-model:checked="form.remember" class="remember-box" />
                    <span>Remember me</span>
                </label>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="forgot-link"
                >
                    Forgot your password?
                </Link>
            </div>

        </form>
    </GuestLayout>
</template>

<style scoped>
/* ── Heading ── */
.login-heading {
    text-align: center;
    margin-bottom: 1.25rem;
}
.login-sub {
    font-size: 0.7rem;
    font-weight: 500;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 0.2rem;
}
.login-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #1e3a5f;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1.4;
}

/* ── Logo ── */
.login-logo-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
}
.login-logo-ring {
    width: 68px; height: 68px;
    border-radius: 50%;
    border: 2px solid #e0e7ff;
    box-shadow: 0 4px 18px rgba(79,70,229,0.15);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    background: #fff;
    transition: box-shadow 0.3s;
}
.login-logo-ring:hover {
    box-shadow: 0 6px 24px rgba(79,70,229,0.28);
}
.login-logo-img {
    width: 100%; height: 100%;
    object-fit: contain;
    padding: 6px;
}

/* ── Alert ── */
.login-alert {
    border-radius: 10px;
    padding: 0.6rem 0.9rem;
    font-size: 0.78rem;
    margin-bottom: 1rem;
    text-align: center;
}
.login-alert--success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
}

/* ── Form ── */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

/* ── Field ── */
.field { display: flex; flex-direction: column; gap: 0.2rem; }
.field-inner {
    position: relative;
    display: flex;
    align-items: center;
}
.field-icon {
    position: absolute;
    display: flex;
    align-items: center;
    color: #9ca3af;
    transition: color 0.2s;
    background: none;
    border: none;
    padding: 0;
}
.field-icon--left  { left: 0.9rem; pointer-events: none; }
.field-icon--right { right: 0.9rem; cursor: pointer; }
.field-icon svg { width: 16px; height: 16px; }
.field-icon--right:hover { color: #4f46e5; }

.f-input {
    width: 100%;
    background: #f8faff !important;
    border: 1.5px solid #dde3f0 !important;
    border-radius: 8px !important;
    color: #1e293b !important;
    padding: 0.65rem 2.6rem !important;
    font-size: 0.78rem !important;
    font-weight: 600;
    letter-spacing: 0.06em;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.f-input::placeholder {
    color: #b0bac8 !important;
    letter-spacing: 0.08em;
    font-weight: 500;
}
.f-input:focus {
    border-color: #6366f1 !important;
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.10) !important;
}
.f-input--error { border-color: #f87171 !important; }
.field-err {
    font-size: 0.7rem;
    color: #ef4444 !important;
    padding-left: 0.25rem;
}

/* ── Login button ── */
.login-btn {
    width: 100%;
    padding: 0.72rem;
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #1e3a5f 0%, #243460 60%, #2d3f7a 100%);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
    box-shadow: 0 4px 18px rgba(30,58,95,0.35);
    display: flex; align-items: center; justify-content: center;
    min-height: 42px;
    margin-top: 0.1rem;
}
.login-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 24px rgba(30,58,95,0.45);
    filter: brightness(1.08);
}
.login-btn:active:not(:disabled) { transform: translateY(0); }
.login-btn:disabled { opacity: 0.65; cursor: not-allowed; }
.spin {
    width: 18px; height: 18px;
    animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Options row ── */
.login-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 0.1rem;
}
.remember {
    display: flex; align-items: center; gap: 0.45rem;
    font-size: 0.72rem; color: #6b7280;
    cursor: pointer; user-select: none;
}
.remember-box { accent-color: #4f46e5; width: 13px; height: 13px; }
.forgot-link {
    font-size: 0.72rem; color: #6b7280;
    text-decoration: none; font-weight: 500;
    transition: color 0.2s;
}
.forgot-link:hover { color: #4f46e5; text-decoration: underline; }
</style>
