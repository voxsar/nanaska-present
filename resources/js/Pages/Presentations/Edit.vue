<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    presentation: Object,
});

const form = useForm({
    title: props.presentation.title,
    description: props.presentation.description || '',
    file: null,
    video: null,
    audio: null,
});

const fileInput = ref(null);
const videoInput = ref(null);
const audioInput = ref(null);

// Media recording state
const isRecordingVideo = ref(false);
const isRecordingAudio = ref(false);
const videoStream = ref(null);
const audioStream = ref(null);
const mediaRecorder = ref(null);
const recordedChunks = ref([]);
const videoPreview = ref(null);
const audioPreview = ref(null);
const recordedVideoUrl = ref(props.presentation.video_path ? '/storage/' + props.presentation.video_path : null);
const recordedAudioUrl = ref(props.presentation.audio_path ? '/storage/' + props.presentation.audio_path : null);

const handleFileChange = (event) => {
    form.file = event.target.files[0];
};

const handleVideoChange = (event) => {
    form.video = event.target.files[0];
    if (form.video) {
        recordedVideoUrl.value = URL.createObjectURL(form.video);
    }
};

const handleAudioChange = (event) => {
    form.audio = event.target.files[0];
    if (form.audio) {
        recordedAudioUrl.value = URL.createObjectURL(form.audio);
    }
};

const startVideoRecording = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        videoStream.value = stream;
        videoPreview.value.srcObject = stream;
        await videoPreview.value.play();

        recordedChunks.value = [];
        const recorder = new MediaRecorder(stream, { mimeType: 'video/webm' });

        recorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunks.value.push(event.data);
            }
        };

        recorder.onstop = () => {
            const blob = new Blob(recordedChunks.value, { type: 'video/webm' });
            form.video = new File([blob], 'video-recording.webm', { type: 'video/webm' });
            recordedVideoUrl.value = URL.createObjectURL(blob);

            stream.getTracks().forEach(track => track.stop());
            videoStream.value = null;
        };

        mediaRecorder.value = recorder;
        recorder.start();
        isRecordingVideo.value = true;
    } catch (error) {
        console.error('Error accessing camera:', error);
        alert('Could not access camera. Please ensure you have granted permission.');
    }
};

const stopVideoRecording = () => {
    if (mediaRecorder.value && isRecordingVideo.value) {
        mediaRecorder.value.stop();
        isRecordingVideo.value = false;
    }
};

const startAudioRecording = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        audioStream.value = stream;

        recordedChunks.value = [];
        const recorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });

        recorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunks.value.push(event.data);
            }
        };

        recorder.onstop = () => {
            const blob = new Blob(recordedChunks.value, { type: 'audio/webm' });
            form.audio = new File([blob], 'audio-recording.webm', { type: 'audio/webm' });
            recordedAudioUrl.value = URL.createObjectURL(blob);

            stream.getTracks().forEach(track => track.stop());
            audioStream.value = null;
        };

        mediaRecorder.value = recorder;
        recorder.start();
        isRecordingAudio.value = true;
    } catch (error) {
        console.error('Error accessing microphone:', error);
        alert('Could not access microphone. Please ensure you have granted permission.');
    }
};

const stopAudioRecording = () => {
    if (mediaRecorder.value && isRecordingAudio.value) {
        mediaRecorder.value.stop();
        isRecordingAudio.value = false;
    }
};

const submit = () => {
    form.post(route('presentations.update', props.presentation.id), {
        forceFormData: true,
        method: 'put',
        _method: 'put',
    });
};

const deletePresentation = () => {
    if (confirm('Are you sure you want to delete this presentation? This action cannot be undone.')) {
        router.delete(route('presentations.destroy', props.presentation.id));
    }
};
</script>

