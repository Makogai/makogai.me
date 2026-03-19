<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, toRefs, watch } from 'vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

type Tag = { id: number; name: string; slug: string };
type Category = { id: number; name: string; slug: string } | null;

type RelatedPost = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    cover_image_path: string | null;
    published_at: string;
};

const props = defineProps<{
    post: {
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        reading_time_minutes: number;
        cover_image_path: string | null;
        content_html: string | null;
        published_at: string;
        seo_title: string | null;
        seo_description: string | null;
        syntax_highlighting_enabled: boolean;
        category: Category;
        tags: Tag[];
    };
    related?: RelatedPost[];
}>();

const { post } = toRefs(props);
const page = usePage();

function coverSrc(path: string | null): string | null {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    if (path.startsWith('/storage/')) return path;
    return `/storage/${path}`;
}

const contentRef = ref<HTMLElement | null>(null);
let prismInitPromise: Promise<void> | null = null;

const shareCopied = ref(false);
const shareUrl = computed(() => {
    if (typeof window !== 'undefined' && window.location?.href) {
        return window.location.href;
    }
    return page.url ? `${page.url}` : '';
});
const twitterShareUrl = computed(
    () =>
        `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl.value)}&text=${encodeURIComponent(post.value.title)}`,
);
const linkedinShareUrl = computed(
    () =>
        `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl.value)}`,
);

async function copyShareUrl(): Promise<void> {
    try {
        await copyTextToClipboard(shareUrl.value);
        shareCopied.value = true;
        window.setTimeout(() => {
            shareCopied.value = false;
        }, 1200);
    } catch {
        // Ignore
    }
}

async function copyTextToClipboard(text: string): Promise<void> {
    // Best-effort: try Clipboard API first, fallback to a textarea copy.
    if (typeof navigator !== 'undefined' && navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(text);
        return;
    }

    const textarea = document.createElement('textarea');
    textarea.value = text;
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

function loadScript(src: string): Promise<void> {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${src}"]`)) {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () =>
            reject(new Error(`Failed to load script: ${src}`));
        document.head.appendChild(script);
    });
}

async function loadOptionalScript(src: string): Promise<void> {
    try {
        await loadScript(src);
    } catch {
        // Ignore optional languages.
    }
}

async function ensurePrism(): Promise<void> {
    // Load Prism core (if needed) and the language components.
    // Important: we should NOT early-return just because `window.Prism` exists,
    // otherwise language components might never load after a previous page visit.
    const loadedKey = '__prismComponentsLoaded_v2';
    if ((window as any)[loadedKey]) return;

    if (!(window as any).Prism) {
        await loadScript(
            'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js',
        );
    }

    const components = [
        // base grammars
        'prism-clike.min.js',
        'prism-markup.min.js',
        'prism-css.min.js',
        // web / scripts
        'prism-javascript.min.js',
        'prism-typescript.min.js',
        'prism-jsx.min.js',
        'prism-tsx.min.js',
        'prism-php.min.js',
        'prism-python.min.js',
        'prism-bash.min.js',
        'prism-powershell.min.js',
        'prism-docker.min.js',
        // data
        'prism-json.min.js',
        'prism-yaml.min.js',
        'prism-toml.min.js',
        'prism-ini.min.js',
        // misc
        'prism-diff.min.js',
        'prism-sql.min.js',
        // “batteries included”
        'prism-go.min.js',
        'prism-rust.min.js',
        'prism-java.min.js',
        'prism-ruby.min.js',
        'prism-c.min.js',
        'prism-cpp.min.js',
        'prism-csharp.min.js',
        'prism-kotlin.min.js',
        'prism-swift.min.js',
    ];

    const base = 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/';
    const loaded: string[] = [];
    const failed: string[] = [];

    for (const f of components) {
        try {
            await loadOptionalScript(`${base}${f}`);
            loaded.push(f);
        } catch {
            failed.push(f);
        }
    }

    // Mark as loaded even if some components failed; we don't want infinite retries.
    (window as any)[loadedKey] = true;

    // Helpful for debugging (only visible in console).
    if (failed.length > 0) {
        // eslint-disable-next-line no-console
        console.warn(
            '[Prism] Failed to load some language components:',
            failed,
        );
    }
}

function addCopyButtons(): void {
    if (!contentRef.value) return;

    const pres = contentRef.value.querySelectorAll('pre');
    pres.forEach((pre) => {
        if (!(pre instanceof HTMLElement)) return;
        if (pre.querySelector('button[data-copy-code]')) return;

        const code = pre.querySelector('code');
        if (!code) return;

        const codeText = code.textContent ?? '';

        pre.style.position = 'relative';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.dataset.copyCode = 'true';
        btn.textContent = 'Copy';
        btn.className =
            'absolute right-3 top-3 z-10 rounded-xl border border-black/10 bg-white/80 px-3 py-1 text-xs text-foreground/70 backdrop-blur-xl transition hover:bg-white/95 dark:border-white/10 dark:bg-black/40 dark:text-foreground/80';

        let timeout: number | undefined;
        btn.addEventListener('click', async () => {
            try {
                await copyTextToClipboard(codeText);
                btn.textContent = 'Copied';
                if (timeout) window.clearTimeout(timeout);
                timeout = window.setTimeout(() => {
                    btn.textContent = 'Copy';
                }, 1200);
            } catch {
                btn.textContent = 'Copy failed';
                if (timeout) window.clearTimeout(timeout);
                timeout = window.setTimeout(() => {
                    btn.textContent = 'Copy';
                }, 1500);
            }
        });

        pre.appendChild(btn);
    });
}

