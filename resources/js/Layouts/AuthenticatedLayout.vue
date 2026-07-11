<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme';
import axios from 'axios';

const { isDark, toggleTheme } = useTheme();

const mobileSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role);
const flash = computed(() => page.props.flash);
const notifications = computed(() => page.props.auth?.notifications || []);
const todayAttendance = computed(() => page.props.today_attendance);

// Geolocation helpers
const geoStatus = ref('idle'); // idle | fetching | done | denied
const geoData   = ref({ lat: null, lng: null, address: '' });

const fetchGeo = () => new Promise((resolve) => {
    if (!navigator.geolocation) return resolve({});
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const base = { lat, lng, address: `${lat.toFixed(5)}, ${lng.toFixed(5)}` };
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, {
                headers: { 'User-Agent': 'UMNG-Attendance-App/1.0' }
            })
                .then(r => r.json())
                .then(d => resolve({ ...base, address: d.display_name || base.address }))
                .catch(() => resolve(base));
        },
        (err) => {
            console.warn("Geolocation error:", err);
            resolve({});
        },
        { enableHighAccuracy: true, timeout: 60000, maximumAge: 0 }
    );
});

const handleClockIn = async () => {
    geoStatus.value = 'fetching';
    const geo = await fetchGeo();
    geoStatus.value = 'done';
    router.post(route('attendance.clock-in'), geo, { preserveScroll: true });
};

const handleClockOut = async () => {
    geoStatus.value = 'fetching';
    const geo = await fetchGeo();
    geoStatus.value = 'done';
    router.post(route('attendance.clock-out'), geo, { preserveScroll: true });
};

// Auto-dismiss alerts
const showSuccess = ref(false);
const showError   = ref(false);
const userMenuOpen = ref(false);

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

const urlBase64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
};

const showPushPrompt = ref(false);

const checkPushPermission = () => {
    if (user.value?.push_notifications === false && Notification.permission !== 'denied') {
        setTimeout(() => {
            showPushPrompt.value = true;
        }, 1000);
    }
};

const enablePushNotifications = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
    
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
        try {
            await navigator.serviceWorker.register('/sw.js');
            const registration = await navigator.serviceWorker.ready;
            const vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY;
            if (!vapidPublicKey) return;

            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            });

            await axios.post(route('push-subscriptions.store'), subscription.toJSON());
            user.value.push_notifications = true;
        } catch (e) {
            console.error("Push subscription failed", e);
        } finally {
            showPushPrompt.value = false;
        }
    } else {
        showPushPrompt.value = false;
    }
};

const dismissPushPrompt = () => {
    showPushPrompt.value = false;
};

onMounted(() => {
    checkPushPermission();
});

const roleBadge = computed(() => {
    let label = role.value;
    if (role.value === 'super_admin') label = 'Super Admin';
    else if (role.value === 'admin') label = 'Admin';
    else if (role.value === 'manager') label = 'Manager';
    else if (role.value === 'staff') label = 'Staff';

    if (role.value !== 'super_admin' && user.value?.designation) {
        label = user.value.designation;
    }

    let cls = 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    if (role.value === 'super_admin') cls = 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400';
    else if (role.value === 'admin') cls = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
    else if (role.value === 'manager') cls = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    else if (role.value === 'staff') cls = 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';

    return { label, cls };
});

