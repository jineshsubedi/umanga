<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    recentMemos: Array,
    company: Object,
});

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <!-- Welcome Banner with Glassmorphism -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12 overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[100%] rounded-full bg-gradient-to-b from-fuchsia-500/20 to-purple-600/10 blur-3xl transform rotate-12"></div>
                <div class="absolute top-[30%] -left-[10%] w-[40%] h-[80%] rounded-full bg-gradient-to-tr from-cyan-400/20 to-indigo-500/10 blur-3xl transform -rotate-12"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-md">
                            Dashboard Overview
                        </h1>
                        <p class="mt-2 text-indigo-200 text-lg font-medium flex items-center gap-2">
                            <span>Welcome to</span>
                            <span class="px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white shadow-sm">
                                {{ company?.name || 'Your Company' }}
                            </span>
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <Link :href="route('admin.users.create')" class="inline-flex items-center px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-xl transition-all duration-300 border border-white/20 backdrop-blur-md shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add User
                        </Link>
                        <Link :href="route('admin.users.index')" class="inline-flex items-center px-5 py-2.5 bg-white text-indigo-700 text-sm font-semibold rounded-xl hover:bg-indigo-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Manage Users
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-10">
                <!-- Total Users -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 flex items-center group hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/50 dark:to-blue-800/50 text-blue-700 dark:text-blue-300 mr-5 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Users</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ stats.users.total }}</p>
                    </div>
                </div>

                <!-- Managers -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 flex items-center group hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/50 dark:to-emerald-800/50 text-emerald-700 dark:text-emerald-300 mr-5 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Managers</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ stats.users.managers }}</p>
                    </div>
                </div>

                <!-- Staffs -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 flex items-center group hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/50 dark:to-amber-800/50 text-amber-700 dark:text-amber-300 mr-5 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Staff</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ stats.users.staffs }}</p>
                    </div>
                </div>

                <!-- Pending Memos -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 flex items-center group hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-fuchsia-100 to-fuchsia-200 dark:from-fuchsia-900/50 dark:to-fuchsia-800/50 text-fuchsia-700 dark:text-fuchsia-300 mr-5 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending Memos</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ stats.memos.pending }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Grids -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-16">
                
                <!-- Recent Users -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Recent Users
                        </h3>
                        <Link :href="route('admin.users.index')" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">View all &rarr;</Link>
                    </div>
                    <div class="flex-1 divide-y divide-gray-100 dark:divide-gray-700">
                        <div v-for="user in recentUsers" :key="user.id" class="px-6 py-4 flex items-center justify-between hover:bg-indigo-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition-transform duration-300">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="text-right flex flex-col items-end gap-1.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold capitalize border"
                                    :class="{
                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800': user.role === 'manager',
                                        'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800': user.role === 'staff'
                                    }">
                                    {{ user.role }}
                                </span>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(user.created_at) }}</p>
                            </div>
                        </div>
                        <div v-if="recentUsers.length === 0" class="px-6 py-12 flex flex-col items-center justify-center text-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">No users added yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Memos -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-800/80 backdrop-blur-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Recent Memos
                        </h3>
                    </div>
                    <div class="flex-1 divide-y divide-gray-100 dark:divide-gray-700">
                        <div v-for="memo in recentMemos" :key="memo.id" class="px-6 py-4 hover:bg-fuchsia-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-fuchsia-700 dark:group-hover:text-fuchsia-400 transition-colors">{{ memo.title }}</p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <div class="w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                            {{ memo.creator.name.charAt(0) }}
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">By {{ memo.creator.name }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end gap-1.5 shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold capitalize border"
                                        :class="{
                                            'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800': ['pending_checker', 'pending_verifier', 'pending_approver'].includes(memo.status),
                                            'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800': memo.status === 'approved',
                                            'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800': memo.status === 'rejected',
                                            'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600': memo.status === 'draft',
                                        }">
                                        {{ memo.status.replace('_', ' ') }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(memo.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentMemos.length === 0" class="px-6 py-12 flex flex-col items-center justify-center text-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">No memos recorded.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
