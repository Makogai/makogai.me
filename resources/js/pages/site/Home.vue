<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import * as simpleIcons from 'simple-icons';
import {
    BriefcaseBusiness,
    Braces,
    CalendarDays,
    Code,
    Check,
    Database,
    ExternalLink,
    Layers,
    MapPin,
    Server,
    Terminal,
} from 'lucide-vue-next';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { useAppearance } from '@/composables/useAppearance';

type Tag = { id: number; name: string; slug: string };
type Category = { id: number; name: string; slug: string } | null;

type Post = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    reading_time_minutes: number;
    cover_image_path: string | null;
    published_at: string;
    category: Category;
    tags: Tag[];
};

type Project = {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    tech_stack: string[] | null;
    cover_image_path: string | null;
    repo_url: string | null;
    demo_url: string | null;
    published_at: string;
};

type Activity = {
    id: number;
    type: string;
    title: string;
    description: string | null;
    happened_at: string;
    url: string | null;
};

type Experience = {
    id: number;
    company: string;
    company_logo_path: string | null;
    role: string;
    location: string | null;
    employment_type: string | null;
    summary: string | null;
    highlights: string[] | null;
    started_on: string | null;
    ended_on: string | null;
    is_current: boolean;
    company_url: string | null;
};

defineProps<{
    featuredProjects: Project[];
    latestPosts: Post[];
    recentActivity: Activity[];
    experience: Experience[];
}>();

const page = usePage();
const profile = computed(
    () => ((page.props as any).settings?.profile ?? {}) as Record<string, any>,
);
const { resolvedAppearance } = useAppearance();
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
const portraitSrc = computed(() => {
    const preferDark = resolvedAppearance.value === 'dark';

    const path = preferDark
        ? (profile.value.portrait_dark_path as string | undefined)
        : (profile.value.portrait_light_path as string | undefined);

    return path ? `/storage/${path}` : null;
});

function coverSrc(path: string | null): string | undefined {
    if (!path) return undefined;
    if (path.startsWith('http')) return path;
    if (path.startsWith('/storage/')) return path;
    return `/storage/${path}`;
}

function formatMonthYear(value: string | null): string {
    if (!value) return '—';

    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '—';

    return d.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
    });
}

function techIcon(tech: string) {
    const t = tech.toLowerCase();

    if (t.includes('laravel')) return Server;
    if (t.includes('vue')) return Braces;
    if (t.includes('react')) return Braces;
    if (t.includes('javascript') || t.includes('js')) return Code;
    if (t.includes('typescript') || t.includes('ts')) return Code;
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

    // Simple Icons SVGs don’t include width/height or currentColor fills, so we add it
    // so the icon can follow theme text color automatically.
    return icon.svg.replace(
        '<svg ',
        '<svg width="16" height="16" fill="currentColor" ',
    );
}
</script>

