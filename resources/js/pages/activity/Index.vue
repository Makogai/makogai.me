<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

const props = defineProps<{
    range: { start: string; end: string };
    byDate: Record<
        string,
        {
            id: number;
            type: string;
            title: string;
            description: string | null;
            happened_at: string;
            url: string | null;
        }[]
    >;
    counts: Record<string, number>;
}>();

const selectedDate = ref<string | null>(null);

const selectedItems = computed(() => {
    if (!selectedDate.value) {
        return [];
    }
    return props.byDate[selectedDate.value] ?? [];
});

function parseDate(dateString: string): Date {
    const [y, m, d] = dateString.split('-').map(Number);
    return new Date(y, (m ?? 1) - 1, d ?? 1);
}

const startDate = computed(() => parseDate(props.range.start));
const endDate = computed(() => parseDate(props.range.end));

function formatDate(d: Date): string {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
}

const days = computed(() => {
    const list: { date: string; weekday: number; count: number }[] = [];

    const start = new Date(startDate.value);
    const end = new Date(endDate.value);
    start.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);

    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
        const iso = formatDate(d);
        const weekday = d.getDay(); // 0 Sun..6 Sat
        const count = props.counts[iso] ?? 0;
        list.push({ date: iso, weekday, count });
    }

    return list;
});

const maxCount = computed(() => Math.max(1, ...days.value.map((d) => d.count)));

const columns = computed(() => Math.ceil(days.value.length / 7));

function intensity(count: number): number {
    if (count <= 0) {
        return 0;
    }
    const ratio = count / maxCount.value;
    if (ratio < 0.25) return 1;
    if (ratio < 0.5) return 2;
    if (ratio < 0.75) return 3;
    return 4;
}
</script>

<template>
    <SiteLayout>
        <Head title="Activity" />

        <div class="flex flex-col gap-2">
            <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                Activity
            </h1>
            <p class="max-w-2xl text-sm text-foreground/65 sm:text-base">
                A calendar-like contribution map. Click a day to reveal what you
                shipped.
            </p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-12">
            <div
                class="rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl sm:p-8 lg:col-span-8 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-medium tracking-tight">
                            Contribution map
                        </div>
                        <div class="mt-1 text-xs text-foreground/55">
                            {{ range.start }} → {{ range.end }}
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-2 text-xs text-foreground/55"
                    >
                        <span>Less</span>
                        <span
                            class="h-2.5 w-2.5 rounded-full bg-black/5 ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                        />
                        <span
                            class="h-2.5 w-2.5 rounded-full bg-indigo-300/50 ring-1 ring-black/10 dark:ring-white/10"
                        />
                        <span
                            class="h-2.5 w-2.5 rounded-full bg-indigo-400/70 ring-1 ring-black/10 dark:ring-white/10"
                        />
                        <span
                            class="h-2.5 w-2.5 rounded-full bg-indigo-500/80 ring-1 ring-black/10 dark:ring-white/10"
                        />
                        <span
                            class="h-2.5 w-2.5 rounded-full bg-indigo-600/90 ring-1 ring-black/10 dark:ring-white/10"
                        />
                        <span>More</span>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <div
                        class="grid gap-2"
                        :style="{
                            gridTemplateColumns: `repeat(${columns}, minmax(0, 1fr))`,
                            gridAutoRows: '14px',
                        }"
                    >
                        <button
                            v-for="(day, index) in days"
                            :key="day.date"
                            type="button"
                            class="group relative flex items-center justify-center"
                            :style="{
                                gridColumn: String(Math.floor(index / 7) + 1),
                                gridRow: String(day.weekday || 7),
                            }"
                            @click="selectedDate = day.date"
                        >
                            <span class="sr-only">{{ day.date }}</span>
                            <span
                                class="h-3 w-3 rounded-full ring-1 transition"
                                :class="[
                                    selectedDate === day.date
                                        ? 'ring-black/40 dark:ring-white/30'
                                        : 'ring-black/10 dark:ring-white/10',
                                    intensity(day.count) === 0
                                        ? 'bg-black/5 dark:bg-white/5'
                                        : intensity(day.count) === 1
                                          ? 'bg-indigo-300/50'
                                          : intensity(day.count) === 2
                                            ? 'bg-indigo-400/70'
                                            : intensity(day.count) === 3
                                              ? 'bg-indigo-500/80'
                                              : 'bg-indigo-600/90',
                                ]"
                            />

                            <div
                                class="pointer-events-none absolute -top-9 left-1/2 hidden -translate-x-1/2 rounded-xl border border-black/10 bg-white/90 px-2 py-1 text-[11px] text-foreground shadow-sm ring-1 ring-black/10 backdrop-blur group-hover:block dark:border-white/10 dark:bg-black/70 dark:ring-white/10"
                            >
                                {{ day.date }} · {{ day.count }} entries
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl sm:p-8 lg:col-span-4 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium tracking-tight">
                        {{ selectedDate ? 'Details' : 'Pick a day' }}
                    </div>
                    <button
                        v-if="selectedDate"
                        type="button"
                        class="text-xs text-foreground/60 hover:text-foreground"
                        @click="selectedDate = null"
                    >
                        Clear
                    </button>
                </div>

                <div
                    v-if="!selectedDate"
                    class="mt-4 text-sm text-foreground/60"
                >
                    Click a dot on the map to reveal the activity for that day.
                </div>

                <div v-else class="mt-4 space-y-3">
                    <div class="text-xs text-foreground/55">
                        {{ selectedDate }}
                    </div>
                    <div
                        v-for="item in selectedItems"
                        :key="item.id"
                        class="rounded-2xl border border-black/10 bg-white/80 p-4 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-full bg-white/80 px-2.5 py-1 text-xs ring-1 ring-black/10 dark:bg-white/5 dark:ring-white/10"
                                    >
                                        {{ item.type }}
                                    </span>
                                    <div
                                        class="text-sm font-medium tracking-tight"
                                    >
                                        {{ item.title }}
                                    </div>
                                </div>
                                <p
                                    v-if="item.description"
                                    class="mt-2 text-sm text-foreground/65"
                                >
                                    {{ item.description }}
                                </p>
                            </div>
                            <a
                                v-if="item.url"
                                :href="item.url"
                                target="_blank"
                                rel="noreferrer"
                                class="text-xs text-foreground/70 hover:text-foreground"
                            >
                                Open →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
