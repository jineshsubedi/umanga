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
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-900 dark:to-purple-900 pb-24 pt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white tracking-tight">Dashboard</h1>
                        <p class="mt-2 text-indigo-100 text-sm font-medium">
                            Overview for <span class="font-bold text-white">{{ company?.name || 'Your Company' }}</span>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link :href="route('admin.users.create')" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition-colors border border-white/20 backdrop-blur-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add User
                        </Link>
                        <Link :href="route('admin.users.index')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 text-sm font-medium rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                            Manage Users
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center hover:shadow-md transition-shadow">
                    <div class="p-3 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users.total }}</p>
                    </div>
                </div>

                <!-- Managers -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center hover:shadow-md transition-shadow">
                    <div class="p-3 rounded-full bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Managers</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users.managers }}</p>
                    </div>
                </div>

                <!-- Staffs -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center hover:shadow-md transition-shadow">
                    <div class="p-3 rounded-full bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Staffs</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users.staffs }}</p>
                    </div>
                </div>

                <!-- Pending Memos -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center hover:shadow-md transition-shadow">
                    <div class="p-3 rounded-full bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Reviews</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.memos.pending }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Grids -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-12">
                
                <!-- Recent Users -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recently Added Users</h3>
                        <Link :href="route('admin.users.index')" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">View all</Link>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div v-for="user in recentUsers" :key="user.id" class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold shadow-inner">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': user.role === 'manager',
                                        'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400': user.role === 'staff'
                                    }">
                                    {{ user.role }}
                                </span>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ formatDate(user.created_at) }}</p>
                            </div>
                        </div>
                        <div v-if="recentUsers.length === 0" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                            No users added yet.
                        </div>
                    </div>
                </div>

                <!-- Recent  Memos -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent  Memos</h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div v-for="memo in recentMemos" :key="memo.id" class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px] sm:max-w-xs">{{ memo.title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">By {{ memo.creator.name }}</p>
                                </div>
                                <div class="text-right flex flex-col items-end gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': memo.status === 'pending',
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': memo.status === 'approved',
                                            'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': memo.status === 'rejected',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': memo.status === 'draft',
                                        }">
                                        {{ memo.status }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(memo.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentMemos.length === 0" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                            No memos recorded.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
