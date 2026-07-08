<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ user: Object, company: Object });

const form = useForm({
    name:        props.user.name,
    role:        props.user.role,
    designation: props.user.designation,
    status:      props.user.status,
    is_checker:  props.user.is_checker ?? false,
    is_verifier: props.user.is_verifier ?? false,
    is_approver: props.user.is_approver ?? false,
});

const submit = () => form.put(route('admin.users.update', props.user.id));
</script>

<template>
    <Head title="Edit User" />
    <AuthenticatedLayout>
        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
            </div>
            <div class="max-w-2xl mx-auto relative z-10 flex items-center gap-4">
                <Link :href="route('admin.users.index')" class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Edit User</h1>
                    <p class="mt-1 text-purple-200 text-sm">Update information for {{ user.email }}.</p>
                </div>
            </div>
        </div>

        <div class="py-8 -mt-12 relative z-10">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-[#7c3aed] rounded-full"></span>
                            User Profile
                        </h3>
                    </div>
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <InputLabel for="name" value="Full Name" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <TextInput id="name" type="text" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3" v-model="form.name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel value="Email Address" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <div class="mt-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ user.email }}</span>
                                    <span class="text-[11px] font-bold text-gray-400 ml-auto uppercase bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded">Locked</span>
                                </div>
                            </div>

                            <div>
                                <InputLabel for="designation" value="Designation (Optional)" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <TextInput id="designation" type="text" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3" v-model="form.designation" />
                                <InputError class="mt-2" :message="form.errors.designation" />
                            </div>

                            <div>
                                <InputLabel for="role" value="Role" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <select id="role" v-model="form.role" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3">
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="staff">Staff</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                            <div>
                                <InputLabel for="status" value="Status" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <select id="status" v-model="form.status" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div v-if="company && (company.has_checker || company.has_verifier || company.has_approver)" class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-300">Memo Workflow Roles</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div v-if="company.has_checker" class="flex items-center gap-2">
                                        <input id="is_checker" type="checkbox" v-model="form.is_checker" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                                        <InputLabel for="is_checker" value="Can Check Memos" class="mb-0 font-semibold text-gray-700 dark:text-gray-300" />
                                    </div>
                                    <div v-if="company.has_verifier" class="flex items-center gap-2">
                                        <input id="is_verifier" type="checkbox" v-model="form.is_verifier" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                                        <InputLabel for="is_verifier" value="Can Verify Memos" class="mb-0 font-semibold text-gray-700 dark:text-gray-300" />
                                    </div>
                                    <div v-if="company.has_approver" class="flex items-center gap-2">
                                        <input id="is_approver" type="checkbox" v-model="form.is_approver" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                                        <InputLabel for="is_approver" value="Can Approve Memos" class="mb-0 font-semibold text-gray-700 dark:text-gray-300" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <Link :href="route('admin.users.index')" class="text-sm font-bold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Cancel</Link>
                                <button type="submit" :disabled="form.processing"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
