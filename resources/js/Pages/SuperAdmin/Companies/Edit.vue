<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    company: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.company.name || '',
    email: props.company.email || '',
    phone: props.company.phone || '',
    address: props.company.address || '',
    status: props.company.status || 'active',
    departments: props.company.departments || [],
    modules: props.company.modules || [],
});

const newDepartment = ref('');

const addDepartment = () => {
    const dept = newDepartment.value.trim();
    if (dept && !form.departments.includes(dept)) {
        form.departments.push(dept);
        newDepartment.value = '';
    }
};

const removeDepartment = (index) => {
    form.departments.splice(index, 1);
};

const submit = () => {
    form.put(route('super-admin.companies.update', props.company.id));
};
</script>

<template>
    <Head title="Edit Company" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('super-admin.companies.index')" class="text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Edit Company</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Company Name" />
                            <TextInput id="name" type="text" class="mt-1 block w-full dark:text-gray-100" v-model="form.name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Address" />
                            <TextInput id="email" type="email" class="mt-1 block w-full dark:text-gray-100" v-model="form.email" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="phone" value="Phone Number" />
                            <TextInput id="phone" type="text" class="mt-1 block w-full dark:text-gray-100" v-model="form.phone" />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>

                        <div>
                            <InputLabel for="address" value="Address" />
                            <TextInput id="address" type="text" class="mt-1 block w-full dark:text-gray-100" v-model="form.address" />
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>

                        <div>
                            <InputLabel for="status" value="Status" />
                            <select id="status" v-model="form.status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>



                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-300">Module Access</h3>
                            
                            <div class="flex items-center gap-2">
                                <input id="module_memo" type="checkbox" value="memo" v-model="form.modules" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                                <InputLabel for="module_memo" value="Memo Module" class="mb-0" />
                            </div>

                            <div class="flex items-center gap-2">
                                <input id="module_procurement" type="checkbox" value="procurement" v-model="form.modules" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                                <InputLabel for="module_procurement" value="Procurement Module" class="mb-0" />
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-300">Departments (Optional)</h3>
                            <div class="flex items-center gap-2">
                                <TextInput id="new_department" type="text" class="flex-1 dark:text-gray-100" v-model="newDepartment" placeholder="e.g. Human Resources, IT" @keydown.enter.prevent="addDepartment" />
                                <button type="button" @click="addDepartment" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors">Add</button>
                            </div>
                            <div v-if="form.departments.length > 0" class="flex flex-wrap gap-2 mt-2">
                                <span v-for="(dept, index) in form.departments" :key="index" class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg text-sm border border-indigo-200 dark:border-indigo-800">
                                    {{ dept }}
                                    <button type="button" @click="removeDepartment(index)" class="text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-200 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            </div>
                            <InputError class="mt-2" :message="form.errors.departments" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update Company
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
