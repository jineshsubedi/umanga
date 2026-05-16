<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    attendances: Array,
    stats:       Object,
    filterDate:  String,
});

const dateFilter = ref(props.filterDate || '');

const applyFilter = () => {
    router.get(route('admin.attendance.index'), { date: dateFilter.value }, { preserveState: true });
};

const clearFilter = () => {
    dateFilter.value = '';
    router.get(route('admin.attendance.index'));
};

const roleBadge = (role) => ({
    admin:   'bg-blue-100 text-blue-700',
    manager: 'bg-green-100 text-green-700',
    client:  'bg-orange-100 text-orange-700',
}[role] ?? 'bg-gray-100 text-gray-700');

const formatDate = (d) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
};

// Group by date for a cleaner display
const groupedByDate = computed(() => {
    const groups = {};
    props.attendances.forEach(a => {
        const dateKey = a.date;
        if (!groups[dateKey]) groups[dateKey] = [];
        groups[dateKey].push(a);
    });
    return groups;
});

const isToday = (dateStr) => {
    const today = new Date().toISOString().split('T')[0];
    return dateStr === today;
};

const mapsUrl = (lat, lng) => lat && lng
    ? `https://www.google.com/maps?q=${lat},${lng}`
    : null;
</script>

<template>
    <Head title="Attendance Records" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Attendance Records</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Today's Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Currently In</p>
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
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.absent_today }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Absent Today</p>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4 flex flex-wrap items-center gap-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Date:</label>
                    <input
                        type="date"
                        v-model="dateFilter"
                        class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-3"
                    />
                    <button @click="applyFilter"
                        class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Apply
                    </button>
                    <button v-if="filterDate" @click="clearFilter"
                        class="px-4 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                        Clear
                    </button>
                    <span v-if="filterDate" class="text-sm text-gray-500 dark:text-gray-400">
                        Showing: <strong class="text-gray-700 dark:text-gray-300">{{ formatDate(filterDate) }}</strong>
                    </span>
                </div>

                <!-- Attendance Records -->
                <div v-if="Object.keys(groupedByDate).length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500 dark:text-gray-400">No attendance records found.</p>
                </div>

                <div v-for="(records, date) in groupedByDate" :key="date" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <!-- Date Header -->
                    <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3"
                        :class="isToday(date) ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'bg-gray-50 dark:bg-gray-700/50'">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h3 class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                            {{ formatDate(date) }}
                            <span v-if="isToday(date)" class="ml-2 px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-xs rounded-full">Today</span>
                        </h3>
                        <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">{{ records.length }} record{{ records.length !== 1 ? 's' : '' }}</span>
                    </div>

                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50/50 dark:bg-gray-700/30">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Clock In</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Clock Out</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="record in records" :key="record.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm">
                                            {{ record.user?.name?.charAt(0) }}
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-white text-sm">{{ record.user?.name }}</span>
                                    </div>
                                </td>
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
                                <td class="px-6 py-4">
                                    <span v-if="record.total_hours" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ record.total_hours }}
                                    </span>
                                    <span v-else class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                </td>
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
    </AuthenticatedLayout>
</template>
