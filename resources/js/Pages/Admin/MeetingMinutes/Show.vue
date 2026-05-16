<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ minute: Object });
</script>

<template>
    <Head :title="minute.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.meeting-minutes.index')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold text-gray-900">{{ minute.title }}</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Minute Detail -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Submitted by <span class="font-medium text-gray-800">{{ minute.creator?.name }}</span></p>
                            <p class="text-sm text-gray-500">Meeting date: <span class="font-medium text-gray-800">{{ minute.formatted_meeting_date }}</span></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium capitalize"
                            :class="{
                                'bg-gray-100 text-gray-600': minute.status === 'draft',
                                'bg-yellow-100 text-yellow-700': minute.status === 'pending',
                                'bg-green-100 text-green-700': minute.status === 'approved',
                                'bg-red-100 text-red-700': minute.status === 'rejected',
                            }">
                            {{ minute.status }}
                        </span>
                    </div>
                    <div class="prose max-w-none">
                        <div class="bg-gray-50 rounded-lg p-4 text-gray-800 text-sm leading-relaxed" v-html="minute.content"></div>
                    </div>
                </div>

                <!-- Review History -->
                <div v-if="minute.reviews?.length" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Review History</h3>
                    <div class="space-y-3">
                        <div v-for="review in minute.reviews" :key="review.id"
                            class="flex gap-3 p-3 rounded-lg"
                            :class="review.status === 'approved' ? 'bg-green-50' : 'bg-red-50'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                :class="review.status === 'approved' ? 'bg-green-500' : 'bg-red-500'">
                                {{ review.reviewer?.name?.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ review.reviewer?.name }}
                                    <span class="font-normal capitalize" :class="review.status === 'approved' ? 'text-green-700' : 'text-red-700'">{{ review.status }}</span>
                                </p>
                                <p v-if="review.comment" class="text-sm text-gray-600 mt-1">{{ review.comment }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
