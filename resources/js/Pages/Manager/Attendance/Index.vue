<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Calendar } from 'v-calendar';
import 'v-calendar/style.css';

const props = defineProps({
    attendances: Array,
    meetingMemos: Array,
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

const formatTime = (time) => {
    if (!time) return 'N/A';
    return new Date(time).toLocaleTimeString([], { hour: '2-digit', memo: '2-digit' });
};

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

const selectedAttendance = computed(() =>
    props.attendances.find(a => toDateStr(a.date) === selectedDateStr.value) || null
);

const selectedMemos = computed(() =>
    (props.meetingMemos || []).filter(m => toDateStr(m.meeting_date) === selectedDateStr.value)
);

const attributes = computed(() => {
    const attrs = [];

    props.attendances.forEach(record => {
        attrs.push({
            key: `attendance-${record.id}`,
            dates: record.date,
            highlight: { color: 'green', fillMode: 'light' },
        });
    });

    (props.meetingMemos || []).forEach(memo => {
        let color = 'gray';
        if (memo.status === 'approved') color = 'blue';
        if (memo.status === 'rejected') color = 'red';
        if (memo.status === 'pending') color = 'yellow';
        attrs.push({
            key: `memo-${memo.id}`,
            dates: memo.meeting_date,
            dot: { color },
        });
    });

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

const statusBadge = (s) => ({
    pending:  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
    approved: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
}[s] ?? 'bg-gray-100 text-gray-700');

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
</script>

<template>
    <Head title="My Calendar" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">My Calendar</h2>
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
                                    <span class="inline-block w-3 h-3 rounded-full bg-green-400"></span> Present
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-yellow-400"></span> Pending
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500"></span> Approved
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-400"></span> Rejected
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
                                <!-- My Attendance -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        My Attendance
                                    </h4>
                                    <div v-if="selectedAttendance" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                                            <span class="text-sm font-semibold text-green-700 dark:text-green-400">Present</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-400 mb-1">Clock In</p>
                                                <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{ formatTime(selectedAttendance.clock_in) }}</p>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                                <p class="text-xs text-gray-400 mb-1">Clock Out</p>
                                                <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{ formatTime(selectedAttendance.clock_out) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No attendance record for this day.</span>
                                    </div>
                                </div>

                                <!-- Assigned  Memos -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Assigned  Memos
                                        <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">{{ selectedMemos.length }}</span>
                                    </h4>
                                    <div v-if="selectedMemos.length > 0" class="space-y-3">
                                        <div v-for="memo in selectedMemos" :key="memo.id"
                                            class="flex items-center justify-between p-4 rounded-xl border transition-colors"
                                            :class="{
                                                'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': memo.status === 'approved',
                                                'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': memo.status === 'rejected',
                                                'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800': memo.status === 'pending',
                                            }">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ memo.title }}</p>
                                            <span class="ml-3 flex-shrink-0 px-2.5 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusBadge(memo.status)">
                                                {{ memo.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-else class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No memos assigned for this day.</span>
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
