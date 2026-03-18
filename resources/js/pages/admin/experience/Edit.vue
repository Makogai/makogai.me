<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    experience: null | {
        id: number;
        company: string;
        role: string;
        location: string | null;
        employment_type: string | null;
        summary: string | null;
        highlights: string[] | null;
        started_on: string | null;
        ended_on: string | null;
        is_current: boolean;
        company_url: string | null;
        company_logo_path?: string | null;
        sort_order: number;
        is_featured: boolean;
    };
}>();

const isEdit = computed(() => !!props.experience?.id);

const form = useForm({
    company: props.experience?.company ?? '',
    role: props.experience?.role ?? '',
    location: props.experience?.location ?? '',
    employment_type: props.experience?.employment_type ?? '',
    summary: props.experience?.summary ?? '',
    highlights: props.experience?.highlights ?? ([] as string[]),
    started_on: props.experience?.started_on ?? '',
    ended_on: props.experience?.ended_on ?? '',
    is_current: props.experience?.is_current ?? false,
    company_url: props.experience?.company_url ?? '',
    company_logo: null as File | null,
    sort_order: props.experience?.sort_order ?? 0,
    is_featured: props.experience?.is_featured ?? true,
});

const logoPreview = computed(() => {
    if (form.company_logo) {
        return URL.createObjectURL(form.company_logo);
    }

    const path = props.experience?.company_logo_path ?? null;

    return path ? `/storage/${path}` : null;
});

const highlightsText = ref((form.highlights ?? []).join('\n'));
watch(
    highlightsText,
    () => {
        form.highlights = highlightsText.value
            .split('\n')
            .map((s) => s.trim())
            .filter(Boolean);
    },
    { immediate: true },
);

watch(
    () => form.is_current,
    () => {
        if (form.is_current) {
            form.ended_on = '';
        }
    },
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Experience', href: '/admin/experience' },
    { title: isEdit.value ? 'Edit' : 'New', href: '#' },
];

function submit() {
    if (isEdit.value) {
        form.put(`/admin/experience/${props.experience!.id}`, {
            forceFormData: true,
        });
        return;
    }
    form.post('/admin/experience', { forceFormData: true });
}
</script>

<template>
    <Head :title="isEdit ? 'Edit experience' : 'New experience'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ isEdit ? 'Edit experience' : 'New experience' }}
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Crisp, recruiter-friendly summaries.
                    </p>
                </div>
                <Link href="/admin/experience" class="site-nav-link">Back</Link>
            </div>

            <form class="grid gap-4 lg:grid-cols-2" @submit.prevent="submit">
                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Company</label
                        >
                        <input
                            v-model="form.company"
                            class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                        />
                        <div
                            v-if="form.errors.company"
                            class="mt-2 text-xs text-red-500"
                        >
                            {{ form.errors.company }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Role</label>
                        <input
                            v-model="form.role"
                            class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                        />
                        <div
                            v-if="form.errors.role"
                            class="mt-2 text-xs text-red-500"
                        >
                            {{ form.errors.role }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Company logo</label
                        >
                        <div class="mt-3 flex items-center gap-4">
                            <div
                                class="h-14 w-14 overflow-hidden rounded-xl border border-black/10 bg-white/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <img
                                    v-if="logoPreview"
                                    :src="logoPreview"
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
                                        (form.company_logo =
                                            (e.target as HTMLInputElement)
                                                .files?.[0] ?? null)
                                "
                            />
                        </div>
                        <div
                            v-if="form.errors.company_logo"
                            class="mt-2 text-xs text-red-500"
                        >
                            {{ form.errors.company_logo }}
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                        >
                            <label class="text-xs text-foreground/60"
                                >Location</label
                            >
                            <input
                                v-model="form.location"
                                class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            />
                        </div>
                        <div
                            class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                        >
                            <label class="text-xs text-foreground/60"
                                >Employment type</label
                            >
                            <input
                                v-model="form.employment_type"
                                class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            />
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Summary</label
                        >
                        <textarea
                            v-model="form.summary"
                            rows="4"
                            class="mt-2 w-full resize-y rounded-xl border border-black/10 bg-white/70 p-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                        />
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Highlights (one per line)</label
                        >
                        <textarea
                            v-model="highlightsText"
                            rows="6"
                            class="mt-2 w-full resize-y rounded-xl border border-black/10 bg-white/70 p-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                        />
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Dates</label>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <div class="text-xs text-foreground/50">
                                    Start
                                </div>
                                <input
                                    v-model="form.started_on"
                                    type="date"
                                    class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                                />
                            </div>
                            <div>
                                <div class="text-xs text-foreground/50">
                                    End
                                </div>
                                <input
                                    :disabled="form.is_current"
                                    v-model="form.ended_on"
                                    type="date"
                                    class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 disabled:opacity-50 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                                />
                            </div>
                        </div>
                        <label
                            class="mt-3 inline-flex items-center gap-2 text-sm"
                        >
                            <input
                                v-model="form.is_current"
                                type="checkbox"
                                class="accent-black dark:accent-white"
                            />
                            Current role
                        </label>
                    </div>

                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Company URL</label
                        >
                        <input
                            v-model="form.company_url"
                            class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                        />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                        >
                            <label class="text-xs text-foreground/60"
                                >Sort order</label
                            >
                            <input
                                v-model="form.sort_order"
                                type="number"
                                class="mt-2 h-10 w-full rounded-xl border border-black/10 bg-white/70 px-3 text-sm ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            />
                        </div>
                        <div
                            class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                        >
                            <label class="text-xs text-foreground/60"
                                >Visibility</label
                            >
                            <label
                                class="mt-3 inline-flex items-center gap-2 text-sm"
                            >
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="accent-black dark:accent-white"
                                />
                                Featured
                            </label>
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
                            class="site-nav-link text-red-600 hover:text-red-700 dark:text-red-300 dark:hover:text-red-200"
                            @click="
                                form.delete(
                                    `/admin/experience/${props.experience!.id}`,
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
