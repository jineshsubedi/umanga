<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ 
    memos: Array, 
    counts: Object, 
    status: String,
    companies: Array,
    staffs: Array,
    approvers: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const company_id = ref(props.filters.company_id || '');
const staff_id = ref(props.filters.staff_id || '');
const approver_id = ref(props.filters.approver_id || '');

watch([search, company_id, staff_id, approver_id], ([newSearch, newCompany, newStaff, newApprover]) => {
    router.get(route('super-admin.meeting-memos.index'), {
        status: props.status,
        search: newSearch,
        company_id: newCompany,
        staff_id: newStaff,
        approver_id: newApprover,
    }, { preserveState: true, preserveScroll: true, replace: true });
});

const statusBadge = (s) => ({
    draft:    'bg-gray-100 text-gray-600',
    pending_manager:  'bg-yellow-100 text-yellow-700',
    pending_admin: 'bg-blue-100 text-blue-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const tabs = ['all', 'pending', 'approved', 'rejected'];
</script>

<template>
    <Head title="All Company Memos" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">All Company Memos</h2>
        </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Tabs -->
                <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg w-fit mb-6">
                    <Link v-for="tab in tabs" :key="tab"
                        :href="route('super-admin.meeting-memos.index', { status: tab })"
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors capitalize flex items-center gap-2"
                        :class="status === tab ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 dark:bg-gray-800 hover:text-gray-900'">
                        {{ tab }}
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full"
                            :class="status === tab ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-600'">
                            {{ counts[tab] }}
                        </span>
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" v-model="search" placeholder="Search by title..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="w-full sm:w-auto">
                        <select v-model="company_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Companies</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-auto">
                        <select v-model="staff_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Submitted By (All)</option>
                            <option v-for="staff in staffs" :key="staff.id" :value="staff.id">{{ staff.name }}</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-auto">
                        <select v-model="approver_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Approved By (All)</option>
                            <option v-for="approver in approvers" :key="approver.id" :value="approver.id">{{ approver.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Submitted By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="memo in memos" :key="memo.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ memo.title }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ memo.company?.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ memo.creator?.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ memo.formatted_meeting_date }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusBadge(memo.status)">
                                        {{ memo.status.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('super-admin.meeting-memos.show', memo.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="memos.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">No {{ status === 'all' ? '' : status }} memos found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
