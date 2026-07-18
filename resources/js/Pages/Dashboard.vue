<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    activities: {
        type: Array,
        default: () => []
    }
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Welcome Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome back, {{ user.name }}! 👋</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Here is what's happening in your workspace today.</p>
                    </div>
                    <!-- <div>
                        <Link :href="route('memos.index')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            Go to Memos
                        </Link>
                    </div> -->
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
                                    <span v-if="activity.status.toLowerCase() === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Awaiting: {{ activity.current_step || 'Approval' }}
                                    </span>
                                    <span v-else-if="activity.status.toLowerCase() === 'approved' || activity.status.toLowerCase() === 'completed'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ activity.status }}
                                    </span>
                                    <span v-else-if="activity.status.toLowerCase() === 'rejected' || activity.status.toLowerCase() === 'returned'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ activity.status }}
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
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
