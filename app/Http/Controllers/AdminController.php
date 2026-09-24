<?php

namespace App\Http\Controllers;

use App\Models\CopyrightClaimLink;
use App\Models\Song;
use App\Models\User;
use App\Models\Verification;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $stats = [
            'total_customers' => User::where('is_admin', false)->count(),
            'total_songs' => Song::count(),
            'pending_verifications' => Verification::where('status', 'pending')->count(),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
            'total_earnings_distributed' => User::where('is_admin', false)->sum('total_earnings'),
            'total_available_balance' => User::where('is_admin', false)->sum('earning_balance'),
            'total_withdrawn_amount' => Withdrawal::where('status', 'approved')->sum('amount'),
            'pending_withdrawals_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
            'total_claim_links' => CopyrightClaimLink::count(),
        ];

        // Customer query with optional search
        $search = $request->input('search');
        $customerQuery = User::where('is_admin', false)
            ->with(['verification', 'profileInfo', 'withdrawals', 'songs', 'earningTransactions' => function ($q) {
                $q->latest()->limit(10);
            }]);

        if (! empty($search)) {
            $customerQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $customerQuery
            ->orderByRaw("CASE 
                WHEN username = 'anowora' THEN 1 
                WHEN username = 'ta' THEN 2 
                WHEN username = 'mafiurislam' THEN 3 
                WHEN username = 'simrantaufik' THEN 4 
                ELSE 5 END, id ASC")
            ->paginate(20, ['*'], 'customers_page');

        $pendingVerifications = Verification::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        $pendingWithdrawals = Withdrawal::where('status', 'pending')
            ->with(['user', 'user.verification'])
            ->latest()
            ->get();

        // Withdrawal status filter
        $withdrawalStatus = $request->input('withdrawal_status');
        $withdrawalQuery = Withdrawal::with(['user', 'user.verification'])->latest();
        if (! empty($withdrawalStatus) && in_array($withdrawalStatus, ['pending', 'approved', 'rejected'])) {
            $withdrawalQuery->where('status', $withdrawalStatus);
        }

        $allWithdrawals = $withdrawalQuery->paginate(20, ['*'], 'withdrawals_page');

        $allClaimLinks = CopyrightClaimLink::with('user')
            ->orderBy('slot_number', 'asc')
            ->get();

        $activeTab = session('active_tab', $request->input('tab', 'earnings'));

        return view('admin.dashboard', compact(
            'stats',
            'users',
            'pendingVerifications',
            'pendingWithdrawals',
            'allWithdrawals',
            'allClaimLinks',
            'activeTab',
            'search',
            'withdrawalStatus'
        ));
    }

    public function increaseEarnings(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::where('is_admin', false)->findOrFail($id);
        $amount = (float) $request->amount;
        $note = $request->note ? trim($request->note) : 'Manual earnings credit by Admin';

        $user->increaseEarnings($amount, $note, Auth::id());

        return back()
            ->with('success', 'Successfully increased earnings by ₹'.number_format($amount, 2)." for {$user->name}!")
            ->with('active_tab', 'earnings');
    }

    public function decreaseEarnings(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::where('is_admin', false)->findOrFail($id);
        $amount = (float) $request->amount;

        if ($amount > (float) $user->earning_balance) {
            return back()
                ->with('error', 'Cannot decrease ₹'.number_format($amount, 2).". Current available balance for {$user->name} is only ₹".number_format($user->earning_balance, 2).'!')
                ->with('active_tab', 'earnings');
        }

        $note = $request->note ? trim($request->note) : 'Manual earnings deduction by Admin';
        $user->decreaseEarnings($amount, $note, Auth::id());

        return back()
            ->with('success', 'Successfully deducted ₹'.number_format($amount, 2)." from {$user->name}'s earnings.")
            ->with('active_tab', 'earnings');
    }

    public function updateEarnings(Request $request, $id)
    {
        $request->validate([
            'earning_balance' => 'required|numeric|min:0',
            'total_earnings' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $user = User::where('is_admin', false)->findOrFail($id);
        $newBalance = (float) $request->earning_balance;
        $newTotal = $request->filled('total_earnings') ? (float) $request->total_earnings : null;
        $note = $request->note ? trim($request->note) : 'Direct earnings update by Admin';

        $user->updateEarningsDirect($newBalance, $newTotal, $note, Auth::id());

        return back()
            ->with('success', "Earnings updated for {$user->name}: Available Balance = ₹".number_format($user->earning_balance, 2).', Total Earnings = ₹'.number_format($user->total_earnings, 2))
            ->with('active_tab', 'earnings');
    }

    public function approveVerification($id)
    {
        $verification = Verification::findOrFail($id);
        $verification->status = 'approved';
        $verification->rejection_reason = null;
        $verification->save();

        return back()
            ->with('success', "Profile Verification approved for customer {$verification->full_name}!")
            ->with('active_tab', 'verifications');
    }

    public function rejectVerification(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $verification = Verification::findOrFail($id);
        $verification->status = 'rejected';
        $verification->rejection_reason = $request->rejection_reason;
        $verification->save();

        return back()
            ->with('info', "Profile Verification rejected for customer {$verification->full_name}. Customer notified.")
            ->with('active_tab', 'verifications');
    }

    public function approveWithdrawal(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $adminNote = $request->filled('admin_note')
            ? trim($request->admin_note)
            : 'Approved and processed payout via Bank Transfer';

        $withdrawal->status = 'approved';
        $withdrawal->admin_note = $adminNote;
        $withdrawal->save();

        return back()
            ->with('success', "Withdrawal request #WD-{$withdrawal->id} of ₹".number_format($withdrawal->amount, 2).' has been approved and processed!')
            ->with('active_tab', 'withdrawals');
    }

    public function rejectWithdrawal(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        $withdrawal = Withdrawal::findOrFail($id);
        $user = $withdrawal->user;

        if ($withdrawal->status === 'pending') {
            // Refund amount back to customer balance
            $before = (float) $user->earning_balance;
            $after = $before + (float) $withdrawal->amount;
            $user->earning_balance = $after;
            $user->save();

            // Record transaction refund log
            $user->earningTransactions()->create([
                'admin_id' => Auth::id(),
                'type' => 'credit',
                'amount' => $withdrawal->amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'note' => "Refund for rejected withdrawal #WD-{$withdrawal->id}: ".$request->admin_note,
            ]);
        }

        $withdrawal->status = 'rejected';
        $withdrawal->admin_note = $request->admin_note;
        $withdrawal->save();

        return back()
            ->with('info', "Withdrawal request #WD-{$withdrawal->id} rejected. ₹".number_format($withdrawal->amount, 2)." has been refunded back to {$user->name}'s available balance.")
            ->with('active_tab', 'withdrawals');
    }

    public function updateCustomerBalance(Request $request, $id)
    {
        return $this->updateEarnings($request, $id);
    }
}
