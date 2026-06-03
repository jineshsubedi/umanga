<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NepaliCalendarGrid from '@/Components/NepaliCalendarGrid.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    attendances:      Array,   // [{ date, role, count }]
    memos:            Array,   // [{ date/meeting_date, count, title, status }]
    totalUsersByRole: Object,  // { manager: { count }, staff: { count }, admin: { count } }
});
</script>

<template>
    <Head title="Company Calendar" />
    <AuthenticatedLayout>
        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-1/4 -left-1/4 w-2/3 h-2/3 rounded-full bg-cyan-400/10 blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
            </div>
            <div class="max-w-7xl mx-auto relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Company Calendar</h1>
                    <p class="mt-1 text-purple-200 text-sm">Track attendance and memos across your organization.</p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10 pb-12">
            <NepaliCalendarGrid
                :attendances="attendances"
                :memos="memos"
                :total-users-by-role="totalUsersByRole"
                view-type="admin"
            />
        </div>
    </AuthenticatedLayout>
</template>
