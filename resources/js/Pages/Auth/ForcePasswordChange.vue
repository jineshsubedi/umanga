<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    password:              '',
    password_confirmation: '',
});

const showPwd     = ref(false);
const showConfirm = ref(false);

/* ── Password strength rules ── */
const rules = computed(() => [
    { label: 'At least 6 characters',       met: form.password.length >= 8 },
    { label: 'One uppercase letter (A–Z)',   met: /[A-Z]/.test(form.password) },
    { label: 'One lowercase letter (a–z)',   met: /[a-z]/.test(form.password) },
    { label: 'One number (0–9)',             met: /[0-9]/.test(form.password) },
    { label: 'One special character (!@#…)', met: /[^A-Za-z0-9]/.test(form.password) },
]);

const strengthScore = computed(() => rules.value.filter(r => r.met).length);

const strengthLabel = computed(() => {
    if (strengthScore.value <= 1) return 'Very Weak';
    if (strengthScore.value === 2) return 'Weak';
    if (strengthScore.value === 3) return 'Fair';
    if (strengthScore.value === 4) return 'Strong';
    return 'Very Strong';
});

const strengthColor = computed(() => {
    if (strengthScore.value <= 1) return '#ef4444';
    if (strengthScore.value === 2) return '#f97316';
    if (strengthScore.value === 3) return '#eab308';
    if (strengthScore.value === 4) return '#22c55e';
    return '#16a34a';
});

const passwordsMatch = computed(() =>
    form.password_confirmation.length > 0 &&
    form.password === form.password_confirmation
);

const passwordMismatch = computed(() =>
    form.password_confirmation.length > 0 &&
    form.password !== form.password_confirmation
);

const isValid = computed(() =>
    strengthScore.value === 5 && passwordsMatch.value
);

