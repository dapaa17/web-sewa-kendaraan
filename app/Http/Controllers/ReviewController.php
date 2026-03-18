<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $status = $request->query('status', 'all');

        if ($user->isAdmin() && ! $request->routeIs('admin.reviews.*')) {
            return redirect()->route('admin.reviews.index', $request->query());
        }

        $query = $user->reviews()->with(['vehicle', 'booking'])->latest();

        if (in_array($status, [Review::STATUS_PENDING, Review::STATUS_APPROVED, Review::STATUS_REJECTED], true)) {
            $query->where('status', $status);
        } else {
            $status = 'all';
        }

        $reviews = $query->paginate(10)->withQueryString();

        $eligibleBookingsQuery = $user->bookings()
            ->with('vehicle')
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->doesntHave('review');

        $counts = [
            'all' => $user->reviews()->count(),
            'pending' => $user->reviews()->pending()->count(),
            'approved' => $user->reviews()->approved()->count(),
            'rejected' => $user->reviews()->rejected()->count(),
            'eligible' => (clone $eligibleBookingsQuery)->count(),
        ];

        $eligibleBookings = $eligibleBookingsQuery
            ->latest('end_date')
            ->limit(6)
            ->get();

        return view('reviews.index', compact('reviews', 'eligibleBookings', 'counts', 'status'));
    }

    public function create(Booking $booking): View
    {
        $booking->loadMissing(['vehicle', 'review']);

        $this->authorize('create', [Review::class, $booking]);

        return view('reviews.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $booking->loadMissing(['vehicle', 'review']);

        $this->authorize('create', [Review::class, $booking]);

        $validated = $request->validate(
            $this->reviewRules(),
            $this->reviewMessages()
        );

        if ($booking->hasReview()) {
            return redirect()->route('bookings.show', $booking)
                ->with('warning', 'Booking ini sudah memiliki review.');
        }

        DB::transaction(function () use ($request, $booking, $validated): void {
            $booking->refresh();

            if ($booking->hasReview()) {
                throw ValidationException::withMessages([
                    'review_text' => 'Booking ini sudah memiliki review.',
                ]);
            }

            Review::create([
                'booking_id' => $booking->id,
                'user_id' => $request->user()->id,
                'vehicle_id' => $booking->vehicle_id,
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'review_text' => $validated['review_text'],
                'status' => Review::STATUS_PENDING,
            ]);
        });

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Review berhasil dikirim dan sedang menunggu moderasi admin.');
    }

    public function edit(Review $review): View
    {
        $review->loadMissing(['vehicle', 'booking']);

        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $review->loadMissing('booking');

        $this->authorize('update', $review);

        $validated = $request->validate(
            $this->reviewRules(),
            $this->reviewMessages()
        );

        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'review_text' => $validated['review_text'],
            'status' => Review::STATUS_PENDING,
            'admin_note' => null,
            'moderated_at' => null,
            'moderated_by' => null,
        ]);

        return redirect()->route('reviews.index')
            ->with('success', 'Review berhasil diperbarui dan dikirim ulang untuk moderasi admin.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        $route = Auth::user()->isAdmin() ? 'admin.reviews.index' : 'reviews.index';
        $message = Auth::user()->isAdmin()
            ? 'Review berhasil dihapus.'
            : 'Review berhasil dihapus. Anda bisa membuat ulang review dari booking yang sama.';

        return redirect()->route($route)->with('success', $message);
    }

    public function toggleHelpful(Review $review): RedirectResponse
    {
        $review->loadMissing('helpfulVotes');

        $this->authorize('markHelpful', $review);

        $userId = Auth::id();
        $existingVote = $review->helpfulVotes()->where('user_id', $userId)->first();

        if ($existingVote) {
            $existingVote->delete();
            $review->decrement('helpful_count');

            return back()->with('success', 'Tanda helpful dihapus dari review ini.');
        }

        $review->helpfulVotes()->create([
            'user_id' => $userId,
        ]);
        $review->increment('helpful_count');

        return back()->with('success', 'Terima kasih, review ini ditandai helpful.');
    }

    public function adminIndex(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));
        $rating = $request->query('rating');

        $query = Review::query()
            ->with(['user', 'vehicle', 'booking', 'moderator'])
            ->latest();

        if (in_array($status, [Review::STATUS_PENDING, Review::STATUS_APPROVED, Review::STATUS_REJECTED], true)) {
            $query->where('status', $status);
        } else {
            $status = 'all';
        }

        if ($search !== '') {
            $query->where(function ($reviewQuery) use ($search) {
                $reviewQuery->where('title', 'like', '%' . $search . '%')
                    ->orWhere('review_text', 'like', '%' . $search . '%')
                    ->orWhereHas('vehicle', fn ($vehicleQuery) => $vehicleQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'));
            });
        }

        if (in_array((int) $rating, [1, 2, 3, 4, 5], true)) {
            $query->where('rating', (int) $rating);
        } else {
            $rating = null;
        }

        $reviews = $query->paginate(12)->withQueryString();

        $counts = [
            'all' => Review::count(),
            'pending' => Review::pending()->count(),
            'approved' => Review::approved()->count(),
            'rejected' => Review::rejected()->count(),
        ];

        return view('reviews.admin-index', compact('reviews', 'counts', 'status', 'search', 'rating'));
    }

    public function approve(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('moderate', $review);

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'admin_note.max' => 'Catatan admin maksimal 1000 karakter.',
        ]);

        $review->update([
            'status' => Review::STATUS_APPROVED,
            'admin_note' => $validated['admin_note'] ?? null,
            'moderated_at' => now(),
            'moderated_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Review berhasil disetujui dan tampil di halaman kendaraan.');
    }

    public function reject(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('moderate', $review);

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'admin_note.required' => 'Alasan penolakan wajib diisi.',
            'admin_note.min' => 'Alasan penolakan minimal 5 karakter.',
            'admin_note.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $review->update([
            'status' => Review::STATUS_REJECTED,
            'admin_note' => $validated['admin_note'],
            'moderated_at' => now(),
            'moderated_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Review ditolak. Customer masih bisa mengedit dan mengirim ulang review ini.');
    }

    /**
     * Base validation rules for customer review forms.
     *
     * @return array<string, array<int, string>>
     */
    private function reviewRules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['required', 'string', 'max:255'],
            'review_text' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    /**
     * Friendly validation messages for customer review forms.
     *
     * @return array<string, string>
     */
    private function reviewMessages(): array
    {
        return [
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating harus berupa angka bulat.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'title.required' => 'Judul review wajib diisi.',
            'title.max' => 'Judul review maksimal 255 karakter.',
            'review_text.required' => 'Isi review wajib diisi.',
            'review_text.min' => 'Isi review minimal 10 karakter.',
            'review_text.max' => 'Isi review maksimal 1000 karakter.',
        ];
    }
}