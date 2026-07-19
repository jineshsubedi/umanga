<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    requests: Object,
    counts: Object,
    status: String,
    filters: Object,
});

const page = usePage();
const currentUser = page.props.auth.user;
const isAuthorized = currentUser.role === 'manager' || currentUser.role === 'admin';

const statusConfig = {
    Pending:   { cls: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800', dot: 'bg-yellow-400' },
    Approved:  { cls: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800', dot: 'bg-emerald-400' },
    Completed: { cls: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800', dot: 'bg-emerald-400' },
    Rejected:  { cls: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800', dot: 'bg-red-400' },
    Returned:  { cls: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 border-orange-200 dark:border-orange-800', dot: 'bg-orange-400' },
};
const statusBadge = (s) => statusConfig[s] ?? statusConfig.Pending;

const tabs = computed(() => [
    { name: 'All', value: 'all', count: props.counts.all },
    { name: 'Pending', value: 'pending', count: props.counts.pending },
    { name: 'Approved', value: 'approved', count: props.counts.approved },
    { name: 'Rejected/Returned', value: 'rejected', count: props.counts.rejected },
]);

const search = ref(props.filters?.search || '');
const creator = ref(props.filters?.creator || '');
const date = ref(props.filters?.date || '');

const activeElementId = ref(null);
const isLoading = ref(false);
let filterTimeout = null;

const runFilter = () => {
    const activeEl = document.activeElement;
    if (activeEl && (activeEl.id === 'search-input' || activeEl.id === 'creator-input' || activeEl.id === 'date-input')) {
        activeElementId.value = activeEl.id;
    } else {
        activeElementId.value = null;
    }

    isLoading.value = true;

    router.get(route('procurement.index'), {
        status: props.status,
        search: search.value,
        creator: creator.value,
        date: date.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['requests', 'counts', 'status', 'filters'],
        onFinish: () => {
            isLoading.value = false;
            if (activeElementId.value) {
                setTimeout(() => {
                    const el = document.getElementById(activeElementId.value);
                    if (el) {
                        el.focus();
                        if (el.setSelectionRange && (el.type === 'text' || el.type === 'search')) {
                            const valLen = el.value.length;
                            el.setSelectionRange(valLen, valLen);
                        }
                    }
                }, 10);
            }
        }
    });
};

watch([search, creator, date], () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(runFilter, 300);
});

const clearFilters = () => {
    search.value = '';
    creator.value = '';
    date.value = '';
};
</script>

<template>
    <Head title="Procurement Requests" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Procurement Requests</h2>
        </template>

        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
            </div>
            <div class="max-w-7xl mx-auto relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Procurement</h1>
                    <p class="mt-1 text-purple-200 text-sm">Manage and track all your procurement requests.</p>
                </div>
                <Link :href="route('procurement.create')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#5b14b8] text-sm font-bold rounded-xl hover:bg-purple-50 transition-all hover:-translate-y-0.5 shadow-lg self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Request
                </Link>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-10 pb-12">
            <!-- Filter Tabs -->
            <div class="flex space-x-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl p-1.5 rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 w-fit mb-6 overflow-x-auto max-w-full">
                <Link v-for="tab in tabs" :key="tab.value"
                    :href="route('procurement.index', { status: tab.value, search: search, creator: creator, date: date })"
                    preserve-state
                    preserve-scroll
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-200 whitespace-nowrap"
                    :class="status === tab.value
                        ? 'bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white shadow-md'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700'">
                    {{ tab.name }}
                    <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full"
                        :class="status === tab.value ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">{{ tab.count }}</span>
                </Link>
            </div>

            <!-- Filters (Only for manager/admin) -->
            <div v-if="isAuthorized" 
                class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl p-5 rounded-2xl shadow-md border border-white/30 dark:border-gray-700 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Item Name</label>
                    <input type="text" v-model="search" id="search-input" placeholder="Search item or number..."
                        class="w-full bg-gray-50 dark:bg-gray-950 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:text-white px-4 py-2.5" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Requester</label>
                    <input type="text" v-model="creator" id="creator-input" placeholder="Search by requester..."
                        class="w-full bg-gray-50 dark:bg-gray-950 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:text-white px-4 py-2.5" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Date Selection</label>
                    <input type="date" v-model="date" id="date-input"
                        class="w-full bg-gray-50 dark:bg-gray-950 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:text-white px-4 py-2.5" />
                </div>
                <div>
                    <button @click="clearFilters" 
                        class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-bold rounded-xl transition-all">
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Procurement Cards -->
            <div class="space-y-4" :class="{ 'opacity-50 pointer-events-none transition-opacity duration-150': isLoading }">
                <div v-for="req in requests.data" :key="req.id"
                    class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-md border border-white/30 dark:border-gray-700 p-5 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 group">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2.5 mb-2 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold capitalize border" :class="statusBadge(req.status).cls">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="statusBadge(req.status).dot"></span>
                                    {{ req.status }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ req.date }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">{{ req.request_number }}</span>
                                <span v-if="req.requested_by === page.props.auth.user.id" class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-lg text-[11px] font-bold border border-purple-200 dark:border-purple-800">My request</span>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white truncate group-hover:text-[#7c3aed] dark:group-hover:text-[#a78bfa] transition-colors">{{ req.item_name }}</h3>

                            <!-- Current Step -->
                            <div class="flex items-center gap-2 mt-3 flex-wrap">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Workflow Status:</span>
                                <div class="flex items-center gap-1">
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border transition-all bg-[#7c3aed] text-white border-[#7c3aed] ring-2 ring-purple-300">
                                        {{ req.status === 'Pending' ? 'Awaiting ' + (req.current_step?.step_title || 'Approval') : req.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <Link :href="route('procurement.show', req.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#7c3aed] dark:text-[#a78bfa] bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/40 rounded-lg border border-purple-100 dark:border-purple-800 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="requests.data.length === 0" class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30 dark:border-gray-700 p-16 text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 text-[#7c3aed]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 font-bold text-lg mb-2">No requests found</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mb-6">There are no procurement requests matching this filter.</p>
                    <Link :href="route('procurement.create')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create a Request
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="requests.links && requests.links.length > 3" class="mt-6 flex justify-center gap-1.5">
                    <template v-for="(link, p) in requests.links" :key="p">
                        <div v-if="link.url === null" class="px-3.5 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl cursor-default" v-html="link.label"></div>
                        <Link v-else :href="link.url" class="px-3.5 py-2 text-sm font-bold border rounded-xl transition-all duration-200 hover:-translate-y-0.5"
                            :class="link.active ? 'bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white border-transparent shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            v-html="link.label"></Link>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
