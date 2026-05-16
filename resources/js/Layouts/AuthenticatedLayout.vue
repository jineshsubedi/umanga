<script setup>
import { ref, computed, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role);
const flash = computed(() => page.props.flash);
const notifications = computed(() => page.props.auth?.notifications || []);

// Auto-dismiss alerts
const showSuccess = ref(false);
const showError = ref(false);

watch(() => flash.value?.success, (val) => {
    if (val) {
        showSuccess.value = true;
        setTimeout(() => { showSuccess.value = false; }, 5000);
    }
}, { immediate: true });

watch(() => flash.value?.error, (val) => {
    if (val) {
        showError.value = true;
        setTimeout(() => { showError.value = false; }, 5000);
    }
}, { immediate: true });

const roleBadge = computed(() => ({
    super_admin: { label: 'Super Admin', cls: 'bg-purple-100 text-purple-800' },
    admin:       { label: 'Admin',       cls: 'bg-blue-100 text-blue-800' },
    manager:     { label: 'Manager',     cls: 'bg-green-100 text-green-800' },
    client:      { label: 'Client',      cls: 'bg-orange-100 text-orange-800' },
}[role.value] ?? { label: role.value, cls: 'bg-gray-100 text-gray-800' }));
</script>

<template>
    <div>
        <!-- Flash Messages -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showSuccess && flash?.success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 max-w-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm flex-1">{{ flash.success }}</span>
                <button @click="showSuccess = false" class="text-green-200 hover:text-white ml-1">✕</button>
            </div>
        </Transition>
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showError && flash?.error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 max-w-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="text-sm flex-1">{{ flash.error }}</span>
                <button @click="showError = false" class="text-red-200 hover:text-white ml-1">✕</button>
            </div>
        </Transition>

        <div class="min-h-screen bg-gray-50">
            <nav class="bg-white border-b border-gray-200 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')" class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">UM</span>
                                    </div>
                                    <span class="font-semibold text-gray-900 hidden sm:block">UMNG Portal</span>
                                </Link>
                            </div>

                            <!-- Role-based Nav Links -->
                            <div class="hidden space-x-1 sm:ms-8 sm:flex">
                                <!-- Super Admin -->
                                <template v-if="role === 'super_admin'">
                                    <NavLink :href="route('super-admin.companies.index')" :active="route().current('super-admin.*')">
                                        Companies
                                    </NavLink>
                                </template>
                                <!-- Admin -->
                                <template v-if="role === 'admin'">
                                    <NavLink :href="route('admin.users.index')" :active="route().current('admin.*')">
                                        Manage Users
                                    </NavLink>
                                </template>
                                <!-- Manager -->
                                <template v-if="role === 'manager'">
                                    <NavLink :href="route('manager.meeting-minutes.index')" :active="route().current('manager.*')">
                                        Meeting Minutes
                                    </NavLink>
                                </template>
                                <!-- Client -->
                                <template v-if="role === 'client'">
                                    <NavLink :href="route('client.meeting-minutes.index')" :active="route().current('client.*')">
                                        My Minutes
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:gap-3">
                            <!-- Role Badge -->
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full" :class="roleBadge.cls">
                                {{ roleBadge.label }}
                            </span>

                            <!-- Notifications Dropdown -->
                            <Dropdown align="right" width="80" v-if="user">
                                <template #trigger>
                                    <button type="button" class="relative inline-flex items-center p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <span v-if="notifications.length > 0" class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                                    </button>
                                </template>
                                <template #content>
                                    <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-md">
                                        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Notifications</span>
                                        <Link v-if="notifications.length > 0" :href="route('notifications.markAllRead')" method="post" as="button" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all read</Link>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        <template v-if="notifications.length > 0">
                                            <Link v-for="notif in notifications" :key="notif.id" :href="route('notifications.markRead', notif.id)"
                                                class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                        {{ notif.data.actor_name?.charAt(0) || 'U' }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-800">{{ notif.data.message }}</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">{{ new Date(notif.created_at).toLocaleDateString() }}</p>
                                                    </div>
                                                </div>
                                            </Link>
                                        </template>
                                        <div v-else class="px-4 py-6 text-center text-sm text-gray-500">
                                            No new notifications
                                        </div>
                                    </div>
                                </template>
                            </Dropdown>

                            <!-- Settings Dropdown -->
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                                        {{ user?.name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <template v-if="role === 'super_admin'">
                            <ResponsiveNavLink :href="route('super-admin.companies.index')">Companies</ResponsiveNavLink>
                        </template>
                        <template v-if="role === 'admin'">
                            <ResponsiveNavLink :href="route('admin.users.index')">Manage Users</ResponsiveNavLink>
                        </template>
                        <template v-if="role === 'manager'">
                            <ResponsiveNavLink :href="route('manager.meeting-minutes.index')">Meeting Minutes</ResponsiveNavLink>
                        </template>
                        <template v-if="role === 'client'">
                            <ResponsiveNavLink :href="route('client.meeting-minutes.index')">My Minutes</ResponsiveNavLink>
                        </template>
                    </div>
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">{{ user?.name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ user?.email }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow-sm border-b border-gray-100" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
