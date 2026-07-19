<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Array,
    filters: {
        type: Object,
        default: () => ({ search: '', role: '', status: '', department: '' })
    },
    departments: {
        type: Array,
        default: () => []
    }
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');
const status = ref(props.filters?.status || '');
const department = ref(props.filters?.department || '');

const activeElementId = ref(null);
let filterTimeout = null;

const runFilter = () => {
    const activeEl = document.activeElement;
    if (activeEl && (activeEl.id === 'search-input' || activeEl.id === 'role-select' || activeEl.id === 'status-select' || activeEl.id === 'department-select')) {
        activeElementId.value = activeEl.id;
    } else {
        activeElementId.value = null;
    }

    router.get(route('admin.users.index'), {
        search: search.value,
        role: role.value,
        status: status.value,
        department: department.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['users', 'filters'],
        onSuccess: () => {
            if (activeElementId.value) {
                setTimeout(() => {
                    const el = document.getElementById(activeElementId.value);
                    if (el) {
                        el.focus();
                        if (el.setSelectionRange && (el.type === 'text' || el.type === 'search')) {
                            const valLen = el.value.length;
                            el.setSelectionRange(valLen, valLen);
                        }
                    }
                }, 50);
            }
        }
    });
};

watch([search, role, status, department], () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(runFilter, 300);
});

const clearFilters = () => {
    search.value = '';
    role.value = '';
    status.value = '';
    department.value = '';
};

const toggleStatus = (user) => {
    if (confirm(`Are you sure you want to ${user.status === 'active' ? 'deactivate' : 'activate'} ${user.name}?`)) {
        router.patch(route('admin.users.toggle-status', user.id), {}, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Manage Users" />
    <AuthenticatedLayout>
        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
            </div>
            <div class="max-w-7xl mx-auto relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Manage Users</h1>
                        <p class="mt-1 text-purple-200 text-sm">View, add, and manage company users.</p>
                    </div>
                </div>
                <Link :href="route('admin.users.create')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5 shadow-lg border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add User
                </Link>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10 pb-12 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-5 shadow-lg border border-white/30 dark:border-gray-700 flex items-center gap-4 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ users.filter(u=>u.role==='admin').length }}</p>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Admins</p>
                    </div>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-5 shadow-lg border border-white/30 dark:border-gray-700 flex items-center gap-4 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ users.filter(u=>u.role==='manager').length }}</p>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Managers</p>
                    </div>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-5 shadow-lg border border-white/30 dark:border-gray-700 flex items-center gap-4 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <div class="bg-gradient-to-br from-amber-500 to-orange-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ users.filter(u=>u.role==='staff').length }}</p>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Staffs</p>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl p-5 rounded-2xl shadow-md border border-white/30 dark:border-gray-700 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Name/Email</label>
                    <input type="text" v-model="search" id="search-input" placeholder="Search user..."
                        class="w-full bg-gray-50 dark:bg-gray-950 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:text-white px-4 py-2.5" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Filter Role</label>
                    <select v-model="role" id="role-select"
                        class="w-full bg-gray-50 dark:bg-gray-950 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:text-white px-4 py-2.5">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Filter Status</label>
                    <select v-model="status" id="status-select"
                        class="w-full bg-gray-50 dark:bg-gray-950 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:text-white px-4 py-2.5">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Department</label>
                    <select v-model="department" id="department-select"
                        class="w-full bg-gray-50 dark:bg-gray-950 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:text-white px-4 py-2.5">
                        <option value="">All Departments</option>
                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                    </select>
                </div>
                <div>
                    <button @click="clearFilters" 
                        class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-bold rounded-xl transition-all">
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Designation / Role</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Memos</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-purple-50/50 dark:hover:bg-purple-900/10 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#7c3aed] to-[#b026ff] text-white flex items-center justify-center font-bold text-sm shadow group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1.5 items-start">
                                        <span v-if="user.designation" class="text-sm font-bold text-gray-900 dark:text-white">{{ user.designation }}</span>
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold capitalize border bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800">
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
                                    <Link :href="route('admin.users.edit', user.id)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-[#7c3aed] dark:text-[#a78bfa] bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/40 rounded-lg border border-purple-100 dark:border-purple-800 transition-colors">
                                        Edit
                                    </Link>
                                    <button @click="toggleStatus(user)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors"
                                        :class="user.status === 'active' ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 border-red-100 dark:border-red-800' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 border-emerald-100 dark:border-emerald-800'">
                                        {{ user.status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <p class="font-medium">No users found matching filters.</p>
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