const submit = () => {
    if (!isValid.value) return;
    form.post(route('password.force-change.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Change Password" />

        <!-- Heading -->
        <div class="rp-heading">
            <div class="rp-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="rp-title">Action Required</h2>
            <p class="rp-sub">Please change your default password before continuing to the application.</p>
        </div>

        <form @submit.prevent="submit" class="rp-form">

            <!-- New Password -->
            <div class="field">
                <label class="field-label">New Password</label>
                <div class="field-inner">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <TextInput
                        id="password"
                        :type="showPwd ? 'text' : 'password'"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="New password"
                        class="f-input f-input--padded"
                        :class="{ 'f-input--error': form.errors.password }"
                    />
                    <button type="button" class="field-icon-right" @click="showPwd = !showPwd" tabindex="-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path v-if="!showPwd" stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <InputError :message="form.errors.password" class="field-err" />

                <!-- ── Strength Meter (visible when typing) ── -->
                <div v-if="form.password.length > 0" class="strength-wrap">
                    <!-- Bar -->
                    <div class="strength-bar-track">
                        <div
                            class="strength-bar-fill"
                            :style="{
                                width: (strengthScore / 5 * 100) + '%',
                                background: strengthColor
                            }"
                        ></div>
                    </div>
                    <span class="strength-label" :style="{ color: strengthColor }">
                        {{ strengthLabel }}
                    </span>

                    <!-- Criteria list -->
                    <ul class="strength-rules">
                        <li v-for="rule in rules" :key="rule.label" class="rule-item" :class="{ 'rule-met': rule.met }">
                            <span class="rule-dot">
                                <svg v-if="rule.met" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                            {{ rule.label }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="field">
                <label class="field-label">Confirm Password</label>
                <div class="field-inner">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                    <TextInput
                        id="password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        class="f-input f-input--padded"
                        :class="{
                            'f-input--error':   form.errors.password_confirmation || passwordMismatch,
                            'f-input--success':  passwordsMatch
                        }"
                    />
                    <button type="button" class="field-icon-right" @click="showConfirm = !showConfirm" tabindex="-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path v-if="!showConfirm" stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>

                <!-- Match indicator -->
                <p v-if="passwordsMatch" class="match-ok">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Passwords match
                </p>
                <p v-else-if="passwordMismatch" class="match-err">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Passwords do not match
                </p>

                <InputError :message="form.errors.password_confirmation" class="field-err" />
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="rp-btn"
                :class="{ 'rp-btn--ready': isValid }"
                :disabled="form.processing || !isValid"
                :title="!isValid ? 'Please meet all password requirements first' : ''"
            >
                <span v-if="!form.processing">Save Password & Continue</span>
                <svg v-else class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
            </button>

        </form>
    </GuestLayout>
</template>

<style scoped>
/* ── Heading ── */
.rp-heading { text-align:center; margin-bottom:1.25rem; }
.rp-icon { width:56px; height:56px; margin:0 auto 0.85rem; border-radius:14px; background:linear-gradient(135deg,#ede9fe,#ddd6fe); border:1.5px solid #c4b5fd; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(109,40,217,0.12); }
.rp-icon svg { width:24px; height:24px; color:#6d28d9; }
.rp-title { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin-bottom:0.3rem; letter-spacing:-0.01em; }
.rp-sub { font-size:0.76rem; color:#6b7280; line-height:1.5; }

/* ── Form ── */
.rp-form { display:flex; flex-direction:column; gap:0.8rem; }

/* ── Fields ── */
.field { display:flex; flex-direction:column; gap:0.28rem; }
.field-label { font-size:0.68rem; font-weight:600; color:#374151; letter-spacing:0.04em; text-transform:uppercase; }
.field-inner { position:relative; display:flex; align-items:center; }
.field-icon { position:absolute; left:0.85rem; display:flex; align-items:center; color:#9ca3af; pointer-events:none; }
.field-icon svg { width:15px; height:15px; }
.field-icon-right { position:absolute; right:0.85rem; display:flex; align-items:center; color:#9ca3af; background:none; border:none; cursor:pointer; padding:0; transition:color 0.2s; }
.field-icon-right:hover { color:#4f46e5; }
.field-icon-right svg { width:15px; height:15px; }

.f-input {
    width:100%; background:#f8faff !important; border:1.5px solid #dde3f0 !important; border-radius:8px !important;
    color:#1e293b !important; padding:0.6rem 1rem 0.6rem 2.4rem !important; font-size:0.84rem !important;
    outline:none; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s;
    box-shadow:0 1px 3px rgba(0,0,0,0.04);
}
.f-input--padded { padding-right:2.4rem !important; }
.f-input::placeholder { color:#b0bac8 !important; }
.f-input:focus { border-color:#6366f1 !important; background:#fff !important; box-shadow:0 0 0 3px rgba(99,102,241,0.10) !important; }
.f-input--error   { border-color:#f87171 !important; }
.f-input--success { border-color:#22c55e !important; }
.field-err { font-size:0.68rem; color:#ef4444 !important; padding-left:0.2rem; }

/* ── Strength Meter ── */
.strength-wrap {
    margin-top: 0.45rem;
    padding: 0.7rem 0.85rem;
    background: #f8faff;
    border: 1px solid #e9eef8;
    border-radius: 8px;
}

/* Bar */
.strength-bar-track {
    height: 5px;
    background: #e5e7eb;
    border-radius: 99px;
    overflow: hidden;
    margin-bottom: 0.35rem;
}
.strength-bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 0.35s ease, background 0.35s ease;
}
.strength-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    display: block;
    margin-bottom: 0.55rem;
    transition: color 0.3s;
}

/* Criteria list */
.strength-rules {
    display: flex;
    flex-direction: column;
    gap: 0.28rem;
    list-style: none;
    margin: 0; padding: 0;
}
.rule-item {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.72rem;
    color: #9ca3af;
    transition: color 0.2s;
}
.rule-item.rule-met { color: #16a34a; }
.rule-dot {
    width: 14px; height: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rule-dot svg { width: 12px; height: 12px; }
.rule-item .rule-dot { color: #d1d5db; }
.rule-item.rule-met .rule-dot { color: #16a34a; }

/* ── Match indicators ── */
.match-ok,
.match-err {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.7rem;
    font-weight: 600;
    margin-top: 0.2rem;
    padding-left: 0.1rem;
}
.match-ok  { color: #16a34a; }
.match-err { color: #ef4444; }
.match-ok svg, .match-err svg { width: 12px; height: 12px; flex-shrink: 0; }

/* ── Submit button ── */
.rp-btn {
    width:100%; padding:0.72rem; border-radius:8px; border:none;
    background:linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    color:#fff; font-size:0.82rem; font-weight:700; letter-spacing:0.06em;
    cursor:not-allowed; opacity:0.65;
    transition:transform 0.15s, box-shadow 0.15s, filter 0.15s, background 0.4s, opacity 0.3s;
    box-shadow:0 2px 8px rgba(0,0,0,0.12);
    display:flex; align-items:center; justify-content:center; min-height:42px;
}
/* Unlocked state */
.rp-btn--ready {
    background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
    box-shadow: 0 4px 18px rgba(79,70,229,0.35);
    cursor: pointer;
    opacity: 1;
}
.rp-btn--ready:hover {
    transform:translateY(-1px);
    box-shadow:0 6px 24px rgba(79,70,229,0.45);
    filter:brightness(1.06);
}
.rp-btn--ready:active { transform:translateY(0); }
.spin { width:18px; height:18px; animation:spin 1s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>
