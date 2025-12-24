<template>
    <div class="w-full min-h-screen flex flex-col items-center p-10 bg-black text-white font-sans">
        <h1 class="text-2xl font-bold mb-6 text-[#FFC0CB]">Cinema 2D Layout Designer</h1>

        <form @submit.prevent="submit"
            class="w-full max-w-2xl bg-zinc-900 p-6 rounded-xl border border-[#FFC0CB]/20 space-y-6">
            <div class="flex flex-col">
                <label class="text-sm text-[#FFC0CB]">Hall Name</label>
                <input type="text" v-model="form.name" required
                    class="bg-black border border-zinc-800 p-2 rounded focus:border-[#FFC0CB] outline-none text-white" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <label class="text-sm text-[#FFC0CB]">Total Rows (Max 50)</label>
                    <input type="number" v-model.number="form.rows" min="1" max="50"
                        class="bg-black border border-zinc-800 p-2 rounded focus:border-[#FFC0CB] outline-none" />
                </div>
                <div class="flex flex-col">
                    <label class="text-sm text-[#FFC0CB]">Total Columns (Max 50)</label>
                    <input type="number" v-model.number="form.columns" min="1" max="50"
                        class="bg-black border border-zinc-800 p-2 rounded focus:border-[#FFC0CB] outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] text-[#FFC0CB] uppercase font-bold">Row Gaps (After Row X)</label>
                        <button type="button" @click="rowWalkways.push(1)"
                            class="text-[10px] bg-zinc-800 px-2 py-1 rounded hover:bg-zinc-700 transition">+</button>
                    </div>
                    <div v-for="(path, i) in rowWalkways" :key="'r-gap-' + i" class="flex flex-col gap-1">
                        <div class="flex gap-2">
                            <input type="number" v-model.number="rowWalkways[i]"
                                class="w-full bg-black border p-1 rounded text-xs text-white"
                                :class="isInvalidGap(rowWalkways[i], form.rows) ? 'border-red-500' : 'border-zinc-800'" />
                            <button type="button" @click="rowWalkways.splice(i, 1)"
                                class="text-red-500 hover:text-red-400">×</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] text-[#FFC0CB] uppercase font-bold">Col Gaps (After Col X)</label>
                        <button type="button" @click="colWalkways.push(1)"
                            class="text-[10px] bg-zinc-800 px-2 py-1 rounded hover:bg-zinc-700 transition">+</button>
                    </div>
                    <div v-for="(path, i) in colWalkways" :key="'c-gap-' + i" class="flex flex-col gap-1">
                        <div class="flex gap-2">
                            <input type="number" v-model.number="colWalkways[i]"
                                class="w-full bg-black border p-1 rounded text-xs text-white"
                                :class="isInvalidGap(colWalkways[i], form.columns) ? 'border-red-500' : 'border-zinc-800'" />
                            <button type="button" @click="colWalkways.splice(i, 1)"
                                class="text-red-500 hover:text-red-400">×</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 bg-black/50 rounded-lg border border-pink-900/30 flex justify-between items-center">
                <span class="text-xs text-zinc-400 uppercase tracking-widest font-bold">VIP Seats Selected:</span>
                <span class="text-lg font-bold text-[#FFC0CB]">{{ vipSeats.length }}</span>
            </div>

            <button type="submit" :disabled="form.processing"
                class="w-full bg-[#FFC0CB] text-black font-bold py-3 rounded-lg hover:bg-pink-200 transition-colors disabled:opacity-50">
                {{ form.processing ? 'Saving...' : 'Confirm Hall & Layout' }}
            </button>
        </form>

        <div class="mt-12 w-full flex flex-col items-center overflow-auto pb-20">
            <div class="w-1/2 h-1 bg-[#FFC0CB]/20 rounded-full mb-12 shadow-[0_0_15px_#FFC0CB33]"></div>
            <p class="text-[10px] text-zinc-600 uppercase mb-8 tracking-[0.5em]">Screen Area</p>

            <div v-if="generatedGrid.length > 2500"
                class="text-red-400 text-sm border border-red-900/50 p-4 rounded bg-red-900/10">
                Grid is too large to preview efficiently ({{ generatedGrid.length }} cells). Please reduce dimensions.
            </div>

            <div v-else class="grid justify-center" :style="gridStyle">
                <template v-for="cell in generatedGrid" :key="cell.id">

                    <div v-if="cell.isPath"
                        class="w-8 h-8 flex items-center justify-center opacity-20 pointer-events-none">
                        <div class="w-1 h-1 bg-white rounded-full"></div>
                    </div>

                    <div v-else @click="toggleVip(cell.seatNumber)"
                        class="group relative w-8 h-8 cursor-pointer transition-all duration-200 flex items-center justify-center"
                        :class="isVip(cell.seatNumber) ? 'text-[#FFC0CB] scale-110' : 'text-zinc-800 hover:text-zinc-500'">

                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path
                                d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3M3 11v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2Z" />
                        </svg>

                        <span
                            class="absolute -top-6 text-[8px] bg-zinc-800 px-1 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10 whitespace-nowrap pointer-events-none border border-zinc-700">
                            {{ cell.seatNumber }}
                        </span>
                    </div>

                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

// --- State ---
const rowWalkways = ref<number[]>([]);
const colWalkways = ref<number[]>([]);
const vipSeats = ref<number[]>([]);

const form = useForm({
    name: '',
    rows: 6,
    columns: 10,
    row_gap: null as string | null, // Preserving backend requirement
    col_gap: null as string | null, // Preserving backend requirement
    vip_list: [] as number[]        // Preserving backend requirement
});

// --- Validation Helpers ---
const isInvalidGap = (val: number, max: number) => val < 1 || val >= max;

// --- Computed Valid Paths (Sorted & Unique) ---
const validRowPaths = computed(() =>
    [...new Set(rowWalkways.value.filter(p => p > 0 && p < form.rows))].sort((a, b) => a - b)
);
const validColPaths = computed(() =>
    [...new Set(colWalkways.value.filter(p => p > 0 && p < form.columns))].sort((a, b) => a - b)
);

// --- The Core Optimization: Pre-calculate the Grid ---
// Instead of calculating on every render loop, we generate the map once.
interface GridCell {
    id: string;
    isPath: boolean;
    seatNumber: number;
}

const generatedGrid = computed(() => {
    // If inputs are crazy, return empty to prevent crash
    if (form.rows > 60 || form.columns > 60) return [];

    const cells: GridCell[] = [];
    let seatCounter = 0;

    // Sets for O(1) lookup
    const rowGaps = new Set(validRowPaths.value);
    const colGaps = new Set(validColPaths.value);

    // Build Rows (including gaps)
    for (let r = 1; r <= form.rows; r++) {
        // 1. Process the row itself
        // Build Columns (including gaps)
        for (let c = 1; c <= form.columns; c++) {
            // Add Seat
            seatCounter++;
            cells.push({
                id: `s-${r}-${c}`,
                isPath: false,
                seatNumber: seatCounter
            });

            // If there is a column gap after this column, add a path cell
            if (colGaps.has(c)) {
                cells.push({ id: `gp-c-${r}-${c}`, isPath: true, seatNumber: -1 });
            }
        }

        // 2. If there is a ROW gap after this row, add a whole row of path cells
        if (rowGaps.has(r)) {
            const visualColsCount = form.columns + validColPaths.value.length;
            for (let k = 0; k < visualColsCount; k++) {
                cells.push({ id: `gp-r-${r}-${k}`, isPath: true, seatNumber: -1 });
            }
        }
    }
    return cells;
});

// --- VIP Logic ---
const toggleVip = (seatNum: number) => {
    if (seatNum === -1) return;
    const index = vipSeats.value.indexOf(seatNum);
    if (index > -1) vipSeats.value.splice(index, 1);
    else vipSeats.value.push(seatNum);
};

const isVip = (seatNum: number) => vipSeats.value.includes(seatNum);

// --- Style Computations ---
const gridStyle = computed(() => {
    const totalCols = form.columns + validColPaths.value.length;
    return {
        display: 'grid',
        gridTemplateColumns: `repeat(${totalCols}, 32px)`,
        gap: '6px'
    };
});

// --- Submission ---
const submit = () => {
    // Exact same logic as before to satisfy backend
    form.row_gap = JSON.stringify(validRowPaths.value);
    form.col_gap = JSON.stringify(validColPaths.value);
    form.vip_list = vipSeats.value.sort((a, b) => a - b);

    form.post('/admin/hall-store', {
        onSuccess: () => {
            // Optional: Reset or notify
            // alert('Hall Saved');
        }
    });
};
</script>