<template>
    <Head title="Edit Presentation" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <Link :href="route('presentations.show', presentation.id)" class="text-gray-500 hover:text-gray-700 mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Presentation</h2>
                </div>
                <DangerButton @click="deletePresentation">
                    Delete Presentation
                </DangerButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Title" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.description"
                                    rows="4"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Presentation File -->
                            <div>
                                <InputLabel for="file" value="Presentation File (PDF, PPT, PPTX)" />
                                <div v-if="presentation.file_path" class="mt-1 mb-2">
                                    <a
                                        :href="'/storage/' + presentation.file_path"
                                        target="_blank"
                                        class="text-sm text-indigo-600 hover:text-indigo-800"
                                    >
                                        View current file
                                    </a>
                                </div>
                                <input
                                    type="file"
                                    id="file"
                                    ref="fileInput"
                                    @change="handleFileChange"
                                    accept=".pdf,.ppt,.pptx"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">Upload a new file to replace the current one</p>
                                <InputError class="mt-2" :message="form.errors.file" />
                            </div>

                            <!-- Video Recording -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Video Recording</h3>

                                <div class="space-y-4">
                                    <!-- Video Preview -->
                                    <div v-if="isRecordingVideo || recordedVideoUrl" class="relative">
                                        <video
                                            v-if="isRecordingVideo"
                                            ref="videoPreview"
                                            class="w-full rounded-lg bg-black"
                                            muted
                                        ></video>
                                        <video
                                            v-else-if="recordedVideoUrl"
                                            :src="recordedVideoUrl"
                                            class="w-full rounded-lg"
                                            controls
                                        ></video>
                                    </div>

                                    <!-- Recording Controls -->
                                    <div class="flex space-x-4">
                                        <button
                                            v-if="!isRecordingVideo"
                                            type="button"
                                            @click="startVideoRecording"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <circle cx="10" cy="10" r="6" />
                                            </svg>
                                            Start Recording
                                        </button>
                                        <button
                                            v-else
                                            type="button"
                                            @click="stopVideoRecording"
                                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <rect x="5" y="5" width="10" height="10" />
                                            </svg>
                                            Stop Recording
                                        </button>
                                    </div>

                                    <!-- Or Upload Video -->
                                    <div class="flex items-center">
                                        <span class="text-gray-500 mr-4">Or upload a video file:</span>
                                        <input
                                            type="file"
                                            id="video"
                                            ref="videoInput"
                                            @change="handleVideoChange"
                                            accept="video/*"
                                            class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                        />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.video" />
                                </div>
                            </div>

                            <!-- Audio Recording -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Audio Recording</h3>

                                <div class="space-y-4">
                                    <!-- Audio Preview -->
                                    <div v-if="recordedAudioUrl">
                                        <audio :src="recordedAudioUrl" controls class="w-full"></audio>
                                    </div>

                                    <!-- Recording Indicator -->
                                    <div v-if="isRecordingAudio" class="flex items-center space-x-2 text-red-600">
                                        <span class="animate-pulse">●</span>
                                        <span>Recording...</span>
                                    </div>

                                    <!-- Recording Controls -->
                                    <div class="flex space-x-4">
                                        <button
                                            v-if="!isRecordingAudio"
                                            type="button"
                                            @click="startAudioRecording"
                                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                            </svg>
                                            Start Recording
                                        </button>
                                        <button
                                            v-else
                                            type="button"
                                            @click="stopAudioRecording"
                                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <rect x="5" y="5" width="10" height="10" />
                                            </svg>
                                            Stop Recording
                                        </button>
                                    </div>

                                    <!-- Or Upload Audio -->
                                    <div class="flex items-center">
                                        <span class="text-gray-500 mr-4">Or upload an audio file:</span>
                                        <input
                                            type="file"
                                            id="audio"
                                            ref="audioInput"
                                            @change="handleAudioChange"
                                            accept="audio/*"
                                            class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                                        />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.audio" />
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end pt-6 border-t">
                                <Link
                                    :href="route('presentations.show', presentation.id)"
                                    class="text-gray-600 hover:text-gray-900 mr-4"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">Saving...</span>
                                    <span v-else>Save Changes</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
