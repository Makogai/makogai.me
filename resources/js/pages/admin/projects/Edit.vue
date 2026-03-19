<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { useCsrfToken } from '@/composables/useCsrfToken';

const props = defineProps<{
    project: null | {
        id: number;
        title: string;
        slug: string;
        description: string | null;
        content_markdown: string | null;
        tech_stack: string[] | null;
        repo_url: string | null;
        demo_url: string | null;
        published_at: string | null;
        archived_at: string | null;
        is_featured: boolean;
        seo_title: string | null;
        seo_description: string | null;
    };
}>();

const isEdit = computed(() => !!props.project?.id);

const form = useForm({
    title: props.project?.title ?? '',
    slug: props.project?.slug ?? '',
    description: props.project?.description ?? '',
    content_markdown: props.project?.content_markdown ?? '',
    tech_stack: props.project?.tech_stack ?? ([] as string[]),
    repo_url: props.project?.repo_url ?? '',
    demo_url: props.project?.demo_url ?? '',
    published_at: props.project?.published_at ?? null,
    archived_at: props.project?.archived_at ?? null,
    is_featured: props.project?.is_featured ?? false,
    seo_title: props.project?.seo_title ?? '',
    seo_description: props.project?.seo_description ?? '',
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

const techCsv = ref((form.tech_stack ?? []).join(', '));
watch(
    techCsv,
    () => {
        form.tech_stack = techCsv.value
            .split(',')
            .map((s) => s.trim())
            .filter(Boolean);
    },
    { immediate: true },
);

const previewHtml = ref<string>('');
const { token } = useCsrfToken();
const refreshPreview = useDebounceFn(async () => {
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
}, 250);

watch(
    () => form.content_markdown,
    () => refreshPreview(),
    { immediate: true },
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Projects', href: '/admin/projects' },
    { title: isEdit.value ? 'Edit' : 'New', href: '#' },
];

function submit() {
    if (isEdit.value) {
        form.put(`/admin/projects/${props.project!.id}`);
        return;
    }
    form.post('/admin/projects');
}
</script>

<template>
    <Head :title="isEdit ? 'Edit project' : 'New project'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ isEdit ? 'Edit project' : 'New project' }}
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Markdown write-up + structured metadata.
                    </p>
                </div>
                <Link href="/admin/projects" class="site-nav-link">Back</Link>
            </div>

            <form class="grid gap-4 lg:grid-cols-2" @submit.prevent="submit">
                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Title</label>
                        <input
                            v-model="form.title"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Slug (optional)</label
                        >
                        <input
                            v-model="form.slug"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Description</label
                        >
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="mt-2 w-full resize-y rounded-xl border border-white/10 bg-white/5 p-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Tech stack (comma separated)</label
                        >
                        <input
                            v-model="techCsv"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Links</label>
                        <div class="mt-2 grid gap-2">
                            <input
                                v-model="form.repo_url"
                                placeholder="Repo URL"
                                class="h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                            />
                            <input
                                v-model="form.demo_url"
                                placeholder="Demo URL"
                                class="h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                            />
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
                            <input
                                v-model="form.published_at"
                                type="datetime-local"
                                :disabled="status === 'archived'"
                                class="h-10 rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                            />
                            <input v-model="form.archived_at" type="hidden" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Markdown</label
                        >
                        <textarea
                            v-model="form.content_markdown"
                            rows="14"
                            class="mt-2 w-full resize-y rounded-xl border border-white/10 bg-white/5 p-3 font-mono text-xs ring-1 ring-white/10"
                        />
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
                                form.delete(
                                    `/admin/projects/${props.project!.id}`,
                                )
                            "
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