const navigation = computed(() => {
    let nav = [];
    if (role.value === 'admin') {
        nav.push({ name: 'Dashboard', href: route('admin.dashboard'), current: route().current('admin.dashboard'), icon: 'dashboard' });
        nav.push({ name: 'Manage Users', href: route('admin.users.index'), current: route().current('admin.users.*'), icon: 'users' });
        nav.push({ name: ' Memos', href: route('memos.index'), current: route().current('memos.*'), icon: 'document' });
        nav.push({ name: 'Reports', href: route('reports.index'), current: route().current('reports.*'), icon: 'chart' });
        nav.push({ name: 'Attendance', href: route('admin.attendance.index'), current: route().current('admin.attendance.index'), icon: 'clock' });
        nav.push({ name: 'Calendar', href: route('admin.calendar.index'), current: route().current('admin.calendar.*'), icon: 'calendar' });
    }
    if (role.value === 'super_admin') {
        nav.push({ name: 'Dashboard', href: route('super-admin.dashboard'), current: route().current('super-admin.dashboard'), icon: 'dashboard' });
        nav.push({ name: 'Companies', href: route('super-admin.companies.index'), current: route().current('super-admin.companies.*'), icon: 'office' });
        nav.push({ name: 'Users', href: route('super-admin.users.index'), current: route().current('super-admin.users.*'), icon: 'users' });
        nav.push({ name: 'Memos', href: route('super-admin.meeting-memos.index'), current: route().current('super-admin.meeting-memos.*'), icon: 'document' });
        nav.push({ name: 'Reports', href: route('super-admin.reports.index'), current: route().current('super-admin.reports.*'), icon: 'chart' });
        nav.push({ name: 'Settings', href: route('super-admin.settings.index'), current: route().current('super-admin.settings.*'), icon: 'settings' });
    }
    if (role.value === 'manager') {
        nav.push({ name: ' Memos', href: route('memos.index'), current: route().current('memos.*'), icon: 'document' });
        nav.push({ name: 'Calendar', href: route('manager.attendance.index'), current: route().current('manager.attendance.*'), icon: 'clock' });
    }
    if (role.value === 'staff') {
        nav.push({ name: 'My Memos', href: route('memos.index'), current: route().current('memos.*'), icon: 'document' });
        nav.push({ name: 'Calendar', href: route('staff.attendance.index'), current: route().current('staff.attendance.*'), icon: 'clock' });
    }
    return nav;
});
</script>

