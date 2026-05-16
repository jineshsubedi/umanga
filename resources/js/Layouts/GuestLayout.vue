<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme';

// Initialize theme
useTheme();
</script>

<template>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-900">
        
        <!-- Background Image & Overlays -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=2000" alt="Corporate Background" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-gray-900/50 mix-blend-multiply"></div>
            <!-- Gradient to ensure readability at edges -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-gray-900/40"></div>
        </div>

        <!-- Glassmorphic Card Container -->
        <div class="relative z-10 w-full max-w-md px-6 py-12 mx-4 sm:mx-auto">
            <div class="backdrop-blur-xl bg-white/80 dark:bg-gray-900/80 border border-white/20 dark:border-gray-700/50 rounded-2xl shadow-2xl p-8 sm:p-10 transition-colors duration-300">
                
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <Link href="/">
                        <ApplicationLogo class="w-16 h-16 text-indigo-600 dark:text-indigo-400 drop-shadow-md" />
                    </Link>
                </div>
                
                <!-- Title & Form Content -->
                <slot />
                
            </div>
            
            <!-- Optional Footer / Links below card -->
            <div class="mt-8 text-center text-sm text-gray-300 dark:text-gray-400">
                &copy; {{ new Date().getFullYear() }} UMNG Portal. Secure Access.
            </div>
        </div>
        
    </div>
</template>
