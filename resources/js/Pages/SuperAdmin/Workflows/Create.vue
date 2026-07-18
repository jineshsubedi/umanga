<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    companies: Array,
    roles: Array,
    users: Array,
});

const form = useForm({
    company_id: '',
    module: 'procurement',
    name: '',
    description: '',
    steps: [
        { step_title: '', approver_type: 'role', approver_value: '' }
    ]
});

const addStep = () => {
    form.steps.push({ step_title: '', approver_type: 'role', approver_value: '' });
};

const removeStep = (index) => {
    form.steps.splice(index, 1);
};

const submit = () => {
    form.post(route('super-admin.workflows.store'));
};
</script>

<template>
    <Head title="Create Workflow" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Workflow</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                                    <select v-model="form.company_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="" disabled>Select Company</option>
                                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Module</label>
                                    <select v-model="form.module" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="procurement">Procurement</option>
                                        <option value="leave">Leave Management</option>
                                        <option value="memo">Memo System</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Workflow Name</label>
                                    <input type="text" v-model="form.name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. Standard Procurement Approval">
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                </div>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 my-6">

                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Approval Steps</h3>
                                    <button type="button" @click="addStep" class="text-sm px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                        + Add Step
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div v-for="(step, index) in form.steps" :key="index" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg flex items-start gap-4">
                                        <div class="pt-2 font-bold text-gray-500">Step {{ index + 1 }}</div>
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-xs text-gray-500">Step Title <span class="text-gray-400">(for PDF)</span></label>
                                                <input type="text" v-model="step.step_title" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. Checker, Verifier">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500">Approver Type</label>
                                                <select v-model="step.approver_type" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="role">By Role</option>
                                                    <option value="specific_user">Specific User</option>
                                                    <option value="department_head">Department Head</option>
                                                </select>
                                            </div>
                                            <div v-if="step.approver_type === 'role'">
                                                <label class="block text-xs text-gray-500">Role</label>
                                                <select v-model="step.approver_value" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="" disabled>Select Role</option>
                                                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                                                </select>
                                                <InputError :message="form.errors[`steps.${index}.approver_value`]" class="mt-2" />
                                            </div>
                                            <div v-if="step.approver_type === 'specific_user'">
                                                <label class="block text-xs text-gray-500">User</label>
                                                <select v-model="step.approver_value" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="" disabled>Select User</option>
                                                    <option v-for="u in users.filter(usr => usr.company_id === form.company_id || !form.company_id)" :key="u.id" :value="u.id">{{ u.name }}</option>
                                                </select>
                                                <InputError :message="form.errors[`steps.${index}.approver_value`]" class="mt-2" />
                                            </div>
                                        </div>
                                        <button type="button" @click="removeStep(index)" v-if="form.steps.length > 1" class="text-red-500 hover:text-red-700 mt-6">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">
                                    Save Workflow
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
