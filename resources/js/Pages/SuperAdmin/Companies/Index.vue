<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ companies: Array });

const toggle = (company) => {
    router.patch(route('super-admin.companies.toggle-status', company.id));
};

const destroy = (company) => {
    if (confirm(`Are you sure you want to PERMANENTLY delete ${company.name} and all its users? This action cannot be undone.`)) {
        router.delete(route('super-admin.companies.destroy', company.id));
    }
};
</script>

<template>
    <Head title="All Companies" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">All Companies</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Companies</p>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ companies.length }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ companies.filter(c => c.status === 'active').length }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Inactive</p>
                        <p class="text-3xl font-bold text-red-500 mt-1">{{ companies.filter(c => c.status !== 'active').length }}</p>
                    </div>
                </div>

                <!-- Table -->
                <div class="flex items-center justify-between w-full">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ companies.length }} companies registered</span>
                    <div class="flex items-center gap-3">
                        <Link :href="route('super-admin.companies.create')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                            Add Company
                        </Link>
                    </div> 
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Users</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Memos</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Registered</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="company in companies" :key="company.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ company.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ company.email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ company.users_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ company.meeting_memos_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="company.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ company.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ company.created_at }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <Link :href="route('super-admin.companies.show', company.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</Link>
                                    <Link :href="route('super-admin.companies.edit', company.id)" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</Link>
                                    <button @click="toggle(company)"
                                        class="text-sm font-medium"
                                        :class="company.status === 'active' ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900'">
                                        {{ company.status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="destroy(company)" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="companies.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">No companies registered yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