<template>
    <div class="h-screen flex overflow-hidden bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        
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

        <!-- Push Notifications Prompt -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-full" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-full">
            <div v-if="showPushPrompt" class="fixed top-0 left-0 right-0 z-50 bg-indigo-600 shadow-md">
                <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between flex-wrap">
                        <div class="w-0 flex-1 flex items-center">
                            <span class="flex p-2 rounded-lg bg-indigo-800">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </span>
                            <p class="ml-3 font-medium text-white truncate">
                                <span>Get instant updates on your memos and attendance! Enable push notifications now.</span>
                            </p>
                        </div>
                        <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto flex gap-2">
                            <button @click="enablePushNotifications" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                                Enable
                            </button>
                            <button @click="dismissPushPrompt" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-500">
                                Maybe Later
                            </button>
                        </div>
                        <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                            <button @click="dismissPushPrompt" type="button" class="-mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none sm:-mr-2">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Mobile sidebar backdrop -->
        <div v-if="mobileSidebarOpen" class="fixed inset-0 z-20 transition-opacity bg-gray-900 bg-opacity-75 sm:hidden" @click="mobileSidebarOpen = false"></div>

        <!-- Sidebar -->
        <div :class="[
                mobileSidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full',
                'fixed inset-y-0 left-0 z-30 overflow-y-auto transition-all duration-300 ease-in-out transform bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 sm:translate-x-0 sm:static sm:inset-0 flex flex-col',
                isSidebarCollapsed ? 'sm:w-20' : 'sm:w-64'
            ]">
            
            <!-- Logo area -->
            <div class="flex items-center h-16 border-b border-gray-200 dark:border-gray-700 px-4" :class="isSidebarCollapsed ? 'justify-center' : 'justify-between'">
                <Link :href="route('dashboard')" class="flex items-center gap-2 overflow-hidden">
                    <ApplicationLogo class="w-8 h-8 text-indigo-600 dark:text-indigo-400 flex-shrink-0 transition-transform duration-300 hover:scale-110" />
                    <span v-if="!isSidebarCollapsed" class="text-xl font-bold text-gray-900 dark:text-white whitespace-nowrap opacity-100 transition-opacity duration-300">{{ $page.props.app_settings?.app_name || 'UMNG Portal' }}</span>
                </Link>
            </div>

            <!-- Company Context Badge -->
            <div v-if="user?.company" class="px-4 py-3 bg-indigo-50/50 dark:bg-indigo-900/10 border-b border-gray-100 dark:border-gray-800 flex-shrink-0 transition-all duration-300">
                <div class="flex items-center gap-2" :class="isSidebarCollapsed ? 'justify-center' : ''">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm ring-1 ring-white/20 flex-shrink-0 transition-transform duration-300 hover:scale-110">
                        {{ user.company.name.charAt(0) }}
                    </div>
                    <div v-if="!isSidebarCollapsed" class="flex flex-col flex-1 overflow-hidden transition-opacity duration-300">
                        <span class="text-[10px] font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider leading-none mb-0.5">Workspace</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-200 leading-tight truncate" :title="user.company.name">{{ user.company.name }}</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 mt-6 space-y-2 overflow-x-hidden">
                <Link v-for="item in navigation" :key="item.name" :href="item.href"
                    :class="[
                        item.current ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-semibold shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
                        'group flex items-center py-2.5 rounded-xl transition-all duration-200 relative overflow-hidden',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3'
                    ]"
                    :title="isSidebarCollapsed ? item.name : ''">
                    <!-- Hover Effect Background -->
                    <div v-if="!item.current" class="absolute inset-0 bg-indigo-500/10 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 rounded-xl"></div>
                    
                    <!-- Icons based on type -->
                    <svg v-if="item.icon === 'dashboard'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <svg v-if="item.icon === 'users'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg v-if="item.icon === 'office'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <svg v-if="item.icon === 'document'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <svg v-if="item.icon === 'clock'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg v-if="item.icon === 'calendar'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <svg v-if="item.icon === 'chart'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <svg v-if="item.icon === 'settings'" :class="[isSidebarCollapsed ? 'mr-0' : 'mr-3', 'w-5 h-5 flex-shrink-0 relative z-10 transition-transform group-hover:scale-110']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>

                    <span v-if="!isSidebarCollapsed" class="whitespace-nowrap relative z-10 transition-opacity duration-300">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- User Settings & Theme Toggle at bottom -->
            <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 transition-all duration-300">
                <!-- Theme Toggle -->
                <div class="flex items-center mb-4 mt-2 justify-center" :class="!isSidebarCollapsed ? 'justify-between px-2' : ''">
                    <span v-if="!isSidebarCollapsed" class="text-sm font-medium text-gray-700 dark:text-gray-300 transition-opacity duration-300">Dark Mode</span>
                    <button @click="toggleTheme" type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        :class="isDark ? 'bg-indigo-600' : 'bg-gray-200'" title="Toggle Dark Mode">
                        <span class="sr-only">Toggle Dark Mode</span>
                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="isDark ? 'translate-x-5' : 'translate-x-0'">
                            <svg v-if="isDark" class="h-5 w-5 text-indigo-600 p-1" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                            <svg v-else class="h-5 w-5 text-gray-400 p-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
                        </span>
                    </button>
                </div>

                <div class="relative flex items-center group justify-center" :class="!isSidebarCollapsed ? 'justify-start' : ''">
                    <div class="flex-shrink-0 cursor-pointer" @click="userMenuOpen = !userMenuOpen" :title="user?.name">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold shadow-md ring-2 ring-white dark:ring-gray-700 transition-transform duration-300 hover:scale-110">
                            {{ user?.name?.charAt(0) || 'U' }}
                        </div>
                    </div>
                    <div v-if="!isSidebarCollapsed" class="ml-3 flex-1 overflow-hidden transition-opacity duration-300">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ user?.name }}</p>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 truncate">{{ user?.email }}</p>
                    </div>
                    
                    <!-- Custom Dropup Menu Trigger -->
                    <button v-if="!isSidebarCollapsed" @click="userMenuOpen = !userMenuOpen" type="button" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all focus:outline-none">
                        <svg class="w-5 h-5 transform transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>

                    <!-- Full screen backdrop for closing -->
                    <div v-if="userMenuOpen" class="fixed inset-0 z-40" @click="userMenuOpen = false"></div>

                    <!-- Floating Dropup Menu Box -->
                    <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-2 scale-95">
                        <div v-if="userMenuOpen" :class="['absolute bottom-14 z-50 w-52 bg-white/90 backdrop-blur-xl dark:bg-gray-800/90 rounded-2xl shadow-xl border border-white/20 dark:border-gray-700 overflow-hidden py-1 mb-2 ring-1 ring-black ring-opacity-5', isSidebarCollapsed ? 'left-14' : 'right-0']">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50">
                                <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Account</p>
                                <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate mt-1">{{ user?.email }}</p>
                            </div>
                            <Link :href="route('profile.edit')" @click="userMenuOpen = false" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profile Settings
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" @click="userMenuOpen = false" class="w-full flex items-center gap-2 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors text-left border-t border-gray-100 dark:border-gray-700/50">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Log Out
                            </Link>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 w-0 overflow-hidden relative">
            <!-- Top header (Glassmorphism) -->
            <div class="relative z-20 flex flex-shrink-0 h-16 bg-white/70 backdrop-blur-md dark:bg-gray-900/70 border-b border-gray-200/50 dark:border-gray-800/50 shadow-sm sm:h-16 transition-colors duration-300">
                <!-- Mobile sidebar toggle -->
                <button @click="mobileSidebarOpen = true" class="px-4 border-r border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>

                <!-- Desktop sidebar toggle (Collapse button) -->
                <button @click="isSidebarCollapsed = !isSidebarCollapsed" class="hidden sm:flex items-center justify-center px-4 border-r border-gray-200/50 dark:border-gray-800/50 text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors focus:outline-none">
                    <span class="sr-only">Toggle Sidebar</span>
                    <svg class="w-6 h-6 transform transition-transform duration-300" :class="isSidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </button>

                <div class="flex justify-between flex-1 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-1 items-center">
                        <slot name="header" />
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Clock In / Clock Out Widget (hidden for super_admin) -->
                        <div v-if="role !== 'super_admin'" class="hidden sm:flex items-center gap-2">

                            <!-- Not clocked in yet -->
                            <button v-if="!todayAttendance"
                                @click="handleClockIn"
                                :disabled="geoStatus === 'fetching'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-400 to-emerald-500 hover:from-emerald-500 hover:to-emerald-600 disabled:opacity-60 text-white text-xs font-bold rounded-lg transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg v-if="geoStatus !== 'fetching'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <svg v-else class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                {{ geoStatus === 'fetching' ? 'Locating...' : 'Clock In' }}
                            </button>

                            <!-- Clocked in, not out yet -->
                            <div v-else-if="todayAttendance && !todayAttendance.clock_out" class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400 hidden md:inline">
                                    In: <strong class="text-indigo-600 dark:text-indigo-400">{{ todayAttendance.formatted_clock_in }}</strong>
                                </span>
                                <button
                                    @click="handleClockOut"
                                    :disabled="geoStatus === 'fetching'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 disabled:opacity-60 text-white text-xs font-bold rounded-lg transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <svg v-if="geoStatus !== 'fetching'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M12 8v4l3 3"/></svg>
                                    <svg v-else class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                    {{ geoStatus === 'fetching' ? 'Locating...' : 'Clock Out' }}
                                </button>
                            </div>

                            <!-- Fully clocked out -->
                            <div v-else class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100/80 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 text-xs font-medium rounded-lg backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-inner">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Done {{ todayAttendance.formatted_clock_out }}
                            </div>
                        </div>

                        <!-- Role Badge -->
                        <span class="hidden sm:inline-flex text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-transparent dark:border-white/5" :class="roleBadge.cls">
                            {{ roleBadge.label }}
                        </span>

                        <!-- Notifications Dropdown -->
                        <Dropdown align="right" width="80" v-if="user">
                            <template #trigger>
                                <button type="button" class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors focus:outline-none">
                                    <span class="sr-only">View notifications</span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span v-if="notifications.length > 0" class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900 animate-pulse"></span>
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-t-xl">
                                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Notifications</span>
                                    <Link v-if="notifications.length > 0" :href="route('notifications.markAllRead')" method="post" as="button" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold transition-colors">Mark all read</Link>
                                </div>
                                <div class="max-h-96 overflow-y-auto bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl">
                                    <template v-if="notifications.length > 0">
                                        <Link v-for="notif in notifications" :key="notif.id" :href="route('notifications.markRead', notif.id)"
                                            class="block px-4 py-3 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-colors border-b border-gray-50 dark:border-gray-700/50 last:border-0 group">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-xs flex-shrink-0 group-hover:scale-110 transition-transform">
                                                    {{ notif.data.actor_name?.charAt(0) || 'U' }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-tight">{{ notif.data.message }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">{{ new Date(notif.created_at).toLocaleDateString() }}</p>
                                                </div>
                                            </div>
                                        </Link>
                                    </template>
                                    <div v-else class="px-4 py-8 text-center flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3 text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">You're all caught up!</p>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- Main Scrollable Content with Page Transitions -->
            <main class="flex-1 relative z-10 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-[#0b1120] transition-colors duration-300">
                <transition name="page" mode="out-in">
                    <div :key="$page.url" class="w-full h-full">
                        <slot />
                    </div>
                </transition>
            </main>
        </div>
    </div>
</template>

<style>
/* Page Transition Animations */
.page-enter-active,
.page-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.page-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
