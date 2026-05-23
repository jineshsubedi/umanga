<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    companies: {
        type: Array,
        required: true
    }
});

const form = useForm({
    company_id: '',
    file: null,
});

const submit = () => {
    form.post(route('super-admin.users.bulk-store'));
};
</script>

<template>
    <Head title="Bulk Create Users" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('super-admin.users.index')" class="text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Bulk Create Users</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Instructions -->
                <div class="bg-indigo-50 dark:bg-indigo-900/30 p-6 rounded-xl border border-indigo-100 dark:border-indigo-800">
                    <h3 class="text-lg font-semibold text-indigo-800 dark:text-indigo-300 mb-2">Instructions</h3>
                    <p class="text-indigo-700 dark:text-indigo-400 text-sm mb-4">
                        Upload a CSV file containing the users you want to add. The file must contain exactly three columns in the following order:
                    </p>
                    <ul class="list-disc list-inside text-sm text-indigo-700 dark:text-indigo-400 space-y-1 mb-4">
                        <li><strong>Name:</strong> The user's full name.</li>
                        <li><strong>Email:</strong> A unique email address.</li>
                        <li><strong>Role:</strong> Must be one of: <code>admin</code>, <code>manager</code>, or <code>staff</code>.</li>
                    </ul>
                    <div class="bg-white dark:bg-gray-900 p-4 rounded border border-indigo-200 dark:border-indigo-700 font-mono text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">
                        Name,Email,Role<br>
                        John Doe,john@example.com,admin<br>
                        Jane Smith,jane@example.com,manager<br>
                        Bob Wilson,bob@example.com,staff
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="company_id" value="Target Company" />
                            <select id="company_id" v-model="form.company_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="" disabled>Select a company</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.company_id" />
                        </div>

                        <div>
                            <InputLabel for="file" value="CSV File" />
                            <input 
                                id="file" 
                                type="file" 
                                accept=".csv,.txt"
                                @input="form.file = $event.target.files[0]"
                                class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                       file:mr-4 file:py-2 file:px-4
                                       file:rounded-md file:border-0
                                       file:text-sm file:font-semibold
                                       file:bg-indigo-50 file:text-indigo-700
                                       hover:file:bg-indigo-100
                                       dark:file:bg-indigo-900 dark:file:text-indigo-300" 
                                required 
                            />
                            <InputError class="mt-2" :message="form.errors.file" />
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="mt-2 w-full">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg text-sm text-blue-800 dark:text-blue-300">
                            <strong>Note:</strong> The default password for all imported users will be <code>password</code>.
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Upload and Create Users
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
