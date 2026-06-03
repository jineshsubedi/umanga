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
        <!-- Welcome Banner -->
        <div class="relative bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pb-28 pt-10 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-2/3 h-2/3 rounded-full bg-fuchsia-500/20 blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-1/4 -left-1/4 w-2/3 h-2/3 rounded-full bg-cyan-400/10 blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-md">Dashboard Overview</h1>
                    <p class="mt-2 text-purple-200 text-base flex items-center gap-2">
                        Welcome to
                        <span class="px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white text-sm font-semibold">{{ company?.name || 'Your Company' }}</span>
                    </p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <Link :href="route('admin.users.create')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-xl border border-white/20 backdrop-blur-md transition-all hover:-translate-y-0.5 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add User
                    </Link>
                    <Link :href="route('admin.users.index')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#5b14b8] text-sm font-bold rounded-xl hover:bg-purple-50 transition-all hover:-translate-y-0.5 shadow-lg">
                        Manage Users
                    </Link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 pb-12">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div v-for="(card, i) in [
                    { label: 'Total Users', value: stats.users.total, color: 'from-blue-500 to-blue-600', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
                    { label: 'Managers', value: stats.users.managers, color: 'from-emerald-500 to-green-600', icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
                    { label: 'Staff', value: stats.users.staffs, color: 'from-amber-500 to-orange-500', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
                    { label: 'Pending Memos', value: stats.memos.pending, color: 'from-fuchsia-500 to-purple-600', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
                ]" :key="i"
                    class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 p-6 flex items-center gap-4 group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div :class="`bg-gradient-to-br ${card.color} p-4 rounded-xl text-white flex-shrink-0 shadow-md group-hover:scale-110 transition-transform duration-300`">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ card.label }}</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ card.value }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Grids -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
                <!-- Recent Users -->
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-900/40">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-indigo-500 rounded-full"></span>
                            Recent Users
                        </h3>
                        <Link :href="route('admin.users.index')" class="text-xs font-bold text-[#7c3aed] dark:text-[#a78bfa] hover:underline transition-colors">View all →</Link>
                    </div>
                    <div class="flex-1 divide-y divide-gray-100 dark:divide-gray-700/50">
                        <div v-for="user in recentUsers" :key="user.id" class="px-6 py-4 flex items-center justify-between hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#7c3aed] to-[#b026ff] text-white flex items-center justify-center font-bold text-sm shadow group-hover:scale-110 transition-transform duration-200">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold capitalize border"
                                    :class="{
                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800': user.role === 'manager',
                                        'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800': user.role === 'staff'
                                    }">{{ user.role }}</span>
                                <p class="text-xs text-gray-400 mt-1">{{ formatDate(user.created_at) }}</p>
                            </div>
                        </div>
                        <div v-if="recentUsers.length === 0" class="px-6 py-12 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/></svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">No users added yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Memos -->
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/80 dark:bg-gray-900/40">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-fuchsia-500 rounded-full"></span>
                            Recent Memos
                        </h3>
                        <Link :href="route('memos.index')" class="text-xs font-bold text-[#7c3aed] dark:text-[#a78bfa] hover:underline transition-colors">View all →</Link>
                    </div>
                    <div class="flex-1 divide-y divide-gray-100 dark:divide-gray-700/50">
                        <div v-for="memo in recentMemos" :key="memo.id" class="px-6 py-4 hover:bg-fuchsia-50/50 dark:hover:bg-fuchsia-900/10 transition-colors group">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-[#7c3aed] dark:group-hover:text-[#a78bfa] transition-colors">{{ memo.title }}</p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-[#7c3aed] to-[#b026ff] flex items-center justify-center text-[9px] font-bold text-white">{{ memo.creator.name.charAt(0) }}</div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">By {{ memo.creator.name }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end gap-1.5 shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold capitalize border"
                                        :class="{
                                            'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800': ['pending_checker','pending_verifier','pending_approver'].includes(memo.status),
                                            'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800': memo.status === 'approved',
                                            'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800': memo.status === 'rejected',
                                            'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600': memo.status === 'draft',
                                        }">{{ memo.status.replace('_', ' ') }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(memo.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentMemos.length === 0" class="px-6 py-12 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">No memos recorded.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
