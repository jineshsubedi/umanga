<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({ companies: Array });

const toggle = (company) => {
    router.patch(route('super-admin.companies.toggle-status', company.id));
};

const destroy = (company) => {
    if (confirm(`Are you sure you want to PERMANENTLY delete ${company.name} and all its users? This action cannot be undone.`)) {
        router.delete(route('super-admin.companies.destroy', company.id));
    }
};
</script>

<template>
    <Head title="All Companies" />
    <AuthenticatedLayout>
        <!-- Hero Strip -->
        <div class="bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] pt-8 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-96 h-96 rounded-full bg-purple-400/20 blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-1/4 -left-1/4 w-2/3 h-2/3 rounded-full bg-cyan-400/10 blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
            </div>
            <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Registered Companies</h1>
                        <p class="mt-1 text-purple-200 text-sm">Overview and management of all tenant companies.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link :href="route('super-admin.companies.create')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-sm font-bold rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5 shadow-lg border border-white/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Company
                    </Link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10 pb-12 space-y-6">
            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ companies.length }}</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Total Companies</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> 
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ companies.filter(c => c.status === 'active').length }}</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Active</p>
                </div>
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30 dark:border-gray-700 flex flex-col items-center justify-center text-center transition-all hover:shadow-2xl hover:-translate-y-1 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 text-white rounded-xl flex items-center justify-center mb-3 font-bold text-lg shadow-md group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ companies.filter(c => c.status !== 'active').length }}</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Inactive</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Memos</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registered</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="company in companies" :key="company.id" class="hover:bg-purple-50/50 dark:hover:bg-purple-900/10 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#7c3aed] to-[#b026ff] text-white flex items-center justify-center font-bold text-sm shadow group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                            {{ company.name?.charAt(0) }}
                                        </div>
                                        <div class="font-bold text-gray-900 dark:text-white text-sm">{{ company.name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ company.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ company.users_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ company.meeting_memos_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border"
                                        :class="company.status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800'">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="company.status === 'active' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                        {{ company.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ company.created_at }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    <Link :href="route('super-admin.companies.show', company.id)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg border border-blue-100 dark:border-blue-800 transition-colors">
                                        View
                                    </Link>
                                    <Link :href="route('super-admin.companies.edit', company.id)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-[#7c3aed] dark:text-[#a78bfa] bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/40 rounded-lg border border-purple-100 dark:border-purple-800 transition-colors">
                                        Edit
                                    </Link>
                                    <button @click="toggle(company)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors"
                                        :class="company.status === 'active' ? 'text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 border-orange-100 dark:border-orange-800' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 border-emerald-100 dark:border-emerald-800'">
                                        {{ company.status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="destroy(company)" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg border border-red-100 dark:border-red-800 transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="companies.length === 0">
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <p class="font-bold text-sm">No companies registered yet.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
