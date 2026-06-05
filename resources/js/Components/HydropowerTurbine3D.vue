<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import * as THREE from 'three';

const containerRef = ref(null);
const isLoading = ref(true);

// Simulation State
const discharge = ref(120); // m3/s (water flow rate)
const head = ref(85); // meters (net head)
const efficiency = ref(0.92); // 92% efficiency
const turbineSpeed = ref(375); // RPM
const powerOutput = ref(0); // Megawatts (MW)

// Calculate Power Output: P = eta * rho * g * Q * H
// P = efficiency * 1000 kg/m3 * 9.81 m/s2 * discharge * head / 1e6 (to get MW)
const calculatePower = () => {
    const P = efficiency.value * 1000 * 9.81 * discharge.value * head.value / 1000000;
    powerOutput.value = parseFloat(P.toFixed(2));
    // Calculate RPM based on discharge (simplified linear relation for visual feedback)
    turbineSpeed.value = Math.round(discharge.value * 3.12);
};

// Recalculate initially
calculatePower();

// Watch discharge to update calculations
watch(discharge, () => {
    calculatePower();
});

// Three.js variables
let scene, camera, renderer, animationFrameId;
let containerGroup, turbineGroup, scrollCase, draftTube;
let particleSystem, particleGeometry, particlesCount = 800;
let particleData = [];

// Drag variables
let isDragging = false;
let previousMousePosition = { x: 0, y: 0 };
const rotationSpeed = 0.005;

const handleMouseDown = (e) => {
    isDragging = true;
    previousMousePosition = {
        x: e.clientX,
        y: e.clientY
    };
};

const handleMouseMove = (e) => {
    if (!isDragging) return;
    const deltaMove = {
        x: e.clientX - previousMousePosition.x,
        y: e.clientY - previousMousePosition.y
    };

    if (containerGroup) {
        containerGroup.rotation.y += deltaMove.x * rotationSpeed;
        containerGroup.rotation.x += deltaMove.y * rotationSpeed;
        // Limit X rotation to avoid flipping upside down
        containerGroup.rotation.x = Math.max(-Math.PI / 3, Math.min(Math.PI / 3, containerGroup.rotation.x));
    }

    previousMousePosition = {
        x: e.clientX,
        y: e.clientY
    };
};

const handleMouseUp = () => {
    isDragging = false;
};

// Handle Touch Events for Mobile
const handleTouchStart = (e) => {
    if (e.touches.length === 1) {
        isDragging = true;
        previousMousePosition = {
            x: e.touches[0].clientX,
            y: e.touches[0].clientY
        };
    }
};

const handleTouchMove = (e) => {
    if (!isDragging || e.touches.length !== 1) return;
    const deltaMove = {
        x: e.touches[0].clientX - previousMousePosition.x,
        y: e.touches[0].clientY - previousMousePosition.y
    };

    if (containerGroup) {
        containerGroup.rotation.y += deltaMove.x * rotationSpeed;
        containerGroup.rotation.x += deltaMove.y * rotationSpeed;
        containerGroup.rotation.x = Math.max(-Math.PI / 3, Math.min(Math.PI / 3, containerGroup.rotation.x));
    }

    previousMousePosition = {
        x: e.touches[0].clientX,
        y: e.touches[0].clientY
    };
};

