<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import CursorGlow from '@/components/site/CursorGlow.vue';

const { resolvedAppearance, updateAppearance } = useAppearance();
const page = usePage();

const isDark = computed(() => resolvedAppearance.value === 'dark');
const brandName = computed(
    () =>
        ((page.props as any).settings?.site?.site_name as string | undefined) ??
        'makogai',
);

const socials = computed(() => {
    const site = ((page.props as any).settings?.site ?? {}) as Record<
        string,
        string | null
    >;

    return {
        github: site.github_url,
        linkedin: site.linkedin_url,
        x: site.x_url,
    };
});

function toggleTheme() {
    updateAppearance(isDark.value ? 'light' : 'dark');
}

const gridMaskStyle = {
    maskImage:
        'radial-gradient(circle at var(--cursor-x, 50%) var(--cursor-y, 50%), rgba(255,255,255,0.95) 0%, transparent 26%)',
    WebkitMaskImage:
        'radial-gradient(circle at var(--cursor-x, 50%) var(--cursor-y, 50%), rgba(255,255,255,0.95) 0%, transparent 26%)',
};
</script>

<template>
    <div
        class="relative isolate flex min-h-screen flex-col overflow-x-clip"
        style="min-height: 100dvh; width: 100%;"
    >
        <CursorGlow />

        <div
            class="pointer-events-none fixed inset-0 z-0"
            style="background-image: linear-gradient(to right, rgba(99,102,241,0.22) 1px, transparent 1px), linear-gradient(to bottom, rgba(34,211,238,0.18) 1px, transparent 1px), radial-gradient(circle, rgba(99,102,241,0.16) 1px, transparent 1.4px); background-size: 48px 48px, 48px 48px, 48px 48px;"
        >
            <div
                class="absolute -top-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.30),transparent_62%)] blur-3xl dark:bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.42),transparent_60%)]"
            />
            <div
                class="absolute -bottom-48 left-[-10rem] h-[32rem] w-[32rem] rounded-full bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.22),transparent_64%)] blur-3xl dark:bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.30),transparent_60%)]"
            />
            <div
                class="absolute right-[-12rem] -bottom-56 h-[40rem] w-[40rem] rounded-full bg-[radial-gradient(circle_at_center,rgba(236,72,153,0.18),transparent_66%)] blur-3xl dark:bg-[radial-gradient(circle_at_center,rgba(236,72,153,0.24),transparent_62%)]"
            />
            <div
                class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(0,0,0,0.35),transparent_40%,rgba(0,0,0,0.35))] opacity-0 dark:opacity-100"
            />
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(255,255,255,0.65),transparent_56%)] opacity-100 dark:opacity-0"
            />
            <div class="noise absolute inset-0 opacity-30" />

            <!-- Spotlight programmer grid (follows cursor vars) -->
            <div
                class="absolute inset-0 opacity-65 mix-blend-overlay"
                :style="{
                    ...gridMaskStyle,
                    backgroundImage:
                        'radial-gradient(circle, rgba(99,102,241,0.55) 1.8px, transparent 2.2px), radial-gradient(circle, rgba(34,211,238,0.42) 1.4px, transparent 2px)',
                    backgroundSize: '48px 48px, 48px 48px',
                }"
            />

            <div
                class="absolute inset-0 opacity-35 mix-blend-screen"
                :style="{
                    backgroundImage:
                        'radial-gradient(circle at var(--cursor-x, 50%) var(--cursor-y, 50%), rgba(99,102,241,0.45), transparent 34%)',
                }"
            />
        </div>

        <header
            class="sticky top-0 z-30 border-b border-black/10 bg-background/70 backdrop-blur-xl dark:border-white/10"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6"
            >
                <Link href="/" class="group inline-flex items-center gap-2">
                    <span
                        class="relative inline-flex h-7 w-7 items-center justify-center"
                    >
                        <span
                            class="absolute inset-0 rounded-xl bg-white/70 ring-1 ring-black/10 backdrop-blur dark:bg-white/10 dark:ring-white/10"
                        />
                        <span
                            class="absolute inset-0 rounded-xl bg-[linear-gradient(135deg,rgba(99,102,241,0.5),rgba(34,211,238,0.35),rgba(236,72,153,0.35))] opacity-0 blur-md transition-opacity duration-300 group-hover:opacity-60"
                        />
                        <img
                            src="/logo.svg"
                            alt=""
                            class="relative h-4 w-4 opacity-95 [filter:drop-shadow(0_1px_0_rgba(0,0,0,0.08))] dark:invert"
                        />
                    </span>
                    <span
                        class="text-sm font-medium tracking-tight text-foreground/90"
                    >
                        {{ brandName }}
                    </span>
                </Link>

                <nav class="hidden items-center gap-1 sm:flex">
                    <Link class="site-nav-link" href="/">Home</Link>
                    <Link class="site-nav-link" href="/about">About</Link>
                    <Link class="site-nav-link" href="/projects">Projects</Link>
                    <Link class="site-nav-link" href="/blog">Blog</Link>
                    <Link class="site-nav-link" href="/activity">Activity</Link>
                </nav>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="glass-button"
                        @click="toggleTheme"
                    >
                        <span class="sr-only">Toggle theme</span>
                        <span class="text-xs font-medium">
                            {{ isDark ? 'Dark' : 'Light' }}
                        </span>
                    </button>

                    <Link
                        v-if="$page.props.auth.user"
                        href="/admin"
                        class="glass-button"
                    >
                        Admin
                    </Link>
                    <Link v-else href="/login" class="glass-button">
                        Sign in
                    </Link>
                </div>
            </div>
        </header>

        <main class="flex-1 relative z-10 mx-auto w-full max-w-6xl px-4 py-10 sm:px-6">
            <Transition name="fade-slide" mode="out-in" appear>
                <div :key="$page.url" class="min-h-[60vh]">
                    <slot />
                </div>
            </Transition>
        </main>

        <footer
            class="relative z-10 mt-auto border-t border-black/10 bg-white backdrop-blur-xl dark:border-white/10 dark:bg-black"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-4 py-10 text-sm text-muted-foreground sm:px-6"
            >
                <p>
                    © {{ new Date().getFullYear() }} {{ brandName }}. Built with
                    Laravel + Inertia + Vue.
                </p>
                <div class="flex items-center gap-3">
                    <a
                        v-if="socials.github"
                        class="hover:text-foreground"
                        :href="socials.github"
                        target="_blank"
                        rel="noreferrer"
                    >
                        GitHub
                    </a>
                    <a
                        v-if="socials.linkedin"
                        class="hover:text-foreground"
                        :href="socials.linkedin"
                        target="_blank"
                        rel="noreferrer"
                    >
                        LinkedIn
                    </a>
                    <a
                        v-if="socials.x"
                        class="hover:text-foreground"
                        :href="socials.x"
                        target="_blank"
                        rel="noreferrer"
                    >
                        X
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>
