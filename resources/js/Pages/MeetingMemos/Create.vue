<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { DatePicker } from 'v-calendar';
import 'v-calendar/style.css';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ users: Array, company: Object });

const form = useForm({ 
    title: '', 
    content: '', 
    meeting_date: new Date(), 
    checker_id: '',
    verifier_id: '',
    approver_id: '',
    attachments: [] 
});

const fileInput = ref(null);

const handleFileChange = (e) => {
    form.attachments = Array.from(e.target.files);
};

const removeFile = (index) => {
    form.attachments.splice(index, 1);
};

const submit = () => {
    let originalDate = form.meeting_date;
    if (form.meeting_date instanceof Date) {
        const d = form.meeting_date;
        const pad = (n) => n.toString().padStart(2, '0');
        form.meeting_date = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:00`;
    }
    form.post(route('memos.store'), { 
        forceFormData: true,
        onError: () => {
            form.meeting_date = originalDate;
        }
    });
};
</script>

<template>
    <Head title="New Memo" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('memos.index')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-400">Create Memo</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput id="title" type="text" class="mt-1 block w-full dark:text-gray-400" v-model="form.title" required autofocus placeholder="e.g. Q1 Planning " />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="meeting_date" value="Date" />
                            <DatePicker v-model="form.meeting_date" mode="dateTime" is24hr hide-time-header>
                                <template #default="{ inputValue, inputEvents }">
                                    <TextInput id="meeting_date" class="mt-1 block w-full dark:text-gray-400" :value="inputValue" v-on="inputEvents" />
                                </template>
                            </DatePicker>
                            <InputError class="mt-2" :message="form.errors.meeting_date" />
                        </div>
                        <div>
                            <InputLabel for="content" value="Content / Memo" />
                            <div class="mt-1">
                                <RichTextEditor v-model="form.content" placeholder="Record what was discussed, decisions made, action items..." />
                            </div>
                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>
                        
                        <!-- Reviewers Section -->
                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-300">Approval Workflow</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div v-if="company.has_checker">
                                    <InputLabel for="checker_id" value="Checked By (Step 1)" />
                                    <select id="checker_id" v-model="form.checker_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="" disabled>Select Checker</option>
                                        <option v-for="user in users.filter(u => u.is_checker)" :key="user.id" :value="user.id">
                                            {{ user.name }} ({{ user.designation || user.role }})
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.checker_id" />
                                </div>
                                
                                <div v-if="company.has_verifier">
                                    <InputLabel for="verifier_id" value="Verified By (Step 2)" />
                                    <select id="verifier_id" v-model="form.verifier_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="" disabled>Select Verifier</option>
                                        <option v-for="user in users.filter(u => u.is_verifier)" :key="user.id" :value="user.id">
                                            {{ user.name }} ({{ user.designation || user.role }})
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.verifier_id" />
                                </div>
                                
                                <div v-if="company.has_approver">
                                    <InputLabel for="approver_id" value="Approved By (Step 3)" />
                                    <select id="approver_id" v-model="form.approver_id" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="" disabled>Select Approver</option>
                                        <option v-for="user in users.filter(u => u.is_approver)" :key="user.id" :value="user.id">
                                            {{ user.name }} ({{ user.designation || user.role }})
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.approver_id" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <InputLabel value="Attachments (Optional)" />
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
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Selected Files ({{ form.attachments.length }})</p>
                                <div v-for="(file, index) in form.attachments" :key="index" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center space-x-3 truncate">
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span class="text-sm font-medium text-gray-900 truncate">{{ file.name }}</span>
                                        <span class="text-xs text-gray-500">({{ (file.size / 1024 / 1024).toFixed(2) }} MB)</span>
                                    </div>
                                    <button type="button" @click.stop="removeFile(index)" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 transition-colors focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.attachments" />
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <Link :href="route('memos.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancel</Link>
                            <PrimaryButton :disabled="form.processing">Save as Draft</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
