<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    attendances:     Array,
    stats:           Object,
    absentees:       Array,
    companies:       Array,
    filterDate:      String,
    filterCompanyId: [String, Number],
});

const dateFilter    = ref(props.filterDate      || '');
const companyFilter = ref(props.filterCompanyId || '');
const showAbsentees = ref(false);

const applyFilter = () => {
    router.get(route('super-admin.attendance.index'), {
        date:       dateFilter.value,
        company_id: companyFilter.value,
    }, { preserveState: true });
};

const clearFilter = () => {
    dateFilter.value    = '';
    companyFilter.value = '';
    router.get(route('super-admin.attendance.index'));
};

const roleBadge = (role) => ({
    admin:   'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    manager: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    staff:  'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
}[role] ?? 'bg-gray-100 text-gray-700');

const formatDate = (d) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
};

const isToday = (dateStr) => {
    return dateStr === new Date().toISOString().split('T')[0];
};

// Group by date
const groupedByDate = computed(() => {
    const groups = {};
    props.attendances.forEach(a => {
        const key = a.date;
        if (!groups[key]) groups[key] = [];
        groups[key].push(a);
    });
    return groups;
});

const mapsUrl = (lat, lng) => lat && lng
    ? `https://www.google.com/maps?q=${lat},${lng}`
    : null;
</script>

