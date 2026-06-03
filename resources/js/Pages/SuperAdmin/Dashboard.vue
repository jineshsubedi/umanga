<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ stats: Object, chartData: Object, filters: Object, availableYears: Array });

const filterYear = ref(props.filters?.year || new Date().getFullYear());
const filterMonth = ref(props.filters?.month || (new Date().getMonth() + 1));

const updateFilter = () => {
    router.get(route('super-admin.dashboard'), {
        year: filterYear.value,
        month: filterMonth.value
    }, { preserveState: true, preserveScroll: true });
};

const calcHeight = (val) => {
    if (props.chartData.max_y === 0) return '0%';
    return `${(val / props.chartData.max_y) * 100}%`;
};

const calcStatusHeight = (val) => {
    if (props.chartData.max_status_y === 0) return '0%';
    return `${(val / props.chartData.max_status_y) * 100}%`;
};
</script>

<template>
    <Head title="Superadmin Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h2>
        </template>

        <!-- Hero Banner -->
        <div class="relative bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pb-28 pt-10 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-2/3 h-2/3 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-1/4 -left-1/4 w-2/3 h-2/3 rounded-full bg-indigo-400/20 blur-3xl animate-pulse" style="animation-delay:1s"></div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <h1 class="text-4xl font-extrabold text-white drop-shadow-md tracking-tight">Super Admin Dashboard</h1>
                <p class="mt-2 text-purple-200 text-base">Monitor all companies, users, and memos across the platform.</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 pb-12">
            <!-- Stats Row 1: Companies & Memos -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div v-for="(card, i) in [
                    { label: 'Total Companies', value: stats.total_companies, color: 'from-blue-500 to-blue-600', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
                    { label: 'Active Companies', value: stats.active_companies, color: 'from-emerald-500 to-green-600', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' },
                    { label: 'Inactive Companies', value: stats.inactive_companies, color: 'from-red-500 to-rose-600', icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
                    { label: 'Total Memos', value: stats.total_memos, color: 'from-orange-400 to-amber-500', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
                ]" :key="i"
                    class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 p-5 flex items-center gap-4 group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div :class="`bg-gradient-to-br ${card.color} w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform`">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ card.value }}</p>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">{{ card.label }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Row 2: Users -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div v-for="(card, i) in [
                    { label: 'Total Users', value: stats.total_users, color: 'from-purple-500 to-violet-600', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
                    { label: 'Active Users', value: stats.active_users, color: 'from-indigo-500 to-blue-600', icon: 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                    { label: 'Company Admins', value: stats.admins_count, color: 'from-teal-500 to-cyan-600', icon: 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14' },
                    { label: 'Managers / Staff', value: `${stats.managers_count} / ${stats.staffs_count}`, color: 'from-amber-500 to-yellow-600', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
                ]" :key="i"
                    class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 p-5 flex items-center gap-4 group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div :class="`bg-gradient-to-br ${card.color} w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform`">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ card.value }}</p>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">{{ card.label }}</p>
                    </div>
                </div>
            </div>

            <!-- Attendance Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div v-for="(item, i) in [
                    { label: 'Total Present Today', value: stats.present_today, icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', clr: 'text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-400' },
                    { label: 'Currently Working', value: stats.clocked_in_now, icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', clr: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-400' },
                    { label: 'Completed Shift Today', value: stats.completed_today, icon: 'M5 13l4 4L19 7', clr: 'text-purple-600 bg-purple-50 dark:bg-purple-900/30 dark:text-purple-400' },
                ]" :key="i"
                    class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 p-5 flex items-center gap-4 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <div :class="`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 ${item.clr}`">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ item.value }}</p>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">{{ item.label }}</p>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Memos Chart -->
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-2 h-5 bg-indigo-500 rounded-full inline-block"></span>
                        Memos Created (Last 6 Months)
                    </h3>
                    <div class="relative h-52 flex items-end justify-between gap-2 pt-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-6">
                            <span>{{ chartData.max_y }}</span>
                            <span>{{ Math.round(chartData.max_y / 2) }}</span>
                            <span>0</span>
                        </div>
                        <div v-for="(val, index) in chartData.memos" :key="'m-'+index" class="relative w-full flex flex-col items-center justify-end h-full z-10 pb-1 group">
                            <div class="w-full max-w-[2.5rem] bg-gradient-to-t from-indigo-600 to-indigo-400 rounded-t-lg transition-all duration-700 group-hover:from-purple-600 group-hover:to-purple-400 shadow-md" :style="{ height: calcHeight(val) }"></div>
                            <span class="absolute -top-6 text-xs font-bold text-indigo-600 dark:text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity">{{ val }}</span>
                            <span class="absolute -bottom-6 text-[10px] font-medium text-gray-500">{{ chartData.labels[index] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Companies Chart -->
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-2 h-5 bg-emerald-500 rounded-full inline-block"></span>
                        Companies Registered (Last 6 Months)
                    </h3>
                    <div class="relative h-52 flex items-end justify-between gap-2 pt-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-6">
                            <span>{{ chartData.max_y }}</span>
                            <span>{{ Math.round(chartData.max_y / 2) }}</span>
                            <span>0</span>
                        </div>
                        <div v-for="(val, index) in chartData.companies" :key="'c-'+index" class="relative w-full flex flex-col items-center justify-end h-full z-10 pb-1 group">
                            <div class="w-full max-w-[2.5rem] bg-gradient-to-t from-emerald-600 to-emerald-400 rounded-t-lg transition-all duration-700 group-hover:from-teal-600 group-hover:to-teal-400 shadow-md" :style="{ height: calcHeight(val) }"></div>
                            <span class="absolute -top-6 text-xs font-bold text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">{{ val }}</span>
                            <span class="absolute -bottom-6 text-[10px] font-medium text-gray-500">{{ chartData.labels[index] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Status Chart -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-blue-500 rounded-full inline-block"></span>
                            Daily Memos (Created vs Approved vs Rejected)
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Showing data for selected year and month</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <select v-model="filterYear" @change="updateFilter" class="text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] py-2 px-3 shadow-sm">
                            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                        </select>
                        <select v-model="filterMonth" @change="updateFilter" class="text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] py-2 px-3 shadow-sm">
                            <option v-for="(m, idx) in ['January','February','March','April','May','June','July','August','September','October','November','December']" :key="idx" :value="idx+1">{{ m }}</option>
                        </select>
                        <div class="flex items-center gap-3 text-xs font-semibold text-gray-600 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700 pl-3">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500"></span> Created</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500"></span> Approved</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-500"></span> Rejected</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto pb-4">
                    <div class="relative h-52 min-w-[700px] flex items-end justify-between gap-1 pt-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-6">
                            <span>{{ chartData.max_status_y }}</span>
                            <span>{{ Math.round(chartData.max_status_y / 2) }}</span>
                            <span>0</span>
                        </div>
                        <div v-for="(day, index) in chartData.days_labels" :key="'d-'+index" class="relative flex-1 flex items-end justify-center gap-0.5 h-full z-10 pb-1 group">
                            <div class="w-full max-w-[10px] bg-gradient-to-t from-blue-600 to-blue-400 rounded-t-sm transition-all duration-500" :style="{ height: calcStatusHeight(chartData.day_created[index]) }"></div>
                            <div class="w-full max-w-[10px] bg-gradient-to-t from-emerald-600 to-emerald-400 rounded-t-sm transition-all duration-500" :style="{ height: calcStatusHeight(chartData.day_approved[index]) }"></div>
                            <div class="w-full max-w-[10px] bg-gradient-to-t from-red-600 to-red-400 rounded-t-sm transition-all duration-500" :style="{ height: calcStatusHeight(chartData.day_rejected[index]) }"></div>
                            <span class="absolute -bottom-6 text-[10px] font-medium text-gray-500">{{ day }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <Link :href="route('super-admin.companies.index')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    View All Companies
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
