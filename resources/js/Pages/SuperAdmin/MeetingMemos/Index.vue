<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ 
    memos: Object, 
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
    pending_checker:  'bg-yellow-100 text-yellow-700',
    pending_verifier: 'bg-orange-100 text-orange-700',
    pending_approver: 'bg-blue-100 text-blue-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const tabs = ['all', 'pending', 'approved', 'rejected'];
</script>

<template>
    <Head title="All Company Memos" />
    <AuthenticatedLayout>
        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-1/4 -left-1/4 w-2/3 h-2/3 rounded-full bg-cyan-400/10 blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
            </div>
            <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">All Company Memos</h1>
                        <p class="mt-1 text-purple-200 text-sm">Overview of meeting memos across all companies.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10 pb-12 space-y-6">
            <!-- Tabs -->
            <div class="flex gap-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl p-1.5 rounded-xl shadow-lg border border-white/30 dark:border-gray-700 w-fit">
                <Link v-for="tab in tabs" :key="tab"
                    :href="route('super-admin.meeting-memos.index', { status: tab })"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all capitalize flex items-center gap-2"
                    :class="status === tab ? 'bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700'">
                    {{ tab }}
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full"
                        :class="status === tab ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
                        {{ counts[tab] }}
                    </span>
                </Link>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" v-model="search" placeholder="Search by title..." class="pl-10 w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                    </div>
                </div>
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Company</label>
                    <select v-model="company_id" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                        <option value="">All Companies</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Submitted By</label>
                    <select v-model="staff_id" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                        <option value="">All Users</option>
                        <option v-for="staff in staffs" :key="staff.id" :value="staff.id">{{ staff.name }}</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[150px]">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Approved By</label>
                    <select v-model="approver_id" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                        <option value="">All Approvers</option>
                        <option v-for="approver in approvers" :key="approver.id" :value="approver.id">{{ approver.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submitted By</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="memo in memos.data" :key="memo.id" class="hover:bg-purple-50/50 dark:hover:bg-purple-900/10 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#7c3aed] to-[#b026ff] text-white flex items-center justify-center font-bold text-sm shadow group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="font-bold text-gray-900 dark:text-white text-sm">{{ memo.title }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ memo.company?.name || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                            {{ memo.creator?.name?.charAt(0) }}
                                        </div>
                                        {{ memo.creator?.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-400">{{ memo.formatted_meeting_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border" :class="statusBadge(memo.status)">
                                        {{ memo.status.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <Link :href="route('super-admin.meeting-memos.show', memo.id)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg border border-blue-100 dark:border-blue-800 transition-colors">
                                        View
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="memos.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="font-bold text-sm">No {{ status === 'all' ? '' : status }} memos found.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="memos.links && memos.links.length > 3" class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50 flex justify-center gap-1">
                        <template v-for="(link, p) in memos.links" :key="p">
                            <div v-if="link.url === null" class="px-4 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg" v-html="link.label"></div>
                            <Link v-else :href="link.url" class="px-4 py-2 text-sm font-bold border rounded-lg transition-colors"
                                :class="link.active ? 'bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white border-transparent shadow-md' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                v-html="link.label"></Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
