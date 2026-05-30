<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    _method: 'patch',
    name: user.name,
    email: user.email,
    signature: null,
});

const previewUrl = ref(null);
const signatureInput = ref(null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    form.signature = file;
    
    if (file) {
        previewUrl.value = URL.createObjectURL(file);
    } else {
        previewUrl.value = null;
    }
};

const updateProfileInformation = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            previewUrl.value = null;
            form.reset('signature');
            if (signatureInput.value) {
                signatureInput.value.value = null;
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="updateProfileInformation" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="['staff', 'manager', 'admin'].includes(user.role)">
                <InputLabel for="signature" value="Signature (For Memo Approval)" />

                <input
                    id="signature"
                    ref="signatureInput"
                    type="file"
                    class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-md file:border-0
                           file:text-sm file:font-semibold
                           file:bg-indigo-50 file:text-indigo-700
                           hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400"
                    @change="handleFileChange"
                    accept="image/*"
                />

                <div v-if="previewUrl" class="mt-3">
                    <p class="text-sm text-gray-500 mb-2">New Signature Preview:</p>
                    <img :src="previewUrl" alt="New Signature Preview" class="h-16 object-contain border border-gray-200 dark:border-gray-700 rounded bg-white p-1" />
                </div>
                <div v-else-if="$page.props.auth.user.signature_path" class="mt-3">
                    <p class="text-sm text-gray-500 mb-2">Current Signature:</p>
                    <img :src="`/storage/${$page.props.auth.user.signature_path}`" alt="Signature" class="h-16 object-contain border border-gray-200 dark:border-gray-700 rounded bg-white p-1" />
                </div>

                <InputError class="mt-2" :message="form.errors.signature" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

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
