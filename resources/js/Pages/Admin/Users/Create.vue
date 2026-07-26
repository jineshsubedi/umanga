<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    company: {
        type: Object,
        required: true
    }
});

const form = useForm({ 
    name: '', 
    email: '', 
    role: 'staff', 
    designation: '', 
    password: '',
    is_checker: false,
    is_verifier: false,
    is_approver: false,
    department: '',
    permissions: [],
});
const submit = () => form.post(route('admin.users.store'));
</script>

<template>
    <Head title="Create User" />
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
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Create User</h1>
                    <p class="mt-1 text-purple-200 text-sm">Add a new user to your company.</p>
                </div>
            </div>
        </div>

        <div class="py-8 -mt-12 relative z-10">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-5 bg-[#7c3aed] rounded-full"></span>
                            User Information
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
                                <InputLabel for="email" value="Email Address" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <TextInput id="email" type="email" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3" v-model="form.email" required />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="designation" value="Designation (Optional)" class="font-semibold text-gray-700 dark:text-gray-300" />
                                    <TextInput id="designation" type="text" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3" v-model="form.designation" />
                                    <InputError class="mt-2" :message="form.errors.designation" />
                                </div>
                                <div v-if="company && company.departments && company.departments.length > 0">
                                    <InputLabel for="department" value="Department (Optional)" class="font-semibold text-gray-700 dark:text-gray-300" />
                                    <select id="department" v-model="form.department" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3">
                                        <option value="">Select a department</option>
                                        <option v-for="dept in company.departments" :key="dept" :value="dept">
                                            {{ dept }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.department" />
                                </div>
                            </div>
                            <div>
                                <InputLabel for="role" value="Assign Role" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <select id="role" v-model="form.role" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3">
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="staff">Staff</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>
                            <div>
                                <InputLabel for="password" value="Password" class="font-semibold text-gray-700 dark:text-gray-300" />
                                <TextInput id="password" type="password" class="mt-2 block w-full bg-gray-50 border-gray-300 rounded-xl focus:ring-[#7c3aed] focus:border-[#7c3aed] dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3" v-model="form.password" required />
                                <InputError class="mt-2" :message="form.errors.password" />
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

                            <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-300">Module Access Permissions</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-2" :class="{'opacity-50': !company || !(company.modules || []).includes('memo')}">
                                        <input id="perm_memo" type="checkbox" value="memo" v-model="form.permissions" :disabled="!company || !(company.modules || []).includes('memo')" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-gray-200" />
                                        <InputLabel for="perm_memo" value="Access Memo Module" class="mb-0 font-semibold text-gray-700 dark:text-gray-300" />
                                    </div>
                                    <div class="flex items-center gap-2" :class="{'opacity-50': !company || !(company.modules || []).includes('procurement')}">
                                        <input id="perm_procurement" type="checkbox" value="procurement" v-model="form.permissions" :disabled="!company || !(company.modules || []).includes('procurement')" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-gray-200" />
                                        <InputLabel for="perm_procurement" value="Access Procurement Module" class="mb-0 font-semibold text-gray-700 dark:text-gray-300" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-3 pt-4">
                                <Link :href="route('admin.users.index')" class="text-sm font-bold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Cancel</Link>
                                <button type="submit" :disabled="form.processing"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
                                    Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
