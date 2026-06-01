<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
        onSuccess: () => {
            // Optional: Show success toast handled globally by flash messages
        },
    });
};
</script>

<template>
    <Head title="App Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                App Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        
                        <form @submit.prevent="submit" class="max-w-xl space-y-6">
                            
                            <div>
                                <InputLabel for="app_name" value="Application Name" />
                                <TextInput
                                    id="app_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.app_name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.app_name" />
                            </div>

                            <div>
                                <InputLabel for="app_logo" value="Application Logo" />
                                
                                <div class="mt-2 flex items-center gap-4">
                                    <div class="h-20 w-20 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden shadow-sm border border-gray-200 dark:border-gray-600">
                                        <img v-if="logoPreview" :src="logoPreview" class="h-full w-full object-contain" />
                                        <img v-else-if="$page.props.app_settings?.app_logo" :src="'/storage/' + $page.props.app_settings.app_logo" class="h-full w-full object-contain" />
                                        <svg v-else class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input
                                        type="file"
                                        id="app_logo"
                                        @change="handleLogoChange"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.app_logo" />
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 my-6" />
                            
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mail Configuration (Admin Settings)</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="mail_host" value="Mail Host" />
                                    <TextInput id="mail_host" type="text" class="mt-1 block w-full" v-model="form.mail_host" />
                                    <InputError class="mt-2" :message="form.errors.mail_host" />
                                </div>

                                <div>
                                    <InputLabel for="mail_port" value="Mail Port" />
                                    <TextInput id="mail_port" type="text" class="mt-1 block w-full" v-model="form.mail_port" />
                                    <InputError class="mt-2" :message="form.errors.mail_port" />
                                </div>

                                <div>
                                    <InputLabel for="mail_username" value="Mail Username" />
                                    <TextInput id="mail_username" type="text" class="mt-1 block w-full" v-model="form.mail_username" />
                                    <InputError class="mt-2" :message="form.errors.mail_username" />
                                </div>

                                <div>
                                    <InputLabel for="mail_password" value="Mail Password" />
                                    <TextInput id="mail_password" type="password" class="mt-1 block w-full" v-model="form.mail_password" />
                                    <InputError class="mt-2" :message="form.errors.mail_password" />
                                </div>

                                <div>
                                    <InputLabel for="mail_encryption" value="Mail Encryption (tls/ssl)" />
                                    <TextInput id="mail_encryption" type="text" class="mt-1 block w-full" v-model="form.mail_encryption" />
                                    <InputError class="mt-2" :message="form.errors.mail_encryption" />
                                </div>

                                <div>
                                    <InputLabel for="mail_from_address" value="Mail From Address" />
                                    <TextInput id="mail_from_address" type="email" class="mt-1 block w-full" v-model="form.mail_from_address" />
                                    <InputError class="mt-2" :message="form.errors.mail_from_address" />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel for="mail_from_name" value="Mail From Name" />
                                    <TextInput id="mail_from_name" type="text" class="mt-1 block w-full" v-model="form.mail_from_name" />
                                    <InputError class="mt-2" :message="form.errors.mail_from_name" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Save Settings</PrimaryButton>

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

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
