<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

const page = usePage();
const profile = computed(
    () => ((page.props as any).settings?.profile ?? {}) as Record<string, any>,
);

const fullName = computed(
    () => (profile.value.full_name as string | undefined) ?? 'Your Name',
);
const headline = computed(
    () =>
        (profile.value.headline as string | undefined) ??
        'Full‑stack Developer',
);
const bio = computed(
    () =>
        (profile.value.bio as string | undefined) ??
        'I build premium web products with Laravel, Vue, and a design-first mindset.',
);
</script>

<template>
    <SiteLayout>
        <Head title="About" />

        <div
            class="relative overflow-hidden rounded-3xl border border-black/10 bg-white/70 p-10 ring-1 ring-black/10 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_20%,rgba(99,102,241,0.16),transparent_40%),radial-gradient(circle_at_90%_20%,rgba(34,211,238,0.10),transparent_35%),radial-gradient(circle_at_50%_90%,rgba(236,72,153,0.10),transparent_42%)]"
            />

            <div class="relative">
                <p class="text-sm font-medium tracking-wide text-foreground/70">
                    {{ headline }}
                </p>
                <h1
                    class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl"
                >
                    About {{ fullName }}
                </h1>
                <p
                    class="mt-5 max-w-3xl text-base leading-relaxed text-foreground/70 sm:text-lg"
                >
                    {{ bio }}
                </p>
            </div>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-12">
            <div
                class="rounded-3xl border border-black/10 bg-white/70 p-8 ring-1 ring-black/10 backdrop-blur-xl lg:col-span-7 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <h2 class="text-lg font-semibold tracking-tight">What I do</h2>
                <p class="mt-3 text-sm leading-relaxed text-foreground/65">
                    I design and build end-to-end experiences: product UI/UX,
                    architecture, and implementation. I prefer fast feedback
                    loops, clean systems, and pragmatic engineering.
                </p>
                <ul class="mt-5 space-y-2 text-sm text-foreground/70">
                    <li>
                        - Product engineering (Laravel, Vue, Inertia, MySQL)
                    </li>
                    <li>- Design systems & premium UI polish</li>
                    <li>- Performance, SSR, and DX</li>
                </ul>
            </div>

            <div
                class="rounded-3xl border border-black/10 bg-white/70 p-8 ring-1 ring-black/10 backdrop-blur-xl lg:col-span-5 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <h2 class="text-lg font-semibold tracking-tight">Now</h2>
                <p class="mt-3 text-sm leading-relaxed text-foreground/65">
                    Currently focused on shipping a premium personal platform,
                    writing high-signal technical content, and showcasing work
                    with product-level presentation.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span
                        class="rounded-full bg-white/80 px-3 py-1.5 text-xs ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        Laravel
                    </span>
                    <span
                        class="rounded-full bg-white/80 px-3 py-1.5 text-xs ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        Vue
                    </span>
                    <span
                        class="rounded-full bg-white/80 px-3 py-1.5 text-xs ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        UI/UX
                    </span>
                    <span
                        class="rounded-full bg-white/80 px-3 py-1.5 text-xs ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        SSR
                    </span>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
