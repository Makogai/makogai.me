<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    experiences: {
        data: {
            id: number;
            company: string;
            role: string;
            started_on: string | null;
            ended_on: string | null;
            is_current: boolean;
            is_featured: boolean;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Experience', href: '/admin/experience' },
];
</script>

<template>
    <Head title="Experience" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Experience
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Work history and highlights.
                    </p>
                </div>
                <Link href="/admin/experience/create" class="glass-button">
                    New experience
                </Link>
            </div>

            <div
                class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full text-sm">
                    <thead class="bg-sidebar/30">
                        <tr class="text-left text-foreground/60">
                            <th class="px-4 py-3">Company</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Dates</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="e in experiences.data"
                            :key="e.id"
                            class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ e.company }}</div>
                                <div class="mt-1 flex gap-2">
                                    <span
                                        v-if="e.is_current"
                                        class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs text-emerald-200 ring-1 ring-emerald-500/20"
                                    >
                                        Current
                                    </span>
                                    <span
                                        v-if="e.is_featured"
                                        class="rounded-full bg-indigo-500/10 px-2.5 py-1 text-xs text-indigo-200 ring-1 ring-indigo-500/20"
                                    >
                                        Featured
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-foreground/80">
                                {{ e.role }}
                            </td>
                            <td class="px-4 py-3 text-foreground/60">
                                <span v-if="e.started_on">{{
                                    e.started_on
                                }}</span>
                                <span v-if="e.started_on" class="opacity-60">
                                    —
                                </span>
                                <span>{{
                                    e.is_current ? 'Present' : e.ended_on
                                }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link
                                    :href="`/admin/experience/${e.id}/edit`"
                                    class="text-foreground/70 hover:text-foreground"
                                >
                                    Edit →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="experiences.links?.length" class="flex flex-wrap gap-2">
                <a
                    v-for="link in experiences.links"
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
