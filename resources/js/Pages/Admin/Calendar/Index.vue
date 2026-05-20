<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Calendar } from 'v-calendar';
import 'v-calendar/style.css';

const props = defineProps({
    attendances: Array,   // [{ date, role, count }]
    minutes: Array,       // [{ date, count }]
    totalUsersByRole: Object, // { manager: { count }, client: { count }, admin: { count } }
});

const isDark = ref(false);
const selectedDate = ref(new Date());
const today = new Date();

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
    const observer = new MutationObserver(() => {
        isDark.value = document.documentElement.classList.contains('dark');
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

const toDateStr = (d) => {
    if (!d) return '';
    if (typeof d === 'string') {
        return d.substring(0, 10);
    }
    if (d instanceof Date) {
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }
    return '';
};

const selectedDateStr = computed(() => {
    if (!selectedDate.value) return '';
    return toDateStr(selectedDate.value);
});

// Active dates = union of attendance and minutes dates
const activeDates = computed(() => {
    const dates = new Set();
    props.attendances.forEach(a => dates.add(a.date));
    props.minutes.forEach(m => dates.add(m.date));
    return [...dates];
});

// Attendance detail for selected date, keyed by role
const selectedAttendanceByRole = computed(() => {
    const map = {};
    props.attendances
        .filter(a => toDateStr(a.date) === selectedDateStr.value)
        .forEach(a => { map[a.role] = a.count; });
    return map;
});

// Minutes count for selected date
const selectedMinutesCount = computed(() =>
    props.minutes.find(m => toDateStr(m.date) === selectedDateStr.value)?.count ?? 0
);

// Compute present/absent per role for selected date
const roles = ['manager', 'client', 'admin'];
const roleInfo = computed(() =>
    roles.map(r => {
        const total = props.totalUsersByRole?.[r]?.count ?? 0;
        const present = selectedAttendanceByRole.value[r] ?? 0;
        return { role: r, total, present, absent: Math.max(0, total - present) };
    }).filter(r => r.total > 0)
);

// Calendar attributes: one dot per active date
const attributes = computed(() => {
    const dateMap = {};

    props.attendances.forEach(a => {
        if (!dateMap[a.date]) dateMap[a.date] = { hasAttendance: false, hasMinutes: false };
        dateMap[a.date].hasAttendance = true;
    });
    props.minutes.forEach(m => {
        if (!dateMap[m.date]) dateMap[m.date] = { hasAttendance: false, hasMinutes: false };
        dateMap[m.date].hasMinutes = true;
    });

    const attrs = Object.entries(dateMap).map(([date, info]) => ({
        key: `day-${date}`,
        dates: date,
        dot: info.hasAttendance && info.hasMinutes
            ? [{ color: 'green' }, { color: 'indigo' }]
            : info.hasAttendance
                ? { color: 'green' }
                : { color: 'indigo' },
    }));

    if (selectedDate.value) {
        attrs.push({
            key: 'selected-date',
            dates: selectedDate.value,
            highlight: { color: 'indigo', fillMode: 'outline' },
        });
    }

    return attrs;
});

const onDayClick = (day) => {
    selectedDate.value = day.date;
};

const formattedSelectedDate = computed(() => {
    if (!selectedDate.value) return 'Select a date';
    return selectedDate.value.toLocaleDateString(undefined, {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
});

const isToday = computed(() => {
    if (!selectedDate.value) return false;
    return toDateStr(selectedDate.value) === toDateStr(today);
});

const roleLabel = { manager: 'Managers', client: 'Clients', admin: 'Admins' };
const roleColor = {
    manager: 'text-blue-600 dark:text-blue-400',
    client: 'text-purple-600 dark:text-purple-400',
    admin: 'text-indigo-600 dark:text-indigo-400',
};
const roleBg = {
    manager: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
    client: 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800',
    admin: 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800',
};
</script>

<template>
    <Head title="Company Calendar" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Company Calendar</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-6">

                    <!-- ── Left: Calendar ──────────────────────────────── -->
                    <div class="lg:w-auto flex-shrink-0">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                            <Calendar
                                @dayclick="onDayClick"
                                :attributes="attributes"
                                :is-dark="isDark"
                                expanded
                                class="border-none !bg-transparent"
                            />
                            <!-- Legend -->
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-x-4 gap-y-2 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span> Attendance Recorded
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Meeting Minutes
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Right: Day Details Panel ────────────────────── -->
                    <div class="flex-1 min-w-0">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

                            <!-- Panel Header -->
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                        {{ isToday ? 'Today' : 'Selected Date' }}
                                    </p>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ formattedSelectedDate }}</h3>
                                </div>
                                <span v-if="isToday" class="px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded-full">Today</span>
                            </div>

                            <div class="p-6 space-y-6">

                                <!-- Meeting Minutes Summary -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Meeting Minutes
                                    </h4>
                                    <div class="flex items-center gap-5 p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                                        <div class="flex-1 text-center">
                                            <p class="text-3xl font-extrabold text-indigo-700 dark:text-indigo-300">{{ selectedMinutesCount }}</p>
                                            <p class="text-xs text-indigo-500 dark:text-indigo-400 mt-1">Total Scheduled</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attendance By Role -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Attendance Breakdown
                                    </h4>

                                    <div v-if="roleInfo.length > 0" class="space-y-3">
                                        <div v-for="r in roleInfo" :key="r.role"
                                            class="p-4 rounded-xl border"
                                            :class="roleBg[r.role]">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-sm font-bold capitalize" :class="roleColor[r.role]">{{ roleLabel[r.role] }}</span>
                                                <span class="text-xs text-gray-400">Total: {{ r.total }}</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                                                    <p class="text-2xl font-extrabold text-green-600 dark:text-green-400">{{ r.present }}</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">Present</p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                                                    <p class="text-2xl font-extrabold text-red-500 dark:text-red-400">{{ r.absent }}</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">Absent</p>
                                                </div>
                                            </div>
                                            <!-- Attendance bar -->
                                            <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-green-500 rounded-full transition-all duration-500"
                                                    :style="{ width: r.total ? `${Math.round((r.present / r.total) * 100)}%` : '0%' }">
                                                </div>
                                            </div>
                                            <p class="text-right text-[11px] text-gray-400 mt-1">
                                                {{ r.total ? Math.round((r.present / r.total) * 100) : 0 }}% attendance
                                            </p>
                                        </div>
                                    </div>

                                    <div v-else class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No attendance data for this day.</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.vc-container.vc-is-dark {
    --vc-bg: transparent;
    --vc-border: transparent;
}
</style>
