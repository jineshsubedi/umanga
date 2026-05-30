<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ memo: Object });

const statusBadge = (s) => ({
    draft:             'bg-gray-100 text-gray-600',
    pending_checker:   'bg-yellow-100 text-yellow-700',
    pending_verifier:  'bg-orange-100 text-orange-700',
    pending_approver:  'bg-blue-100 text-blue-700',
    approved:          'bg-green-100 text-green-700',
    rejected:          'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const stepLabel = (s) => ({
    draft:             'Draft',
    pending_checker:   'Pending Checker',
    pending_verifier:  'Pending Verifier',
    pending_approver:  'Pending Approver',
    approved:          'Approved',
    rejected:          'Rejected',
}[s] ?? s);
</script>

<template>
    <Head :title="memo.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('super-admin.meeting-memos.index')" class="text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <div class="flex items-center gap-3">
                    <span class="text-sm px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 font-medium">{{ memo.company?.name }}</span>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Title + PDF -->
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-400">{{ memo.title }}</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-medium" :class="statusBadge(memo.status)">
                        {{ stepLabel(memo.status) }}
                    </span>
                    <a v-if="memo.status === 'approved'" :href="route('super-admin.meeting-memos.pdf', memo.id)" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors ml-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download PDF
                    </a>
                </div>

                <!-- Workflow Progress -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Approval Workflow</h3>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                                :class="{
                                    'bg-green-100 text-green-700': memo.status !== 'pending_checker' && memo.status !== 'draft',
                                    'bg-yellow-100 text-yellow-700 ring-2 ring-yellow-400': memo.status === 'pending_checker',
                                    'bg-gray-100 text-gray-400': memo.status === 'draft',
                                    'bg-red-100 text-red-700': memo.status === 'rejected' && memo.reviews?.find(r => r.reviewed_by === memo.checker_id && r.status === 'rejected'),
                                }">
                                {{ memo.checker?.name?.charAt(0) }}
                            </div>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">{{ memo.checker?.name }}</p>
                            <p class="text-[11px] text-gray-500">Checker</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                                :class="{
                                    'bg-green-100 text-green-700': ['pending_approver','approved'].includes(memo.status),
                                    'bg-orange-100 text-orange-700 ring-2 ring-orange-400': memo.status === 'pending_verifier',
                                    'bg-gray-100 text-gray-400': ['draft','pending_checker'].includes(memo.status),
                                    'bg-red-100 text-red-700': memo.status === 'rejected' && memo.reviews?.find(r => r.reviewed_by === memo.verifier_id && r.status === 'rejected'),
                                }">
                                {{ memo.verifier?.name?.charAt(0) }}
                            </div>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">{{ memo.verifier?.name }}</p>
                            <p class="text-[11px] text-gray-500">Verifier</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                                :class="{
                                    'bg-green-100 text-green-700': memo.status === 'approved',
                                    'bg-blue-100 text-blue-700 ring-2 ring-blue-400': memo.status === 'pending_approver',
                                    'bg-gray-100 text-gray-400': ['draft','pending_checker','pending_verifier'].includes(memo.status),
                                    'bg-red-100 text-red-700': memo.status === 'rejected' && memo.reviews?.find(r => r.reviewed_by === memo.approver_id && r.status === 'rejected'),
                                }">
                                {{ memo.approver?.name?.charAt(0) }}
                            </div>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">{{ memo.approver?.name }}</p>
                            <p class="text-[11px] text-gray-500">Approver</p>
                        </div>
                    </div>
                </div>

                <!-- Memo Content -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Submitted by <span class="font-medium text-gray-800 dark:text-gray-200">{{ memo.creator?.name }}</span></p>
                            <p class="text-sm text-gray-500">Meeting date: <span class="font-medium text-gray-800 dark:text-gray-200">{{ memo.formatted_meeting_date }}</span></p>
                        </div>
                    </div>
                    <div class="prose max-w-none">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 text-gray-800 dark:text-gray-200 text-sm leading-relaxed" v-html="memo.content"></div>
                    </div>
                </div>

                <!-- Attachments -->
                <div v-if="memo.attachments && memo.attachments.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Attachments ({{ memo.attachments.length }})
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="attachment in memo.attachments" :key="attachment.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-600 transition-colors">
                            <div class="flex items-center space-x-3 truncate">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs flex-shrink-0 uppercase">
                                    {{ attachment.file_type || 'FILE' }}
                                </div>
                                <div class="truncate">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">{{ attachment.file_name }}</p>
                                    <p class="text-xs text-gray-500">{{ attachment.file_size_formatted }}</p>
                                </div>
                            </div>
                            <a :href="`/storage/${attachment.file_path}`" target="_blank" class="flex-shrink-0 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Review History -->
                <div v-if="memo.reviews?.length" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Review History</h3>
                    <div class="space-y-3">
                        <div v-for="review in memo.reviews" :key="review.id"
                            class="flex gap-3 p-3 rounded-lg"
                            :class="review.status === 'approved' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                :class="review.status === 'approved' ? 'bg-green-500' : 'bg-red-500'">
                                {{ review.reviewer?.name?.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ review.reviewer?.name }}
                                    <span class="font-normal capitalize" :class="review.status === 'approved' ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'">{{ review.status }}</span>
                                </p>
                                <p v-if="review.comment" class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ review.comment }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
