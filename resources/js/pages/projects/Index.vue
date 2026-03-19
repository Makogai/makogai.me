<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import * as simpleIcons from 'simple-icons';
import {
    Braces,
    Code,
    Database,
    Layers,
    Server,
    Terminal,
} from 'lucide-vue-next';

type Project = {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    tech_stack: string[] | null;
    repo_url: string | null;
    demo_url: string | null;
    is_featured: boolean;
    published_at: string;
};

const props = defineProps<{
    projects: Project[];
    filters?: {
        q?: string;
        tech?: string;
        featured?: string;
    };
}>();

const technologies = computed(() => {
    const set = new Set<string>();
    for (const project of props.projects) {
        for (const tech of project.tech_stack ?? []) {
            set.add(tech);
        }
    }
    return Array.from(set).sort((a, b) => a.localeCompare(b));
});

const activeTech = computed({
    get: () => props.filters?.tech ?? null,
    set: (value: string | null) => {
        router.get(
            '/projects',
            {
                ...props.filters,
                tech: value || undefined,
            },
            { preserveState: true, replace: true },
        );
    },
});

const search = computed({
    get: () => props.filters?.q ?? '',
    set: (value: string) => {
        router.get(
            '/projects',
            {
                ...props.filters,
                q: value || undefined,
            },
            { preserveState: true, replace: true },
        );
    },
});

const featuredFirst = computed({
    get: () => (props.filters?.featured ?? '0') === '1',
    set: (value: boolean) => {
        router.get(
            '/projects',
            {
                ...props.filters,
                featured: value ? '1' : undefined,
            },
            { preserveState: true, replace: true },
        );
    },
});

const filtered = computed(() => props.projects);

function techIcon(tech: string) {
    const t = tech.toLowerCase();

    if (t.includes('laravel')) return Server;
    if (t.includes('vue')) return Braces;
    if (t.includes('react')) return Braces;
    if (t.includes('javascript') || t === 'js') return Code;
    if (t.includes('typescript') || t === 'ts') return Code;
    if (t.includes('php')) return Code;
    if (t.includes('mysql')) return Database;
    if (t.includes('tailwind')) return Layers;

    return Terminal;
}

function techLogoSvg(tech: string): string | null {
    const t = tech.toLowerCase();

    let iconKey: string | null = null;
    if (t.includes('laravel')) iconKey = 'siLaravel';
    else if (t.includes('vue')) iconKey = 'siVuedotjs';
    else if (t.includes('react')) iconKey = 'siReact';
    else if (t.includes('javascript') || t === 'js' || t.includes(' js'))
        iconKey = 'siJavascript';
    else if (t.includes('typescript') || t === 'ts') iconKey = 'siTypescript';
    else if (t.includes('php')) iconKey = 'siPhp';
    else if (t.includes('mysql')) iconKey = 'siMysql';
    else if (t.includes('tailwind')) iconKey = 'siTailwindcss';
    else if (t.includes('inertia')) iconKey = 'siInertia';

    if (!iconKey) return null;

    const icon = (simpleIcons as any)[iconKey] as { svg?: string } | undefined;
    if (!icon?.svg) return null;

    return icon.svg.replace(
        '<svg ',
        '<svg width="14" height="14" fill="currentColor" ',
    );
}
</script>

<template>
    <SiteLayout>
        <Head title="Projects" />

        <div
            class="relative overflow-hidden rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl sm:p-8 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_20%,rgba(99,102,241,0.12),transparent_40%),radial-gradient(circle_at_90%_20%,rgba(34,211,238,0.10),transparent_35%)]"
            />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                        Projects
                    </h1>
                    <p
                        class="mt-2 max-w-2xl text-sm text-foreground/65 sm:text-base"
                    >
                        Shipped work, prototypes, and experiments—designed with
                        taste and built with engineering rigor.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Search…"
                        class="h-10 w-full min-w-[14rem] rounded-xl border border-black/10 bg-white/80 px-3 text-sm text-foreground ring-1 ring-black/10 backdrop-blur-xl placeholder:text-foreground/40 focus:ring-2 focus:ring-black/20 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:focus:ring-white/15"
                    />
                    <label class="inline-flex items-center gap-2 text-xs text-foreground/70">
                        <input
                            v-model="featuredFirst"
                            type="checkbox"
                            class="accent-black dark:accent-white"
                        />
                        Featured first
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-full px-3 py-1.5 text-xs border border-black/10 bg-white/70 ring-1 ring-black/10 transition hover:bg-white/90 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10"
                :class="
                    activeTech === null
                        ? 'bg-white/90 text-foreground dark:bg-white/10 dark:text-foreground'
                        : 'bg-white/70 text-foreground/70 dark:bg-white/5 dark:text-foreground/70'
                "
                @click="activeTech = null"
            >
                All
            </button>
            <button
                v-for="tech in technologies"
                :key="tech"
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-full px-3 py-1.5 text-xs border border-black/10 bg-white/70 ring-1 ring-black/10 transition hover:bg-white/90 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10"
                :class="
                    activeTech === tech
                        ? 'bg-white/90 text-foreground dark:bg-white/10 dark:text-foreground'
                        : 'bg-white/70 text-foreground/70 dark:bg-white/5 dark:text-foreground/70'
                "
                @click="activeTech = tech"
            >
                <span
                    v-if="techLogoSvg(tech)"
                    class="inline-flex h-4 w-4 flex-shrink-0 items-center justify-center text-foreground/70"
                    v-html="techLogoSvg(tech)!"
                />
                {{ tech }}
            </button>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="project in filtered"
                :key="project.id"
                class="group rounded-2xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl transition hover:bg-white/90 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10"
                :href="`/projects/${project.slug}`"
            >
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-lg font-medium tracking-tight">
                        {{ project.title }}
                    </h2>
                    <span class="text-xs text-foreground/55">→</span>
                </div>
                <p class="mt-2 line-clamp-3 text-sm text-foreground/65">
                    {{ project.description }}
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span
                        v-for="tech in (project.tech_stack ?? []).slice(0, 6)"
                        :key="tech"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-white/80 px-2.5 py-1 text-xs text-foreground/70 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        <span
                            v-if="techLogoSvg(tech)"
                            class="inline-flex h-4 w-4 flex-shrink-0 items-center justify-center text-foreground/70"
                            v-html="techLogoSvg(tech)!"
                        />
                        {{ tech }}
                    </span>
                </div>
                <div
                    class="mt-5 flex items-center gap-3 text-xs text-foreground/60"
                >
                    <span>{{
                        new Date(project.published_at).getFullYear()
                    }}</span>
                    <span class="opacity-60">·</span>
                    <span v-if="project.repo_url" class="opacity-90">Repo</span>
                    <span v-if="project.demo_url" class="opacity-90">Demo</span>
                </div>
            </Link>
        </div>
    </SiteLayout>
</template>