async function highlightPost(): Promise<void> {
    if (!contentRef.value) return;
    addCopyButtons();

    if (!post.value.syntax_highlighting_enabled) return;
    if (contentRef.value.dataset.prismHighlighted === 'true') return;

    if (!prismInitPromise) {
        prismInitPromise = ensurePrism();
    }

    await prismInitPromise;
    await nextTick();

    // Prism will annotate code blocks under this container.
    (window as any).Prism?.highlightAllUnder(contentRef.value);
    contentRef.value.dataset.prismHighlighted = 'true';
}

onMounted(() => {
    highlightPost().catch(() => {
        // Syntax highlighting is best-effort.
    });
});

watch(
    () => post.value.content_html,
    () => {
        if (contentRef.value) {
            contentRef.value.dataset.prismHighlighted = 'false';
        }
        highlightPost().catch(() => {
            // Syntax highlighting is best-effort.
        });
    },
);
</script>

<template>
    <SiteLayout>
        <Head :title="post.seo_title || post.title">
            <meta
                v-if="post.seo_description || post.excerpt"
                name="description"
                :content="post.seo_description || post.excerpt || ''"
            />
        </Head>

        <div class="mx-auto max-w-3xl">
            <div class="mb-6">
                <Link
                    href="/blog"
                    class="text-sm text-foreground/60 hover:text-foreground"
                >
                    ← Back to blog
                </Link>
            </div>

            <header
                class="rounded-3xl border border-black/10 bg-white/70 p-8 ring-1 ring-black/10 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <div
                    class="flex flex-wrap items-center gap-2 text-xs text-foreground/55"
                >
                    <span
                        v-if="post.category"
                        class="rounded-full bg-white/80 px-2.5 py-1 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        {{ post.category.name }}
                    </span>
                    <span class="opacity-70">·</span>
                    <span>{{
                        new Date(post.published_at).toLocaleDateString()
                    }}</span>
                    <span class="opacity-70">·</span>
                    <span>{{ post.reading_time_minutes }} min read</span>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <button
                        type="button"
                        class="site-nav-link px-3 py-1.5 text-xs"
                        @click="copyShareUrl"
                    >
                        {{ shareCopied ? 'Link copied' : 'Copy link' }}
                    </button>
                    <a
                        :href="twitterShareUrl"
                        target="_blank"
                        rel="noreferrer"
                        class="site-nav-link px-3 py-1.5 text-xs"
                    >
                        Share on X
                    </a>
                    <a
                        :href="linkedinShareUrl"
                        target="_blank"
                        rel="noreferrer"
                        class="site-nav-link px-3 py-1.5 text-xs"
                    >
                        Share on LinkedIn
                    </a>
                </div>

                <h1
                    class="mt-4 text-3xl font-semibold tracking-tight sm:text-5xl"
                >
                    {{ post.title }}
                </h1>

                <div
                    v-if="coverSrc(post.cover_image_path)"
                    class="mt-6 overflow-hidden rounded-2xl border border-black/10 bg-white/60 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                >
                    <img
                        :src="coverSrc(post.cover_image_path)!"
                        :alt="post.title"
                        class="h-56 w-full object-cover"
                        loading="lazy"
                    />
                </div>

                <p
                    v-if="post.excerpt"
                    class="mt-4 text-base text-foreground/65 sm:text-lg"
                >
                    {{ post.excerpt }}
                </p>

                <div v-if="post.tags?.length" class="mt-5 flex flex-wrap gap-2">
                    <span
                        v-for="tag in post.tags"
                        :key="tag.id"
                        class="rounded-full bg-white/80 px-2.5 py-1 text-xs text-foreground/70 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        #{{ tag.name }}
                    </span>
                </div>
            </header>

            <div
                class="mt-10 rounded-3xl border border-black/10 bg-white/75 p-8 ring-1 ring-black/10 dark:border-white/10 dark:bg-black/40 dark:ring-white/10"
            >
                <article ref="contentRef" class="prose mt-0">
                    <div v-if="post.content_html" v-html="post.content_html" />
                    <div v-else class="text-sm text-foreground/60">
                        This post is still being compiled.
                    </div>
                </article>
            </div>

            <section
                v-if="related && related.length"
                class="mt-10 rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <h2 class="text-sm font-medium tracking-tight">Related posts</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <Link
                        v-for="r in related"
                        :key="r.id"
                        :href="`/blog/${r.slug}`"
                        class="group rounded-2xl border border-black/10 bg-white/80 p-4 ring-1 ring-black/10 backdrop-blur transition hover:bg-white/95 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/8"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-sm font-medium tracking-tight">
                                {{ r.title }}
                            </h3>
                            <span class="text-xs text-foreground/55">→</span>
                        </div>
                        <p
                            v-if="r.excerpt"
                            class="mt-2 line-clamp-2 text-xs text-foreground/65"
                        >
                            {{ r.excerpt }}
                        </p>
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
