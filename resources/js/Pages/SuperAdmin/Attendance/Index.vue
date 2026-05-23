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
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Attendance — All Companies</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Today's Platform-wide Stats -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_today }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Present Today</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.clocked_in }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Currently Working</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.clocked_out }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Clocked Out</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <div class="w-10 h-10 bg-red-50 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l-2-2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.absentees_count }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Absent Today</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_users }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Active Users</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4 flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date:</label>
                        <input type="date" v-model="dateFilter"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Company:</label>
                        <select v-model="companyFilter"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3">
                            <option value="">All Companies</option>
                            <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <button @click="applyFilter"
                        class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Apply
                    </button>
                    <button v-if="filterDate || filterCompanyId" @click="clearFilter"
                        class="px-4 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                        Clear Filters
                    </button>
                </div>

                <!-- Empty State -->
                <div v-if="Object.keys(groupedByDate).length === 0"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500 dark:text-gray-400">No attendance records found for the selected filters.</p>
                </div>

                <!-- Absentees List -->
                <div v-if="absentees && absentees.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-100 dark:border-red-900/50 overflow-hidden">
                    <div @click="showAbsentees = !showAbsentees" class="px-6 py-4 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-900/50 flex items-center justify-between cursor-pointer select-none group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm text-red-900 dark:text-red-200">Absentees List</h3>
                                <p class="text-xs text-red-600 dark:text-red-400">Users who have not clocked in on the selected date</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 text-xs font-semibold rounded-full">{{ absentees.length }} Absent</span>
                            <svg class="w-5 h-5 text-red-500 dark:text-red-400 transform transition-transform duration-200" :class="{ 'rotate-180': showAbsentees }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <div v-show="showAbsentees" class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div v-for="user in absentees" :key="user.id" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center font-bold text-sm flex-shrink-0 uppercase">
                                    {{ user.name?.charAt(0) }}
                                </div>
                                <div class="truncate">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize" :class="roleBadge(user.role)">{{ user.role }}</span>
                                        <span v-if="user.company" class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user.company.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Records grouped by date -->
                <div v-for="(records, date) in groupedByDate" :key="date"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

                    <!-- Date header -->
                    <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3"
                        :class="isToday(date) ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'bg-gray-50 dark:bg-gray-700/50'">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                            {{ formatDate(date) }}
                            <span v-if="isToday(date)" class="ml-2 px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-xs rounded-full">Today</span>
                        </h3>
                        <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">{{ records.length }} record{{ records.length !== 1 ? 's' : '' }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50/50 dark:bg-gray-700/30">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Clock In</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Clock Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Hrs</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <tr v-for="record in records" :key="record.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <!-- Employee -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                {{ record.user?.name?.charAt(0) }}
                                            </div>
                                            <span class="font-medium text-gray-900 dark:text-white text-sm">{{ record.user?.name }}</span>
                                        </div>
                                    </td>
                                    <!-- Company -->
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ record.user?.company?.name ?? '-' }}
                                    </td>
                                    <!-- Role -->
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="roleBadge(record.user?.role)">
                                            {{ record.user?.role }}
                                        </span>
                                    </td>
                                    <!-- Clock In + Location -->
                                    <td class="px-6 py-4 min-w-[200px]">
                                        <div class="flex items-start gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0 mt-1"></span>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ record.formatted_clock_in }}</p>
                                                <a v-if="record.clock_in_lat && record.clock_in_lng"
                                                    :href="mapsUrl(record.clock_in_lat, record.clock_in_lng)"
                                                    target="_blank"
                                                    class="text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mt-0.5 group">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span class="truncate max-w-[160px]" :title="record.clock_in_address">
                                                        {{ record.clock_in_address ? record.clock_in_address.split(',').slice(0, 2).join(',') : 'View map' }}
                                                    </span>
                                                </a>
                                                <span v-else class="text-xs text-gray-400 dark:text-gray-500">No location</span>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Clock Out + Location -->
                                    <td class="px-6 py-4 min-w-[200px]">
                                        <div v-if="record.formatted_clock_out" class="flex items-start gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0 mt-1"></span>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ record.formatted_clock_out }}</p>
                                                <a v-if="record.clock_out_lat && record.clock_out_lng"
                                                    :href="mapsUrl(record.clock_out_lat, record.clock_out_lng)"
                                                    target="_blank"
                                                    class="text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mt-0.5 group">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span class="truncate max-w-[160px]" :title="record.clock_out_address">
                                                        {{ record.clock_out_address ? record.clock_out_address.split(',').slice(0, 2).join(',') : 'View map' }}
                                                    </span>
                                                </a>
                                                <span v-else class="text-xs text-gray-400 dark:text-gray-500">No location</span>
                                            </div>
                                        </div>
                                        <span v-else class="text-sm text-gray-400 dark:text-gray-500 italic">Still in</span>
                                    </td>
                                    <!-- Total Hours -->
                                    <td class="px-6 py-4">
                                        <span v-if="record.total_hours" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ record.total_hours }}</span>
                                        <span v-else class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="record.clock_out
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'">
                                            {{ record.clock_out ? 'Completed' : 'In Progress' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
