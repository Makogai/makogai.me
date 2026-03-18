<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    activity: null | {
        id: number;
        type: string;
        title: string;
        description: string | null;
        happened_at: string;
        url: string | null;
        meta: Record<string, unknown> | null;
    };
}>();

const isEdit = computed(() => !!props.activity?.id);

const form = useForm({
    type: props.activity?.type ?? 'feature',
    title: props.activity?.title ?? '',
    description: props.activity?.description ?? '',
    happened_at:
        props.activity?.happened_at ?? new Date().toISOString().slice(0, 10),
    url: props.activity?.url ?? '',
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Activities', href: '/admin/activities' },
    { title: isEdit.value ? 'Edit' : 'New', href: '#' },
];

function submit() {
    if (isEdit.value) {
        form.put(`/admin/activities/${props.activity!.id}`);
        return;
    }
    form.post('/admin/activities');
}
</script>

<template>
    <Head :title="isEdit ? 'Edit activity' : 'New activity'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ isEdit ? 'Edit activity' : 'New activity' }}
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Keep it concise and shippable.
                    </p>
                </div>
                <Link href="/admin/activities" class="site-nav-link">Back</Link>
            </div>

            <form class="grid gap-4 lg:grid-cols-2" @submit.prevent="submit">
                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Type</label>
                        <select
                            v-model="form.type"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        >
                            <option value="feature">feature</option>
                            <option value="fix">fix</option>
                            <option value="idea">idea</option>
                        </select>
                    </div>
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
                            >Description</label
                        >
                        <textarea
                            v-model="form.description"
                            rows="5"
                            class="mt-2 w-full resize-y rounded-xl border border-white/10 bg-white/5 p-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Date</label>
                        <input
                            v-model="form.happened_at"
                            type="date"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >URL (optional)</label
                        >
                        <input
                            v-model="form.url"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
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
                                form.delete(
                                    `/admin/activities/${props.activity!.id}`,
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
