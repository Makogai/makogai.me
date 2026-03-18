<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
const siteName =
    ((page.props as any).settings?.site?.site_name as string | undefined) ??
    (page.props as any).name ??
    'Portfolio';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <img
            src="/logo.svg"
            alt=""
            class="size-5 opacity-95 [filter:drop-shadow(0_1px_0_rgba(0,0,0,0.08))] dark:invert"
        />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{
            siteName
        }}</span>
    </div>
</template>
