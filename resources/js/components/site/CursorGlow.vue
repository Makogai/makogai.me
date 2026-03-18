<script setup lang="ts">
import { computed, onMounted, watchEffect } from 'vue';
import { useMouse } from '@vueuse/core';

// Use `client` coordinates so the glow stays aligned while the page scrolls.
const { x, y } = useMouse({ type: 'client', touch: false });

const style = computed(() => ({
    transform: `translate3d(${(x.value ?? 0) - 160}px, ${(y.value ?? 0) - 160}px, 0)`,
}));

onMounted(() => {
    if (typeof document === 'undefined') {
        return;
    }

    // Used by the background grid highlight (interactive w/ cursor glow position).
    document.documentElement.style.setProperty('--cursor-x', '50%');
    document.documentElement.style.setProperty('--cursor-y', '50%');

    watchEffect(() => {
        document.documentElement.style.setProperty(
            '--cursor-x',
            `${x.value ?? 0}px`,
        );
        document.documentElement.style.setProperty(
            '--cursor-y',
            `${y.value ?? 0}px`,
        );
    });
});
</script>

<template>
    <div
        class="pointer-events-none fixed top-0 left-0 z-50 hidden h-[320px] w-[320px] rounded-full opacity-0 blur-3xl transition-opacity duration-500 md:block md:opacity-100"
        :style="style"
        aria-hidden="true"
    >
        <div
            class="h-full w-full rounded-full bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.12),transparent_62%)] dark:bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.22),transparent_60%)]"
        />
        <div
            class="absolute inset-0 rounded-full bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.10),transparent_64%)] dark:bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.16),transparent_62%)]"
        />
        <div
            class="absolute inset-0 rounded-full bg-[radial-gradient(circle_at_center,rgba(236,72,153,0.08),transparent_66%)] dark:bg-[radial-gradient(circle_at_center,rgba(236,72,153,0.12),transparent_62%)]"
        />
    </div>
</template>
