<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import axios from 'axios';

const page = usePage();

// Use computed to always read latest Inertia shared props
const userProps = computed(() => page.props.auth.user);

const form = useForm({
    email_notifications: userProps.value.email_notifications ?? true,
    database_notifications: userProps.value.database_notifications ?? true,
    push_notifications: userProps.value.push_notifications ?? false,
});

// Keep form in sync whenever Inertia refreshes page props after save
watch(userProps, (newUser) => {
    form.email_notifications = newUser.email_notifications ?? true;
    form.database_notifications = newUser.database_notifications ?? true;
    form.push_notifications = newUser.push_notifications ?? false;
}, { deep: true });

const pushError = ref(null);

const urlBase64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
};

const subscribeUser = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        throw new Error('Push notifications not supported in this browser.');
    }
    const vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;
    if (!vapidPublicKey) throw new Error('VAPID public key is not configured.');

    await navigator.serviceWorker.register('/sw.js');
    const registration = await navigator.serviceWorker.ready;

    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
    });

    await axios.post(route('push-subscriptions.store'), subscription.toJSON());
};

const unsubscribeUser = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
    try {
        await navigator.serviceWorker.register('/sw.js');
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        if (subscription) {
            await axios.delete(route('push-subscriptions.destroy'), {
                data: { endpoint: subscription.endpoint },
            });
            await subscription.unsubscribe();
        }
    } catch (e) {
        console.error('Push unsubscription failed', e);
    }
};

const updatePreferences = async () => {
    pushError.value = null;

    if (form.push_notifications) {
        if (Notification.permission === 'denied') {
            pushError.value = 'Push notifications are blocked in your browser. Please allow them in site settings, then try again.';
            form.push_notifications = false;
        } else {
            // Request permission if not already granted
            const permission = Notification.permission === 'granted'
                ? 'granted'
                : await Notification.requestPermission();

            if (permission === 'granted') {
                try {
                    await subscribeUser();
                } catch (e) {
                    console.error('Push subscription failed:', e);
                    pushError.value = 'Could not subscribe to push notifications. Your other preferences will still be saved.';
                    form.push_notifications = false;
                }
            } else {
                // User dismissed the prompt without granting
                form.push_notifications = false;
            }
        }
    } else {
        await unsubscribeUser();
    }

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

            <div>
                <label class="flex items-center">
                    <input type="checkbox" v-model="form.push_notifications" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Receive Push Notifications (Desktop/Mobile)</span>
                </label>
                <InputError class="mt-2" :message="form.errors.push_notifications" />
                <p v-if="pushError" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ pushError }}</p>
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
