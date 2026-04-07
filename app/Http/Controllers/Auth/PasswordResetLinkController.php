<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->save();

            return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru Anda.');
        }

        return back()->withInput($request->only('email'))
                     ->withErrors(['email' => 'Email/Username tidak ditemukan.']);
    }
}
