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
            // Delete old cover image
            if ($song->cover_image && Storage::disk('public')->exists($song->cover_image)) {
                Storage::disk('public')->delete($song->cover_image);
            }
            $song->cover_image = $this->processAndStoreCover($request->file('cover_image'));
        }

        if ($request->hasFile('audio_file')) {
            // Delete old audio file
            if ($song->audio_file && Storage::disk('public')->exists($song->audio_file)) {
                Storage::disk('public')->delete($song->audio_file);
            }
            $song->audio_file = $request->file('audio_file')->store('songs/audio', 'public');
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
        if ($song->cover_image && Storage::disk('public')->exists($song->cover_image)) {
            Storage::disk('public')->delete($song->cover_image);
        }
        if ($song->audio_file && Storage::disk('public')->exists($song->audio_file)) {
            Storage::disk('public')->delete($song->audio_file);
        }

        $song->delete();

        return back()->with('success', "Song '{$title}' and its files have been deleted.");
    }

    /**
     * Process cover image and ensure 3000x3000px dimensions using GD, then store in storage/app/public/songs/covers
     */
    public function processAndStoreCover($file): string
    {
        $filename = 'songs/covers/'.Str::random(40).'.jpg';

        $imageContent = file_get_contents($file->getRealPath());
        $src = @imagecreatefromstring($imageContent);

        if ($src !== false) {
            $origW = imagesx($src);
            $origH = imagesy($src);

            ob_start();
            if ($origW === 3000 && $origH === 3000) {
                // Already exact 3000x3000px, stream directly
                imagejpeg($src, null, 92);
            } else {
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
                imagejpeg($target, null, 92);
                imagedestroy($target);
            }
            $streamData = ob_get_clean();
            imagedestroy($src);

            Storage::disk('public')->put($filename, $streamData);

            return $filename;
        }

        // Fallback: normal Laravel store if GD fails to parse
        return $file->store('songs/covers', 'public');
    }
}
