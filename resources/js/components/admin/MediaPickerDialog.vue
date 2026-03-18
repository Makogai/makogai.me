<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

type MediaItem = {
    id: number;
    url: string | null;
    path: string;
    alt: string | null;
};

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'select', item: MediaItem): void;
}>();

const isOpen = computed({
    get: () => props.open,
    set: (v) => emit('update:open', v),
});

const loading = ref(false);
const items = ref<MediaItem[]>([]);
const page = ref(1);
const lastPage = ref(1);

async function load() {
    loading.value = true;
    try {
        const res = await fetch(`/admin/media/library?page=${page.value}`, {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = (await res.json()) as {
            data: MediaItem[];
            current_page: number;
            last_page: number;
        };
        items.value = json.data;
        lastPage.value = json.last_page;
    } finally {
        loading.value = false;
    }
}

watch(
    () => props.open,
    (v) => {
        if (v) {
            page.value = 1;
            load();
        }
    },
);
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="max-w-4xl">
            <DialogHeader>
                <DialogTitle>Media Library</DialogTitle>
            </DialogHeader>

            <div v-if="loading" class="text-sm text-foreground/60">
                Loading…
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <button
                    v-for="m in items"
                    :key="m.id"
                    type="button"
                    class="group overflow-hidden rounded-2xl border border-black/10 bg-white/70 text-left ring-1 ring-black/10 backdrop-blur transition hover:bg-white/80 dark:border-white/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/8"
                    @click="
                        emit('select', m);
                        isOpen = false;
                    "
                >
                    <div class="aspect-[4/3] bg-black/5 dark:bg-white/5">
                        <img
                            v-if="m.url"
                            :src="m.url"
                            :alt="m.alt ?? ''"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                    </div>
                    <div class="p-3">
                        <div class="line-clamp-1 text-xs text-foreground/70">
                            {{ m.path }}
                        </div>
                    </div>
                </button>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <button
                    type="button"
                    class="site-nav-link"
                    :disabled="page <= 1"
                    @click="
                        page = Math.max(1, page - 1);
                        load();
                    "
                >
                    Prev
                </button>
                <div class="text-xs text-foreground/60">
                    Page {{ page }} / {{ lastPage }}
                </div>
                <button
                    type="button"
                    class="site-nav-link"
                    :disabled="page >= lastPage"
                    @click="
                        page = Math.min(lastPage, page + 1);
                        load();
                    "
                >
                    Next
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