onMounted(() => {
    if (!containerRef.value) return;

    // 1. Scene setup
    scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(0x0a192f, 0.05);

    // 2. Camera setup
    const width = containerRef.value.clientWidth;
    const height = containerRef.value.clientHeight || 450;
    camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
    camera.position.set(0, 3, 8);

    // 3. Renderer setup
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    containerRef.value.appendChild(renderer.domElement);

    // 4. Lights
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.35);
    scene.add(ambientLight);

    const dirLight = new THREE.DirectionalLight(0x00d2ff, 1.5);
    dirLight.position.set(5, 10, 7);
    scene.add(dirLight);

    const blueLight = new THREE.PointLight(0x0052cc, 2, 15);
    blueLight.position.set(-4, 2, -3);
    scene.add(blueLight);

    const purpleLight = new THREE.PointLight(0x9d4edd, 2.5, 15);
    purpleLight.position.set(3, -2, 4);
    scene.add(purpleLight);

    // 5. Container Group (allows rotating the scene view)
    containerGroup = new THREE.Group();
    containerGroup.rotation.x = 0.3; // Tilt slightly
    containerGroup.rotation.y = -0.5;
    scene.add(containerGroup);

    // 6. Build Turbine Generator (Francis/Kaplan Hybrid Shiny Model)
    turbineGroup = new THREE.Group();
    containerGroup.add(turbineGroup);

    // Shiny Metallic Material
    const metalMaterial = new THREE.MeshStandardMaterial({
        color: 0xcccccc,
        metalness: 0.9,
        roughness: 0.15,
        flatShading: true
    });

    const bronzeMaterial = new THREE.MeshStandardMaterial({
        color: 0xdfa050,
        metalness: 0.8,
        roughness: 0.25,
        flatShading: true
    });

    const glassMaterial = new THREE.MeshPhysicalMaterial({
        color: 0x00a8ff,
        transparent: true,
        opacity: 0.15,
        roughness: 0.1,
        transmission: 0.9,
        thickness: 0.5
    });

    // Shaft
    const shaftGeometry = new THREE.CylinderGeometry(0.12, 0.12, 3.5, 16);
    const shaft = new THREE.Mesh(shaftGeometry, metalMaterial);
    shaft.position.y = 0.5;
    turbineGroup.add(shaft);

    // Rotor Hub (generator top)
    const hubGeometry = new THREE.CylinderGeometry(0.7, 0.7, 0.6, 12);
    const hub = new THREE.Mesh(hubGeometry, bronzeMaterial);
    hub.position.y = 1.8;
    turbineGroup.add(hub);

    // Generator Copper Coils (around the hub)
    const coilGroup = new THREE.Group();
    containerGroup.add(coilGroup); // Fixed stator
    const coilGeometry = new THREE.BoxGeometry(0.2, 0.8, 0.15);
    const coilMat = new THREE.MeshStandardMaterial({ color: 0xc87d55, metalness: 0.7, roughness: 0.3 });
    for (let i = 0; i < 12; i++) {
        const angle = (i / 12) * Math.PI * 2;
        const coil = new THREE.Mesh(coilGeometry, coilMat);
        coil.position.set(Math.cos(angle) * 1.0, 1.8, Math.sin(angle) * 1.0);
        coil.rotation.y = -angle;
        coilGroup.add(coil);
    }

    // Stator Outer Ring
    const statorRingGeom = new THREE.TorusGeometry(1.1, 0.1, 16, 48);
    const statorRing = new THREE.Mesh(statorRingGeom, metalMaterial);
    statorRing.rotation.x = Math.PI / 2;
    statorRing.position.y = 1.8;
    containerGroup.add(statorRing);

    // Runner Hub (bottom turbine)
    const runnerHubGeom = new THREE.CylinderGeometry(0.4, 0.25, 0.7, 16);
    const runnerHub = new THREE.Mesh(runnerHubGeom, metalMaterial);
    runnerHub.position.y = -0.8;
    turbineGroup.add(runnerHub);

    // Blades (Kaplan adjustable pitch look)
    const bladeGeometry = new THREE.BoxGeometry(0.7, 0.08, 0.45);
    // Offset geometry so rotation point is at the edge of the box
    bladeGeometry.translate(0.35, 0, 0);

    const bladeCount = 6;
    for (let i = 0; i < bladeCount; i++) {
        const angle = (i / bladeCount) * Math.PI * 2;
        const blade = new THREE.Mesh(bladeGeometry, bronzeMaterial);
        blade.position.y = -0.8;
        // Pitch angle
        blade.rotation.x = 0.35; 
        blade.rotation.y = angle;
        turbineGroup.add(blade);
    }

    // 7. Fixed Transparent Casing (Outer Scroll Case)
    const scrollCaseGeom = new THREE.TorusGeometry(1.6, 0.4, 12, 36);
    scrollCase = new THREE.Mesh(scrollCaseGeom, glassMaterial);
    scrollCase.rotation.x = Math.PI / 2;
    scrollCase.position.y = -0.8;
    containerGroup.add(scrollCase);

    // Draft Tube (Water exit at bottom)
    const draftTubeGeom = new THREE.CylinderGeometry(0.7, 1.2, 1.5, 16, 1, true);
    draftTube = new THREE.Mesh(draftTubeGeom, glassMaterial);
    draftTube.position.y = -2.0;
    containerGroup.add(draftTube);

    // 8. Water Flow Particle System
    particleGeometry = new THREE.BufferGeometry();
    const positions = new Float32Array(particlesCount * 3);
    const colors = new Float32Array(particlesCount * 3);

    const colorWater = new THREE.Color(0x00f0ff);
    const colorFoam = new THREE.Color(0xffffff);

    for (let i = 0; i < particlesCount; i++) {
        // Define initial reset state for particles
        resetParticle(i);

        positions[i * 3] = particleData[i].x;
        positions[i * 3 + 1] = particleData[i].y;
        positions[i * 3 + 2] = particleData[i].z;

        // Blended blue-cyan-white color
        const mixedColor = colorWater.clone().lerp(colorFoam, Math.random() * 0.3);
        colors[i * 3] = mixedColor.r;
        colors[i * 3 + 1] = mixedColor.g;
        colors[i * 3 + 2] = mixedColor.b;
    }

    particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    particleGeometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

    // Custom glowing particle texture (using Canvas fallback to avoid loading assets)
    const pCanvas = document.createElement('canvas');
    pCanvas.width = 16;
    pCanvas.height = 16;
    const ctx = pCanvas.getContext('2d');
    const grad = ctx.createRadialGradient(8, 8, 0, 8, 8, 8);
    grad.addColorStop(0, 'rgba(255,255,255,1)');
    grad.addColorStop(0.3, 'rgba(0,210,255,0.8)');
    grad.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 16, 16);
    
    const pTexture = new THREE.CanvasTexture(pCanvas);

    const particleMaterial = new THREE.PointsMaterial({
        size: 0.12,
        map: pTexture,
        vertexColors: true,
        transparent: true,
        opacity: 0.8,
        depthWrite: false,
        blending: THREE.AdditiveBlending
    });

    particleSystem = new THREE.Points(particleGeometry, particleMaterial);
    containerGroup.add(particleSystem);

    isLoading.value = false;

    // Window resize handler
    const handleResize = () => {
        if (!containerRef.value || !renderer || !camera) return;
        const w = containerRef.value.clientWidth;
        const h = containerRef.value.clientHeight || 450;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    };
    window.addEventListener('resize', handleResize);

    // 9. Animation Loop
    const animate = () => {
        animationFrameId = requestAnimationFrame(animate);

        // Rotate Turbine based on discharge setting
        const speedFactor = discharge.value / 200; // Normalised ratio
        turbineGroup.rotation.y += 0.05 * speedFactor;

        // Slow continuous automatic rotaton of whole container when not dragging
        if (!isDragging) {
            containerGroup.rotation.y += 0.0015;
        }

        // Animate particles
        const posArr = particleGeometry.attributes.position.array;
        for (let i = 0; i < particlesCount; i++) {
            const data = particleData[i];
            
            // Advance particle progress along its trajectory path
            // Speed of flow depends on discharge rate
            data.progress += 0.012 * (0.4 + speedFactor * 0.8);

            if (data.progress >= 1.0) {
                resetParticle(i);
            }

            const p = data.progress;

            if (data.type === 'inlet') {
                // Spiral inward to simulate water entering scroll case
                const angle = data.startAngle - p * Math.PI * 1.5;
                const radius = 2.0 - p * 0.6;
                data.x = Math.cos(angle) * radius;
                data.y = -0.5 - p * 0.3; // descend towards blade level
                data.z = Math.sin(angle) * radius;
            } else {
                // Flowing through turbine runner and down the draft tube
                // Spiralling tight spinning helix
                const angle = data.startAngle + p * Math.PI * 4 * speedFactor;
                // Expanding out at the bottom
                const radius = 0.4 + p * 0.6;
                data.x = Math.cos(angle) * radius;
                data.y = -0.8 - p * 1.5; // fall downward
                data.z = Math.sin(angle) * radius;
            }

            posArr[i * 3] = data.x;
            posArr[i * 3 + 1] = data.y;
            posArr[i * 3 + 2] = data.z;
        }
        
        particleGeometry.attributes.position.needsUpdate = true;

        renderer.render(scene, camera);
    };

    animate();

    onUnmounted(() => {
        window.removeEventListener('resize', handleResize);
        cancelAnimationFrame(animationFrameId);
        if (renderer) {
            renderer.dispose();
        }
    });
});

