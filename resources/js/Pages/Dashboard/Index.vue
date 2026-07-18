<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    modules: Array,
    stats: Object,
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="HRIS Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                HRIS MANAGEMENT SYSTEM
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Welcome Banner -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold">Welcome, {{ user.name }}</h3>
                            <p class="text-gray-500 mt-1">Select a module below to get started.</p>
                        </div>
                    </div>
                </div>

                <!-- Module Selection Grid -->
                <div>
                    <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Select Module</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- Memo System -->
                        <Link v-if="modules.includes('memo')" :href="route('memos.index')" class="block bg-indigo-50 dark:bg-gray-700 border border-indigo-100 dark:border-gray-600 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center gap-4">
                                <div class="bg-indigo-600 text-white p-3 rounded-full">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xl font-bold text-gray-900 dark:text-white">Memo System</h5>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage corporate memos</p>
                                </div>
                            </div>
                        </Link>

                        <!-- Procurement -->
                        <Link v-if="modules.includes('procurement')" :href="route('procurement.index')" class="block bg-emerald-50 dark:bg-gray-700 border border-emerald-100 dark:border-gray-600 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center gap-4">
                                <div class="bg-emerald-600 text-white p-3 rounded-full">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xl font-bold text-gray-900 dark:text-white">Procurement</h5>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage purchase requests</p>
                                </div>
                            </div>
                        </Link>

                        <!-- Leave Management -->
                        <Link v-if="modules.includes('leave')" href="#" class="block bg-orange-50 dark:bg-gray-700 border border-orange-100 dark:border-gray-600 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center gap-4">
                                <div class="bg-orange-600 text-white p-3 rounded-full">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xl font-bold text-gray-900 dark:text-white">Leave Management</h5>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Request and approve time off</p>
                                </div>
                            </div>
                        </Link>

                    </div>
                </div>

                <!-- Statistics Grid -->
                <div>
                    <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Quick Stats</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div v-if="modules.includes('memo')" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pending Memos</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.pending_memos }}</p>
                        </div>
                        <div v-if="modules.includes('procurement')" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pending Procurement</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.pending_procurement }}</p>
                        </div>
                        <div v-if="modules.includes('leave')" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Today's Leaves</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.today_leave }}</p>
                        </div>
                        <div v-if="user.role === 'admin'" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Employees</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.employees }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
