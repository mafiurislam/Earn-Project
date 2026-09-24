<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminCustomerController extends Controller
{
    /**
     * Create a new customer profile from Admin Console.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'earning_balance' => 'nullable|numeric|min:0',
            'total_earnings' => 'nullable|numeric|min:0',
        ]);

        $balance = $request->filled('earning_balance') ? (float) $request->earning_balance : 0.00;
        $totalEarnings = $request->filled('total_earnings') ? (float) $request->total_earnings : $balance;

        $customer = User::create([
            'name' => trim($request->name),
            'username' => $request->filled('username') ? trim($request->username) : null,
            'email' => strtolower(trim($request->email)),
            'phone' => $request->filled('phone') ? trim($request->phone) : null,
            'password' => Hash::make($request->password),
            'earning_balance' => $balance,
            'total_earnings' => $totalEarnings,
            'is_admin' => false,
        ]);

        if ($balance > 0) {
            $customer->earningTransactions()->create([
                'admin_id' => Auth::id(),
                'type' => 'credit',
                'amount' => $balance,
                'balance_before' => 0.00,
                'balance_after' => $balance,
                'note' => 'Initial balance assigned during customer creation by Admin',
            ]);
        }

        return redirect()->route('admin.customers.show', $customer->id)
            ->with('success', "Customer profile for '{$customer->name}' created successfully!");
    }

    /**
     * Display an individual customer's comprehensive profile.
     */
    public function show($id)
    {
        $customer = User::where('is_admin', false)
            ->with([
                'songs',
                'verification',
                'profileInfo',
                'copyrightClaimLinks',
                'withdrawals' => function ($q) {
                    $q->latest();
                },
                'earningTransactions' => function ($q) {
                    $q->latest();
                },
            ])
            ->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Update customer profile details.
     */
    public function update(Request $request, $id)
    {
        $customer = User::where('is_admin', false)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,'.$customer->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$customer->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $customer->name = trim($request->name);
        $customer->username = $request->filled('username') ? trim($request->username) : null;
        $customer->email = strtolower(trim($request->email));
        $customer->phone = $request->filled('phone') ? trim($request->phone) : null;

        if ($request->filled('password')) {
            $customer->password = Hash::make($request->password);
        }

        $customer->save();

        return back()->with('success', "Customer profile for '{$customer->name}' updated successfully!");
    }

    /**
     * Delete customer profile and all related files and records.
     */
    public function destroy($id)
    {
        $customer = User::where('is_admin', false)
            ->with(['songs', 'verification'])
            ->findOrFail($id);

        $customerName = $customer->name;

        // Delete all song files from storage
        foreach ($customer->songs as $song) {
            if ($song->cover_image && Storage::disk('public')->exists($song->cover_image)) {
                Storage::disk('public')->delete($song->cover_image);
            }
            if ($song->audio_file && Storage::disk('public')->exists($song->audio_file)) {
                Storage::disk('public')->delete($song->audio_file);
            }
        }

        // Delete verification documents
        if ($customer->verification) {
            if ($customer->verification->pan_card_photo && Storage::disk('public')->exists($customer->verification->pan_card_photo)) {
                Storage::disk('public')->delete($customer->verification->pan_card_photo);
            }
            if ($customer->verification->signature_photo && Storage::disk('public')->exists($customer->verification->signature_photo)) {
                Storage::disk('public')->delete($customer->verification->signature_photo);
            }
        }

        // Delete profile photo
        if ($customer->profile_photo && Storage::disk('public')->exists($customer->profile_photo)) {
            Storage::disk('public')->delete($customer->profile_photo);
        }

        $customer->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', "Customer profile for '{$customerName}' and all associated songs and files were permanently deleted.");
    }

    /**
     * Admin adds/uploads a song for this specific customer.
     */
    public function storeSong(Request $request, $id)
    {
        $customer = User::where('is_admin', false)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'singer' => 'required|string|max:255',
            'composer' => 'required|string|max:255',
            'producer' => 'required|string|max:255',
            'cover_image' => 'required|file|mimes:jpeg,png,jpg,webp|max:20480',
            'audio_file' => 'required|file|mimes:mp3,wav,ogg,aac|max:61440',
        ]);

        $songController = new SongController;
        $coverPath = $songController->processAndStoreCover($request->file('cover_image'));
        $audioPath = $request->file('audio_file')->store('songs/audio', 'public');

        $song = Song::create([
            'user_id' => $customer->id,
            'title' => trim($request->title),
            'singer' => trim($request->singer),
            'composer' => trim($request->composer),
            'producer' => trim($request->producer),
            'copyright' => $request->filled('copyright') ? trim($request->copyright) : '℗ 2026 Rajdoot Nivedan',
            'cover_image' => $coverPath,
            'audio_file' => $audioPath,
            'status' => 'active',
            'admin_notes' => 'Uploaded by Admin',
        ]);

        return back()->with('success', "Song '{$song->title}' uploaded successfully for customer {$customer->name}!");
    }

    /**
     * Admin updates an existing song.
     */
    public function updateSong(Request $request, $songId)
    {
        $song = Song::with('user')->findOrFail($songId);

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
            if ($song->cover_image && Storage::disk('public')->exists($song->cover_image)) {
                Storage::disk('public')->delete($song->cover_image);
            }
            $songController = new SongController;
            $song->cover_image = $songController->processAndStoreCover($request->file('cover_image'));
        }

        if ($request->hasFile('audio_file')) {
            if ($song->audio_file && Storage::disk('public')->exists($song->audio_file)) {
                Storage::disk('public')->delete($song->audio_file);
            }
            $song->audio_file = $request->file('audio_file')->store('songs/audio', 'public');
        }

        $song->save();

        return back()->with('success', "Song '{$song->title}' updated successfully by Admin!");
    }

    /**
     * Admin deletes a customer's song.
     */
    public function destroySong($songId)
    {
        $song = Song::with('user')->findOrFail($songId);
        $title = $song->title;

        if ($song->cover_image && Storage::disk('public')->exists($song->cover_image)) {
            Storage::disk('public')->delete($song->cover_image);
        }
        if ($song->audio_file && Storage::disk('public')->exists($song->audio_file)) {
            Storage::disk('public')->delete($song->audio_file);
        }

        $song->delete();

        return back()->with('success', "Song '{$title}' has been deleted by Admin.");
    }
}
