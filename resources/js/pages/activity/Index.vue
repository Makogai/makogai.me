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
const hoveredDate = ref<string | null>(null);

const selectedItems = computed(() => {
    if (!selectedDate.value) {
        return [];
    }
    return props.byDate[selectedDate.value] ?? [];
});

const hoveredCount = computed(() =>
    hoveredDate.value ? props.counts[hoveredDate.value] ?? 0 : 0,
);

const totalEntries = computed(() =>
    Object.values(props.counts).reduce((sum, count) => sum + count, 0),
);

const activeDays = computed(
    () => Object.values(props.counts).filter((count) => count > 0).length,
);

function parseDate(dateString: string): Date {
    const [y, m, d] = dateString.split('-').map(Number);
    return new Date(y, (m ?? 1) - 1, d ?? 1);
}

const startDate = computed(() => parseDate(props.range.start));
const endDate = computed(() => parseDate(props.range.end));
const gridColumns = 22;
const gridRows = 7;
const totalGridDays = gridColumns * gridRows;

function formatDate(d: Date): string {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
}

const days = computed(() => {
    const list: {
        date: string;
        weekday: number;
        count: number;
        week: number;
        row: number;
    }[] = [];

    // GitHub-style fixed week grid:
    // - align to Monday..Sunday columns
    // - include future cells up to end of current week for a complete grid
    const today = new Date(endDate.value);
    today.setHours(0, 0, 0, 0);

    const mondayIndex = (today.getDay() + 6) % 7; // Mon=0..Sun=6
    const end = new Date(today);
    end.setDate(today.getDate() + (6 - mondayIndex)); // end-of-week (Sunday)
    end.setHours(0, 0, 0, 0);

    const start = new Date(end);
    start.setDate(end.getDate() - (totalGridDays - 1));
    start.setHours(0, 0, 0, 0);

    for (let dayIndex = 0; dayIndex < totalGridDays; dayIndex++) {
        const d = new Date(start);
        d.setDate(start.getDate() + dayIndex);

        const iso = formatDate(d);
        const weekday = d.getDay(); // 0 Sun..6 Sat
        const row = ((weekday + 6) % 7) + 1; // Mon=1..Sun=7
        const week = Math.floor(dayIndex / 7) + 1;
        const count = props.counts[iso] ?? 0;
        list.push({ date: iso, weekday, count, week, row });
    }

    return list;
});

const maxCount = computed(() => Math.max(1, ...days.value.map((d) => d.count)));

const columns = computed(() => gridColumns);

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

const weekdayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const headerDate = computed(() => hoveredDate.value ?? selectedDate.value);
const headerCount = computed(() => {
    const date = headerDate.value;
    if (!date) {
        return null;
    }
    return props.counts[date] ?? 0;
});

const displayStart = computed(() => days.value[0]?.date ?? props.range.start);
const displayEnd = computed(
    () => days.value[days.value.length - 1]?.date ?? props.range.end,
);

