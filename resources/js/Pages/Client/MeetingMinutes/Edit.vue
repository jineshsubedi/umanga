<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ minute: Object });

const form = useForm({
    title: props.minute.title,
    content: props.minute.content,
    meeting_date: props.minute.meeting_date,
});

const submit = () => form.put(route('client.meeting-minutes.update', props.minute.id));
</script>

<template>
    <Head title="Edit Meeting Minute" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('client.meeting-minutes.index')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold text-gray-900">Edit Meeting Minute</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Rejection Notice -->
                <div v-if="minute.status === 'rejected' && minute.latest_review?.comment"
                    class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm font-semibold text-red-700">Rejected — Manager's feedback:</p>
                    <p class="text-sm text-red-600 mt-1">{{ minute.latest_review.comment }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="meeting_date" value="Meeting Date" />
                            <TextInput id="meeting_date" type="date" class="mt-1 block w-full" v-model="form.meeting_date" required />
                            <InputError class="mt-2" :message="form.errors.meeting_date" />
                        </div>
                        <div>
                            <InputLabel for="content" value="Meeting Content / Minutes" />
                            <div class="mt-1">
                                <RichTextEditor v-model="form.content" />
                            </div>
                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <Link :href="route('client.meeting-minutes.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancel</Link>
                            <PrimaryButton :disabled="form.processing">Save Changes</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
