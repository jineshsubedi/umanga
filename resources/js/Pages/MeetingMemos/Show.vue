<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ memo: Object });
const page = usePage();
const userId = page.props.auth.user.id;

const submit = () => router.post(route('memos.submit', props.memo.id));
const duplicate = () => {
    if (confirm('Create a new draft based on this rejected memo?')) {
        router.post(route('memos.duplicate', props.memo.id));
    }
};

// Determine if current user is the active reviewer
const isChecker   = computed(() => props.memo.checker_id === userId && props.memo.status === 'pending_checker');
const isVerifier  = computed(() => props.memo.verifier_id === userId && props.memo.status === 'pending_verifier');
const isApprover  = computed(() => props.memo.approver_id === userId && props.memo.status === 'pending_approver');
const canReview   = computed(() => isChecker.value || isVerifier.value || isApprover.value);
const isCreator   = computed(() => props.memo.created_by === userId);

const reviewForm = useForm({ status: '', comment: '' });
const submitting = ref(false);

const submitReview = (status) => {
    reviewForm.status = status;
    if (status === 'rejected' && !reviewForm.comment.trim()) return;
    reviewForm.post(route('memos.review', props.memo.id));
};

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
    pending_checker:   'Pending Checker Review',
    pending_verifier:  'Pending Verifier Review',
    pending_approver:  'Pending Final Approval',
    approved:          'Fully Approved',
    rejected:          'Rejected',
}[s] ?? s);
</script>

<template>
    <Head :title="memo.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('memos.index')" class="text-gray-400 dark:text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ memo.title }}</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-medium" :class="statusBadge(memo.status)">
                        {{ stepLabel(memo.status) }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Creator Actions -->
                <div v-if="isCreator && memo.status === 'draft'" class="flex gap-3">
                    <Link :href="route('memos.edit', memo.id)"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Edit
                    </Link>
                    <button @click="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                        Submit for Approval
                    </button>
                </div>
                
                <div v-if="isCreator && memo.status === 'rejected'" class="flex gap-3">
                    <button @click="duplicate"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition-colors">
                        Revise (Create New Draft)
                    </button>
                </div>

                <!-- PDF Download -->
                <div v-if="memo.status === 'approved'">
                    <a :href="route('memos.pdf', memo.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download PDF
                    </a>
                </div>

                <!-- Workflow Progress -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Approval Workflow</h3>
                    <div class="flex items-center gap-2">
                        <!-- Step 1: Checker -->
                        <div v-if="memo.checker_id" class="flex-1 flex flex-col items-center gap-1">
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
                        <svg v-if="memo.checker_id && (memo.verifier_id || memo.approver_id)" class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <!-- Step 2: Verifier -->
                        <div v-if="memo.verifier_id" class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                                :class="{
                                    'bg-green-100 text-green-700': memo.status !== 'pending_checker' && memo.status !== 'draft' && memo.status !== 'pending_verifier',
                                    'bg-yellow-100 text-yellow-700 ring-2 ring-yellow-400': memo.status === 'pending_verifier',
                                    'bg-gray-100 text-gray-400': ['draft','pending_checker'].includes(memo.status),
                                    'bg-red-100 text-red-700': memo.status === 'rejected' && memo.reviews?.find(r => r.reviewed_by === memo.verifier_id && r.status === 'rejected'),
                                }">
                                {{ memo.verifier?.name?.charAt(0) }}
                            </div>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">{{ memo.verifier?.name }}</p>
                            <p class="text-[11px] text-gray-500">Verifier</p>
                        </div>
                        <svg v-if="memo.verifier_id && memo.approver_id" class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <!-- Step 3: Approver -->
                        <div v-if="memo.approver_id" class="flex-1 flex flex-col items-center gap-1">
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

                <!-- Review Action Panel (for checker / verifier / approver) -->
                <div v-if="canReview" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/30 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your Review
                        <span class="text-sm font-normal text-gray-500">
                            ({{ isChecker ? 'Checking' : isVerifier ? 'Verifying' : 'Final Approval' }})
                        </span>
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Remarks <span class="text-gray-400 font-normal">(required if rejecting)</span>
                            </label>
                            <textarea v-model="reviewForm.comment" rows="3"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Add remarks or feedback..."></textarea>
                            <p v-if="reviewForm.errors.comment" class="text-red-600 text-sm mt-1">{{ reviewForm.errors.comment }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button @click="submitReview('approved')" :disabled="reviewForm.processing"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                                ✓ Approve
                            </button>
                            <button @click="submitReview('rejected')" :disabled="reviewForm.processing || !reviewForm.comment.trim()"
                                class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                                ✕ Reject
                            </button>
                        </div>
                        <p v-if="reviewForm.status === 'rejected' && !reviewForm.comment.trim()" class="text-amber-600 text-xs">Please add a rejection remark before rejecting.</p>
                    </div>
                </div>

                <!-- Memo Content -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-6 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-500">Meeting Date</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ memo.formatted_meeting_date }}</p>
                        </div>
                        <div class="border-l border-gray-100 dark:border-gray-700 pl-6">
                            <p class="text-sm text-gray-500">Created By</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ memo.creator?.name }}</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 text-sm text-gray-800 dark:text-gray-200 prose dark:prose-invert max-w-none" v-html="memo.content"></div>
                </div>

                <!-- Attachments -->
                <div v-if="memo.attachments && memo.attachments.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Attachments ({{ memo.attachments.length }})
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="attachment in memo.attachments" :key="attachment.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-indigo-200 transition-colors">
                            <div class="flex items-center space-x-3 truncate">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs flex-shrink-0 uppercase">
                                    {{ attachment.file_type || 'FILE' }}
                                </div>
                                <div class="truncate">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ attachment.file_name }}</p>
                                    <p class="text-xs text-gray-500">{{ attachment.file_size_formatted }}</p>
                                </div>
                            </div>
                            <a :href="`/storage/${attachment.file_path}`" target="_blank"
                                class="flex-shrink-0 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
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
                            class="flex gap-3 p-3 rounded-lg dark:bg-gray-900"
                            :class="review.status === 'approved' ? 'bg-green-50 border border-green-100 dark:border-green-700' : 'bg-red-50 border border-red-100 dark:border-red-700'">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                :class="review.status === 'approved' ? 'bg-green-500 dark:bg-green-700' : 'bg-red-500 dark:bg-red-700'">
                                {{ review.reviewer?.name?.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ review.reviewer?.name }}
                                    <span class="font-semibold capitalize ml-1" :class="review.status === 'approved' ? 'text-green-700' : 'text-red-700'">{{ review.status }}</span>
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
