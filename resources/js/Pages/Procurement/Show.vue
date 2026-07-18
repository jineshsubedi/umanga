<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    procurement: Object,
    canApprove: Boolean,
});

const actionForm = useForm({
    action: '',
    comment: '',
    signature_data: '',
});

const showActionModal = ref(false);
const selectedAction = ref('');
// A real app would use a canvas here for drawing signatures. For simplicity, we assume text or upload for now.

const openActionModal = (action) => {
    selectedAction.value = action;
    actionForm.action = action;
    showActionModal.value = true;
};

const submitAction = () => {
    // Collect signature data from canvas if applicable before submitting
    actionForm.post(route('procurement.action', props.procurement.id), {
        onSuccess: () => {
            showActionModal.value = false;
            actionForm.reset();
        }
    });
};
</script>

<template>
    <Head title="Procurement Request Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Procurement Request: {{ procurement.request_number }}
                </h2>
                <Link :href="route('procurement.index')" class="text-indigo-600 hover:underline text-sm">
                    &larr; Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Status Banner -->
                <div class="p-4 rounded-md shadow-sm bg-white dark:bg-gray-800 border-l-4" :class="{
                    'border-yellow-400': procurement.status === 'Pending',
                    'border-green-400': procurement.status === 'Approved' || procurement.status === 'Completed',
                    'border-red-400': procurement.status === 'Rejected' || procurement.status === 'Returned'
                }">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="text-lg font-bold" :class="{
                                'text-yellow-700': procurement.status === 'Pending',
                                'text-green-700': procurement.status === 'Approved' || procurement.status === 'Completed',
                                'text-red-700': procurement.status === 'Rejected' || procurement.status === 'Returned'
                            }">{{ procurement.status }}</p>
                        </div>
                        <div v-if="canApprove" class="flex gap-2 items-center">
                            <button @click="openActionModal('Approve')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                            <button @click="openActionModal('Return')" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Return</button>
                            <button @click="openActionModal('Reject')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                        </div>
                    </div>
                </div>

                <!-- Workflow Progress -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Approval Workflow</h3>
                    
                    <div v-if="!procurement.workflow" class="text-gray-500 text-sm italic">
                        Workflow will be determined upon submission.
                    </div>
                    
                    <div v-else class="flex items-center gap-2 overflow-x-auto pb-2">
                        <template v-for="(step, index) in procurement.workflow.steps" :key="step.id">
                            <div class="flex-1 flex flex-col items-center gap-1 min-w-[100px]">
                                <!-- Determine step status based on current_step_id and reviews -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                                    :class="{
                                        'bg-green-100 text-green-700': procurement.status === 'Approved' || procurement.status === 'Completed' || (procurement.current_step_id && step.step_order < (procurement.workflow.steps.find(s => s.id === procurement.current_step_id)?.step_order || 0)),
                                        'bg-blue-100 text-blue-700 ring-2 ring-blue-400': procurement.current_step_id === step.id,
                                        'bg-gray-100 text-gray-400': procurement.status === 'Pending' && (!procurement.current_step_id || step.step_order > (procurement.workflow.steps.find(s => s.id === procurement.current_step_id)?.step_order || 0)),
                                        'bg-red-100 text-red-700': (procurement.status === 'Rejected' || procurement.status === 'Returned') && procurement.reviews?.find(r => r.workflow_step_id === step.id && (r.action === 'Reject' || r.action === 'Return')),
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
                            <svg v-if="index < procurement.workflow.steps.length - 1" class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </template>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4 border-b pb-2">Request Details</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.requester?.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.date }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Company</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.company?.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.department?.name || '-' }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Item Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 font-bold">{{ procurement.item_name }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Specification</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.specification || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.quantity }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estimated Cost</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.estimated_cost ? '$' + procurement.estimated_cost : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.priority }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Vendor</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.vendor || '-' }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Purpose</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ procurement.purpose || '-' }}</dd>
                            </div>
                            <div v-if="procurement.attachment_path" class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Attachment</dt>
                                <dd class="mt-1 text-sm text-indigo-600 hover:underline">
                                    <a :href="'/storage/' + procurement.attachment_path" target="_blank">View Attachment</a>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Approval History -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4 border-b pb-2">Approval History</h3>
                        <div v-if="procurement.reviews && procurement.reviews.length > 0" class="space-y-6">
                            <div v-for="review in procurement.reviews" :key="review.id" class="flex flex-col sm:flex-row sm:items-start gap-4 p-4 border rounded-lg bg-gray-50 dark:bg-gray-700">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ review.reviewer?.name }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded" :class="{
                                            'bg-green-100 text-green-800': review.action === 'Approve',
                                            'bg-red-100 text-red-800': review.action === 'Reject',
                                            'bg-yellow-100 text-yellow-800': review.action === 'Return'
                                        }">{{ review.action }}d</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ review.comment || 'No comment provided.' }}</p>
                                    <p class="text-xs text-gray-400 mt-2">{{ new Date(review.created_at).toLocaleString() }}</p>
                                </div>
                                <div v-if="review.signature_data" class="shrink-0">
                                    <img :src="review.signature_data" alt="Signature" class="h-16 object-contain border p-1 bg-white">
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500 italic">
                            No approvals yet.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Action Modal -->
        <div v-if="showActionModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showActionModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitAction">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                        {{ selectedAction }} Request
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comments</label>
                                            <textarea v-model="actionForm.comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Add optional comments..."></textarea>
                                        </div>
                                        <div v-if="selectedAction === 'Approve'">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Digital Signature</label>
                                            <p class="text-xs text-gray-500 mb-2">By default, your saved signature will be used. If you want to draw a new one, you can implement a canvas here.</p>
                                            <!-- Signature pad would go here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="actionForm.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm" :class="{
                                'bg-green-600 hover:bg-green-700 focus:ring-green-500': selectedAction === 'Approve',
                                'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-500': selectedAction === 'Return',
                                'bg-red-600 hover:bg-red-700 focus:ring-red-500': selectedAction === 'Reject',
                            }">
                                Confirm {{ selectedAction }}
                            </button>
                            <button type="button" @click="showActionModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-white dark:border-gray-500 dark:hover:bg-gray-500">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
