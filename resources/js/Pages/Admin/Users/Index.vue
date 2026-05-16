<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ users: Array });

const roleBadge = (role) => ({
    admin:   'bg-blue-100 text-blue-700',
    manager: 'bg-green-100 text-green-700',
    client:  'bg-orange-100 text-orange-700',
}[role] ?? 'bg-gray-100 text-gray-700');

const deactivate = (user) => {
    if (confirm(`Deactivate ${user.name}?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};
</script>

<template>
    <Head title="Manage Users" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Manage Users</h2>
                <Link :href="route('admin.users.create')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add User
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ users.filter(u=>u.role==='admin').length }}</p>
                        <p class="text-xs text-gray-500 mt-1">Admins</p>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ users.filter(u=>u.role==='manager').length }}</p>
                        <p class="text-xs text-gray-500 mt-1">Managers</p>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                        <p class="text-2xl font-bold text-orange-600">{{ users.filter(u=>u.role==='client').length }}</p>
                        <p class="text-xs text-gray-500 mt-1">Clients</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ user.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="roleBadge(user.role)">{{ user.role }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <Link :href="route('admin.users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</Link>
                                    <button v-if="user.status === 'active'" @click="deactivate(user)" class="text-red-600 hover:text-red-900 text-sm font-medium">Deactivate</button>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">No users yet. Create your first user.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
