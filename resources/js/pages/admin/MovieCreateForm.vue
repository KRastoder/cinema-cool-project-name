<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    title: '',
    duration: '',
    description: '',
    poster: null,
    release_date: '',
})

const submit = () => {
    form.post('/admin/movies')
}

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form.poster = target.files[0]
    }
}
</script>

<template>
    <div class="min-h-screen bg-black text-white">
        <div class="max-w-2xl mx-auto py-12 px-4">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">Create Movie</h1>
                <p class="text-gray-400">Add a new movie to your cinema</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium mb-2">Title</label>
                    <input v-model="form.title" type="text" placeholder="Enter movie title"
                        class="w-full px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition" />
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-400">
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium mb-2">Duration (minutes)</label>
                    <input v-model="form.duration" type="number" placeholder="120"
                        class="w-full px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition" />
                    <p v-if="form.errors.duration" class="mt-1 text-sm text-red-400">
                        {{ form.errors.duration }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea v-model="form.description" rows="4" placeholder="Enter movie description..."
                        class="w-full px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition resize-none"></textarea>
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-400">
                        {{ form.errors.description }}
                    </p>
                </div>

                <!-- Poster Upload -->
                <div>
                    <label class="block text-sm font-medium mb-2">Poster</label>
                    <div class="relative">
                        <input type="file" accept="image/*" @change="handleFileUpload"
                            class="w-full px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-white file:text-black file:font-medium hover:file:bg-gray-200 file:cursor-pointer" />
                    </div>
                    <p v-if="form.errors.poster" class="mt-1 text-sm text-red-400">
                        {{ form.errors.poster }}
                    </p>
                </div>

                <!-- Release Date -->
                <div>
                    <label class="block text-sm font-medium mb-2">Release Date</label>
                    <input v-model="form.release_date" type="date"
                        class="w-full px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition" />
                    <p v-if="form.errors.release_date" class="mt-1 text-sm text-red-400">
                        {{ form.errors.release_date }}
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-3 bg-white text-black font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-black transition disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ form.processing ? 'Creating...' : 'Create Movie' }}
                    </button>
                    <a href="/admin/movies" class="px-6 py-3 text-gray-400 hover:text-white transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>
