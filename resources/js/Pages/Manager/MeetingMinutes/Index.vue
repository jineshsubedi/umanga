<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ minutes: Array, counts: Object, status: String });

const statusBadge = (s) => ({
    draft:    'bg-gray-100 text-gray-600',
    pending:  'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const tabs = ['pending', 'approved', 'rejected'];
</script>

<template>
    <Head title="Meeting Minutes" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900">Meeting Minutes Review</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Tabs -->
                <div class="flex gap-1 bg-gray-100 p-1 rounded-lg w-fit mb-6">
                    <Link v-for="tab in tabs" :key="tab"
                        :href="route('manager.meeting-minutes.index', { status: tab })"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors capitalize flex items-center gap-2"
                        :class="status === tab ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'">
                        {{ tab }}
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full"
                            :class="status === tab ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-600'">
                            {{ counts[tab] }}
                        </span>
                    </Link>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Submitted By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Meeting Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr v-for="minute in minutes" :key="minute.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ minute.title }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ minute.creator?.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ minute.meeting_date }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusBadge(minute.status)">
                                        {{ minute.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('manager.meeting-minutes.show', minute.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        {{ minute.status === 'pending' ? 'Review' : 'View' }}
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="minutes.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">No {{ status }} meeting minutes.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
