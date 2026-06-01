<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    email_notifications: user.email_notifications ?? true,
    database_notifications: user.database_notifications ?? true,
});

const updatePreferences = () => {
    form.patch(route('profile.preferences.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Notification Preferences</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage how you receive notifications and emails from the application.
            </p>
        </header>

        <form @submit.prevent="updatePreferences" class="mt-6 space-y-6">
            <div>
                <label class="flex items-center">
                    <input type="checkbox" v-model="form.email_notifications" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Receive Email Notifications</span>
                </label>
                <InputError class="mt-2" :message="form.errors.email_notifications" />
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" v-model="form.database_notifications" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Receive In-App Notifications</span>
                </label>
                <InputError class="mt-2" :message="form.errors.database_notifications" />
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
