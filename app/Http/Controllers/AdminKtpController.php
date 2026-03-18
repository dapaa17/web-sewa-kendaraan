<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminKtpController extends Controller
{
    /**
     * Display list of KTP verifications.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        $search = trim((string) $request->query('search', ''));

        if (! in_array($status, ['pending', 'verified', 'rejected'], true)) {
            $status = 'pending';
        }
        
        $query = User::where('role', 'customer')
            ->whereNotNull('ktp_image');
        
        if ($status === 'pending') {
            $query->where('ktp_status', 'pending');
        } elseif ($status === 'verified') {
            $query->where('ktp_status', 'verified');
        } elseif ($status === 'rejected') {
            $query->where('ktp_status', 'rejected');
        }

        if ($search !== '') {
            $query->where(function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('ktp_number', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->latest()->paginate(10)->withQueryString();
        
        // Get counts
        $counts = [
            'all' => User::where('role', 'customer')->whereNotNull('ktp_image')->count(),
            'pending' => User::where('role', 'customer')->whereNotNull('ktp_image')->where('ktp_status', 'pending')->count(),
            'verified' => User::where('role', 'customer')->whereNotNull('ktp_image')->where('ktp_status', 'verified')->count(),
            'rejected' => User::where('role', 'customer')->whereNotNull('ktp_image')->where('ktp_status', 'rejected')->count(),
        ];
        
        return view('admin.ktp.index', compact('users', 'counts', 'status', 'search'));
    }

    /**
     * Show KTP detail for verification.
     */
    public function show(User $user): View
    {
        return view('admin.ktp.show', compact('user'));
    }

    /**
     * Verify or reject KTP.
     */
    public function verify(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:verify,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        if ($request->action === 'verify') {
            $user->update([
                'ktp_status' => 'verified',
                'ktp_verified_at' => now(),
                'ktp_rejection_reason' => null,
            ]);
            
            return redirect()->route('admin.ktp.index')
                ->with('success', "KTP {$user->name} berhasil diverifikasi!");
        } else {
            $user->update([
                'ktp_status' => 'rejected',
                'ktp_verified_at' => null,
                'ktp_rejection_reason' => $request->rejection_reason,
            ]);
            
            return redirect()->route('admin.ktp.index')
                ->with('success', "KTP {$user->name} ditolak.");
        }
    }
}