// Helper to initialize or reset particle data path
function resetParticle(index) {
    const isScrollCase = Math.random() > 0.4;
    particleData[index] = {
        type: isScrollCase ? 'inlet' : 'outlet',
        progress: Math.random(), // Start at random point initially to distribute
        startAngle: Math.random() * Math.PI * 2,
        x: 0,
        y: 0,
        z: 0
    };
}
</script>

<template>
    <div class="relative w-full h-full bg-slate-950/40 rounded-3xl border border-slate-800/80 backdrop-blur-md overflow-hidden flex flex-col justify-between p-6 shadow-2xl shadow-indigo-950/20 group/canvas">
        <!-- Floating Cyber Labels -->
        <div class="absolute top-4 left-4 z-10 pointer-events-none">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">Turbine Simulation Live</span>
            </div>
            <p class="text-[10px] font-mono text-slate-500 mt-1">Francis Runner | Interactive 3D Model</p>
        </div>

        <div class="absolute top-4 right-4 z-10 pointer-events-none">
            <span class="text-[10px] font-mono text-indigo-400 bg-indigo-950/60 border border-indigo-800/50 px-2 py-0.5 rounded-full">
                Drag to Rotate Scene
            </span>
        </div>

        <!-- 3D Canvas Mount Point -->
        <div 
            ref="containerRef" 
            class="flex-1 w-full min-h-[300px] cursor-grab active:cursor-grabbing relative"
            @mousedown="handleMouseDown"
            @mousemove="handleMouseMove"
            @mouseup="handleMouseUp"
            @mouseleave="handleMouseUp"
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleMouseUp"
        >
            <!-- Loading overlay -->
            <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-slate-950/90 z-20">
                <div class="flex flex-col items-center gap-3">
                    <svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs text-slate-400 font-mono tracking-widest uppercase">Initializing WebGL Engine...</span>
                </div>
            </div>
        </div>

        <!-- Simulated Metrics & Interactive Controls -->
        <div class="mt-4 border-t border-slate-800/80 pt-4 z-10">
            <!-- Simulated Readouts -->
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-slate-900/60 border border-slate-800/50 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 font-mono">Discharge</span>
                    <p class="text-lg font-bold font-mono text-cyan-400 mt-1">{{ discharge }} <span class="text-xs font-normal text-slate-400">m³/s</span></p>
                </div>
                <div class="bg-slate-900/60 border border-slate-800/50 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 font-mono">Runner Speed</span>
                    <p class="text-lg font-bold font-mono text-amber-400 mt-1">{{ turbineSpeed }} <span class="text-xs font-normal text-slate-400">RPM</span></p>
                </div>
                <div class="bg-slate-900/60 border border-slate-800/50 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 font-mono">Gen Power</span>
                    <p class="text-lg font-bold font-mono text-indigo-400 mt-1">{{ powerOutput }} <span class="text-xs font-normal text-slate-400">MW</span></p>
                </div>
            </div>

            <!-- Controller Slider -->
            <div class="space-y-2 bg-slate-950/60 border border-slate-900 p-4 rounded-2xl">
                <div class="flex justify-between items-center text-xs font-mono text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-cyan-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Adjust Water Discharge Flow Rate
                    </span>
                    <span class="text-cyan-400 font-bold">{{ discharge }} m³/s</span>
                </div>
                <input 
                    type="range" 
                    v-model.number="discharge" 
                    min="10" 
                    max="300" 
                    step="5"
                    class="w-full h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400/50"
                />
                <div class="flex justify-between text-[10px] text-slate-500 font-mono">
                    <span>Min (Low Flow)</span>
                    <span>Design Flow: 120 m³/s</span>
                    <span>Max (Flood Peak)</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom range input styling */
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #00f0ff;
    cursor: pointer;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.6);
    transition: transform 0.1s ease;
}

input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.25);
}

input[type="range"]::-moz-range-thumb {
    width: 14px;
    height: 14px;
    border: none;
    border-radius: 50%;
    background: #00f0ff;
    cursor: pointer;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.6);
    transition: transform 0.1s ease;
}

input[type="range"]::-moz-range-thumb:hover {
    transform: scale(1.25);
}
</style>