<template>
    <Head title="Attendance — All Companies" />
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">System Attendance</h1>
                        <p class="mt-1 text-purple-200 text-sm">Platform-wide attendance overview and tracking.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10 pb-12 space-y-6">

            <!-- Today's Platform-wide Stats -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.total_today }}</p>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Present Today</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.clocked_in }}</p>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Clocked In</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#7c3aed] to-[#b026ff] text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.clocked_out }}</p>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Clocked Out</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l-2-2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.absentees_count }}</p>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Absent Today</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ stats.total_users }}</p>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Total Active Users</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-wrap items-end gap-4">
                <div class="min-w-[200px]">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Date Filter</label>
                    <input type="date" v-model="dateFilter"
                        class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]" />
                </div>
                <div class="min-w-[200px]">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Company Filter</label>
                    <select v-model="companyFilter"
                        class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                        <option value="">All Companies</option>
                        <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <button @click="applyFilter"
                    class="px-5 py-2.5 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl hover:shadow-lg transition-all hover:-translate-y-0.5 border border-white/10">
                    Apply Filter
                </button>
                <button v-if="filterDate || filterCompanyId" @click="clearFilter"
                    class="px-5 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-bold rounded-xl transition-all">
                    Clear Filters
                </button>
            </div>

            <!-- Empty State -->
            <div v-if="Object.keys(groupedByDate).length === 0"
                class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 p-16 text-center">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-gray-500 dark:text-gray-400 font-bold">No attendance records found for the selected filters.</p>
            </div>

            <!-- Absentees List -->
            <div v-if="absentees && absentees.length > 0" class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-red-200 dark:border-red-900/50 overflow-hidden">
                <div @click="showAbsentees = !showAbsentees" class="px-6 py-4 bg-red-50/80 dark:bg-red-900/20 border-b border-red-100 dark:border-red-900/50 flex items-center justify-between cursor-pointer select-none group transition-colors hover:bg-red-100/50 dark:hover:bg-red-900/40">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-400 to-red-600 text-white flex items-center justify-center font-bold shadow-md group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-red-900 dark:text-red-300">Absentees List</h3>
                            <p class="text-xs font-medium text-red-600 dark:text-red-400 mt-0.5">Users who have not clocked in on the selected date</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-white dark:bg-red-900/50 text-red-700 dark:text-red-300 text-[11px] font-bold rounded-lg shadow-sm border border-red-100 dark:border-red-800">{{ absentees.length }} Absent</span>
                        <svg class="w-5 h-5 text-red-500 dark:text-red-400 transform transition-transform duration-300" :class="{ 'rotate-180': showAbsentees }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                <div v-show="showAbsentees" class="p-6 bg-white/50 dark:bg-gray-800/50">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div v-for="user in absentees" :key="user.id" class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-100 to-rose-200 dark:from-red-900/50 dark:to-rose-900/50 text-red-700 dark:text-red-300 flex items-center justify-center font-bold text-sm flex-shrink-0 uppercase group-hover:scale-110 transition-transform">
                                {{ user.name?.charAt(0) }}
                            </div>
                            <div class="truncate">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ user.name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded border capitalize" :class="roleBadge(user.role)">{{ user.role }}</span>
                                    <span v-if="user.company" class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 truncate">{{ user.company.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Records grouped by date -->
            <div v-for="(records, date) in groupedByDate" :key="date"
                class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">

                <!-- Date header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3"
                    :class="isToday(date) ? 'bg-purple-50/50 dark:bg-purple-900/20' : 'bg-gray-50/50 dark:bg-gray-700/30'">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="isToday(date) ? 'bg-[#7c3aed] text-white shadow-md' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                        {{ formatDate(date) }}
                        <span v-if="isToday(date)" class="px-2 py-0.5 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-[10px] font-bold rounded-md shadow-sm uppercase tracking-wider">Today</span>
                    </h3>
                    <span class="ml-auto text-xs font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded-lg">{{ records.length }} record{{ records.length !== 1 ? 's' : '' }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/30 dark:bg-gray-900/20">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clock In</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clock Out</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Hrs</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="record in records" :key="record.id"
                                class="hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-colors group">
                                <!-- Employee -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                            {{ record.user?.name?.charAt(0) }}
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ record.user?.name }}</span>
                                    </div>
                                </td>
                                <!-- Company -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ record.user?.company?.name ?? '-' }}
                                </td>
                                <!-- Role -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border" :class="roleBadge(record.user?.role)">
                                        {{ record.user?.role }}
                                    </span>
                                </td>
                                <!-- Clock In + Location -->
                                <td class="px-6 py-4 min-w-[200px] whitespace-nowrap">
                                    <div class="flex items-start gap-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0 mt-1.5 shadow-[0_0_8px_rgba(52,211,153,0.5)]"></div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ record.formatted_clock_in }}</p>
                                            <a v-if="record.clock_in_lat && record.clock_in_lng"
                                                :href="mapsUrl(record.clock_in_lat, record.clock_in_lng)"
                                                target="_blank"
                                                class="text-[11px] font-semibold text-indigo-500 hover:text-[#7c3aed] dark:text-indigo-400 flex items-center gap-1 mt-0.5 group/link transition-colors">
                                                <svg class="w-3 h-3 group-hover/link:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span class="truncate max-w-[160px]" :title="record.clock_in_address">
                                                    {{ record.clock_in_address ? record.clock_in_address.split(',').slice(0, 2).join(',') : 'View map' }}
                                                </span>
                                            </a>
                                            <span v-else class="text-[11px] font-medium text-gray-400 dark:text-gray-500">No location</span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Clock Out + Location -->
                                <td class="px-6 py-4 min-w-[200px] whitespace-nowrap">
                                    <div v-if="record.formatted_clock_out" class="flex items-start gap-2">
                                        <div class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0 mt-1.5 shadow-[0_0_8px_rgba(248,113,113,0.5)]"></div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ record.formatted_clock_out }}</p>
                                            <a v-if="record.clock_out_lat && record.clock_out_lng"
                                                :href="mapsUrl(record.clock_out_lat, record.clock_out_lng)"
                                                target="_blank"
                                                class="text-[11px] font-semibold text-indigo-500 hover:text-[#7c3aed] dark:text-indigo-400 flex items-center gap-1 mt-0.5 group/link transition-colors">
                                                <svg class="w-3 h-3 group-hover/link:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span class="truncate max-w-[160px]" :title="record.clock_out_address">
                                                    {{ record.clock_out_address ? record.clock_out_address.split(',').slice(0, 2).join(',') : 'View map' }}
                                                </span>
                                            </a>
                                            <span v-else class="text-[11px] font-medium text-gray-400 dark:text-gray-500">No location</span>
                                        </div>
                                    </div>
                                    <span v-else class="text-xs font-bold text-purple-500 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-2 py-1 rounded-md border border-purple-100 dark:border-purple-800">Working</span>
                                </td>
                                <!-- Total Hours -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="record.total_hours" class="text-sm font-black text-transparent bg-clip-text bg-gradient-to-r from-[#7c3aed] to-[#b026ff]">{{ record.total_hours }}</span>
                                    <span v-else class="text-gray-400 dark:text-gray-500 text-sm font-bold">-</span>
                                </td>
                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border"
                                        :class="record.clock_out
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800'
                                            : 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800'">
                                        {{ record.clock_out ? 'Completed' : 'Active' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
