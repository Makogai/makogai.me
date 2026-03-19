<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { useCsrfToken } from '@/composables/useCsrfToken';
import MediaPickerDialog from '@/components/admin/MediaPickerDialog.vue';

type Category = { id: number; name: string };
type Tag = { id: number; name: string };

const props = defineProps<{
    post: null | {
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        content_markdown: string;
        category_id: number | null;
        tag_ids: number[];
        published_at: string | null;
        archived_at?: string | null;
        is_featured: boolean;
        syntax_highlighting_enabled: boolean;
        cover_image_path?: string | null;
        seo_title: string | null;
        seo_description: string | null;
        preview_url?: string | null;
    };
    categories: Category[];
    tags: Tag[];
}>();

const isEdit = computed(() => !!props.post?.id);

const form = useForm({
    title: props.post?.title ?? '',
    slug: props.post?.slug ?? '',
    excerpt: props.post?.excerpt ?? '',
    content_markdown: props.post?.content_markdown ?? '',
    category_id: props.post?.category_id ?? null,
    tag_ids: props.post?.tag_ids ?? ([] as number[]),
    cover_image: null as File | null,
    cover_image_path: props.post?.cover_image_path ?? '',
    published_at: props.post?.published_at ?? null,
    archived_at: props.post?.archived_at ?? null,
    is_featured: props.post?.is_featured ?? false,
    syntax_highlighting_enabled:
        props.post?.syntax_highlighting_enabled ?? true,
    seo_title: props.post?.seo_title ?? '',
    seo_description: props.post?.seo_description ?? '',
});

const status = computed<'draft' | 'published' | 'archived'>(() => {
    if (form.archived_at) {
        return 'archived';
    }
    if (form.published_at) {
        return 'published';
    }
    return 'draft';
});

