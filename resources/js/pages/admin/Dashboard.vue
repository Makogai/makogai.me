<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    stats: {
        posts: number;
        projects: number;
        activities: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Admin', href: '/admin' }];
</script>

<template>
    <Head title="Admin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold tracking-tight">Admin</h1>
                <p class="text-sm text-foreground/60">
                    Content control center.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <p class="text-xs text-foreground/60">Posts</p>
                    <p class="mt-2 text-2xl font-semibold">{{ stats.posts }}</p>
                    <Link
                        href="/admin/posts"
                        class="mt-3 inline-flex text-sm text-foreground/70 hover:text-foreground"
                    >
                        Manage →
                    </Link>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <p class="text-xs text-foreground/60">Projects</p>
                    <p class="mt-2 text-2xl font-semibold">
                        {{ stats.projects }}
                    </p>
                    <Link
                        href="/admin/projects"
                        class="mt-3 inline-flex text-sm text-foreground/70 hover:text-foreground"
                    >
                        Manage →
                    </Link>
                </div>
                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <p class="text-xs text-foreground/60">Activities</p>
                    <p class="mt-2 text-2xl font-semibold">
                        {{ stats.activities }}
                    </p>
                    <Link
                        href="/admin/activities"
                        class="mt-3 inline-flex text-sm text-foreground/70 hover:text-foreground"
                    >
                        Manage →
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
