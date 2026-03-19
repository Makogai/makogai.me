<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import * as simpleIcons from 'simple-icons';
import { Copy, Linkedin, Share2 } from 'lucide-vue-next';
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
    content_html: string | null;
    cover_image_path: string | null;
    gallery: string[] | null;
    tech_stack: string[] | null;
    repo_url: string | null;
    demo_url: string | null;
    published_at: string;
    seo_title: string | null;
    seo_description: string | null;
};

type RelatedProject = {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    tech_stack: string[] | null;
    cover_image_path: string | null;
    published_at: string;
};

const props = defineProps<{
    project: Project;
    related?: RelatedProject[];
}>();

const page = usePage();
const shareCopied = ref(false);
const shareUrl = computed(() => {
    if (typeof window !== 'undefined' && window.location?.href) {
        return window.location.href;
    }
    return page.url ? `${page.url}` : '';
});
const twitterShareUrl = computed(
    () =>
        `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl.value)}&text=${encodeURIComponent(props.project.title)}`,
);
const linkedinShareUrl = computed(
    () =>
        `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl.value)}`,
);

async function copyShareUrl(): Promise<void> {
    try {
        if (typeof navigator !== 'undefined' && navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(shareUrl.value);
        } else if (typeof document !== 'undefined') {
            const textarea = document.createElement('textarea');
            textarea.value = shareUrl.value;
            textarea.setAttribute('readonly', 'true');
            textarea.style.position = 'fixed';
            textarea.style.top = '0';
            textarea.style.left = '0';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }
        shareCopied.value = true;
        window.setTimeout(() => {
            shareCopied.value = false;
        }, 1200);
    } catch {
        // ignore
    }
}

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
        '<svg width="16" height="16" fill="currentColor" ',
    );
}
</script>

<template>
    <SiteLayout>
        <Head :title="project.seo_title || project.title">
            <meta
                v-if="project.seo_description || project.description"
                name="description"
                :content="project.seo_description || project.description || ''"
            />
        </Head>

        <div class="mx-auto max-w-3xl">
            <div class="mb-6">
                <Link
                    href="/projects"
                    class="text-sm text-foreground/60 hover:text-foreground"
                >
                    ← Back to projects
                </Link>
            </div>

            <header
                class="rounded-3xl border border-black/10 bg-white/70 p-8 ring-1 ring-black/10 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-xs text-foreground/55">
                        {{
                            new Date(project.published_at).toLocaleDateString()
                        }}
                    </div>
                    <div class="flex items-center gap-2">
                        <a
                            v-if="project.repo_url"
                            :href="project.repo_url"
                            target="_blank"
                            rel="noreferrer"
                            class="glass-button"
                        >
                            Repo
                        </a>
                        <a
                            v-if="project.demo_url"
                            :href="project.demo_url"
                            target="_blank"
                            rel="noreferrer"
                            class="glass-button"
                        >
                            Demo
                        </a>
                    </div>
                </div>

                <h1
                    class="mt-4 text-3xl font-semibold tracking-tight sm:text-5xl"
                >
                    {{ project.title }}
                </h1>
                <p
                    v-if="project.description"
                    class="mt-4 text-base text-foreground/65 sm:text-lg"
                >
                    {{ project.description }}
                </p>

                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-full border border-black/12 bg-white/85 px-3 py-1.5 text-xs font-medium text-foreground/80 ring-1 ring-black/10 transition hover:bg-white dark:border-white/12 dark:bg-white/8 dark:text-foreground/85 dark:ring-white/10 dark:hover:bg-white/12"
                        @click="copyShareUrl"
                    >
                        <Copy class="h-3.5 w-3.5" />
                        {{ shareCopied ? 'Link copied' : 'Copy link' }}
                    </button>
                    <a
                        :href="twitterShareUrl"
                        target="_blank"
                        rel="noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-full border border-black/12 bg-white/85 px-3 py-1.5 text-xs font-medium text-foreground/80 ring-1 ring-black/10 transition hover:bg-white dark:border-white/12 dark:bg-white/8 dark:text-foreground/85 dark:ring-white/10 dark:hover:bg-white/12"
                    >
                        <Share2 class="h-3.5 w-3.5" />
                        Share on X
                    </a>
                    <a
                        :href="linkedinShareUrl"
                        target="_blank"
                        rel="noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-full border border-black/12 bg-white/85 px-3 py-1.5 text-xs font-medium text-foreground/80 ring-1 ring-black/10 transition hover:bg-white dark:border-white/12 dark:bg-white/8 dark:text-foreground/85 dark:ring-white/10 dark:hover:bg-white/12"
                    >
                        <Linkedin class="h-3.5 w-3.5" />
                        Share on LinkedIn
                    </a>
                </div>

                <div
                    v-if="project.tech_stack?.length"
                    class="mt-5 flex flex-wrap gap-2"
                >
                    <span
                        v-for="tech in project.tech_stack"
                        :key="tech"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-white/80 px-2.5 py-1 text-xs text-foreground/70 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        <span
                            v-if="techLogoSvg(tech)"
                            class="inline-flex h-4 w-4 flex-shrink-0 items-center justify-center text-foreground/80"
                            v-html="techLogoSvg(tech)!"
                        />
                        <component
                            v-else
                            :is="techIcon(tech)"
                            class="inline-block h-4 w-4 flex-shrink-0 opacity-80"
                        />
                        {{ tech }}
                    </span>
                </div>
            </header>

            <div
                class="mt-10 rounded-3xl border border-black/10 bg-white/75 p-8 ring-1 ring-black/10 dark:border-white/10 dark:bg-black/40 dark:ring-white/10"
            >
                <article class="prose mt-0">
                    <div
                        v-if="project.content_html"
                        v-html="project.content_html"
                    />
                    <div v-else class="text-sm text-foreground/60">
                        This project write-up is still being compiled.
                    </div>
                </article>
            </div>

            <section
                v-if="related && related.length"
                class="mt-10 rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <h2 class="text-sm font-medium tracking-tight">
                    Related projects
                </h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <Link
                        v-for="r in related"
                        :key="r.id"
                        :href="`/projects/${r.slug}`"
                        class="group rounded-2xl border border-black/10 bg-white/80 p-4 ring-1 ring-black/10 backdrop-blur transition hover:bg-white/95 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/8"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-sm font-medium tracking-tight">
                                {{ r.title }}
                            </h3>
                            <span class="text-xs text-foreground/55">→</span>
                        </div>
                        <p
                            v-if="r.description"
                            class="mt-2 line-clamp-2 text-xs text-foreground/65"
                        >
                            {{ r.description }}
                        </p>
                        <div
                            v-if="r.tech_stack?.length"
                            class="mt-3 flex flex-wrap gap-1 text-[0.7rem] text-foreground/60"
                        >
                            <span
                                v-for="tech in r.tech_stack.slice(0, 3)"
                                :key="tech"
                                class="rounded-full bg-white/80 px-2 py-0.5 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                {{ tech }}
                            </span>
                        </div>
                        <div class="mt-3 text-[0.7rem] text-foreground/55">
                            {{
                                new Date(r.published_at).toLocaleDateString()
                            }}
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </SiteLayout>
</template>
