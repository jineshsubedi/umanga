<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { DatePicker } from 'v-calendar';
import 'v-calendar/style.css';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({ memo: Object, managers: Array });

const form = useForm({
    title: props.memo.title,
    content: props.memo.content,
    meeting_date: props.memo.meeting_date ? new Date(props.memo.meeting_date) : new Date(),
    manager_ids: props.memo.managers?.map(m => m.id) || [],
    attachments: [],
    _method: 'put',
});

const fileInput = ref(null);

const isOpen = ref(false);
const searchQuery = ref('');

const filteredManagers = computed(() => {
    if (!searchQuery.value) return props.managers;
    return props.managers.filter(m => m.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const toggleManager = (id) => {
    const index = form.manager_ids.indexOf(id);
    if (index === -1) {
        form.manager_ids.push(id);
    } else {
        form.manager_ids.splice(index, 1);
    }
};

const closeDropdown = (e) => {
    if (!e.target.closest('.manager-dropdown')) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', closeDropdown));
onUnmounted(() => document.removeEventListener('click', closeDropdown));

const handleFileChange = (e) => {
    form.attachments = Array.from(e.target.files);
};

const removeNewFile = (index) => {
    form.attachments.splice(index, 1);
};

const deleteExistingAttachment = (attachment) => {
    if (confirm(`Remove ${attachment.file_name}?`)) {
        router.delete(route('staff.meeting-memos.attachments.destroy', attachment.id), {
            preserveScroll: true,
        });
    }
};

const submit = () => {
    let originalDate = form.meeting_date;
    if (form.meeting_date instanceof Date) {
        const d = form.meeting_date;
        const pad = (n) => n.toString().padStart(2, '0');
        form.meeting_date = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:00`;
    }

    form.post(route('staff.meeting-memos.update', props.memo.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.attachments = [];
            if (fileInput.value) fileInput.value.value = null;
        },
        onError: () => {
            form.meeting_date = originalDate;
        }
    });
};
</script>

<template>
    <Head title="Edit Memo" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('staff.meeting-memos.index')" class="text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-400">Edit Memo</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Rejection Notice -->
                <div v-if="memo.status === 'rejected' && memo.latest_review?.comment"
                    class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm font-semibold text-red-700">Rejected — Manager's feedback:</p>
                    <p class="text-sm text-red-600 mt-1">{{ memo.latest_review.comment }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput id="title" type="text" class="mt-1 block w-full dark:text-gray-400" v-model="form.title" required autofocus />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="meeting_date" value=" Date" />
                            <DatePicker v-model="form.meeting_date" mode="dateTime" is24hr hide-time-header>
                                <template #default="{ inputValue, inputEvents }">
                                    <TextInput id="meeting_date" class="mt-1 block w-full dark:text-gray-400" :value="inputValue" v-on="inputEvents" />
                                </template>
                            </DatePicker>
                            <InputError class="mt-2" :message="form.errors.meeting_date" />
                        </div>
                        <div>
                            <InputLabel for="content" value=" Content / Memos" />
                            <div class="mt-1">
                                <RichTextEditor v-model="form.content" />
                            </div>
                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>

                        <!-- Multi-select Dropdown with Search -->
                        <div class="relative manager-dropdown">
                            <InputLabel value="Assign Managers to Approve" />
                            <div class="mt-1 relative">
                                <!-- Selected Pills + Trigger -->
                                <div @click="isOpen = !isOpen" class="min-h-[42px] p-1.5 bg-white border border-gray-300 rounded-lg shadow-sm flex flex-wrap items-center gap-1.5 cursor-pointer focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                    <div v-if="form.manager_ids.length === 0" class="text-sm text-gray-400 px-2 py-1">
                                        Select managers...
                                    </div>
                                    <span v-for="id in form.manager_ids" :key="id" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-medium">
                                        {{ managers.find(m => m.id === id)?.name }}
                                        <button type="button" @click.stop="toggleManager(id)" class="text-indigo-400 hover:text-indigo-600 focus:outline-none">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                    <div class="ml-auto pr-2 text-gray-400">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'transform rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>

                                <!-- Dropdown Menu -->
                                <div v-if="isOpen" class="absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-2 max-h-60 overflow-y-auto">
                                    <div class="px-3 pb-2 border-b border-gray-100">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                            <input type="text" v-model="searchQuery" @click.stop placeholder="Search managers..." class="w-full pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:bg-white" />
                                        </div>
                                    </div>
                                    <div class="pt-1">
                                        <div v-if="filteredManagers.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center italic">
                                            No managers found.
                                        </div>
                                        <div v-for="manager in filteredManagers" :key="manager.id" @click.stop="toggleManager(manager.id)" class="px-4 py-2.5 flex items-center justify-between text-sm hover:bg-gray-50 cursor-pointer transition-colors" :class="{ 'bg-indigo-50/50 text-indigo-900 font-medium': form.manager_ids.includes(manager.id) }">
                                            <div class="flex items-center gap-2.5 truncate">
                                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                                                    {{ manager.name?.charAt(0) }}
                                                </div>
                                                <span class="truncate">{{ manager.name }}</span>
                                            </div>
                                            <div v-if="form.manager_ids.includes(manager.id)" class="text-indigo-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.manager_ids" />
                        </div>

                        <!-- Existing Attachments -->
                        <div v-if="memo.attachments && memo.attachments.length > 0">
                            <InputLabel value="Existing Attachments" />
                            <div class="mt-2 grid grid-cols-1 gap-2">
                                <div v-for="attachment in memo.attachments" :key="attachment.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
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
                            <Link :href="route('staff.meeting-memos.index')" class="text-sm text-gray-600 hover:text-gray-900">Cancel</Link>
                            <PrimaryButton :disabled="form.processing">Save Changes</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
