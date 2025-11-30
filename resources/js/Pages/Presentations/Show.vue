<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    presentation: Object,
});
</script>

<template>
    <Head :title="presentation.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <Link :href="route('presentations.index')" class="text-gray-500 hover:text-gray-700 mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ presentation.title }}</h2>
                </div>
                <Link
                    :href="route('presentations.edit', presentation.id)"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    Edit Presentation
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Presentation Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Presentation Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <span
                                    :class="[
                                        'mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        presentation.status === 'evaluated'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-yellow-100 text-yellow-800'
                                    ]"
                                >
                                    {{ presentation.status }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Created</p>
                                <p class="mt-1 text-sm text-gray-900">{{ new Date(presentation.created_at).toLocaleString() }}</p>
                            </div>
                        </div>

                        <div v-if="presentation.description" class="mt-6">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1 text-sm text-gray-900">{{ presentation.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Media Files -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Media Files</h3>

                        <div class="space-y-6">
                            <!-- Presentation File -->
                            <div v-if="presentation.file_path">
                                <p class="text-sm font-medium text-gray-500 mb-2">Presentation File</p>
                                <a
                                    :href="'/storage/' + presentation.file_path"
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Presentation
                                </a>
                            </div>
                            <div v-else class="text-gray-500 text-sm">No presentation file uploaded</div>

                            <!-- Video -->
                            <div v-if="presentation.video_path">
                                <p class="text-sm font-medium text-gray-500 mb-2">Video Recording</p>
                                <video
                                    :src="'/storage/' + presentation.video_path"
                                    controls
                                    class="w-full rounded-lg max-h-96"
                                ></video>
                            </div>
                            <div v-else class="text-gray-500 text-sm">No video recording</div>

                            <!-- Audio -->
                            <div v-if="presentation.audio_path">
                                <p class="text-sm font-medium text-gray-500 mb-2">Audio Recording</p>
                                <audio
                                    :src="'/storage/' + presentation.audio_path"
                                    controls
                                    class="w-full"
                                ></audio>
                            </div>
                            <div v-else class="text-gray-500 text-sm">No audio recording</div>
                        </div>
                    </div>
                </div>

                <!-- Evaluation Results -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Evaluation Results</h3>

                        <div v-if="presentation.evaluation">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Overall Score</p>
                                    <div class="mt-1">
                                        <span
                                            :class="[
                                                'text-3xl font-bold',
                                                presentation.evaluation.score >= 80 ? 'text-green-600' :
                                                presentation.evaluation.score >= 60 ? 'text-yellow-600' : 'text-red-600'
                                            ]"
                                        >
                                            {{ presentation.evaluation.score }}
                                        </span>
                                        <span class="text-gray-500">/100</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Evaluated By</p>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ presentation.evaluation.evaluator?.name || 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ new Date(presentation.evaluation.created_at).toLocaleString() }}
                                    </p>
                                </div>
                            </div>

                            <!-- Criteria Scores -->
                            <div v-if="presentation.evaluation.criteria_scores" class="mb-6">
                                <p class="text-sm font-medium text-gray-500 mb-2">Criteria Scores</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div
                                        v-for="(score, criteria) in presentation.evaluation.criteria_scores"
                                        :key="criteria"
                                        class="bg-gray-50 rounded-lg p-3"
                                    >
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-700">{{ criteria }}</span>
                                            <span class="text-sm font-semibold text-indigo-600">{{ score }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feedback -->
                            <div v-if="presentation.evaluation.feedback">
                                <p class="text-sm font-medium text-gray-500 mb-2">Feedback</p>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ presentation.evaluation.feedback }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Pending Evaluation</h3>
                            <p class="mt-1 text-sm text-gray-500">This presentation has not been evaluated yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
