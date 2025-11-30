<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PresentationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presentations = Presentation::where('user_id', Auth::id())
            ->with('evaluation')
            ->latest()
            ->paginate(10);

        return Inertia::render('Presentations/Index', [
            'presentations' => $presentations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Presentations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx|max:50000',
            'video' => 'nullable|file|mimes:mp4,webm,ogg|max:500000',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,webm|max:100000',
        ]);

        $presentation = new Presentation();
        $presentation->user_id = Auth::id();
        $presentation->title = $validated['title'];
        $presentation->description = $validated['description'] ?? null;

        if ($request->hasFile('file')) {
            $presentation->file_path = $request->file('file')->store('presentations', 'public');
        }

        if ($request->hasFile('video')) {
            $presentation->video_path = $request->file('video')->store('videos', 'public');
        }

        if ($request->hasFile('audio')) {
            $presentation->audio_path = $request->file('audio')->store('audio', 'public');
        }

        $presentation->save();

        return redirect()->route('presentations.index')
            ->with('success', 'Presentation uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Presentation $presentation)
    {
        // Ensure user can only view their own presentations
        if ($presentation->user_id !== Auth::id()) {
            abort(403);
        }

        $presentation->load('evaluation.evaluator');

        return Inertia::render('Presentations/Show', [
            'presentation' => $presentation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presentation $presentation)
    {
        // Ensure user can only edit their own presentations
        if ($presentation->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Presentations/Edit', [
            'presentation' => $presentation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presentation $presentation)
    {
        // Ensure user can only update their own presentations
        if ($presentation->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx|max:50000',
            'video' => 'nullable|file|mimes:mp4,webm,ogg|max:500000',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg,webm|max:100000',
        ]);

        $presentation->title = $validated['title'];
        $presentation->description = $validated['description'] ?? null;

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($presentation->file_path) {
                Storage::disk('public')->delete($presentation->file_path);
            }
            $presentation->file_path = $request->file('file')->store('presentations', 'public');
        }

        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($presentation->video_path) {
                Storage::disk('public')->delete($presentation->video_path);
            }
            $presentation->video_path = $request->file('video')->store('videos', 'public');
        }

        if ($request->hasFile('audio')) {
            // Delete old audio if exists
            if ($presentation->audio_path) {
                Storage::disk('public')->delete($presentation->audio_path);
            }
            $presentation->audio_path = $request->file('audio')->store('audio', 'public');
        }

        $presentation->save();

        return redirect()->route('presentations.show', $presentation)
            ->with('success', 'Presentation updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presentation $presentation)
    {
        // Ensure user can only delete their own presentations
        if ($presentation->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete associated files
        if ($presentation->file_path) {
            Storage::disk('public')->delete($presentation->file_path);
        }
        if ($presentation->video_path) {
            Storage::disk('public')->delete($presentation->video_path);
        }
        if ($presentation->audio_path) {
            Storage::disk('public')->delete($presentation->audio_path);
        }

        $presentation->delete();

        return redirect()->route('presentations.index')
            ->with('success', 'Presentation deleted successfully!');
    }

    /**
     * Handle media blob upload for video/audio recording.
     */
    public function uploadMedia(Request $request, Presentation $presentation)
    {
        // Ensure user can only upload to their own presentations
        if ($presentation->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:video,audio',
            'media' => 'required|file|max:500000',
        ]);

        $type = $validated['type'];
        $directory = $type === 'video' ? 'videos' : 'audio';
        $pathField = $type === 'video' ? 'video_path' : 'audio_path';

        // Delete old file if exists
        if ($presentation->$pathField) {
            Storage::disk('public')->delete($presentation->$pathField);
        }

        $presentation->$pathField = $request->file('media')->store($directory, 'public');
        $presentation->save();

        return response()->json([
            'success' => true,
            'path' => $presentation->$pathField,
        ]);
    }
}
