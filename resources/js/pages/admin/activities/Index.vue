<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    activities: {
        data: {
            id: number;
            type: string;
            title: string;
            happened_at: string;
            url: string | null;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Activities', href: '/admin/activities' },
];

const syncing = ref(false);

function syncNow(): void {
    if (syncing.value) {
        return;
    }

    syncing.value = true;
    router.post(
        '/admin/activities/sync-github',
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                syncing.value = false;
            },
        },
    );
}
</script>

<template>
    <Head title="Activities" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Activities
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Commit-style timeline entries.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="site-nav-link px-4 py-2 text-sm"
                        :disabled="syncing"
                        @click="syncNow"
                    >
                        {{ syncing ? 'Syncing…' : 'Sync GitHub now' }}
                    </button>
                    <Link href="/admin/activities/create" class="glass-button">
                        New activity
                    </Link>
                </div>
            </div>

            <div
                class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full text-sm">
                    <thead class="bg-sidebar/30">
                        <tr class="text-left text-foreground/60">
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="a in activities.data"
                            :key="a.id"
                            class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3 text-foreground/70">
                                {{ a.type }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ a.title }}</div>
                                <div
                                    v-if="a.url"
                                    class="text-xs text-foreground/50"
                                >
                                    {{ a.url }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-foreground/60">
                                {{
                                    new Date(a.happened_at).toLocaleDateString()
                                }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link
                                    :href="`/admin/activities/${a.id}/edit`"
                                    class="text-foreground/70 hover:text-foreground"
                                >
                                    Edit →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="activities.links?.length" class="flex flex-wrap gap-2">
                <a
                    v-for="link in activities.links"
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
