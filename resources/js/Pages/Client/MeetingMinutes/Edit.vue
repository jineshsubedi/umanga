<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ minute: Object });

const form = useForm({
    title: props.minute.title,
    content: props.minute.content,
    meeting_date: props.minute.meeting_date ? new Date(props.minute.meeting_date).toISOString().slice(0, 16) : '',
    attachments: [],
    _method: 'put',
});

const fileInput = ref(null);

const handleFileChange = (e) => {
    form.attachments = Array.from(e.target.files);
};

const removeNewFile = (index) => {
    form.attachments.splice(index, 1);
};

const deleteExistingAttachment = (attachment) => {
    if (confirm(`Remove ${attachment.file_name}?`)) {
        router.delete(route('client.meeting-minutes.attachments.destroy', attachment.id), {
            preserveScroll: true,
        });
    }
};

const submit = () => form.post(route('client.meeting-minutes.update', props.minute.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
        form.attachments = [];
        if (fileInput.value) fileInput.value.value = null;
    },
});
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
                            <TextInput id="meeting_date" type="datetime-local" class="mt-1 block w-full" v-model="form.meeting_date" required />
                            <InputError class="mt-2" :message="form.errors.meeting_date" />
                        </div>
                        <div>
                            <InputLabel for="content" value="Meeting Content / Minutes" />
                            <div class="mt-1">
                                <RichTextEditor v-model="form.content" />
                            </div>
                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>

                        <!-- Existing Attachments -->
                        <div v-if="minute.attachments && minute.attachments.length > 0">
                            <InputLabel value="Existing Attachments" />
                            <div class="mt-2 grid grid-cols-1 gap-2">
                                <div v-for="attachment in minute.attachments" :key="attachment.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center space-x-3 truncate">
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <a :href="`/storage/${attachment.file_path}`" target="_blank" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 truncate">{{ attachment.file_name }}</a>
                                        <span class="text-xs text-gray-500">({{ attachment.file_size_formatted }})</span>
                                    </div>
                                    <button type="button" @click="deleteExistingAttachment(attachment)" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 transition-colors focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Add New Attachments -->
                        <div>
                            <InputLabel value="Add New Attachments (Optional)" />
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-indigo-500 transition-colors cursor-pointer" @click="fileInput.click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <span class="relative font-medium text-indigo-600 hover:text-indigo-500">Upload files</span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF, DOC, XLS, PPT up to 10MB each</p>
                                    <input ref="fileInput" id="attachments" type="file" multiple class="sr-only" @change="handleFileChange" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" />
                                </div>
                            </div>
                            <div v-if="form.attachments.length > 0" class="mt-4 space-y-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Selected New Files ({{ form.attachments.length }})</p>
                                <div v-for="(file, index) in form.attachments" :key="index" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center space-x-3 truncate">
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span class="text-sm font-medium text-gray-900 truncate">{{ file.name }}</span>
                                        <span class="text-xs text-gray-500">({{ (file.size / 1024 / 1024).toFixed(2) }} MB)</span>
                                    </div>
                                    <button type="button" @click.stop="removeNewFile(index)" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 transition-colors focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.attachments" />
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
