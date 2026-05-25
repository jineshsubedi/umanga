<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ memos: Array });

const statusBadge = (s) => ({
    draft:    'bg-gray-100 text-gray-600',
    pending_manager:  'bg-yellow-100 text-yellow-700',
    pending_admin: 'bg-blue-100 text-blue-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const submit = (memo) => {
    router.post(route('staff.meeting-memos.submit', memo.id));
};

const del = (memo) => {
    if (confirm('Delete this meeting memo?')) {
        router.delete(route('staff.meeting-memos.destroy', memo.id));
    }
};

const duplicate = (memo) => {
    if (confirm('Create a new draft based on this rejected memo?')) {
        router.post(route('staff.meeting-memos.duplicate', memo.id));
    }
};
</script>

<template>
    <Head title="My  Memos" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">My  Memos</h2>
                <!-- <Link :href="route('staff.meeting-memos.create')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Memo
                </Link> -->
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="space-y-4">
                    <Link :href="route('staff.meeting-memos.create')"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Memo
                </Link>
                    <div v-for="memo in memos" :key="memo.id"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusBadge(memo.status)">
                                        {{ memo.status.replace('_', ' ') }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ memo.formatted_meeting_date }}</span>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">{{ memo.title }}</h3>
                                <div v-if="memo.managers && memo.managers.length > 0" class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="text-xs text-gray-500 font-medium">Assigned to:</span>
                                    <span v-for="m in memo.managers" :key="m.id" class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[11px] font-medium border border-indigo-100">
                                        {{ m.name }}
                                    </span>
                                </div>

                                <!-- Rejection comment -->
                                <div v-if="memo.status === 'rejected' && memo.latest_review?.comment"
                                    class="mt-2 p-3 bg-red-50 rounded-lg border border-red-100">
                                    <p class="text-xs font-medium text-red-700">Manager's comment:</p>
                                    <p class="text-sm text-red-600 mt-0.5">{{ memo.latest_review.comment }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <Link :href="route('staff.meeting-memos.show', memo.id)"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">View</Link>

                                <Link v-if="memo.status === 'draft'"
                                    :href="route('staff.meeting-memos.edit', memo.id)"
                                    class="text-sm text-gray-600 hover:text-gray-900 font-medium">Edit</Link>

                                <button v-if="memo.status === 'draft'"
                                    @click="submit(memo)"
                                    class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-md font-medium transition-colors">
                                    Submit
                                </button>

                                <button v-if="memo.status === 'rejected'"
                                    @click="duplicate(memo)"
                                    class="text-sm bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md font-medium transition-colors">
                                    Revise (New)
                                </button>

                                <button v-if="memo.status !== 'approved'"
                                    @click="del(memo)"
                                    class="text-sm text-red-500 hover:text-red-700 font-medium">Delete</button>
                            </div>
                        </div>
                    </div>

                    <div v-if="memos.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-gray-500 mb-3">No memos yet.</p>
                        <Link :href="route('staff.meeting-memos.create')" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Create your first memo →</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
