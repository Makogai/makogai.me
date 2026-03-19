<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const page = usePage();
const brandName =
    ((page.props as any).settings?.site?.site_name as string | undefined) ??
    (page.props as any).name ??
    'Portfolio';
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-x-clip bg-background p-6 md:p-10"
    >
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,rgba(99,102,241,0.14)_1px,transparent_1px),linear-gradient(to_bottom,rgba(34,211,238,0.10)_1px,transparent_1px)] [background-size:52px_52px]"
            />
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
                            class="mb-1 flex h-10 w-10 items-center justify-center rounded-xl border border-black/10 bg-white/70 ring-1 ring-black/10 backdrop-blur-xl dark:border-white/10 dark:bg-white/10 dark:ring-white/10"
                        >
                            <img
                                src="/logo.svg"
                                alt=""
                                class="size-5 opacity-95 [filter:drop-shadow(0_1px_0_rgba(0,0,0,0.08))] dark:invert"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <div class="text-xs uppercase tracking-[0.14em] text-foreground/55">
                            {{ brandName }}
                        </div>
                        <h1 class="text-xl font-semibold tracking-tight">{{ title }}</h1>
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
