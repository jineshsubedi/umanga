<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    activities: {
        type: Array,
        default: () => []
    },
    memosStats: {
        type: Object,
        default: () => ({ total: 0, pending: 0, approved: 0, rejected: 0 })
    },
    procurementsStats: {
        type: Object,
        default: () => ({ total: 0, pending: 0, approved: 0, rejected: 0 })
    },
    chartData: {
        type: Array,
        default: () => []
    }
});

const user = usePage().props.auth.user;

const maxVal = computed(() => {
    let max = 1;
    props.chartData.forEach(item => {
        if (item.memos > max) max = item.memos;
        if (item.procurements > max) max = item.procurements;
    });
    return max;
});

const getBarHeight = (value) => {
    if (value === 0) return 3; // minimal height so a tiny bar shows
    return (value / maxVal.value) * 100;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Premium Dashboard Banner for Admin, Manager, and Staff -->
                <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
                        <div class="absolute -bottom-1/4 -left-1/4 w-96 h-96 rounded-full bg-indigo-500/10 blur-3xl"></div>
                    </div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider text-purple-200">
                                {{ user.role === 'admin' ? 'Company Administration Dashboard' : user.role === 'manager' ? 'Company Management Dashboard' : 'Company Staff Workspace' }}
                            </span>
                            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-3">Welcome back, {{ user.name }}! 👋</h1>
                            <p class="mt-2 text-purple-200 text-sm max-w-xl">
                                {{ user.role === 'admin' ? 'Manage company activities, review procurement requests, and verify meeting memos efficiently from this workspace dashboard.' : user.role === 'manager' ? 'Oversee team tasks, review documents, and manage procurement/memo processes from your management workspace.' : 'Track your memos, check pending approvals, and submit new procurement requests easily.' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div class="px-5 py-4 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl text-center min-w-[100px]">
                                <span class="block text-2xl font-black text-white">{{ memosStats.total }}</span>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-purple-200">
                                    {{ user.role === 'staff' ? 'My Memos' : 'Total Memos' }}
                                </span>
                            </div>
                            <div class="px-5 py-4 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl text-center min-w-[100px]">
                                <span class="block text-2xl font-black text-white">{{ procurementsStats.total }}</span>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-purple-200">
                                    {{ user.role === 'staff' ? 'My Requests' : 'Total Requests' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Access Modules Stat Counters for Admin -->
                <div v-if="user.role === 'admin'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Memo Module Stats Card -->
                    <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-md border border-white/30 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Memos Module Stats</h3>
                            </div>
                            <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Active Access</span>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-yellow-50/50 dark:bg-yellow-950/20 border border-yellow-100 dark:border-yellow-900/30 p-4 rounded-xl text-center">
                                <span class="block text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ memosStats.pending }}</span>
                                <span class="text-[11px] font-semibold text-yellow-600 dark:text-yellow-500 uppercase tracking-wide">Pending</span>
                            </div>
                            <div class="bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 p-4 rounded-xl text-center">
                                <span class="block text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ memosStats.approved }}</span>
                                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-500 uppercase tracking-wide">Approved</span>
                            </div>
                            <div class="bg-red-50/50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 p-4 rounded-xl text-center">
                                <span class="block text-2xl font-bold text-red-700 dark:text-red-400">{{ memosStats.rejected }}</span>
                                <span class="text-[11px] font-semibold text-red-600 dark:text-red-500 uppercase tracking-wide">Rejected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Procurement Module Stats Card -->
                    <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-md border border-white/30 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Procurement Module Stats</h3>
                            </div>
                            <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Active Access</span>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-yellow-50/50 dark:bg-yellow-950/20 border border-yellow-100 dark:border-yellow-900/30 p-4 rounded-xl text-center">
                                <span class="block text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ procurementsStats.pending }}</span>
                                <span class="text-[11px] font-semibold text-yellow-600 dark:text-yellow-500 uppercase tracking-wide">Pending</span>
                            </div>
                            <div class="bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 p-4 rounded-xl text-center">
                                <span class="block text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ procurementsStats.approved }}</span>
                                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-500 uppercase tracking-wide">Approved</span>
                            </div>
                            <div class="bg-red-50/50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 p-4 rounded-xl text-center">
                                <span class="block text-2xl font-bold text-red-700 dark:text-red-400">{{ procurementsStats.rejected }}</span>
                                <span class="text-[11px] font-semibold text-red-600 dark:text-red-500 uppercase tracking-wide">Rejected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Charts / Activity Graph for Admin -->
                <div v-if="user.role === 'admin' && chartData && chartData.length > 0" class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-md border border-white/30 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Monthly Activity Analytics</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total creation counts over the last 6 months</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] rounded animate-pulse"></span>
                                <span class="text-gray-600 dark:text-gray-400">Memos</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 bg-indigo-400 rounded"></span>
                                <span class="text-gray-600 dark:text-gray-400">Procurements</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Grid -->
                    <div class="relative">
                        <!-- Y-Axis indicators -->
                        <div class="absolute inset-y-0 left-0 flex flex-col justify-between pointer-events-none text-[10px] font-bold text-gray-400 dark:text-gray-500 h-48 border-r border-gray-200 dark:border-gray-700 pr-2 min-w-[32px]">
                            <span>{{ maxVal }}</span>
                            <span>{{ Math.round(maxVal / 2) }}</span>
                            <span>0</span>
                        </div>

                        <!-- Bar Chart Grid -->
                        <div class="flex items-end justify-around h-48 pt-4 pb-2 border-b border-gray-200 dark:border-gray-700 ml-10">
                            <div v-for="(data, index) in chartData" :key="index" class="flex flex-col items-center gap-2 w-full group">
                                <div class="flex items-end gap-1.5 justify-center h-32 w-full">
                                    <!-- Memo Bar -->
                                    <div class="w-4 sm:w-6 bg-gradient-to-t from-[#7c3aed] to-[#b026ff] rounded-t-md hover:opacity-90 transition-all duration-300 relative cursor-pointer"
                                        :style="{ height: getBarHeight(data.memos) + '%' }">
                                        <!-- Tooltip -->
                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap shadow-md z-30">
                                            {{ data.memos }} Memos
                                        </span>
                                    </div>
                                    <!-- Procurement Bar -->
                                    <div class="w-4 sm:w-6 bg-indigo-400 rounded-t-md hover:opacity-90 transition-all duration-300 relative cursor-pointer"
                                        :style="{ height: getBarHeight(data.procurements) + '%' }">
                                        <!-- Tooltip -->
                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap shadow-md z-30">
                                            {{ data.procurements }} Procurements
                                        </span>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400 mt-2">{{ data.label }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Activities</h4>
                    </div>
                    
                    <div v-if="activities.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="activity in activities" :key="activity.id" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150">
                            <div class="flex items-center justify-between">
                                <Link :href="activity.url" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium text-lg">
                                <div class="flex flex-col">
                                        {{ activity.title }}
                                        <div class="mt-1 text-sm text-gray-500 flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ activity.type }}
                                            </span>
                                            <span>•</span>
                                            <span>Created by {{ activity.creator }}</span>
                                            <span>•</span>
                                            <span>{{ activity.created_at }}</span>
                                        </div>
                                    </div>
                                </Link>
                                <div class="flex items-center gap-3">
                                    <span v-if="activity.status.toLowerCase() === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 font-bold capitalize">
                                        Awaiting: {{ activity.current_step || 'Approval' }}
                                    </span>
                                    <span v-else-if="activity.status.toLowerCase() === 'approved' || activity.status.toLowerCase() === 'completed'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 font-bold capitalize">
                                        {{ activity.status }}
                                    </span>
                                    <span v-else-if="activity.status.toLowerCase() === 'rejected' || activity.status.toLowerCase() === 'returned'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 font-bold capitalize">
                                        {{ activity.status }}
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize font-bold">
                                        {{ activity.status }}
                                    </span>
                                    
                                    <Link :href="activity.url" class="text-gray-400 hover:text-indigo-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">No recent activities</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new request or memo.</p>
                        <div class="mt-6 flex justify-center gap-3">
                            <Link :href="route('memos.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                New Memo
                            </Link>
                            <Link :href="route('procurement.create')" class="inline-flex items-center px-4 py-2 border border-indigo-200 shadow-sm text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                New Procurement
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
