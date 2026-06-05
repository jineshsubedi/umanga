<script setup>
import { Link } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme';
import { ref, onMounted } from 'vue';

// Initialize theme
useTheme();

const bgVideo = ref(null);

onMounted(() => {
    if (bgVideo.value) {
        bgVideo.value.play().catch(e => console.log("Video autoplay prevented:", e));
    }
});
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#243460] to-[#1a264a] font-sans p-4 sm:p-8">
        
        <!-- Main Card -->
        <div class="w-full max-w-[1100px] bg-white rounded-3xl shadow-2xl flex flex-col md:flex-row overflow-hidden min-h-[600px] lg:min-h-[650px] relative">
            
            <!-- Left Side - Form Container -->
            <div class="w-full md:w-[45%] lg:w-[40%] p-8 sm:p-10 flex flex-col items-center justify-center relative bg-white z-10 rounded-l-3xl">
                
                <div class="w-full max-w-xs mx-auto mt-12 mb-8">
                    <slot />
                </div>
                
                <!-- Bottom dots indicator -->
                <div class="absolute bottom-8 flex gap-2 text-xs text-gray-500">
                    &copy; {{ new Date().getFullYear() }} {{ $page.props.app_settings?.app_name || 'UMNG Portal' }}. All rights reserved.
                </div>
            </div>
            
            <!-- Right Side - Video Background & Welcome -->
            <div class="w-full md:w-[55%] lg:w-[60%] relative hidden md:block overflow-hidden bg-[#243460]">
                <!-- Video Background -->
                <video ref="bgVideo" autoplay muted playsinline class="absolute inset-0 w-full h-full object-cover opacity-90 scale-105 mix-blend-screen pointer-events-none">
                    <source src="/videos/apivideo.mp4" type="video/mp4" />
                </video>
                
                <!-- Overlay Gradients to blend -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#243460]/80 via-transparent to-transparent opacity-60"></div>
                
                <!-- Welcome Text -->
                <div class="absolute bottom-24 right-16 text-right max-w-md z-20">
                    <h1 class="text-6xl lg:text-7xl font-extrabold text-white mb-4 tracking-tight drop-shadow-lg">
                        {{ $page.props.app_settings?.app_name || 'UMNG Portal' }}
                    </h1>
                    <p class="text-white/80 text-xs leading-relaxed mb-6">
                        Memos for the modern workplace.
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</template>
