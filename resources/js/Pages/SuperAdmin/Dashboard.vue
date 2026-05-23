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

// Helper to calculate the height of a bar relative to the maximum Y value
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
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Dashboard Overview</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Grid -->
                <!-- Stats Section 1: Companies & Memos -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total_companies }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Total Companies</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.active_companies }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Active Companies</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.inactive_companies }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Inactive Companies</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total_memos }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Total Memos</p>
                    </div>
                </div>

                <!-- Stats Section 2: Users Breakdown -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total_users }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Total Users</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.active_users }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Active Users</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.admins_count }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Company Admins</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.managers_count }} / {{ stats.staffs_count }}</p>
                        <p class="text-sm text-gray-500 dark:text-white font-medium mt-1">Managers / Staffs</p>
                    </div>
                </div>

                <!-- Stats Section 3: Attendance Today -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.present_today }}</p>
                            <p class="text-sm text-gray-500 font-medium">Total Present Today</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.clocked_in_now }}</p>
                            <p class="text-sm text-gray-500 font-medium">Currently Working</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.completed_today }}</p>
                            <p class="text-sm text-gray-500 font-medium">Completed Shift Today</p>
                        </div>
                    </div>
                </div>

                <!-- Graphs -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!--  Memos Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Memos Created (Last 6 Months)</h3>
                        <div class="relative h-64 flex items-end justify-between gap-2 pt-6 border-b border-gray-200">
                            <!-- Y-axis scale lines -->
                            <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-6">
                                <span>{{ chartData.max_y }}</span>
                                <span>{{ Math.round(chartData.max_y / 2) }}</span>
                                <span>0</span>
                            </div>
                            
                            <!-- Bars -->
                            <div v-for="(val, index) in chartData.memos" :key="'m-'+index" 
                                class="relative w-full flex flex-col items-center justify-end h-full z-10 pb-1 group">
                                <div class="w-full max-w-[3rem] bg-indigo-500 rounded-t-md transition-all duration-500 group-hover:bg-indigo-600"
                                     :style="{ height: calcHeight(val) }"></div>
                                <span class="absolute -top-6 text-xs font-semibold text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity">{{ val }}</span>
                                <span class="absolute -bottom-6 text-xs font-medium text-gray-500">{{ chartData.labels[index] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Companies Registered Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Companies Registered (Last 6 Months)</h3>
                        <div class="relative h-64 flex items-end justify-between gap-2 pt-6 border-b border-gray-200">
                            <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-6">
                                <span>{{ chartData.max_y }}</span>
                                <span>{{ Math.round(chartData.max_y / 2) }}</span>
                                <span>0</span>
                            </div>
                            
                            <div v-for="(val, index) in chartData.companies" :key="'c-'+index" 
                                class="relative w-full flex flex-col items-center justify-end h-full z-10 pb-1 group">
                                <div class="w-full max-w-[3rem] bg-emerald-500 rounded-t-md transition-all duration-500 group-hover:bg-emerald-600"
                                     :style="{ height: calcHeight(val) }"></div>
                                <span class="absolute -top-6 text-xs font-semibold text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity">{{ val }}</span>
                                <span class="absolute -bottom-6 text-xs font-medium text-gray-500">{{ chartData.labels[index] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Current Month Daily Memos Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 col-span-full">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daily Memos (Created vs Approved vs Rejected)</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Showing data for selected year and month</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <select v-model="filterYear" @change="updateFilter" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3">
                                        <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                                    </select>
                                    <select v-model="filterMonth" @change="updateFilter" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3">
                                        <option :value="1">January</option>
                                        <option :value="2">February</option>
                                        <option :value="3">March</option>
                                        <option :value="4">April</option>
                                        <option :value="5">May</option>
                                        <option :value="6">June</option>
                                        <option :value="7">July</option>
                                        <option :value="8">August</option>
                                        <option :value="9">September</option>
                                        <option :value="10">October</option>
                                        <option :value="11">November</option>
                                        <option :value="12">December</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-3 text-xs font-medium text-gray-600 border-l border-gray-200 pl-4 sm:pl-4">
                                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-blue-500"></span> Created</span>
                                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-green-500"></span> Approved</span>
                                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-500"></span> Rejected</span>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto pb-4">
                            <div class="relative h-64 min-w-[700px] flex items-end justify-between gap-2 pt-6 border-b border-gray-200">
                                <!-- Y-axis scale lines -->
                                <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-6">
                                    <span>{{ chartData.max_status_y }}</span>
                                    <span>{{ Math.round(chartData.max_status_y / 2) }}</span>
                                    <span>0</span>
                                </div>
                                
                                <!-- Daily Groups -->
                                <div v-for="(day, index) in chartData.days_labels" :key="'d-'+index" 
                                    class="relative flex-1 flex items-end justify-center gap-0.5 h-full z-10 pb-1 group">
                                    
                                    <!-- Created Bar -->
                                    <div class="w-full max-w-[12px] bg-blue-500 rounded-t-sm transition-all duration-500 group-hover:bg-blue-600 relative"
                                         :style="{ height: calcStatusHeight(chartData.day_created[index]) }">
                                        <span v-if="chartData.day_created[index] > 0" class="absolute -top-5 left-1/2 -translate-x-1/2 text-[10px] font-bold text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">{{ chartData.day_created[index] }}</span>
                                    </div>

                                    <!-- Approved Bar -->
                                    <div class="w-full max-w-[12px] bg-green-500 rounded-t-sm transition-all duration-500 group-hover:bg-green-600 relative"
                                         :style="{ height: calcStatusHeight(chartData.day_approved[index]) }">
                                        <span v-if="chartData.day_approved[index] > 0" class="absolute -top-5 left-1/2 -translate-x-1/2 text-[10px] font-bold text-green-600 opacity-0 group-hover:opacity-100 transition-opacity">{{ chartData.day_approved[index] }}</span>
                                    </div>

                                    <!-- Rejected Bar -->
                                    <div class="w-full max-w-[12px] bg-red-500 rounded-t-sm transition-all duration-500 group-hover:bg-red-600 relative"
                                         :style="{ height: calcStatusHeight(chartData.day_rejected[index]) }">
                                        <span v-if="chartData.day_rejected[index] > 0" class="absolute -top-5 left-1/2 -translate-x-1/2 text-[10px] font-bold text-red-600 opacity-0 group-hover:opacity-100 transition-opacity">{{ chartData.day_rejected[index] }}</span>
                                    </div>

                                    <span class="absolute -bottom-6 text-[11px] font-medium text-gray-500">{{ day }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <Link :href="route('super-admin.companies.index')" class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        View All Companies →
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
