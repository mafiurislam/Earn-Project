<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SongController extends Controller
{
    /**
     * Customer: Dedicated Upload Song & Management Page.
     */
    public function index(): View
    {
        $user = Auth::user();
        $user->load('songs');

        return view('customer.songs', compact('user'));
    }

    /**
     * Store a new song uploaded by customer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'singer' => 'required|string|max:255',
            'composer' => 'required|string|max:255',
            'producer' => 'required|string|max:255',
            'cover_image' => 'required|file|mimes:jpeg,png,jpg,webp|max:20480',
            'audio_file' => 'required|file|mimes:mp3,wav,ogg,aac|max:61440',
        ], [
            'cover_image.required' => 'Please upload a 3000 × 3000 px wallpaper or cover image.',
            'cover_image.mimes' => 'Cover image must be a JPEG, PNG, JPG, or WEBP file.',
            'audio_file.required' => 'Please upload an MP3 song file.',
            'audio_file.mimes' => 'The audio file must be in MP3 format.',
        ]);

        $coverPath = $this->processAndStoreCover($request->file('cover_image'));
        $audioPath = $request->file('audio_file')->store('songs/audio', 'public');
        Song::syncToPublic($coverPath);
        Song::syncToPublic($audioPath);

        $song = Song::create([
            'user_id' => Auth::id(),
            'title' => trim($request->title),
            'singer' => trim($request->singer),
            'composer' => trim($request->composer),
            'producer' => trim($request->producer),
            'copyright' => $request->filled('copyright') ? trim($request->copyright) : '℗ 2026 Rajdoot Nivedan',
            'cover_image' => $coverPath,
            'audio_file' => $audioPath,
            'status' => 'active',
        ]);

        return back()->with('success', "Song '{$song->title}' uploaded successfully with 3000 × 3000 px cover artwork and MP3 file!");
    }

    /**
     * Update an existing song for the customer.
     */
    public function update(Request $request, $id)
    {
        $song = Song::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'singer' => 'required|string|max:255',
            'composer' => 'required|string|max:255',
            'producer' => 'required|string|max:255',
            'cover_image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:20480',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,aac|max:61440',
        ]);

        $song->title = trim($request->title);
        $song->singer = trim($request->singer);
        $song->composer = trim($request->composer);
        $song->producer = trim($request->producer);
        if ($request->filled('copyright')) {
            $song->copyright = trim($request->copyright);
        }

        if ($request->hasFile('cover_image')) {
            Song::deleteStorageFile($song->cover_image);
            $song->cover_image = $this->processAndStoreCover($request->file('cover_image'));
            Song::syncToPublic($song->cover_image);
        }

        if ($request->hasFile('audio_file')) {
            Song::deleteStorageFile($song->audio_file);
            $song->audio_file = $request->file('audio_file')->store('songs/audio', 'public');
            Song::syncToPublic($song->audio_file);
        }

        $song->save();

        return back()->with('success', "Song '{$song->title}' details updated successfully!");
    }

    /**
     * Delete an uploaded song.
     */
    public function destroy($id)
    {
        $song = Song::where('user_id', Auth::id())->findOrFail($id);
        $title = $song->title;

        // Clean up storage files
        Song::deleteStorageFile($song->cover_image);
        Song::deleteStorageFile($song->audio_file);

        $song->delete();

        return back()->with('success', "Song '{$title}' and its files have been deleted.");
    }

    /**
     * Download the exact customer uploaded MP3 audio file.
     */
    public function download($id)
    {
        $song = Song::with('user')->findOrFail($id);

        // Security check: only admin or the song owner can download
        if (! Auth::user()->is_admin && $song->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this audio file.');
        }

        $filePath = null;
        if (Storage::disk('public')->exists($song->audio_file)) {
            $filePath = Storage::disk('public')->path($song->audio_file);
        } elseif (file_exists(storage_path('app/public/'.$song->audio_file))) {
            $filePath = storage_path('app/public/'.$song->audio_file);
        } elseif (file_exists(public_path('storage/'.$song->audio_file))) {
            $filePath = public_path('storage/'.$song->audio_file);
        }

        if (! $filePath || ! file_exists($filePath)) {
            abort(404, 'Audio file not found on server.');
        }

        $downloadName = Str::slug($song->title ?: 'song').'.mp3';

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'audio/mpeg',
        ]);
    }

    /**
     * Download the exact customer uploaded cover image.
     */
    public function downloadCover($id)
    {
        $song = Song::with('user')->findOrFail($id);

        if (! Auth::user()->is_admin && $song->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this cover image.');
        }

        $filePath = null;
        $clean = ltrim(str_replace('storage/', '', $song->cover_image ?? ''), '/\\');

        if (! empty($clean)) {
            if (Storage::disk('public')->exists($clean)) {
                $filePath = Storage::disk('public')->path($clean);
            } elseif (file_exists(storage_path('app/public/'.$clean))) {
                $filePath = storage_path('app/public/'.$clean);
            } elseif (file_exists(public_path('storage/'.$clean))) {
                $filePath = public_path('storage/'.$clean);
            }
        }

        if (! $filePath || ! file_exists($filePath)) {
            abort(404, 'Cover image file not found on server.');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
        $downloadName = Str::slug($song->title ?: 'cover').'-cover.'.$extension;

        return response()->download($filePath, $downloadName, [
            'Content-Type' => mime_content_type($filePath) ?: 'image/jpeg',
        ]);
    }

    /**
     * Process cover image and ensure 3000x3000px dimensions using GD, then store in storage/app/public/songs/covers
     */
    public function processAndStoreCover($file): string
    {
        @ini_set('memory_limit', '256M');

        $filename = 'songs/covers/'.Str::random(40).'.jpg';

        $imageContent = file_get_contents($file->getRealPath());
        $src = @imagecreatefromstring($imageContent);

        if ($src !== false) {
            $origW = imagesx($src);
            $origH = imagesy($src);

            if ($origW === 3000 && $origH === 3000) {
                // Already exact 3000x3000px, store original file directly to preserve 100% quality
                imagedestroy($src);
                $storedPath = $file->store('songs/covers', 'public');
                Song::syncToPublic($storedPath);

                return $storedPath;
            }

            // Resample and fit to exact 3000x3000px high resolution
            $target = imagecreatetruecolor(3000, 3000);

            // Fill with dark background before copying
            $bg = imagecolorallocate($target, 10, 16, 29);
            imagefill($target, 0, 0, $bg);

            // Center crop or fit
            $minDim = min($origW, $origH);
            $cropX = (int) (($origW - $minDim) / 2);
            $cropY = (int) (($origH - $minDim) / 2);

            imagecopyresampled($target, $src, 0, 0, $cropX, $cropY, 3000, 3000, $minDim, $minDim);

            ob_start();
            imagejpeg($target, null, 95);
            $streamData = ob_get_clean();

            imagedestroy($target);
            imagedestroy($src);

            Storage::disk('public')->put($filename, $streamData);
            Song::syncToPublic($filename);

            return $filename;
        }

        // Fallback: normal Laravel store if GD fails to parse
        $fallbackPath = $file->store('songs/covers', 'public');
        Song::syncToPublic($fallbackPath);

        return $fallbackPath;
    }
}
