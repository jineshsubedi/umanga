<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const passwordInput        = ref(null);
const currentPasswordInput = ref(null);
const showCurrent          = ref(false);
const showNew              = ref(false);
const showConfirm          = ref(false);

const form = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
});

/* ── Password strength rules (matches backend: min 8 + letters + mixedCase + numbers + symbols) ── */
const rules = computed(() => [
    { label: 'At least 8 characters',        met: form.password.length >= 8 },
    { label: 'One uppercase letter (A–Z)',    met: /[A-Z]/.test(form.password) },
    { label: 'One lowercase letter (a–z)',    met: /[a-z]/.test(form.password) },
    { label: 'One number (0–9)',              met: /[0-9]/.test(form.password) },
    { label: 'One special character (!@#…)',  met: /[^A-Za-z0-9]/.test(form.password) },
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

const isPasswordValid = computed(() =>
    strengthScore.value === 5 && passwordsMatch.value
);

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showSuccessToast('Password updated successfully');
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Update Password</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">

            <!-- Current Password -->
            <div>
                <InputLabel for="current_password" value="Current Password" />
                <div class="pwd-wrap">
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        :type="showCurrent ? 'text' : 'password'"
                        class="mt-1 block w-full"
                        autocomplete="current-password"
                    />
                    <button type="button" class="pwd-eye" @click="showCurrent = !showCurrent" tabindex="-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path v-if="!showCurrent" stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <!-- New Password -->
            <div>
                <InputLabel for="password" value="New Password" />
                <div class="pwd-wrap">
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        :type="showNew ? 'text' : 'password'"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />
                    <button type="button" class="pwd-eye" @click="showNew = !showNew" tabindex="-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path v-if="!showNew" stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <InputError :message="form.errors.password" class="mt-2" />

                <!-- ── Strength Meter ── -->
                <div v-if="form.password.length > 0" class="strength-wrap">
                    <!-- Progress bar -->
                    <div class="strength-bar-track">
                        <div
                            class="strength-bar-fill"
                            :style="{ width: (strengthScore / 5 * 100) + '%', background: strengthColor }"
                        ></div>
                    </div>
                    <span class="strength-label" :style="{ color: strengthColor }">
                        {{ strengthLabel }}
                    </span>

                    <!-- Criteria checklist -->
                    <ul class="strength-rules">
                        <li
                            v-for="rule in rules"
                            :key="rule.label"
                            class="rule-item"
                            :class="{ 'rule-met': rule.met }"
                        >
                            <span class="rule-icon">
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
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="pwd-wrap">
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        class="mt-1 block w-full"
                        :class="{
                            'border-green-400 dark:border-green-500': passwordsMatch,
                            'border-red-400  dark:border-red-500':   passwordMismatch,
                        }"
                        autocomplete="new-password"
                    />
                    <button type="button" class="pwd-eye" @click="showConfirm = !showConfirm" tabindex="-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path v-if="!showConfirm" stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>

                <!-- Match indicator -->
                <p v-if="passwordsMatch" class="match-ok mt-1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Passwords match
                </p>
                <p v-else-if="passwordMismatch" class="match-err mt-1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Passwords do not match
                </p>

                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing || !isPasswordValid">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                </Transition>
            </div>

        </form>
    </section>
</template>

<style scoped>
/* ── Show/hide password toggle ── */
.pwd-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.pwd-eye {
    position: absolute;
    right: 0.75rem;
    display: flex;
    align-items: center;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    color: #9ca3af;
    transition: color 0.2s;
}
.pwd-eye:hover { color: #4f46e5; }
.pwd-eye svg { width: 16px; height: 16px; }

/* ── Strength meter container ── */
.strength-wrap {
    margin-top: 0.6rem;
    padding: 0.75rem 0.9rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
}
.dark .strength-wrap {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
}

/* Bar */
.strength-bar-track {
    height: 5px;
    background: #e5e7eb;
    border-radius: 99px;
    overflow: hidden;
    margin-bottom: 0.4rem;
}
.dark .strength-bar-track { background: rgba(255,255,255,0.12); }
.strength-bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 0.35s ease, background 0.35s ease;
}

/* Strength label */
.strength-label {
    display: block;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 0.55rem;
    transition: color 0.3s;
}

/* Criteria list */
.strength-rules {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    list-style: none;
    margin: 0; padding: 0;
}
.rule-item {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.73rem;
    color: #9ca3af;
    transition: color 0.2s;
}
.rule-item.rule-met { color: #16a34a; }
.dark .rule-item { color: #6b7280; }
.dark .rule-item.rule-met { color: #4ade80; }
.rule-icon {
    width: 14px; height: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: #d1d5db;
}
.rule-item.rule-met .rule-icon { color: #16a34a; }
.dark .rule-item.rule-met .rule-icon { color: #4ade80; }
.rule-icon svg { width: 12px; height: 12px; }

/* ── Match indicators ── */
.match-ok,
.match-err {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 600;
}
.match-ok  { color: #16a34a; }
.match-err { color: #ef4444; }
.match-ok svg, .match-err svg { width: 12px; height: 12px; flex-shrink: 0; }
</style>
