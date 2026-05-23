<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ company: Object, stats: Object });

const roleBadge = (role) => ({
    admin:   'bg-blue-100 text-blue-700',
    manager: 'bg-green-100 text-green-700',
    staff:  'bg-orange-100 text-orange-700',
}[role] ?? 'bg-gray-100 text-gray-700');
</script>

<template>
    <Head :title="company.name" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('super-admin.companies.index')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ company.name }}</h2>
                    <p class="text-sm text-gray-500">{{ company.email }}</p>
                </div>
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="company.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ company.status }}
                </span>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div v-for="(val, key) in stats" :key="key" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-700 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ val }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 capitalize mt-1">{{ key.replace(/_/g,' ') }}</p>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">Users ({{ company.users.length }})</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Memos</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="user in company.users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ user.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium" :class="roleBadge(user.role)">{{ user.role }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="user.role === 'staff'" class="text-sm font-medium text-gray-600">
                                        {{ user.meeting_memos_count }}
                                    </span>
                                    <span v-else class="text-sm text-gray-400 dark:text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="user.status === 'active' ? 'bg-green-100 dark:bg-green-800 text-white' : 'bg-red-100 text-white'">
                                        {{ user.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
