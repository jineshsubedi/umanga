<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ company: Object, stats: Object });

const roleBadge = (role) => ({
    admin:   'bg-blue-100 text-blue-700',
    manager: 'bg-green-100 text-green-700',
    client:  'bg-orange-100 text-orange-700',
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
                    <div v-for="(val, key) in stats" :key="key" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ val }}</p>
                        <p class="text-xs text-gray-500 capitalize mt-1">{{ key.replace(/_/g,' ') }}</p>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Users ({{ company.users.length }})</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr v-for="user in company.users" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ user.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium" :class="roleBadge(user.role)">{{ user.role }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
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
