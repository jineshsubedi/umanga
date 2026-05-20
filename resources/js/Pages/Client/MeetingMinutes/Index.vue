<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ minutes: Array });

const statusBadge = (s) => ({
    draft:    'bg-gray-100 text-gray-600',
    pending:  'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const submit = (minute) => {
    router.post(route('client.meeting-minutes.submit', minute.id));
};

const del = (minute) => {
    if (confirm('Delete this meeting minute?')) {
        router.delete(route('client.meeting-minutes.destroy', minute.id));
    }
};

const duplicate = (minute) => {
    if (confirm('Create a new draft based on this rejected minute?')) {
        router.post(route('client.meeting-minutes.duplicate', minute.id));
    }
};
</script>

<template>
    <Head title="My Meeting Minutes" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">My Meeting Minutes</h2>
                <!-- <Link :href="route('client.meeting-minutes.create')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Minute
                </Link> -->
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="space-y-4">
                    <Link :href="route('client.meeting-minutes.create')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Minute
                </Link>
                    <div v-for="minute in minutes" :key="minute.id"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusBadge(minute.status)">
                                        {{ minute.status }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ minute.formatted_meeting_date }}</span>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">{{ minute.title }}</h3>
                                <div v-if="minute.managers && minute.managers.length > 0" class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="text-xs text-gray-500 font-medium">Assigned to:</span>
                                    <span v-for="m in minute.managers" :key="m.id" class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[11px] font-medium border border-indigo-100">
                                        {{ m.name }}
                                    </span>
                                </div>

                                <!-- Rejection comment -->
                                <div v-if="minute.status === 'rejected' && minute.latest_review?.comment"
                                    class="mt-2 p-3 bg-red-50 rounded-lg border border-red-100">
                                    <p class="text-xs font-medium text-red-700">Manager's comment:</p>
                                    <p class="text-sm text-red-600 mt-0.5">{{ minute.latest_review.comment }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <Link :href="route('client.meeting-minutes.show', minute.id)"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">View</Link>

                                <Link v-if="minute.status === 'draft'"
                                    :href="route('client.meeting-minutes.edit', minute.id)"
                                    class="text-sm text-gray-600 hover:text-gray-900 font-medium">Edit</Link>

                                <button v-if="minute.status === 'draft'"
                                    @click="submit(minute)"
                                    class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-md font-medium transition-colors">
                                    Submit
                                </button>

                                <button v-if="minute.status === 'rejected'"
                                    @click="duplicate(minute)"
                                    class="text-sm bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md font-medium transition-colors">
                                    Revise (New)
                                </button>

                                <button v-if="minute.status !== 'approved'"
                                    @click="del(minute)"
                                    class="text-sm text-red-500 hover:text-red-700 font-medium">Delete</button>
                            </div>
                        </div>
                    </div>

                    <div v-if="minutes.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-gray-500 mb-3">No meeting minutes yet.</p>
                        <Link :href="route('client.meeting-minutes.create')" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Create your first minute →</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
