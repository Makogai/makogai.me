<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    projects: {
        data: {
            id: number;
            title: string;
            slug: string;
            published_at: string | null;
            archived_at: string | null;
            is_featured: boolean;
            updated_at: string;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Projects', href: '/admin/projects' },
];
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Projects
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Showcase work with detail.
                    </p>
                </div>
                <Link href="/admin/projects/create" class="glass-button">
                    New project
                </Link>
            </div>

            <div
                class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full text-sm">
                    <thead class="bg-sidebar/30">
                        <tr class="text-left text-foreground/60">
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Updated</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="project in projects.data"
                            :key="project.id"
                            class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ project.title }}
                                </div>
                                <div class="text-xs text-foreground/50">
                                    /{{ project.slug }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs ring-1"
                                    :class="
                                        project.archived_at
                                            ? 'bg-amber-500/10 text-amber-200 ring-amber-500/20'
                                            : project.published_at
                                              ? 'bg-emerald-500/10 text-emerald-200 ring-emerald-500/20'
                                              : 'bg-white/5 text-foreground/70 ring-white/10'
                                    "
                                >
                                    {{
                                        project.archived_at
                                            ? 'Archived'
                                            : project.published_at
                                              ? 'Published'
                                              : 'Draft'
                                    }}
                                </span>
                                <span
                                    v-if="project.is_featured"
                                    class="ml-2 rounded-full bg-indigo-500/10 px-2.5 py-1 text-xs text-indigo-200 ring-1 ring-indigo-500/20"
                                >
                                    Featured
                                </span>
                            </td>
                            <td class="px-4 py-3 text-foreground/60">
                                {{
                                    new Date(
                                        project.updated_at,
                                    ).toLocaleDateString()
                                }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link
                                    :href="`/admin/projects/${project.id}/edit`"
                                    class="text-foreground/70 hover:text-foreground"
                                >
                                    Edit →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="projects.links?.length" class="flex flex-wrap gap-2">
                <a
                    v-for="link in projects.links"
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