<template>
    <SiteLayout>
        <Head title="Home">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>

        <section class="relative flex flex-col">
            <div
                class="relative overflow-hidden rounded-3xl border border-black/10 bg-white/70 p-8 ring-1 ring-black/10 backdrop-blur-xl sm:p-12 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(99,102,241,0.22),transparent_40%),radial-gradient(circle_at_80%_20%,rgba(34,211,238,0.14),transparent_35%),radial-gradient(circle_at_40%_90%,rgba(236,72,153,0.06),transparent_42%)]"
                />
                <div
                    class="relative grid gap-10 lg:grid-cols-12 lg:items-center"
                >
                    <div class="lg:col-span-8">
                        <p
                            class="bg-[linear-gradient(135deg,#6366f1,#06b6d4)] bg-clip-text text-sm font-medium tracking-wide text-transparent"
                        >
                            {{ headline }}
                        </p>
                        <h1
                            class="mt-3 text-4xl font-semibold tracking-tight sm:text-6xl"
                        >
                            {{ fullName }} — I build products with
                            <span
                                class="bg-[linear-gradient(135deg,#a5b4fc,#67e8f9)] bg-clip-text text-transparent"
                            >
                                taste, speed, and depth
                            </span>
                            .
                        </h1>
                        <p
                            class="mt-5 max-w-2xl text-base leading-relaxed text-foreground/70 sm:text-lg"
                        >
                            {{ bio }}
                        </p>

                        <div
                            class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center"
                        >
                            <Link
                                href="/projects"
                                class="glass-button justify-center px-5"
                            >
                                View projects
                            </Link>
                            <Link
                                href="/blog"
                                class="site-nav-link justify-center px-5"
                            >
                                Read the blog
                            </Link>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="relative mx-auto max-w-[18rem]">
                            <div
                                class="absolute -inset-6 rounded-[2rem] bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.18),transparent_60%)] blur-2xl"
                            />
                            <div
                                class="relative overflow-hidden rounded-[2rem] border border-black/10 bg-white/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <div
                                    class="aspect-[4/5] bg-[linear-gradient(135deg,rgba(99,102,241,0.10),rgba(34,211,238,0.08))]"
                                >
                                    <img
                                        v-if="portraitSrc"
                                        :src="portraitSrc"
                                        alt="Portrait"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div class="p-4">
                                    <div
                                        class="text-sm font-medium tracking-tight"
                                    >
                                        {{ fullName }}
                                    </div>
                                    <div
                                        class="mt-1 text-xs text-foreground/60"
                                    >
                                        {{ headline }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="order-2 mt-8 h-px bg-[linear-gradient(to_right,rgba(99,102,241,0.32),rgba(34,211,238,0.22))] opacity-60"
            />

            <div class="order-4 mt-6 grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <div class="flex items-end justify-between">
                        <h2
                            class="bg-[linear-gradient(135deg,#6366f1,#06b6d4)] bg-clip-text text-2xl font-semibold tracking-tight text-transparent"
                        >
                            Featured projects
                        </h2>
                        <Link
                            href="/projects"
                            class="text-sm text-foreground/60 hover:text-foreground"
                        >
                            See all
                        </Link>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <Link
                            v-for="project in featuredProjects"
                            :key="project.id"
                            class="group relative overflow-hidden rounded-2xl border border-black/10 bg-white/70 p-5 ring-1 ring-black/10 backdrop-blur-xl transition hover:bg-white/80 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/8"
                            :href="`/projects/${project.slug}`"
                        >
                            <div
                                class="absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            >
                                <div
                                    class="absolute -inset-20 bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.16),transparent_60%)] blur-3xl"
                                />
                            </div>

                            <div class="relative">
                                <div
                                    class="flex items-start justify-between gap-3"
                                >
                                    <h3 class="font-medium tracking-tight">
                                        {{ project.title }}
                                    </h3>
                                    <span class="text-xs text-foreground/60">
                                        →
                                    </span>
                                </div>
                                <p
                                    class="mt-2 line-clamp-2 text-sm text-foreground/65"
                                >
                                    {{ project.description }}
                                </p>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span
                                        v-for="tech in (
                                            project.tech_stack ?? []
                                        ).slice(0, 4)"
                                        :key="tech"
                                        class="inline-flex items-center justify-center gap-2 rounded-full bg-white/80 px-2.5 py-1.5 text-xs font-semibold text-foreground/80 ring-1 ring-black/10 dark:bg-white/5 dark:text-foreground/90 dark:ring-white/10"
                                    >
                                        <span
                                            v-if="techLogoSvg(tech)"
                                            class="inline-flex h-4 w-4 flex-shrink-0 items-center justify-center text-foreground/80"
                                            v-html="techLogoSvg(tech)!"
                                        />
                                        <component
                                            v-else
                                            :is="techIcon(tech)"
                                            class="h-4 w-4 flex-shrink-0 opacity-80"
                                        />
                                        {{ tech }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="flex items-end justify-between">
                        <h2
                            class="bg-[linear-gradient(135deg,#6366f1,#06b6d4)] bg-clip-text text-2xl font-semibold tracking-tight text-transparent"
                        >
                            Latest posts
                        </h2>
                        <Link
                            href="/blog"
                            class="text-sm text-foreground/60 hover:text-foreground"
                        >
                            See all
                        </Link>
                    </div>

                    <div class="mt-4 space-y-3">
                        <Link
                            v-for="post in latestPosts"
                            :key="post.id"
                            class="group relative block rounded-2xl border border-black/10 bg-white/70 p-5 ring-1 ring-black/10 backdrop-blur-xl transition hover:bg-white/80 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/8"
                            :href="`/blog/${post.slug}`"
                        >
                            <div
                                class="absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                            >
                                <div
                                    class="absolute -inset-20 bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.20),rgba(34,211,238,0.12),transparent_62%)] blur-3xl"
                                />
                            </div>

                            <div
                                v-if="coverSrc(post.cover_image_path)"
                                class="mb-4 overflow-hidden rounded-xl border border-black/10 bg-white/60 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <img
                                    :src="coverSrc(post.cover_image_path)!"
                                    :alt="post.title"
                                    class="h-32 w-full object-cover"
                                    loading="lazy"
                                />
                            </div>

                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-medium tracking-tight">
                                    {{ post.title }}
                                </h3>
                                <span class="text-xs text-foreground/60">
                                    {{ post.reading_time_minutes }} min
                                </span>
                            </div>
                            <p
                                class="mt-2 line-clamp-2 text-sm text-foreground/65"
                            >
                                {{ post.excerpt }}
                            </p>
                            <div
                                class="mt-3 flex items-center gap-2 text-xs text-foreground/55"
                            >
                                <span
                                    v-if="post.category"
                                    class="rounded-full bg-[linear-gradient(135deg,rgba(99,102,241,0.16),rgba(34,211,238,0.12))] px-2 py-1 ring-1 ring-black/10 dark:ring-white/10"
                                >
                                    {{ post.category.name }}
                                </span>
                                <span class="opacity-70">·</span>
                                <span class="opacity-80">{{
                                    new Date(
                                        post.published_at,
                                    ).toLocaleDateString()
                                }}</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="order-3 mt-12">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2
                            class="bg-[linear-gradient(135deg,#6366f1,#06b6d4)] bg-clip-text text-2xl font-semibold tracking-tight text-transparent"
                        >
                            Experience
                        </h2>
                        <p class="mt-1 text-sm text-foreground/60">
                            Where I’ve worked
                        </p>
                    </div>
                </div>

                <div class="relative mt-4">
                    <!-- vertical timeline rail -->
                    <div
                        class="pointer-events-none absolute left-[13px] top-2 bottom-2 w-px bg-black/10 dark:bg-white/10"
                    />

                    <div class="grid gap-4">
                        <div
                            v-for="e in experience"
                            :key="e.id"
                            class="grid grid-cols-[28px_1fr] gap-4"
                        >
                            <div class="flex flex-col items-center">
                                <div
                                    class="relative mt-2 h-3 w-3 rounded-full ring-2 ring-black/10 dark:ring-white/10"
                                    :class="
                                        e.is_current
                                            ? 'bg-[linear-gradient(135deg,#6366f1,#06b6d4)]'
                                            : 'bg-black/10 dark:bg-white/10'
                                    "
                                >
                                    <div
                                        class="absolute -right-8 top-1/2 h-px w-7 -translate-y-1/2"
                                        :class="
                                            e.is_current
                                                ? 'bg-[linear-gradient(90deg,#6366f1,#06b6d4)]'
                                                : 'bg-black/10 dark:bg-white/10'
                                        "
                                    />
                                </div>

                                <div
                                    v-if="e.started_on"
                                    class="mt-3 rounded-full border border-black/10 bg-white/70 px-2 py-1 text-[0.68rem] font-mono text-foreground/55 dark:border-white/10 dark:bg-white/5 dark:text-foreground/60"
                                    :class="
                                        e.is_current
                                            ? 'text-foreground shadow-[0_0_0_1px_rgba(99,102,241,0.15)]'
                                            : ''
                                    "
                                >
                                    {{ formatMonthYear(e.started_on) }}
                                </div>
                            </div>

                            <div
                                class="relative overflow-hidden rounded-2xl border border-black/10 bg-white/85 p-6 ring-1 ring-black/10 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                                :class="
                                    e.is_current
                                        ? 'shadow-[0_0_0_1px_rgba(99,102,241,0.22),0_18px_60px_rgba(99,102,241,0.10)]'
                                        : ''
                                "
                            >
                                <div
                                    v-if="e.is_current"
                                    class="absolute left-0 top-0 h-full w-1 bg-[linear-gradient(180deg,rgba(99,102,241,0.95),rgba(6,182,212,0.75))]"
                                />

                                <div class="flex items-start gap-4">
                                    <div
                                        v-if="e.company_logo_path"
                                        class="relative z-10 h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-black/10 bg-white/60 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                                    >
                                        <img
                                            :src="coverSrc(e.company_logo_path)"
                                            :alt="e.company"
                                            class="h-full w-full object-contain p-2"
                                            loading="lazy"
                                        />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="flex flex-wrap items-center justify-between gap-3"
                                        >
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <div
                                                        class="text-sm font-medium tracking-tight"
                                                    >
                                                        <a
                                                            v-if="e.company_url"
                                                            :href="e.company_url"
                                                            target="_blank"
                                                            rel="noreferrer"
                                                            class="inline-flex items-center gap-1 hover:underline"
                                                        >
                                                            {{ e.company }}
                                                            <ExternalLink
                                                                class="h-3.5 w-3.5 opacity-70"
                                                            />
                                                        </a>
                                                        <span v-else>{{ e.company }}</span>
                                                    </div>

                                                    <span
                                                        v-if="e.is_current"
                                                        class="inline-flex items-center rounded-full bg-[linear-gradient(135deg,rgba(99,102,241,0.16),rgba(6,182,212,0.12))] px-2.5 py-1 text-xs text-foreground/90 ring-1 ring-black/10 dark:ring-white/10"
                                                    >
                                                        Current
                                                    </span>
                                                </div>

                                                <div
                                                    class="mt-1 text-base font-semibold"
                                                    :class="
                                                        e.is_current
                                                            ? 'text-foreground'
                                                            : 'text-foreground/70'
                                                    "
                                                >
                                                    {{ e.role }}
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-3 flex flex-wrap items-center gap-2"
                                        >
                                            <span
                                                v-if="e.started_on"
                                                class="inline-flex items-center gap-2 rounded-full bg-black/5 px-2.5 py-1 text-xs text-foreground/80 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                                            >
                                                <CalendarDays
                                                    class="h-3.5 w-3.5 opacity-70"
                                                />
                                                <span class="font-mono">
                                                    {{ formatMonthYear(e.started_on) }}
                                                    →
                                                    {{
                                                        e.is_current
                                                            ? 'Present'
                                                            : formatMonthYear(e.ended_on)
                                                    }}
                                                </span>
                                            </span>

                                            <span
                                                v-if="e.location"
                                                class="inline-flex items-center gap-2 rounded-full bg-black/5 px-2.5 py-1 text-xs text-foreground/80 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                                            >
                                                <MapPin
                                                    class="h-3.5 w-3.5 opacity-70"
                                                />
                                                <span class="font-mono">
                                                    {{ e.location }}
                                                </span>
                                            </span>

                                            <span
                                                v-if="e.employment_type"
                                                class="inline-flex items-center gap-2 rounded-full bg-[linear-gradient(135deg,rgba(99,102,241,0.12),rgba(34,211,238,0.10))] px-2.5 py-1 text-xs text-foreground/80 ring-1 ring-black/10 dark:ring-white/10"
                                            >
                                                <BriefcaseBusiness
                                                    class="h-3.5 w-3.5 opacity-70"
                                                />
                                                <span class="font-mono">
                                                    {{ e.employment_type }}
                                                </span>
                                            </span>
                                        </div>

                                        <p
                                            v-if="e.summary"
                                            class="mt-3 text-sm text-foreground/70"
                                        >
                                            {{ e.summary }}
                                        </p>

                                        <div
                                            v-if="e.highlights?.length"
                                            class="mt-3"
                                        >
                                            <div class="text-xs text-foreground/55">
                                                Highlights
                                            </div>
                                            <ul class="mt-2 space-y-2">
                                                <li
                                                    v-for="h in e.highlights.slice(
                                                        0,
                                                        3,
                                                    )"
                                                    :key="h"
                                                    class="flex items-start gap-2 text-sm text-foreground/70"
                                                >
                                                    <Check
                                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-[linear-gradient(135deg,#6366f1,#06b6d4)]"
                                                        :class="
                                                            e.is_current
                                                                ? 'text-[#6366f1]'
                                                                : 'text-foreground/70'
                                                        "
                                                    />
                                                    <span class="leading-relaxed">
                                                        {{ h }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-5 mt-12">
                <div class="flex items-end justify-between">
                    <h2
                        class="bg-[linear-gradient(135deg,#6366f1,#06b6d4)] bg-clip-text text-2xl font-semibold tracking-tight text-transparent"
                    >
                        Activity
                    </h2>
                    <Link
                        href="/activity"
                        class="text-sm text-foreground/60 hover:text-foreground"
                    >
                        View timeline
                    </Link>
                </div>

                <div class="mt-4 grid gap-3">
                    <div
                        v-for="item in recentActivity"
                        :key="item.id"
                        class="flex flex-col gap-2 rounded-2xl border border-black/10 bg-white/70 p-5 ring-1 ring-black/10 backdrop-blur-xl sm:flex-row sm:items-start sm:justify-between dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="rounded-full bg-[linear-gradient(135deg,rgba(99,102,241,0.14),rgba(34,211,238,0.10))] px-2.5 py-1 text-xs text-foreground/80 ring-1 ring-black/10 dark:ring-white/10"
                                >
                                    {{ item.type }}
                                </span>
                                <p class="font-medium tracking-tight">
                                    {{ item.title }}
                                </p>
                            </div>
                            <p
                                v-if="item.description"
                                class="mt-2 text-sm text-foreground/65"
                            >
                                {{ item.description }}
                            </p>
                        </div>
                        <div
                            class="flex items-center justify-between gap-3 sm:flex-col sm:items-end"
                        >
                            <span class="text-xs text-foreground/55">
                                {{
                                    new Date(
                                        item.happened_at,
                                    ).toLocaleDateString()
                                }}
                            </span>
                            <a
                                v-if="item.url"
                                :href="item.url"
                                target="_blank"
                                rel="noreferrer"
                                class="text-xs text-foreground/70 hover:text-foreground"
                            >
                                Open →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
