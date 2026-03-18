<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    site: {
        site_name: string;
        tagline: string | null;
        email: string | null;
        github_url: string | null;
        linkedin_url: string | null;
        x_url: string | null;
    };

    profile: {
        full_name: string | null;
        headline: string | null;
        bio: string | null;
        portrait_light_path: string | null;
        portrait_dark_path: string | null;
        cover_path: string | null;
    };
}>();

const form = useForm({
    site_name: props.site.site_name ?? '',
    tagline: props.site.tagline ?? '',
    email: props.site.email ?? '',
    github_url: props.site.github_url ?? '',
    linkedin_url: props.site.linkedin_url ?? '',
    x_url: props.site.x_url ?? '',
});

const profileForm = useForm({
    full_name: props.profile.full_name ?? '',
    headline: props.profile.headline ?? '',
    bio: props.profile.bio ?? '',
});

const mediaForm = useForm({
    portrait_light: null as File | null,
    portrait_dark: null as File | null,
    cover: null as File | null,
});

const preview = computed(() => ({
    portrait_light: mediaForm.portrait_light
        ? URL.createObjectURL(mediaForm.portrait_light)
        : props.profile.portrait_light_path
          ? `/storage/${props.profile.portrait_light_path}`
          : null,
    portrait_dark: mediaForm.portrait_dark
        ? URL.createObjectURL(mediaForm.portrait_dark)
        : props.profile.portrait_dark_path
          ? `/storage/${props.profile.portrait_dark_path}`
          : null,
    cover: mediaForm.cover
        ? URL.createObjectURL(mediaForm.cover)
        : props.profile.cover_path
          ? `/storage/${props.profile.cover_path}`
          : null,
}));

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Settings', href: '/admin/settings' },
];
</script>

<template>
    <Head title="Settings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Settings
                    </h1>
                    <p class="text-sm text-foreground/60">
                        Site-wide configuration.
                    </p>
                </div>
                <Link href="/admin" class="site-nav-link">Back</Link>
            </div>

            <form
                class="max-w-2xl space-y-4"
                @submit.prevent="form.put('/admin/settings')"
            >
                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <label class="text-xs text-foreground/60">Site name</label>
                    <input
                        v-model="form.site_name"
                        class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                    />
                    <div
                        v-if="form.errors.site_name"
                        class="mt-2 text-xs text-red-400"
                    >
                        {{ form.errors.site_name }}
                    </div>
                </div>

                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <label class="text-xs text-foreground/60">Tagline</label>
                    <input
                        v-model="form.tagline"
                        class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                    />
                </div>

                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <label class="text-xs text-foreground/60">Email</label>
                    <input
                        v-model="form.email"
                        class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                    />
                </div>

                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <label class="text-xs text-foreground/60">Socials</label>
                    <div class="mt-2 grid gap-2">
                        <input
                            v-model="form.github_url"
                            placeholder="GitHub URL"
                            class="h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                        <input
                            v-model="form.linkedin_url"
                            placeholder="LinkedIn URL"
                            class="h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                        <input
                            v-model="form.x_url"
                            placeholder="X/Twitter URL"
                            class="h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button
                        type="submit"
                        class="glass-button"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving…' : 'Save settings' }}
                    </button>
                </div>
            </form>

            <div class="max-w-2xl space-y-4">
                <div
                    class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                >
                    <h2 class="text-sm font-medium tracking-tight">Profile</h2>
                    <p class="mt-1 text-xs text-foreground/60">
                        This is what shows on the homepage hero.
                    </p>
                </div>

                <form
                    class="space-y-4"
                    @submit.prevent="profileForm.put('/admin/settings/profile')"
                >
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Full name</label
                        >
                        <input
                            v-model="profileForm.full_name"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60"
                            >Headline</label
                        >
                        <input
                            v-model="profileForm.headline"
                            class="mt-2 h-10 w-full rounded-xl border border-white/10 bg-white/5 px-3 text-sm ring-1 ring-white/10"
                            placeholder="Senior Full‑stack Developer"
                        />
                    </div>
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <label class="text-xs text-foreground/60">Bio</label>
                        <textarea
                            v-model="profileForm.bio"
                            rows="4"
                            class="mt-2 w-full resize-y rounded-xl border border-white/10 bg-white/5 p-3 text-sm ring-1 ring-white/10"
                        />
                    </div>
                    <div class="flex items-center justify-end">
                        <button
                            type="submit"
                            class="glass-button"
                            :disabled="profileForm.processing"
                        >
                            {{
                                profileForm.processing
                                    ? 'Saving…'
                                    : 'Save profile'
                            }}
                        </button>
                    </div>
                </form>

                <form
                    class="space-y-4"
                    @submit.prevent="
                        mediaForm.post('/admin/profile/media', {
                            forceFormData: true,
                        })
                    "
                >
                    <div
                        class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    >
                        <h3 class="text-sm font-medium tracking-tight">
                            Profile media
                        </h3>
                        <p class="mt-1 text-xs text-foreground/60">
                            Upload a light-mode portrait, dark-mode portrait,
                            and an optional cover.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div
                            class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                        >
                            <div class="text-xs text-foreground/60">
                                Portrait (light)
                            </div>
                            <div
                                class="mt-3 aspect-square overflow-hidden rounded-2xl border border-black/10 bg-white/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <img
                                    v-if="preview.portrait_light"
                                    :src="preview.portrait_light"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <input
                                class="mt-3 block w-full text-xs"
                                type="file"
                                accept="image/*"
                                @change="
                                    (e) =>
                                        (mediaForm.portrait_light =
                                            (e.target as HTMLInputElement)
                                                .files?.[0] ?? null)
                                "
                            />
                        </div>

                        <div
                            class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                        >
                            <div class="text-xs text-foreground/60">
                                Portrait (dark)
                            </div>
                            <div
                                class="mt-3 aspect-square overflow-hidden rounded-2xl border border-black/10 bg-white/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <img
                                    v-if="preview.portrait_dark"
                                    :src="preview.portrait_dark"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <input
                                class="mt-3 block w-full text-xs"
                                type="file"
                                accept="image/*"
                                @change="
                                    (e) =>
                                        (mediaForm.portrait_dark =
                                            (e.target as HTMLInputElement)
                                                .files?.[0] ?? null)
                                "
                            />
                        </div>

                        <div
                            class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                        >
                            <div class="text-xs text-foreground/60">Cover</div>
                            <div
                                class="mt-3 aspect-square overflow-hidden rounded-2xl border border-black/10 bg-white/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <img
                                    v-if="preview.cover"
                                    :src="preview.cover"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <input
                                class="mt-3 block w-full text-xs"
                                type="file"
                                accept="image/*"
                                @change="
                                    (e) =>
                                        (mediaForm.cover =
                                            (e.target as HTMLInputElement)
                                                .files?.[0] ?? null)
                                "
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button
                            type="submit"
                            class="glass-button"
                            :disabled="mediaForm.processing"
                        >
                            {{
                                mediaForm.processing
                                    ? 'Uploading…'
                                    : 'Upload media'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
