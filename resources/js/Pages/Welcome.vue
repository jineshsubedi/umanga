<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head :title="`Welcome to ${$page.props.app_settings?.app_name || 'UMNG Portal'}`" />

    <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300 font-sans">
        
        <!-- Navbar -->
        <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <ApplicationLogo class="h-10 w-10 text-indigo-600 dark:text-indigo-400" />
                        <span class="font-bold text-2xl tracking-tight text-gray-900 dark:text-white">{{ $page.props.app_settings?.app_name || 'UMNG Portal' }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')" class="text-sm font-semibold text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400 transition-colors">
                                Go to Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link v-if="canLogin" :href="route('login')" class="text-sm font-semibold text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400 transition-colors px-3 py-2 rounded-md">
                                Log in
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors px-5 py-2.5 rounded-lg shadow-md shadow-indigo-600/20">
                                Register Company
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=2000" alt="Corporate Office" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-gray-900/70 mix-blend-multiply"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-transparent dark:from-gray-900 dark:via-gray-900/80 dark:to-transparent"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-8 drop-shadow-sm">
                    Elevate Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">Corporate Governance</span>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-700 dark:text-gray-300 mb-10">
                    A centralized, multi-tenant platform for managing meeting minutes, streamlining approvals, and fostering organizational transparency.
                </p>
                <div class="flex justify-center gap-4">
                    <Link :href="route('register')" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-base font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-600/30 transition-all transform hover:-translate-y-0.5">
                        Start for Free
                    </Link>
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-3.5 border-2 border-gray-300 dark:border-gray-700 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                        Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-24 bg-white dark:bg-gray-900 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">Features</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Everything you need to manage meetings
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <!-- Feature 1 -->
                    <div class="relative p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-shadow duration-300 group">
                        <div class="absolute -top-6 left-8">
                            <div class="inline-flex items-center justify-center p-3 bg-indigo-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-8 text-xl font-bold text-gray-900 dark:text-white">Role-Based Access</h3>
                        <p class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                            Granular control over who can create, review, and approve meeting minutes. Secure workspaces for Super Admins, Company Admins, Managers, and Clients.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="relative p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-shadow duration-300 group">
                        <div class="absolute -top-6 left-8">
                            <div class="inline-flex items-center justify-center p-3 bg-purple-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-8 text-xl font-bold text-gray-900 dark:text-white">Approval Workflows</h3>
                        <p class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                            Clients submit minutes, Managers review and approve or reject with detailed comments. A seamless, transparent workflow that maintains a clear audit trail.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="relative p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-shadow duration-300 group">
                        <div class="absolute -top-6 left-8">
                            <div class="inline-flex items-center justify-center p-3 bg-pink-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-8 text-xl font-bold text-gray-900 dark:text-white">Real-Time Notifications</h3>
                        <p class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                            Stay informed instantly. Receive alerts when new minutes are submitted, approved, or require your attention. Never miss a critical update again.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
                <div class="flex justify-center space-x-6 md:order-2">
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">GitHub</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
                <div class="mt-8 md:mt-0 md:order-1 flex items-center justify-center md:justify-start gap-2">
                    <ApplicationLogo class="w-6 h-6 text-gray-400" />
                    <p class="text-base text-gray-400">
                        &copy; {{ new Date().getFullYear() }} {{ $page.props.app_settings?.app_name || 'UMNG Portal' }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