function toDatetimeLocal(value: Date): string {
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${value.getFullYear()}-${pad(value.getMonth() + 1)}-${pad(value.getDate())}T${pad(
        value.getHours(),
    )}:${pad(value.getMinutes())}`;
}

function setDraft(): void {
    form.published_at = null;
    form.archived_at = null;
}

function setPublished(): void {
    form.archived_at = null;
    form.published_at = form.published_at ?? toDatetimeLocal(new Date());
}

function setArchived(): void {
    form.archived_at = form.archived_at ?? toDatetimeLocal(new Date());
    form.published_at = null;
}

const coverPreview = computed(() => {
    if (form.cover_image) {
        return URL.createObjectURL(form.cover_image);
    }
    return form.cover_image_path ? `/storage/${form.cover_image_path}` : null;
});

const mediaPickerOpen = ref(false);

const previewHtml = ref<string>('');
const previewLoading = ref(false);
const { token } = useCsrfToken();

const refreshPreview = useDebounceFn(async () => {
    previewLoading.value = true;
    try {
        const res = await fetch('/admin/markdown/preview', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token.value,
            },
            body: JSON.stringify({ markdown: form.content_markdown }),
        });

        if (!res.ok) {
            previewHtml.value = '';
            return;
        }
        const json = (await res.json()) as { html: string };
        previewHtml.value = json.html;
    } finally {
        previewLoading.value = false;
    }
}, 250);

watch(
    () => form.content_markdown,
    () => {
        refreshPreview();
    },
    { immediate: true },
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Posts', href: '/admin/posts' },
    { title: isEdit.value ? 'Edit' : 'New', href: '#' },
];

function submit() {
    if (isEdit.value) {
        form.put(`/admin/posts/${props.post!.id}`, { forceFormData: true });
        return;
    }
    form.post('/admin/posts', { forceFormData: true });
}
</script>

<template>
    <Head :title="isEdit ? 'Edit post' : 'New post'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ isEdit ? 'Edit post' : 'New post' }}
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Markdown-first with live preview.
                    </p>
                </div>
                <Link href="/admin/posts" class="site-nav-link"> Back </Link>
                <a
                    v-if="isEdit && props.post?.preview_url"
                    :href="props.post.preview_url"
                    target="_blank"
                    rel="noreferrer"
                    class="glass-button"
                >
                    Preview draft
                </a>
            </div>

            <form class="grid gap-4 lg:grid-cols-2" @submit.prevent="submit">
                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Title</label>
                        <input
                            v-model="form.title"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm text-foreground ring-1 ring-white/10 focus:ring-2 focus:ring-white/15 focus:outline-none"
                        />
                        <div
                            v-if="form.errors.title"
                            class="mt-2 text-xs text-red-400"
                        >
                            {{ form.errors.title }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Slug (optional)</label
                        >
                        <input
                            v-model="form.slug"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm text-foreground ring-1 ring-white/10 focus:ring-2 focus:ring-white/15 focus:outline-none"
                            placeholder="auto-generated"
                        />
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Category</label
                        >
                        <select
                            v-model="form.category_id"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm text-foreground ring-1 ring-white/10 focus:ring-2 focus:ring-white/15 focus:outline-none"
                        >
                            <option :value="null">None</option>
                            <option
                                v-for="c in categories"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Tags</label>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <label
                                v-for="t in tags"
                                :key="t.id"
                                class="inline-flex items-center gap-2 rounded-full bg-white/5 px-3 py-1.5 text-xs ring-1 ring-white/10"
                            >
                                <input
                                    type="checkbox"
                                    :value="t.id"
                                    v-model="form.tag_ids"
                                    class="accent-white"
                                />
                                {{ t.name }}
                            </label>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Featured photo</label
                        >
                        <div class="mt-3 flex items-center gap-4">
                            <div
                                class="h-16 w-24 overflow-hidden rounded-xl border border-black/10 bg-white/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <img
                                    v-if="coverPreview"
                                    :src="coverPreview"
                                    alt=""
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <input
                                type="file"
                                accept="image/*"
                                class="block w-full text-xs"
                                @change="
                                    (e) =>
                                        (form.cover_image =
                                            (e.target as HTMLInputElement)
                                                .files?.[0] ?? null)
                                "
                            />
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="glass-button px-3 py-2 text-xs"
                                @click="mediaPickerOpen = true"
                            >
                                Pick from library
                            </button>
                            <a
                                href="/admin/media"
                                class="site-nav-link px-3 py-2 text-xs"
                            >
                                Open library
                            </a>
                        </div>
                        <div class="mt-3 text-xs text-foreground/60">
                            Or paste a path from Media Library (e.g.
                            <span class="font-mono">media/...</span>)
                        </div>
                        <input
                            v-model="form.cover_image_path"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                            placeholder="media/your-image.webp"
                        />
                        <div
                            v-if="form.errors.cover_image"
                            class="mt-2 text-xs text-red-400"
                        >
                            {{ form.errors.cover_image }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Status</label>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full px-2.5 py-1 text-xs ring-1"
                                :class="[
                                    status === 'draft'
                                        ? 'bg-black/5 text-foreground/70 ring-black/10 dark:bg-white/5 dark:ring-white/10'
                                        : status === 'published'
                                          ? 'bg-emerald-500/10 text-emerald-700 ring-emerald-500/20 dark:text-emerald-300'
                                          : 'bg-amber-500/10 text-amber-700 ring-amber-500/20 dark:text-amber-300',
                                ]"
                            >
                                {{ status }}
                            </span>
                            <button
                                type="button"
                                class="site-nav-link px-3 py-2 text-xs"
                                @click="setDraft"
                            >
                                Draft
                            </button>
                            <button
                                type="button"
                                class="glass-button px-3 py-2 text-xs"
                                @click="setPublished"
                            >
                                Publish
                            </button>
                            <button
                                type="button"
                                class="site-nav-link px-3 py-2 text-xs"
                                @click="setArchived"
                            >
                                Archive
                            </button>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <label
                                class="inline-flex items-center gap-2 text-sm"
                            >
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="accent-white"
                                />
                                Featured
                            </label>
                            <label
                                class="inline-flex items-center gap-2 text-sm"
                            >
                                <input
                                    v-model="form.syntax_highlighting_enabled"
                                    type="checkbox"
                                    class="accent-white"
                                />
                                Syntax highlighting
                            </label>
                            <input
                                v-model="form.published_at"
                                type="datetime-local"
                                :disabled="status === 'archived'"
                                class="h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm text-foreground ring-1 ring-white/10 focus:ring-2 focus:ring-white/15 focus:outline-none"
                            />
                            <input v-model="form.archived_at" type="hidden" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <div class="flex items-center justify-between">
                            <label class="text-xs text-foreground/60"
                                >Markdown</label
                            >
                            <span class="text-xs text-foreground/50">
                                {{
                                    previewLoading
                                        ? 'Rendering…'
                                        : 'Preview ready'
                                }}
                            </span>
                        </div>
                        <textarea
                            v-model="form.content_markdown"
                            rows="14"
                            class="mt-2 w-full resize-y rounded-xl border border-white/10 bg-white/5 p-3 font-mono text-xs text-foreground ring-1 ring-white/10 focus:ring-2 focus:ring-white/15 focus:outline-none"
                        />
                        <div
                            v-if="form.errors.content_markdown"
                            class="mt-2 text-xs text-red-400"
                        >
                            {{ form.errors.content_markdown }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Live preview</label
                        >
                        <div class="prose prose-invert mt-3">
                            <div v-html="previewHtml" />
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">SEO</label>
                        <input
                            v-model="form.seo_title"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm text-foreground ring-1 ring-white/10 focus:ring-2 focus:ring-white/15 focus:outline-none"
                            placeholder="SEO title (optional)"
                        />
                        <input
                            v-model="form.seo_description"
                            class="mt-3 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm text-foreground ring-1 ring-white/10 focus:ring-2 focus:ring-white/15 focus:outline-none"
                            placeholder="SEO description (optional)"
                        />
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="submit"
                            class="glass-button"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Saving…' : 'Save' }}
                        </button>
                        <button
                            v-if="isEdit"
                            type="button"
                            class="site-nav-link text-red-300 hover:text-red-200"
                            @click="
                                form.delete(`/admin/posts/${props.post!.id}`)
                            "
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <MediaPickerDialog
            v-model:open="mediaPickerOpen"
            @select="
                (m) => {
                    if (m.path) {
                        form.cover_image_path = m.path;
                        form.cover_image = null;
                    }
                }
            "
        />
    </AppLayout>
</template>
