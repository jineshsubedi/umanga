<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ users: Array });

const roleBadge = (role) => ({
    admin:   'bg-blue-100 text-blue-700',
    manager: 'bg-green-100 text-green-700',
    staff:  'bg-orange-100 text-orange-700',
}[role] ?? 'bg-gray-100 text-gray-700');

const toggleStatus = (user) => {
    if (confirm(`Are you sure you want to ${user.status === 'active' ? 'deactivate' : 'activate'} ${user.name}?`)) {
        router.patch(route('admin.users.toggle-status', user.id), {}, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Manage Users" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Users</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ users.filter(u=>u.role==='admin').length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Admins</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ users.filter(u=>u.role==='manager').length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Managers</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ users.filter(u=>u.role==='staff').length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Staffs</p>
                    </div>
                </div>
                <Link :href="route('admin.users.create')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add User
                </Link>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Designation / Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Memos</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ user.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span v-if="user.designation" class="text-sm font-medium text-gray-900 dark:text-white">{{ user.designation }}</span>
                                        <span class="inline-flex w-fit px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                            :class="{
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': user.role === 'admin',
                                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': user.role === 'manager',
                                                'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400': user.role === 'staff'
                                            }">
                                            {{ user.role }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="user.role === 'staff'" class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                        {{ user.meeting_memos_count }}
                                    </span>
                                    <span v-else class="text-gray-400 dark:text-gray-600 text-sm">-</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="user.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <Link :href="route('admin.users.edit', user.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium">Edit</Link>
                                    <button @click="toggleStatus(user)" class="text-sm font-medium transition-colors"
                                        :class="user.status === 'active' ? 'text-red-600 dark:text-red-400 hover:text-red-900' : 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-900'">
                                        {{ user.status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">No users yet. Create your first user.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
