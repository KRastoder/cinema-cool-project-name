<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

interface ContentItem {
    id: number;
    title: string;
    duration: string | null;
    description: string | null;
    poster: string | null;
    release_date: string | null;
    created_at: string;
    updated_at: string;
}

interface FormData {
    title: string;
    duration: string;
    description: string;
    poster: File | null;
    release_date: string;
}

// Receive props from Inertia
const props = defineProps<{
    movies: ContentItem[]
}>();

const items = ref<ContentItem[]>(props.movies);

// Modal states
const showModal = ref<boolean>(false);
const isEditing = ref<boolean>(false);
const currentItemId = ref<number | null>(null);
const saving = ref<boolean>(false);
const formError = ref<string | null>(null);
const previewUrl = ref<string | null>(null);

// Delete confirmation states
const showDeleteConfirm = ref<boolean>(false);
const deleteItemId = ref<number | null>(null);
const deleting = ref<boolean>(false);

const formData = ref<FormData>({
    title: '',
    duration: '',
    description: '',
    poster: null,
    release_date: ''
});

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'N/A';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
};

const getImageUrl = (posterPath: string | null): string | null => {
    if (!posterPath) return null;
    return `/storage/${posterPath}`;
};

const handleFileChange = (event: Event): void => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        formData.value.poster = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const openEditModal = (item: ContentItem): void => {
    isEditing.value = true;
    currentItemId.value = item.id;
    formData.value = {
        title: item.title,
        duration: item.duration || '',
        description: item.description || '',
        poster: null,
        release_date: item.release_date || ''
    };
    previewUrl.value = item.poster ? getImageUrl(item.poster) : null;
    formError.value = null;
    showModal.value = true;
};

const closeModal = (): void => {
    showModal.value = false;
    formError.value = null;
    previewUrl.value = null;
    formData.value.poster = null;
};

const saveItem = (): void => {
    if (!currentItemId.value) return;

    saving.value = true;
    formError.value = null;

    const data = new FormData();
    data.append('title', formData.value.title);
    data.append('duration', formData.value.duration);
    data.append('description', formData.value.description);
    data.append('release_date', formData.value.release_date);

    if (formData.value.poster) {
        data.append('poster', formData.value.poster);
    }

    data.append('_method', 'PUT');

    router.post(`/admin/movies/${currentItemId.value}`, data, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            saving.value = false;
        },
        onError: (errors) => {
            formError.value = Object.values(errors).flat().join(', ');
            saving.value = false;
        }
    });
};

const confirmDelete = (id: number): void => {
    deleteItemId.value = id;
    showDeleteConfirm.value = true;
};

const cancelDelete = (): void => {
    deleteItemId.value = null;
    showDeleteConfirm.value = false;
};

