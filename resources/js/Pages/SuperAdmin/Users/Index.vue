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
        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-1/4 -left-1/4 w-2/3 h-2/3 rounded-full bg-cyan-400/10 blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
            </div>
            <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">System Users</h1>
                        <p class="mt-1 text-purple-200 text-sm">Overview and management of all users across all companies.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link :href="route('super-admin.users.bulk-create')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-xl border border-white/20 backdrop-blur-md transition-all hover:-translate-y-0.5 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Bulk Add Users
                    </Link>
                    <Link :href="route('super-admin.users.create')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5 shadow-lg border border-white/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add User
                    </Link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10 pb-12 space-y-6">
            <!-- Stats Summary -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ users.length }}</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Filtered Users</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ users.filter(u => u.role === 'admin').length }}</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Admins</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ users.filter(u => u.role === 'manager').length }}</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Managers</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ users.filter(u => u.role === 'staff').length }}</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Staffs</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-wrap items-end justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4 flex-1">
                    <!-- Search input -->
                    <div class="min-w-[220px] flex-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Search User</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input v-model="form.search" type="text" placeholder="Name or email..." class="pl-10 w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]" />
                        </div>
                    </div>

                    <!-- Filter by Company -->
                    <div class="min-w-[200px]">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Company</label>
                        <select v-model="form.company_id" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                            <option value="">All Companies</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">
                                {{ company.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Filter by Role -->
                    <div class="min-w-[150px]">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Role</label>
                        <select v-model="form.role" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>

                    <!-- Filter by Status -->
                    <div class="min-w-[150px]">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                        <select v-model="form.status" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-end">
                    <button @click="resetFilters" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        Reset
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Designation / Role</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Memos</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-purple-50/50 dark:hover:bg-purple-900/10 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#7c3aed] to-[#b026ff] text-white flex items-center justify-center font-bold text-sm shadow group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                            {{ user.name?.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-sm">{{ user.name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ user.company?.name || 'No Company' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1.5 items-start">
                                        <span v-if="user.designation" class="text-sm font-bold text-gray-900 dark:text-white">{{ user.designation }}</span>
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold capitalize border bg-gradient-to-br from-[#7c3aed] to-[#b026ff] text-white border-transparent shadow-sm">
                                            {{ user.role }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="user.role === 'staff'" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ user.meeting_memos_count }}
                                    </span>
                                    <span v-else class="text-gray-400 dark:text-gray-600 text-sm font-medium">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border"
                                        :class="user.status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800'">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="user.status === 'active' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    <Link :href="route('super-admin.users.edit', user.id)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-[#7c3aed] dark:text-[#a78bfa] bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/40 rounded-lg border border-purple-100 dark:border-purple-800 transition-colors">
                                        Edit
                                    </Link>
                                    <button @click="toggleStatus(user)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors"
                                        :class="user.status === 'active' ? 'text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 border-orange-100 dark:border-orange-800' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 border-emerald-100 dark:border-emerald-800'">
                                        {{ user.status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="destroy(user)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg border border-red-100 dark:border-red-800 transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <p class="font-bold text-sm">No users found matching your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
