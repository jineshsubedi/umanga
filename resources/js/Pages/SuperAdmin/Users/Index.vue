<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Array,
    companies: Array,
    filters: Object,
});

const form = ref({
    company_id: props.filters?.company_id ?? '',
    role:       props.filters?.role ?? '',
    status:     props.filters?.status ?? '',
    search:     props.filters?.search ?? '',
});

let searchTimeout = null;

const applyFilters = () => {
    router.get(route('super-admin.users.index'), form.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch(form, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
}, { deep: true });

const resetFilters = () => {
    form.value = { company_id: '', role: '', status: '', search: '' };
    router.get(route('super-admin.users.index'));
};

const toggleStatus = (user) => {
    if (confirm(`Are you sure you want to ${user.status === 'active' ? 'deactivate' : 'activate'} ${user.name}?`)) {
        router.patch(route('super-admin.users.toggle-status', user.id), {}, {
            preserveScroll: true,
            preserveState: true,
        });
    }
};

const destroy = (user) => {
    if (confirm(`Are you sure you want to PERMANENTLY delete ${user.name}? This action cannot be undone.`)) {
        router.delete(route('super-admin.users.destroy', user.id), {
            preserveScroll: true,
            preserveState: true,
        });
    }
};
</script>

<template>
    <Head title="System Users" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">System Users Overview</h2>
                <div class="flex items-center gap-3">
                    <Link :href="route('super-admin.users.bulk-create')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Bulk Add Users
                    </Link>
                    <Link :href="route('super-admin.users.create')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                        Add User
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Summary -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mb-3 font-bold text-lg">
                            {{ users.length }}
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ users.length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Filtered Users</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mb-3 font-bold text-lg">
                            {{ users.filter(u => u.role === 'admin').length }}
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ users.filter(u => u.role === 'admin').length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Company Admins</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mb-3 font-bold text-lg">
                            {{ users.filter(u => u.role === 'manager').length }}
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ users.filter(u => u.role === 'manager').length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Managers</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-full flex items-center justify-center mb-3 font-bold text-lg">
                            {{ users.filter(u => u.role === 'staff').length }}
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ users.filter(u => u.role === 'staff').length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Staffs</p>
                    </div>
                </div>

                <!-- Filters Bar -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4 flex-1">
                        <!-- Search input -->
                        <div class="min-w-[220px] flex-1">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search User</label>
                            <input v-model="form.search" type="text" placeholder="Name or email..." class="w-full text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>

                        <!-- Filter by Company -->
                        <div class="min-w-[200px]">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Company</label>
                            <select v-model="form.company_id" class="w-full text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Companies</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Filter by Role -->
                        <div class="min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Role</label>
                            <select v-model="form.role" class="w-full text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>

                        <!-- Filter by Status -->
                        <div class="min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                            <select v-model="form.status" class="w-full text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-end self-end">
                        <button @click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Memos</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            {{ user.name?.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white text-sm">{{ user.name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ user.company?.name || 'No Company' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                        :class="{
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': user.role === 'admin',
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': user.role === 'manager',
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400': user.role === 'staff'
                                        }">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="user.role === 'staff'" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
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
                                    <Link :href="route('super-admin.users.edit', user.id)" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</Link>
                                    <button @click="toggleStatus(user)" class="text-sm font-medium transition-colors"
                                        :class="user.status === 'active' ? 'text-orange-600 dark:text-orange-400 hover:text-orange-800' : 'text-green-600 dark:text-green-400 hover:text-green-800'">
                                        {{ user.status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="destroy(user)" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">No users found matching your filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