const deleteItem = (): void => {
    if (!deleteItemId.value) return;

    deleting.value = true;

    router.delete(`/admin/movies/${deleteItemId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteConfirm.value = false;
            deleteItemId.value = null;
            deleting.value = false;
        },
        onError: () => {
            alert('Failed to delete item');
            deleting.value = false;
        }
    });
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Content Management</h1>
                    <p class="text-slate-400">Manage your video content library</p>
                </div>
                <a href="/admin/create-movies"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New
                </a>
            </div>

            <!-- Content Grid -->
            <div v-if="items.length > 0" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="item in items" :key="item.id"
                    class="bg-slate-900 rounded-lg border border-slate-800 overflow-hidden hover:border-slate-700 transition-all duration-200 group">
                    <!-- Poster Image -->
                    <div class="relative aspect-video bg-slate-800 overflow-hidden">
                        <img v-if="item.poster" :src="getImageUrl(item.poster)" :alt="item.title"
                            class="h-full w-full  group-hover:scale-105 transition-transform duration-200" />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <!-- Duration Badge -->
                        <div v-if="item.duration"
                            class="absolute top-2 right-2 bg-slate-950/80 backdrop-blur-sm px-2 py-1 rounded text-xs font-medium">
                            {{ item.duration }}
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2 line-clamp-1">{{ item.title }}</h3>

                        <p v-if="item.description" class="text-slate-400 text-sm mb-4 line-clamp-2">
                            {{ item.description }}
                        </p>

                        <!-- Metadata -->
                        <div class="space-y-2 text-xs text-slate-500 mb-4">
                            <div v-if="item.release_date" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Released: {{ formatDate(item.release_date) }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Created: {{ formatDate(item.created_at) }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button @click="openEditModal(item)"
                                class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-50 px-3 py-2 rounded text-sm font-medium transition-colors">
                                Edit
                            </button>
                            <button @click="confirmDelete(item.id)"
                                class="bg-red-950/50 hover:bg-red-950 text-red-400 hover:text-red-300 px-3 py-2 rounded text-sm font-medium transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 bg-slate-900 rounded-lg border border-slate-800">
                <svg class="w-16 h-16 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
                <p class="text-slate-400 text-lg">No content found</p>
            </div>
        </div>

        <!-- Edit Modal -->
        <Teleport to="body">
            <div v-if="showModal"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
                @click.self="closeModal">
                <div
                    class="bg-slate-900 rounded-xl border border-slate-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-6 border-b border-slate-800">
                        <h2 class="text-2xl font-bold">Edit Content</h2>
                        <button @click="closeModal" class="text-slate-400 hover:text-slate-50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="saveItem" class="p-6 space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Title *</label>
                            <input v-model="formData.title" type="text" required
                                class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter title" />
                        </div>

                        <!-- Duration -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Duration</label>
                            <input v-model="formData.duration" type="text"
                                class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., 2h 30m" />
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Description</label>
                            <textarea v-model="formData.description" rows="4"
                                class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                placeholder="Enter description"></textarea>
                        </div>

                        <!-- Poster Upload -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Poster Image</label>

                            <!-- Current/Preview Image -->
                            <div v-if="previewUrl" class="mb-3 relative">
                                <img :src="previewUrl" alt="Poster preview"
                                    class="w-full max-h-64 object-contain rounded-lg border border-slate-700 bg-slate-800" />
                            </div>

                            <!-- File Input -->
                            <div class="relative">
                                <input type="file" @change="handleFileChange" accept="image/*" class="hidden"
                                    id="poster-upload" />
                                <label for="poster-upload"
                                    class="flex items-center justify-center gap-2 w-full bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg px-4 py-3 text-slate-50 cursor-pointer transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span>{{ formData.poster ? 'Change Image' : 'Upload New Image' }}</span>
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Upload a new image to replace the current one</p>
                        </div>

                        <!-- Release Date -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Release Date</label>
                            <input v-model="formData.release_date" type="date"
                                class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <!-- Error Message -->
                        <div v-if="formError" class="bg-red-950/50 border border-red-900 rounded-lg p-3">
                            <p class="text-red-400 text-sm">{{ formError }}</p>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="closeModal"
                                class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-50 px-4 py-2 rounded-lg font-medium transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="saving"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg v-if="saving" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ saving ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <div v-if="showDeleteConfirm"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
                @click.self="cancelDelete">
                <div class="bg-slate-900 rounded-xl border border-slate-800 w-full max-w-md shadow-2xl">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-red-950/50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Delete Content</h3>
                                <p class="text-slate-400 text-sm">This action cannot be undone</p>
                            </div>
                        </div>
                        <p class="text-slate-300 mb-6">Are you sure you want to delete this content? All data will be
                            permanently removed.</p>
                        <div class="flex gap-3">
                            <button @click="cancelDelete"
                                class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-50 px-4 py-2 rounded-lg font-medium transition-colors">
                                Cancel
                            </button>
                            <button @click="deleteItem" :disabled="deleting"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg v-if="deleting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ deleting ? 'Deleting...' : 'Delete' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>