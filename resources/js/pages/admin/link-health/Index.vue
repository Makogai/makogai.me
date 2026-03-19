<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    links: Array<{
        url: string;
        source: string;
        label: string;
        admin_url: string;
        status_code: number | null;
        ok: boolean;
        error: string | null;
    }>;
    summary: {
        total: number;
        ok: number;
        broken: number;
    };
    refreshed?: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Link Health', href: '/admin/link-health' },
];
</script>

<template>
    <Head title="Link Health" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Link Health</h1>
                    <p class="text-sm text-foreground/60">
                        External URL checks across settings, posts, and projects.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/admin/link-health?refresh=1" class="glass-button text-xs">
                        Force refresh
                    </a>
                    <Link href="/admin" class="site-nav-link">Back</Link>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <div class="text-xs text-foreground/60">Total checked</div>
                    <div class="mt-1 text-xl font-semibold">{{ summary.total }}</div>
                </div>
                <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 ring-1 ring-emerald-500/20">
                    <div class="text-xs text-emerald-700 dark:text-emerald-300">Healthy</div>
                    <div class="mt-1 text-xl font-semibold text-emerald-700 dark:text-emerald-300">
                        {{ summary.ok }}
                    </div>
                </div>
                <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 ring-1 ring-red-500/20">
                    <div class="text-xs text-red-700 dark:text-red-300">Broken</div>
                    <div class="mt-1 text-xl font-semibold text-red-700 dark:text-red-300">
                        {{ summary.broken }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <table class="min-w-full text-sm">
                    <thead class="bg-black/5 dark:bg-white/5">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium">Status</th>
                            <th class="px-3 py-2 text-left font-medium">URL</th>
                            <th class="px-3 py-2 text-left font-medium">Source</th>
                            <th class="px-3 py-2 text-left font-medium">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in links"
                            :key="`${item.url}-${index}`"
                            class="border-t border-sidebar-border/50 dark:border-sidebar-border/70"
                        >
                            <td class="px-3 py-2 align-top">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs ring-1"
                                    :class="
                                        item.ok
                                            ? 'bg-emerald-500/10 text-emerald-700 ring-emerald-500/20 dark:text-emerald-300'
                                            : 'bg-red-500/10 text-red-700 ring-red-500/20 dark:text-red-300'
                                    "
                                >
                                    {{
                                        item.ok
                                            ? `OK${item.status_code ? ` (${item.status_code})` : ''}`
                                            : `Broken${item.status_code ? ` (${item.status_code})` : ''}`
                                    }}
                                </span>
                                <div
                                    v-if="item.error && !item.ok"
                                    class="mt-1 max-w-xs text-[11px] text-red-600/80 dark:text-red-300/80"
                                >
                                    {{ item.error }}
                                </div>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <a :href="item.url" target="_blank" rel="noreferrer" class="text-xs text-foreground/80 hover:underline">
                                    {{ item.url }}
                                </a>
                            </td>
                            <td class="px-3 py-2 align-top text-xs text-foreground/70">
                                {{ item.source }}: {{ item.label }}
                            </td>
                            <td class="px-3 py-2 align-top">
                                <a :href="item.admin_url" class="site-nav-link px-2 py-1 text-xs">
                                    Open
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

