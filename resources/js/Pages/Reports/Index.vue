<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    companies: Array,
    users: Array,
    memocreators: Array,
    memoverifers: Array,
    memoapprovers: Array,
    isSuperAdmin: Boolean,
    exportUrl: String,
});

const form = useForm({
    status: 'all',
    company_id: '',
    date_from: '',
    date_to: '',
    memocreators: '',
    memoverifers: '',
    memoapprovers: '',
    created_by: '',
});

const isExporting = ref(false);

const filteredUsers = computed(() => {
    if (props.isSuperAdmin && form.company_id) {
        return props.users.filter(u => u.company_id == form.company_id);
    }
    return props.users;
});
const filteredMemocreators = computed(() => {
    if (props.isSuperAdmin && form.company_id) {
        return props.memocreators.filter(u => u.company_id == form.company_id);
    }
    return props.memocreators;
});
const filteredMemoverifers = computed(() => {
    if (props.isSuperAdmin && form.company_id) {
        return props.memoverifers.filter(u => u.company_id == form.company_id);
    }
    return props.memoverifers;
});
const filteredMemoapprovers = computed(() => {
    if (props.isSuperAdmin && form.company_id) {
        return props.memoapprovers.filter(u => u.company_id == form.company_id);
    }
    return props.memoapprovers;
});

const generateReport = () => {
    isExporting.value = true;

    const params = new URLSearchParams();
    if (form.status && form.status !== 'all') params.append('status', form.status);
    if (form.company_id) params.append('company_id', form.company_id);
    if (form.date_from) params.append('date_from', form.date_from);
    if (form.date_to) params.append('date_to', form.date_to);
    if (form.created_by) params.append('created_by', form.created_by);
    if (form.memocreators) params.append('memocreators', form.memocreators);
    if (form.memoverifers) params.append('memoverifers', form.memoverifers);
    if (form.memoapprovers) params.append('memoapprovers', form.memoapprovers);

    const url = props.exportUrl + '?' + params.toString();

    // Create a hidden anchor tag to trigger the download
    const a = document.createElement('a');
    a.href = url;
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    setTimeout(() => {
        isExporting.value = false;
    }, 3000);
};

const resetFilters = () => {
    form.status = 'all';
    form.company_id = '';
    form.date_from = '';
    form.date_to = '';
    form.memocreators = '';
    form.memoverifers = '';
    form.memoapprovers = '';
    form.created_by = '';
};

const statuses = [
    { value: 'all', label: 'All Statuses' },
    { value: 'draft', label: 'Draft' },
    { value: 'pending', label: 'Pending Review' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
];
</script>

<template>
    <Head title="Reports" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Memo Reports</h2>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Page Header Card -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 mb-8 shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Generate Memo Report</h3>
                            <p class="text-indigo-100 text-sm mt-1">Apply filters and export your memo data as an Excel spreadsheet.</p>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Row 1: Status + Company -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select v-model="form.status"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                                </select>
                            </div>

                            <div v-if="isSuperAdmin">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company</label>
                                <select v-model="form.company_id"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                    <option value="">All Companies</option>
                                    <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Date Range -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                                <input type="date" v-model="form.date_from"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                                <input type="date" v-model="form.date_to"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
                            </div>
                        </div>

                        <!-- Row 3: Users -->
                        <div v-if="isSuperAdmin" class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Created By</label>
                                <select v-model="form.memocreators"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                    <option value="">All Users</option>
                                    <option v-for="u in filteredMemocreators" :key="u.id" :value="u.id">{{ u.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Memo Checkers/Verifiers</label>
                                <select v-model="form.memoverifers"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                    <option value="">All Users</option>
                                    <option v-for="u in filteredMemoverifers" :key="u.id" :value="u.id">{{ u.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Memo Approvers</label>
                                <select v-model="form.memoapprovers"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                    <option value="">All Users</option>
                                    <option v-for="u in filteredMemoapprovers" :key="u.id" :value="u.id">{{ u.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-else class="bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/30 rounded-xl p-4 text-sm text-indigo-700 dark:text-indigo-400 font-semibold">
                            🔒 Note: As a staff member, your generated reports are automatically scoped strictly to your own memos.
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-5 bg-gray-50/80 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-4">
                        <button @click="resetFilters"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filters
                        </button>

                        <button @click="generateReport" :disabled="isExporting"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-60 disabled:cursor-not-allowed hover:-translate-y-0.5">
                            <svg v-if="!isExporting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isExporting ? 'Generating...' : 'Generate Excel Report' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
