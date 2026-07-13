<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const props = defineProps({
    app_name: String,
    mail_host: String,
    mail_port: String,
    mail_username: String,
    mail_password: String,
    mail_encryption: String,
    mail_from_address: String,
    mail_from_name: String,
    gmail_host: String,
    gmail_port: String,
    gmail_username: String,
    gmail_password: String,
    gmail_encryption: String,
});

const form = useForm({
    app_name: props.app_name,
    app_logo: null,
    mail_host: props.mail_host,
    mail_port: props.mail_port,
    mail_username: props.mail_username,
    mail_password: props.mail_password,
    mail_encryption: props.mail_encryption,
    mail_from_address: props.mail_from_address,
    mail_from_name: props.mail_from_name,
    gmail_host: props.gmail_host,
    gmail_port: props.gmail_port,
    gmail_username: props.gmail_username,
    gmail_password: props.gmail_password,
    gmail_encryption: props.gmail_encryption,
});

const logoPreview = ref(null);

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.app_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('super-admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="App Settings" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">App Settings</h2>
        </template>

        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
            </div>
            <div class="max-w-4xl mx-auto relative z-10">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">App Settings</h1>
                </div>
                <p class="text-purple-200 text-sm mt-1">Configure application name, logo, and email server settings.</p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10 pb-12">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- App Identity Card -->
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-[#7c3aed] rounded-full"></span>
                            Application Identity
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <InputLabel for="app_name" value="Application Name" class="font-semibold text-gray-700 dark:text-gray-300" />
                            <TextInput id="app_name" type="text"
                                class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3"
                                v-model="form.app_name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.app_name" />
                        </div>

                        <div>
                            <InputLabel for="app_logo" value="Application Logo" class="font-semibold text-gray-700 dark:text-gray-300" />
                            <div class="mt-3 flex items-center gap-5">
                                <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center overflow-hidden shadow-md border-2 border-white dark:border-gray-700 ring-2 ring-purple-200 dark:ring-purple-900/50">
                                    <img v-if="logoPreview" :src="logoPreview" class="h-full w-full object-contain" />
                                    <img v-else-if="$page.props.app_settings?.app_logo" :src="'/storage/' + $page.props.app_settings.app_logo" class="h-full w-full object-contain" />
                                    <svg v-else class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="app_logo" @change="handleLogoChange" accept="image/*"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400 
                                               file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold 
                                               file:bg-purple-50 file:text-[#7c3aed] hover:file:bg-purple-100 dark:file:bg-purple-900/30 dark:file:text-purple-400 file:cursor-pointer file:transition-colors" />
                                    <p class="text-xs text-gray-400 mt-2">PNG, JPG, SVG. Max 2MB.</p>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.app_logo" />
                        </div>
                    </div>
                </div>

                <!-- Mail Configuration Card -->
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-blue-500 rounded-full"></span>
                            Mail Configuration
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SMTP settings for outgoing email delivery.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div v-for="field in [
                                { id: 'mail_host', label: 'Mail Host', type: 'text', model: 'mail_host' },
                                { id: 'mail_port', label: 'Mail Port', type: 'text', model: 'mail_port' },
                                { id: 'mail_username', label: 'Mail Username', type: 'text', model: 'mail_username' },
                                { id: 'mail_password', label: 'Mail Password', type: 'password', model: 'mail_password' },
                                { id: 'mail_encryption', label: 'Encryption (tls / ssl)', type: 'text', model: 'mail_encryption' },
                                { id: 'mail_from_address', label: 'From Address', type: 'email', model: 'mail_from_address' },
                            ]" :key="field.id">
                                <div>
                                    <InputLabel :for="field.id" :value="field.label" class="font-semibold text-gray-700 dark:text-gray-300" />
                                    <TextInput :id="field.id" :type="field.type"
                                        class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3"
                                        v-model="form[field.model]" />
                                    <InputError class="mt-2" :message="form.errors[field.model]" />
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="mail_from_name" value="From Name" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <TextInput id="mail_from_name" type="text"
                                    class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3"
                                    v-model="form.mail_from_name" />
                                <InputError class="mt-2" :message="form.errors.mail_from_name" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gmail Configuration Card -->
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-red-500 rounded-full"></span>
                            Gmail Routing Configuration
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SMTP settings for routing emails to @gmail.com addresses.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div v-for="field in [
                                { id: 'gmail_host', label: 'Gmail Host', type: 'text', model: 'gmail_host' },
                                { id: 'gmail_port', label: 'Gmail Port', type: 'text', model: 'gmail_port' },
                                { id: 'gmail_username', label: 'Gmail Username', type: 'text', model: 'gmail_username' },
                                { id: 'gmail_password', label: 'Gmail Password', type: 'password', model: 'gmail_password' },
                                { id: 'gmail_encryption', label: 'Encryption (tls / ssl)', type: 'text', model: 'gmail_encryption' },
                            ]" :key="field.id">
                                <div>
                                    <InputLabel :for="field.id" :value="field.label" class="font-semibold text-gray-700 dark:text-gray-300" />
                                    <TextInput :id="field.id" :type="field.type"
                                        class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3"
                                        v-model="form[field.model]" />
                                    <InputError class="mt-2" :message="form.errors[field.model]" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex items-center justify-end gap-4 pt-2">
                    <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0 scale-95" leave-active-class="transition ease-in-out duration-200" leave-to-class="opacity-0">
                        <p v-if="form.recentlySuccessful" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Settings saved!
                        </p>
                    </Transition>
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ form.processing ? 'Saving...' : 'Save Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
