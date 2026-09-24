<?php

namespace App\Http\Controllers;

use App\Models\CopyrightClaimLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CopyrightClaimLinkController extends Controller
{
    /**
     * Customer: Dedicated Copyright Claim Remove Links Page.
     */
    public function index(): View
    {
        $user = Auth::user();
        $user->load('copyrightClaimLinks');
        $totalClaimLinksCount = CopyrightClaimLink::count();

        return view('customer.copyright-links', compact('user', 'totalClaimLinksCount'));
    }

    /**
     * Customer: Upload and save a new copyright claim link.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'url' => 'required|url|max:2048',
        ]);

        $slot = CopyrightClaimLink::nextAvailableSlot();

        if ($slot === null) {
            return back()->with('error', 'All 10 slots are currently occupied. Please delete one of your existing links or wait until a slot becomes available.');
        }

        $link = CopyrightClaimLink::create([
            'user_id' => Auth::id(),
            'slot_number' => $slot,
            'title' => $request->filled('title') ? trim($request->title) : 'Untitled',
            'url' => $request->url,
        ]);

        return back()->with('success', "Link successfully saved and published to Home Page Slot #{$link->slot_number}!");
    }

    /**
     * Customer: Update own link.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $link = CopyrightClaimLink::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'url' => 'required|url|max:2048',
        ]);

        $link->update([
            'title' => $request->filled('title') ? trim($request->title) : 'Untitled',
            'url' => $request->url,
        ]);

        return back()->with('success', "Copyright claim link for Slot #{$link->slot_number} updated successfully!");
    }

    /**
     * Customer: Delete own link.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $link = CopyrightClaimLink::where('user_id', Auth::id())->findOrFail($id);
        $slot = $link->slot_number;
        $link->delete();

        return back()->with('success', "Link removed from Slot #{$slot}. Slot is now available on the Home Page!");
    }

    /**
     * Admin: Create / Upload a link directly from Admin Console.
     */
    public function adminStore(Request $request): RedirectResponse
    {
        if (! Auth::user()?->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'url' => 'required|url|max:2048',
            'slot_number' => 'nullable|integer|between:1,10',
        ]);

        $requestedSlot = $request->filled('slot_number') ? (int) $request->slot_number : null;

        if ($requestedSlot !== null) {
            // If slot is taken, find next available slot for existing link or abort if full
            $existing = CopyrightClaimLink::where('slot_number', $requestedSlot)->first();
            if ($existing) {
                $vacant = CopyrightClaimLink::nextAvailableSlot();
                if ($vacant === null) {
                    return back()
                        ->with('error', "Cannot assign to Slot #{$requestedSlot} because all 10 slots are full.")
                        ->with('active_tab', 'copyright_links');
                }
                $existing->slot_number = $vacant;
                $existing->save();
            }
            $targetSlot = $requestedSlot;
        } else {
            $targetSlot = CopyrightClaimLink::nextAvailableSlot();
            if ($targetSlot === null) {
                return back()
                    ->with('error', 'All 10 slots are currently occupied.')
                    ->with('active_tab', 'copyright_links');
            }
        }

        $link = CopyrightClaimLink::create([
            'user_id' => Auth::id(),
            'slot_number' => $targetSlot,
            'title' => $request->filled('title') ? trim($request->title) : 'Untitled',
            'url' => $request->url,
        ]);

        return back()
            ->with('success', "Link successfully published to Slot #{$link->slot_number}!")
            ->with('active_tab', 'copyright_links');
    }

    /**
     * Admin: Update any customer link (title, url, and serial slot number 1-10).
     */
    public function adminUpdate(Request $request, int|string $id): RedirectResponse
    {
        if (! Auth::user()?->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $link = CopyrightClaimLink::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'url' => 'required|url|max:2048',
            'slot_number' => 'required|integer|between:1,10',
        ]);

        $newSlot = (int) $request->slot_number;
        $oldSlot = $link->slot_number;

        if ($newSlot !== $oldSlot) {
            $existing = CopyrightClaimLink::where('slot_number', $newSlot)->where('id', '!=', $link->id)->first();
            if ($existing) {
                // Swap slot numbers cleanly
                $existing->slot_number = $oldSlot;
                $existing->save();
            }
            $link->slot_number = $newSlot;
        }

        $link->title = $request->filled('title') ? trim($request->title) : 'Untitled';
        $link->url = $request->url;
        $link->save();

        return back()
            ->with('success', "Slot #{$link->slot_number} updated successfully and synchronized with Home Page!")
            ->with('active_tab', 'copyright_links');
    }

    /**
     * Admin: Delete any link.
     */
    public function adminDestroy(int|string $id): RedirectResponse
    {
        if (! Auth::user()?->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $link = CopyrightClaimLink::findOrFail($id);
        $slot = $link->slot_number;
        $link->delete();

        return back()
            ->with('success', "Link from Slot #{$slot} has been deleted. Slot is now free on the Home Page.")
            ->with('active_tab', 'copyright_links');
    }
}
