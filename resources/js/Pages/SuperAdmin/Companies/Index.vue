<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ companies: Array });

const toggle = (company) => {
    router.patch(route('super-admin.companies.toggle-status', company.id));
};
</script>

<template>
    <Head title="All Companies" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">All Companies</h2>
                <!-- <span class="text-sm text-gray-500">{{ companies.length }} companies registered</span> -->
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                        <p class="text-sm text-gray-500">Total Companies</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ companies.length }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                        <p class="text-sm text-gray-500">Active</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ companies.filter(c => c.status === 'active').length }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                        <p class="text-sm text-gray-500">Inactive</p>
                        <p class="text-3xl font-bold text-red-500 mt-1">{{ companies.filter(c => c.status !== 'active').length }}</p>
                    </div>
                </div>

                <!-- Table -->
                 <span class="text-sm text-gray-500">{{ companies.length }} companies registered</span>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Users</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Minutes</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Registered</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr v-for="company in companies" :key="company.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ company.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ company.email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ company.users_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ company.meeting_minutes_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="company.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ company.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ company.created_at }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <Link :href="route('super-admin.companies.show', company.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</Link>
                                    <button @click="toggle(company)"
                                        class="text-sm font-medium"
                                        :class="company.status === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'">
                                        {{ company.status === 'active' ? 'Deactivate' : 'Activate' }}
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
