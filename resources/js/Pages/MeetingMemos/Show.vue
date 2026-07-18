<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ memo: Object, canApprove: Boolean });
const page = usePage();
const userId = page.props.auth.user.id;

// Use useForm for submit so validation errors (e.g. no workflow configured) are captured
const submitForm = useForm({});
const submit = () => submitForm.post(route('memos.submit', props.memo.id));

const duplicateForm = useForm({});
const duplicate = () => {
    if (confirm('Create a new draft based on this rejected memo?')) {
        duplicateForm.post(route('memos.duplicate', props.memo.id));
    }
};

const isCreator = computed(() => props.memo.created_by === userId);

const formatApprover = (step) => {
    if (step.approver_type === 'department_head') return 'Department Head';
    if (step.approver_type === 'role') return `Role: ${step.approver_value}`;
    if (step.approver_type === 'specific_user') return `User ID: ${step.approver_value}`;
    return step.approver_type;
};

const reviewForm = useForm({ status: '', comment: '' });
const submitting = ref(false);

const submitReview = (status) => {
    reviewForm.status = status;
    if (status === 'rejected' && !reviewForm.comment.trim()) return;
    reviewForm.post(route('memos.review', props.memo.id));
};

const statusBadge = (s) => ({
    draft:     'bg-gray-100 text-gray-600',
    pending:   'bg-yellow-100 text-yellow-700',
    approved:  'bg-green-100 text-green-700',
    rejected:  'bg-red-100 text-red-700',
    returned:  'bg-orange-100 text-orange-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const stepLabel = (s) => ({
    draft:     'Draft',
    pending:   'Pending Approval',
    approved:  'Fully Approved',
    rejected:  'Rejected',
    returned:  'Returned for Revision',
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
                <div v-if="isCreator && memo.status === 'draft'" class="flex flex-col gap-2">
                    <div class="flex gap-3">
                        <Link :href="route('memos.edit', memo.id)"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Edit
                        </Link>
                        <button @click="submit" :disabled="submitForm.processing"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg text-sm font-medium transition-colors">
                            {{ submitForm.processing ? 'Submitting...' : 'Submit for Approval' }}
                        </button>
                    </div>
                    <!-- Show workflow config error if submit fails -->
                    <div v-if="submitForm.errors.workflow" class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ submitForm.errors.workflow }}</span>
                    </div>
                </div>

                <div v-if="isCreator && memo.status === 'rejected'" class="flex gap-3">
                    <button @click="duplicate" :disabled="duplicateForm.processing"
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
                    
                    <div v-if="!memo.workflow" class="text-gray-500 text-sm italic">
                        Workflow will be determined upon submission.
                    </div>
                    
                    <div v-else class="flex items-center gap-2 overflow-x-auto pb-2">
                        <template v-for="(step, index) in memo.workflow.steps" :key="step.id">
                            <div class="flex-1 flex flex-col items-center gap-1 min-w-[100px]">
                                <!-- Determine step status based on current_step_id and reviews -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                                    :class="{
                                        'bg-green-100 text-green-700': memo.status === 'approved' || (memo.current_step_id && step.step_order < (memo.workflow.steps.find(s => s.id === memo.current_step_id)?.step_order || 0)),
                                        'bg-blue-100 text-blue-700 ring-2 ring-blue-400': memo.current_step_id === step.id,
                                        'bg-gray-100 text-gray-400': memo.status === 'draft' || (memo.current_step_id && step.step_order > (memo.workflow.steps.find(s => s.id === memo.current_step_id)?.step_order || 0)),
                                        'bg-red-100 text-red-700': memo.status === 'rejected' && memo.reviews?.find(r => r.workflow_step_id === step.id && r.status === 'rejected'),
                                    }">
                                    {{ step.step_order }}
                                </div>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">
                                    {{ step.step_title || step.approver_type.replace(/_/g, ' ') }}
                                </p>
                                <p v-if="step.approver_value && !step.step_title" class="text-[11px] text-gray-500 truncate max-w-full px-1" :title="step.approver_value">
                                    {{ step.approver_value }}
                                </p>
                            </div>
                            <svg v-if="index < memo.workflow.steps.length - 1" class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </template>
                    </div>
                </div>

                <!-- Review Action Panel (for checker / verifier / approver) -->
                <div v-if="canApprove" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/30 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your Review
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
