<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/SiteLayout.vue';

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

function coverSrc(path: string | null): string | null {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    if (path.startsWith('/storage/')) return path;
    return `/storage/${path}`;
}

const props = defineProps<{
    posts: {
        data: Post[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters?: {
        q?: string;
        category?: string;
        tag?: string;
    };
}>();

const search = computed({
    get: () => props.filters?.q ?? '',
    set: (value: string) => {
        router.get(
            '/blog',
            { ...props.filters, q: value || undefined },
            { preserveState: true, replace: true },
        );
    },
});
</script>

<template>
    <SiteLayout>
        <Head title="Blog" />

        <div
            class="relative overflow-hidden rounded-3xl border border-black/10 bg-white/70 p-8 ring-1 ring-black/10 backdrop-blur-xl sm:p-10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
        >
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_20%,rgba(99,102,241,0.16),transparent_40%),radial-gradient(circle_at_90%_20%,rgba(34,211,238,0.10),transparent_35%)]"
            />
            <div class="relative">
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                    Blog
                </h1>
                <p
                    class="mt-2 max-w-2xl text-sm text-foreground/65 sm:text-base"
                >
                    Deep dives, notes, and experiments—written like shipping
                    logs.
                </p>
                <div class="mt-4 max-w-md">
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Search posts…"
                        class="h-10 w-full rounded-xl border border-black/10 bg-white/80 px-3 text-sm text-foreground ring-1 ring-black/10 backdrop-blur-xl placeholder:text-foreground/40 focus:ring-2 focus:ring-black/20 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:focus:ring-white/15"
                    />
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <Link
                v-for="post in posts.data"
                :key="post.id"
                class="group relative overflow-hidden rounded-2xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl transition hover:bg-white/80 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/8"
                :href="`/blog/${post.slug}`"
            >
                <div
                    class="absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                >
                    <div
                        class="absolute -inset-24 bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.12),transparent_62%)] blur-3xl"
                    />
                </div>

                <div
                    v-if="coverSrc(post.cover_image_path)"
                    class="relative mb-5 overflow-hidden rounded-xl border border-black/10 bg-white/60 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                >
                    <img
                        :src="coverSrc(post.cover_image_path)!"
                        :alt="post.title"
                        class="h-40 w-full object-cover"
                        loading="lazy"
                    />
                </div>

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="relative text-lg font-medium tracking-tight">
                            {{ post.title }}
                        </h2>
                        <p
                            class="relative mt-2 line-clamp-3 text-sm text-foreground/65"
                        >
                            {{ post.excerpt }}
                        </p>
                    </div>
                    <span class="relative text-xs text-foreground/55">
                        {{ post.reading_time_minutes }} min
                    </span>
                </div>

                <div
                    class="relative mt-4 flex flex-wrap items-center gap-2 text-xs text-foreground/55"
                >
                    <span
                        v-if="post.category"
                        class="rounded-full bg-white/80 px-2.5 py-1 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        {{ post.category.name }}
                    </span>
                    <span class="opacity-70">·</span>
                    <span class="opacity-80">{{
                        new Date(post.published_at).toLocaleDateString()
                    }}</span>
                    <span v-if="post.tags?.length" class="opacity-70">·</span>
                    <span
                        v-for="tag in (post.tags ?? []).slice(0, 4)"
                        :key="tag.id"
                        class="rounded-full bg-white/80 px-2.5 py-1 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        #{{ tag.name }}
                    </span>
                </div>
            </Link>
        </div>

        <nav
            v-if="posts.links?.length"
            class="mt-10 flex flex-wrap gap-2"
            aria-label="Pagination"
        >
            <a
                v-for="link in posts.links"
                :key="link.label"
                class="rounded-xl px-3 py-2 text-sm ring-1 ring-white/10 transition"
                :class="
                    link.active
                        ? 'bg-white/10 text-foreground'
                        : 'bg-white/5 text-foreground/70 hover:bg-white/10'
                "
                :href="link.url ?? undefined"
                v-html="link.label"
            />
        </nav>
    </SiteLayout>
</template>
