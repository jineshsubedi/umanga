<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ memos: Array, counts: Object, status: String });

const statusBadge = (s) => ({
    draft:    'bg-gray-100 text-gray-600',
    pending_manager:  'bg-yellow-100 text-yellow-700',
    pending_admin: 'bg-blue-100 text-blue-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const tabs = ['pending_manager', 'pending_admin', 'approved', 'rejected'];
</script>

<template>
    <Head title=" Memos" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100"> Memos Review</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Tabs -->
                <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg w-fit mb-6">
                    <Link v-for="tab in tabs" :key="tab"
                        :href="route('manager.meeting-memos.index', { status: tab })"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors capitalize flex items-center gap-2"
                        :class="status === tab ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 dark:bg-gray-800 hover:text-gray-900'">
                        {{ tab.replace('_', ' ') }}
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full"
                            :class="status === tab ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-600'">
                            {{ counts[tab.replace('_manager', '').replace('_admin', '')] || counts[tab] || 0 }}
                        </span>
                    </Link>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Submitted By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"> Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="memo in memos" :key="memo.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ memo.title }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ memo.creator?.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ memo.formatted_meeting_date }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusBadge(memo.status)">
                                        {{ memo.status.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('manager.meeting-memos.show', memo.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium">
                                        {{ memo.status === 'pending_manager' ? 'Review' : 'View' }}
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="memos.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">No {{ status }} memos.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
