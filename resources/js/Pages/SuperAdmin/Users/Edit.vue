<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    companies: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    company_id: props.user.company_id || '',
    role: props.user.role || 'staff',
    status: props.user.status || 'active',
});

const submit = () => {
    form.put(route('super-admin.users.update', props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('super-admin.users.index')" class="text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Edit User</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Full Name" />
                            <TextInput id="name" type="text" class="mt-1 block w-full dark:text-gray-100" v-model="form.name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Address" />
                            <TextInput id="email" type="email" class="mt-1 block w-full dark:text-gray-100" v-model="form.email" required />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="company_id" value="Company" />
                            <select id="company_id" v-model="form.company_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="" disabled>Select a company</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.company_id" />
                        </div>

                        <div>
                            <InputLabel for="role" value="Role" />
                            <select id="role" v-model="form.role" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="staff">Staff</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Company Admin</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.role" />
                        </div>

                        <div>
                            <InputLabel for="status" value="Status" />
                            <select id="status" v-model="form.status" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update User
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
