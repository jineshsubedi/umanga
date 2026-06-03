<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme';

// Initialize theme
useTheme();
</script>

<template>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-[#3b0e77] via-[#5b14b8] to-[#2c0b5c] p-4 sm:p-8 font-sans">
        
        <!-- Main Card -->
        <div class="relative z-10 w-full max-w-[1000px] flex flex-col md:flex-row bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl overflow-hidden min-h-[600px]">
            
            <!-- Left Side - Illustration -->
            <div class="w-full md:w-1/2 relative hidden md:block overflow-hidden bg-[#7c3aed]">
                <img src="/images/auth/login_illustration_corporate.png" alt="Welcome Illustration" class="absolute inset-0 w-full h-full object-cover scale-105" />
            </div>
            
            <!-- Right Side - Form Container -->
            <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 relative flex flex-col justify-center bg-white dark:bg-gray-900">
                
                <!-- Form Content -->
                <div class="w-full max-w-sm mx-auto mt-0 md:mt-0">
                    <slot />
                </div>
                
            </div>
        </div>
        
    </div>
</template>
