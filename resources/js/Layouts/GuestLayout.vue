<script setup>
import { useTheme } from '@/Composables/useTheme';
import { ref, onMounted } from 'vue';

useTheme();

const bgVideo = ref(null);

onMounted(() => {
    if (bgVideo.value) {
        bgVideo.value.play().catch(() => {});
    }
});
</script>

<template>
    <!-- Animated gradient background -->
    <div class="guest-root">

        <!-- Decorative blobs for depth -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <!-- Main split card -->
        <div class="guest-card">

            <!-- ── Left: Form Panel ── -->
            <div class="guest-left">
                <!-- Top accent bar -->
                <div class="left-accent"></div>

                <div class="left-body">
                    <slot />
                </div>

                <!-- Footer -->
                <p class="left-footer">
                    &copy; {{ new Date().getFullYear() }}
                    {{ $page.props.app_settings?.app_name || 'UMNG Portal' }}.
                    All rights reserved.
                </p>
            </div>

            <!-- ── Right: Video / Visual Panel ── -->
            <div class="guest-right">
                <!-- Video -->
                <video
                    ref="bgVideo"
                    autoplay
                    muted
                    loop
                    playsinline
                    class="right-video"
                >
                    <source src="/videos/apivideo.mp4" type="video/mp4" />
                </video>

                <!-- Dark gradient overlay -->
                <div class="right-overlay"></div>

                <!-- Branding text -->
                <div class="right-brand">
                    <p class="right-tagline">HRIS for the modern workplace.</p>
                    <h1 class="right-title">
                        {{ $page.props.app_settings?.app_name || 'HUMAN RESOURCE INFORMATION SYSTEM' }}
                    </h1>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

/* ─────────────────────────────────────────
   Root – animated gradient background
───────────────────────────────────────── */
.guest-root {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    font-family: 'Inter', sans-serif;
    position: relative;
    overflow: hidden;

    /* Eye-catching multi-stop gradient */
    background: linear-gradient(135deg,
        #0f0c29 0%,
        #1a1a4e 15%,
        #24243e 30%,
        #1e3a5f 50%,
        #0d1b2a 70%,
        #16213e 85%,
        #0f3460 100%
    );
    background-size: 400% 400%;
    animation: bgShift 18s ease infinite;
}

@keyframes bgShift {
    0%   { background-position: 0%   50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0%   50%; }
}

/* ─────────────────────────────────────────
   Decorative blobs
───────────────────────────────────────── */
.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.25;
    pointer-events: none;
    animation: blobFloat 12s ease-in-out infinite alternate;
}
.blob-1 {
    width: 520px; height: 520px;
    background: radial-gradient(circle, #6366f1, #8b5cf6);
    top: -120px; left: -100px;
    animation-delay: 0s;
}
.blob-2 {
    width: 420px; height: 420px;
    background: radial-gradient(circle, #06b6d4, #3b82f6);
    bottom: -80px; right: -80px;
    animation-delay: -4s;
}
.blob-3 {
    width: 300px; height: 300px;
    background: radial-gradient(circle, #ec4899, #f43f5e);
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: -8s;
    opacity: 0.12;
}
@keyframes blobFloat {
    from { transform: scale(1) translateY(0); }
    to   { transform: scale(1.12) translateY(20px); }
}
.blob-3 { animation-name: blobFloat3; }
@keyframes blobFloat3 {
    from { transform: translate(-50%, -50%) scale(1); }
    to   { transform: translate(-50%, -52%) scale(1.08); }
}

/* ─────────────────────────────────────────
   Main Card
───────────────────────────────────────── */
.guest-card {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1080px;
    min-height: 620px;
    display: flex;
    flex-direction: column;
    border-radius: 28px;
    overflow: hidden;
    box-shadow:
        0 40px 100px rgba(0, 0, 0, 0.50),
        0 0 0 1px rgba(255,255,255,0.06);
    animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
}
@keyframes cardIn {
    from { opacity: 0; transform: translateY(28px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@media (min-width: 768px) {
    .guest-card { flex-direction: row; }
}

/* ─────────────────────────────────────────
   Left Panel
───────────────────────────────────────── */
.guest-left {
    position: relative;
    width: 100%;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2.5rem 5rem;
    flex-shrink: 0;
}
@media (min-width: 768px) {
    .guest-left {
        width: 42%;
        min-height: 620px;
        padding: 3.5rem 3rem 5rem;
    }
}

/* Coloured top accent stripe */
.left-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed, #0ea5e9);
    background-size: 200% 100%;
    animation: accentSlide 3s linear infinite;
}
@keyframes accentSlide {
    0%   { background-position: 0% 0%; }
    100% { background-position: 200% 0%; }
}

.left-body {
    width: 100%;
    max-width: 320px;
}

.left-footer {
    position: absolute;
    bottom: 1.5rem;
    left: 0; right: 0;
    text-align: center;
    font-size: 0.68rem;
    color: #9ca3af;
    letter-spacing: 0.02em;
}

/* ─────────────────────────────────────────
   Right Panel
───────────────────────────────────────── */
.guest-right {
    display: none;
    position: relative;
    flex: 1;
    background: #0f1a35;
    overflow: hidden;
}
@media (min-width: 768px) {
    .guest-right { display: block; }
}

.right-video {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.85;
    transform: scale(1.05);
    pointer-events: none;
}

.right-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(15, 26, 53, 0.72) 0%,
        rgba(30, 58, 95, 0.45) 50%,
        rgba(15, 26, 53, 0.65) 100%
    );
}

.right-brand {
    position: absolute;
    bottom: 2.5rem;
    right: 2.5rem;
    text-align: right;
    max-width: 380px;
    z-index: 10;
}
.right-tagline {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.65);
    margin-bottom: 0.5rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.right-title {
    font-size: clamp(2rem, 4vw, 3.5rem);
    font-weight: 900;
    color: #ffffff;
    line-height: 1.05;
    letter-spacing: -0.01em;
    text-transform: uppercase;
    text-shadow: 0 4px 24px rgba(0,0,0,0.5);
}
</style>
