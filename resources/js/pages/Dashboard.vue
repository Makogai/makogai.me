<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div
                class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border"
            >
                <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
                <p class="mt-2 text-sm text-foreground/60">
                    Your content admin lives at
                    <span class="font-medium">/admin</span>.
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="/admin" class="glass-button"> Open Admin → </a>
                    <a href="/" class="site-nav-link"> View site </a>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <a
                    href="/admin/posts"
                    class="rounded-xl border border-sidebar-border/70 p-4 transition hover:bg-white/5 dark:border-sidebar-border"
                >
                    <div class="text-xs text-foreground/60">Posts</div>
                    <div class="mt-2 font-medium">Manage writing →</div>
                </a>
                <a
                    href="/admin/projects"
                    class="rounded-xl border border-sidebar-border/70 p-4 transition hover:bg-white/5 dark:border-sidebar-border"
                >
                    <div class="text-xs text-foreground/60">Projects</div>
                    <div class="mt-2 font-medium">Manage work →</div>
                </a>
                <a
                    href="/admin/experience"
                    class="rounded-xl border border-sidebar-border/70 p-4 transition hover:bg-white/5 dark:border-sidebar-border"
                >
                    <div class="text-xs text-foreground/60">Experience</div>
                    <div class="mt-2 font-medium">Manage timeline →</div>
                </a>
            </div>
        </div>
    </AppLayout>
</template>
