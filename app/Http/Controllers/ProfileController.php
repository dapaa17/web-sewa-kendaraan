<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display KTP verification form.
     */
    public function showKtp(Request $request): View
    {
        return view('profile.ktp', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Upload KTP image.
     */
    public function uploadKtp(Request $request): RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            return Redirect::route('profile.ktp')
                ->with('warning', 'Akun admin tidak memerlukan verifikasi KTP.');
        }

        $request->validate([
            'ktp_number' => 'required|string|size:16|regex:/^[0-9]+$/',
            'ktp_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'ktp_number.required' => 'Nomor KTP wajib diisi.',
            'ktp_number.size' => 'Nomor KTP harus 16 digit.',
            'ktp_number.regex' => 'Nomor KTP hanya boleh berisi angka.',
            'ktp_image.required' => 'Foto KTP wajib diupload.',
            'ktp_image.image' => 'File harus berupa gambar.',
            'ktp_image.mimes' => 'Format gambar harus JPEG, PNG, atau JPG.',
            'ktp_image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $user = $request->user();

        // Delete old KTP image if exists
        if ($user->ktp_image) {
            Storage::disk('public')->delete($user->ktp_image);
        }

        // Store new KTP image
        $path = $request->file('ktp_image')->store('ktp', 'public');

        $user->update([
            'ktp_number' => $request->ktp_number,
            'ktp_image' => $path,
            'ktp_status' => 'pending',
            'ktp_verified_at' => null,
            'ktp_rejection_reason' => null,
        ]);

        return Redirect::route('profile.ktp')->with('success', 'KTP berhasil diupload! Menunggu verifikasi admin.');
    }
}
