<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
    >
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div
                class="absolute -top-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.18),transparent_62%)] blur-3xl dark:bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.32),transparent_60%)]"
            />
            <div
                class="absolute -bottom-48 left-[-10rem] h-[32rem] w-[32rem] rounded-full bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.12),transparent_64%)] blur-3xl dark:bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.22),transparent_60%)]"
            />
            <div
                class="absolute right-[-12rem] -bottom-56 h-[40rem] w-[40rem] rounded-full bg-[radial-gradient(circle_at_center,rgba(236,72,153,0.10),transparent_66%)] blur-3xl dark:bg-[radial-gradient(circle_at_center,rgba(236,72,153,0.18),transparent_62%)]"
            />
            <div class="noise absolute inset-0 opacity-20" />
        </div>
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex h-9 w-9 items-center justify-center rounded-md"
                        >
                            <AppLogoIcon
                                class="size-9 fill-current text-[var(--foreground)] dark:text-white"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
