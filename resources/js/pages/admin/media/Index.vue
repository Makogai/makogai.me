<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    media: {
        data: {
            id: number;
            url: string | null;
            path: string;
            alt: string | null;
            mime_type: string | null;
            size_bytes: number;
            created_at: string | null;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Media', href: '/admin/media' },
];

const uploadForm = useForm({
    file: null as File | null,
    alt: '',
});

const deleteForm = useForm({});

const previewUrl = computed(() =>
    uploadForm.file ? URL.createObjectURL(uploadForm.file) : null,
);

const copied = ref<number | null>(null);
async function copyUrl(url: string, id: number) {
    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(url);
        } else {
            const textarea = document.createElement('textarea');
            textarea.value = url;
            textarea.setAttribute('readonly', 'true');
            textarea.style.position = 'fixed';
            textarea.style.top = '-1000px';
            textarea.style.left = '-1000px';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }
    } catch {
        // ignore; UI will not show "Copied"
        return;
    }
    copied.value = id;
    window.setTimeout(() => (copied.value = null), 900);
}
</script>

<template>
    <Head title="Media" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Media</h1>
                    <p class="text-sm text-foreground/60">
                        Upload once, reuse everywhere. Copy URLs into Markdown.
                    </p>
                </div>
            </div>

            <form
                class="grid gap-4 rounded-xl border border-sidebar-border/70 p-4 md:grid-cols-3 dark:border-sidebar-border"
                @submit.prevent="
                    uploadForm.post('/admin/media', { forceFormData: true })
                "
            >
                <div class="md:col-span-2">
                    <div class="text-xs text-foreground/60">File</div>
                    <div class="mt-2 flex items-center gap-4">
                        <div
                            class="h-16 w-16 overflow-hidden rounded-xl border border-black/10 bg-white/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                        >
                            <img
                                v-if="previewUrl"
                                :src="previewUrl"
                                alt=""
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <input
                            class="block w-full text-xs"
                            type="file"
                            accept="image/*"
                            @change="
                                (e) =>
                                    (uploadForm.file =
                                        (e.target as HTMLInputElement)
                                            .files?.[0] ?? null)
                            "
                        />
                    </div>
                    <div
                        v-if="uploadForm.errors.file"
                        class="mt-2 text-xs text-red-500"
                    >
                        {{ uploadForm.errors.file }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-foreground/60">Alt text</div>
                    <input
                        v-model="uploadForm.alt"
                        class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        placeholder="Optional"
                    />
                    <button
                        type="submit"
                        class="glass-button mt-3 w-full"
                        :disabled="uploadForm.processing"
                    >
                        {{ uploadForm.processing ? 'Uploading…' : 'Upload' }}
                    </button>
                </div>
            </form>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="m in media.data"
                    :key="m.id"
                    class="group overflow-hidden rounded-2xl border border-black/10 bg-white/70 ring-1 ring-black/10 backdrop-blur dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                >
                    <div class="aspect-[4/3] bg-black/5 dark:bg-white/5">
                        <img
                            v-if="m.url"
                            :src="m.url"
                            :alt="m.alt ?? ''"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                    </div>
                    <div class="p-4">
                        <div class="line-clamp-1 text-xs text-foreground/60">
                            {{ m.path }}
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <button
                                v-if="m.url"
                                type="button"
                                class="glass-button px-3 py-2 text-xs"
                                @click="copyUrl(m.url, m.id)"
                            >
                                {{ copied === m.id ? 'Copied' : 'Copy URL' }}
                            </button>
                            <a
                                v-if="m.url"
                                :href="m.url"
                                target="_blank"
                                rel="noreferrer"
                                class="site-nav-link px-3 py-2 text-xs"
                            >
                                Open
                            </a>
                            <button
                                type="button"
                                class="site-nav-link px-3 py-2 text-xs text-red-600 hover:text-red-700 dark:text-red-300 dark:hover:text-red-200"
                                @click="
                                    deleteForm.delete(`/admin/media/${m.id}`)
                                "
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <nav v-if="media.links?.length" class="flex flex-wrap gap-2">
                <a
                    v-for="link in media.links"
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
        </div>
    </AppLayout>
</template>