function formatPrettyDate(date: string): string {
    return new Date(`${date}T00:00:00`).toLocaleDateString(undefined, {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <SiteLayout>
        <Head title="Activity" />

        <div
            class="rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl sm:p-8 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
        >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-5xl">
                        Activity
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-foreground/65 sm:text-base">
                        A contribution timeline inspired by commit heatmaps.
                        Click any day to inspect entries.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="rounded-full border border-black/10 bg-white/80 px-3 py-1 text-xs ring-1 ring-black/10 dark:border-white/10 dark:bg-white/10 dark:ring-white/10"
                    >
                        {{ totalEntries }} entries
                    </span>
                    <span
                        class="rounded-full border border-black/10 bg-white/80 px-3 py-1 text-xs ring-1 ring-black/10 dark:border-white/10 dark:bg-white/10 dark:ring-white/10"
                    >
                        {{ activeDays }} active days
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div
                class="rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl sm:p-8 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-base font-semibold tracking-tight">
                            Contribution map
                        </div>
                        <div class="mt-1 text-xs text-foreground/55">
                            {{ displayStart }} → {{ displayEnd }}
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

                <div class="mt-6">
                    <div
                        class="mb-4 flex h-10 items-center justify-between rounded-xl border border-black/10 bg-white/80 px-3 py-2 text-xs ring-1 ring-black/10 dark:border-white/10 dark:bg-white/8 dark:ring-white/10"
                    >
                        <div class="text-foreground/65">
                            {{
                                headerDate
                                    ? formatPrettyDate(headerDate)
                                    : 'Hover a day to inspect activity'
                            }}
                        </div>
                        <div
                            v-if="headerCount !== null"
                            class="rounded-full border border-black/10 bg-white/80 px-2 py-0.5 text-[11px] font-medium text-foreground/75 dark:border-white/10 dark:bg-white/10 dark:text-foreground/80"
                        >
                            {{ headerCount }} entries
                        </div>
                    </div>

                    <!-- Mobile: compact 7-column grid (no horizontal scroll) -->
                    <div class="md:hidden">
                        <div class="mb-2 grid grid-cols-7 gap-1.5">
                            <div
                                v-for="label in weekdayLabels"
                                :key="`m-${label}`"
                                class="text-center text-[10px] text-foreground/50"
                            >
                                {{ label }}
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1.5">
                            <button
                                v-for="day in days"
                                :key="`m-${day.date}`"
                                type="button"
                                class="relative flex items-center justify-center"
                                @click="selectedDate = day.date"
                                @mouseenter="hoveredDate = day.date"
                                @mouseleave="hoveredDate = null"
                            >
                                <span class="sr-only">{{ day.date }}</span>
                                <span
                                    class="h-4 w-4 rounded-[5px] ring-1 transition-all duration-150"
                                    :class="[
                                        selectedDate === day.date
                                            ? 'ring-black/45 dark:ring-white/35 scale-[1.03]'
                                            : hoveredDate === day.date
                                              ? 'ring-black/30 dark:ring-white/25'
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
                            </button>
                        </div>
                    </div>

                    <!-- Desktop/tablet: week-column heatmap -->
                    <div class="hidden min-w-max gap-3 md:flex">
                        <div class="grid grid-rows-7 gap-2 pt-0.5">
                            <div
                                v-for="label in weekdayLabels"
                                :key="label"
                                class="h-[24px] text-[11px] leading-[24px] text-foreground/50"
                            >
                                {{ label }}
                            </div>
                        </div>

                        <div
                            class="grid gap-2"
                            :style="{
                                gridTemplateColumns: `repeat(${columns}, minmax(0, 1fr))`,
                                gridAutoRows: '24px',
                            }"
                        >
                            <button
                                v-for="(day, index) in days"
                                :key="day.date"
                                type="button"
                                class="relative flex items-center justify-center"
                                :style="{
                                    gridColumn: String(day.week),
                                    gridRow: String(day.row),
                                }"
                                @click="selectedDate = day.date"
                                @mouseenter="hoveredDate = day.date"
                                @mouseleave="hoveredDate = null"
                            >
                                <span class="sr-only">{{ day.date }}</span>
                                <span
                                    class="h-[20px] w-[20px] rounded-md ring-1 transition-all duration-150"
                                    :class="[
                                        selectedDate === day.date
                                            ? 'ring-black/45 dark:ring-white/35 scale-[1.03]'
                                            : hoveredDate === day.date
                                              ? 'ring-black/30 dark:ring-white/25'
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

                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="rounded-3xl border border-black/10 bg-white/70 p-6 ring-1 ring-black/10 backdrop-blur-xl sm:p-8 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
            >
                <div class="flex items-center justify-between">
                    <div class="text-base font-semibold tracking-tight">
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

                    <div class="mt-4 h-[360px] min-h-[360px]">
                    <div class="mb-3 flex items-center justify-between text-xs">
                        <div class="text-foreground/55">
                            {{
                                selectedDate
                                    ? formatPrettyDate(selectedDate)
                                    : 'Select a day'
                            }}
                        </div>
                        <div
                            class="rounded-full border border-black/10 bg-white/80 px-2 py-0.5 text-[11px] text-foreground/70 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/10 dark:text-foreground/75 dark:ring-white/10"
                        >
                            {{ selectedItems.length }} entries
                        </div>
                    </div>

                    <div class="activity-scroll h-[318px] overflow-y-auto pr-1.5">
                        <div
                            v-if="!selectedDate"
                            class="rounded-2xl border border-black/10 bg-white/80 p-4 text-sm text-foreground/60 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                        >
                            Click a cell on the map to reveal activities for that day.
                        </div>

                        <div v-else class="space-y-2.5">
                            <div
                                v-for="item in selectedItems"
                                :key="item.id"
                                class="rounded-2xl border border-black/10 bg-white/80 p-3 ring-1 ring-black/10 dark:border-white/10 dark:bg-white/5 dark:ring-white/10"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] ring-1 ring-black/10 dark:bg-white/10 dark:ring-white/10"
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
                                            class="mt-1.5 text-xs leading-relaxed text-foreground/65"
                                        >
                                            {{ item.description }}
                                        </p>
                                    </div>
                                    <a
                                        v-if="item.url"
                                        :href="item.url"
                                        target="_blank"
                                        rel="noreferrer"
                                        class="text-[11px] text-foreground/70 hover:text-foreground"
                                    >
                                        Open →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
