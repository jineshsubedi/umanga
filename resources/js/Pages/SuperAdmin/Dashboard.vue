<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ stats: Object, chartData: Object });

// Helper to calculate the height of a bar relative to the maximum Y value
const calcHeight = (val) => {
    if (props.chartData.max_y === 0) return '0%';
    return `${(val / props.chartData.max_y) * 100}%`;
};
</script>

<template>
    <Head title="Superadmin Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Dashboard Overview</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ stats.total_companies }}</p>
                        <p class="text-sm text-gray-500 font-medium mt-1">Total Companies</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ stats.active_companies }}</p>
                        <p class="text-sm text-gray-500 font-medium mt-1">Active Companies</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ stats.total_users }}</p>
                        <p class="text-sm text-gray-500 font-medium mt-1">Total Users</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ stats.total_minutes }}</p>
                        <p class="text-sm text-gray-500 font-medium mt-1">Total Minutes</p>
                    </div>
                </div>

                <!-- Graphs -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Meeting Minutes Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Minutes Created (Last 6 Months)</h3>
                        <div class="relative h-64 flex items-end justify-between gap-2 pt-6 border-b border-gray-200">
                            <!-- Y-axis scale lines -->
                            <div class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-6">
                                <span>{{ chartData.max_y }}</span>
                                <span>{{ Math.round(chartData.max_y / 2) }}</span>
                                <span>0</span>
                            </div>
                            
                            <!-- Bars -->
                            <div v-for="(val, index) in chartData.minutes" :key="'m-'+index" 
                                class="relative w-full flex flex-col items-center justify-end h-full z-10 pb-1 group">
                                <div class="w-full max-w-[3rem] bg-indigo-500 rounded-t-md transition-all duration-500 group-hover:bg-indigo-600"
                                     :style="{ height: calcHeight(val) }"></div>
                                <span class="absolute -top-6 text-xs font-semibold text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity">{{ val }}</span>
                                <span class="absolute -bottom-6 text-xs font-medium text-gray-500">{{ chartData.labels[index] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Companies Registered Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Companies Registered (Last 6 Months)</h3>
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
                </div>

                <div class="flex justify-end pt-4">
                    <Link :href="route('super-admin.companies.index')" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                        View All Companies →
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
