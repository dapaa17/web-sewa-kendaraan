@php
    $currentRating = (int) old('rating', ($review ?? null)?->rating ?? 0);
    $ratingLabels = [
        1 => 'Kurang puas',
        2 => 'Masih kurang',
        3 => 'Cukup oke',
        4 => 'Bagus',
        5 => 'Mantap sekali',
    ];
@endphp

@once
    <style>
        .rv-form-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(203,213,225,0.78);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-card);
            padding: 1.8rem;
        }
        .rv-form-grid {
            display: grid;
            gap: 1.4rem;
        }
        .rv-field label {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.65rem;
            display: block;
        }
        .rv-star-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 0.35rem;
        }
        .rv-star-input input {
            display: none;
        }
        .rv-star-input label {
            font-size: clamp(2rem, 5vw, 2.6rem);
            line-height: 1;
            cursor: pointer;
            filter: grayscale(1);
            opacity: 0.35;
            transition: transform 0.2s ease, opacity 0.2s ease, filter 0.2s ease;
            margin: 0;
        }
        .rv-star-input label:hover,
        .rv-star-input label:hover ~ label,
        .rv-star-input input:checked ~ label {
            filter: grayscale(0);
            opacity: 1;
            transform: translateY(-2px);
        }
        .rv-rating-hint {
            margin-top: 0.65rem;
            color: #64748b;
            font-size: 0.92rem;
        }
        .rv-form-card .form-control {
            border-radius: 1rem;
            border: 1px solid rgba(148,163,184,0.45);
            padding: 0.85rem 1rem;
        }
        .rv-form-card .form-control:focus {
            border-color: var(--color-secondary);
            box-shadow: 0 0 0 0.25rem rgba(var(--color-secondary-rgb), 0.16);
        }
    </style>
@endonce

<div class="rv-form-card">
    <div class="rv-form-grid">
        <div class="rv-field">
            <label>Rating Anda</label>
            <div class="rv-star-input" data-rating-input>
                @for($rating = 5; $rating >= 1; $rating--)
                    <input type="radio" name="rating" id="rating-{{ $rating }}" value="{{ $rating }}" @checked($currentRating === $rating)>
                    <label for="rating-{{ $rating }}" title="{{ $rating }} bintang">⭐</label>
                @endfor
            </div>
            <div class="rv-rating-hint" data-rating-hint>
                {{ $currentRating > 0 ? ($ratingLabels[$currentRating] ?? 'Pilih rating') : 'Pilih 1 sampai 5 bintang untuk kendaraan ini.' }}
            </div>
            @error('rating')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="rv-field">
            <label for="title">Judul Review</label>
            <input
                type="text"
                id="title"
                name="title"
                class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', ($review ?? null)?->title ?? '') }}"
                maxlength="255"
                placeholder="Contoh: Unit bersih, proses pickup cepat"
                required
            >
            @error('title')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="rv-field">
            <label for="review_text">Isi Review</label>
            <textarea
                id="review_text"
                name="review_text"
                rows="6"
                class="form-control @error('review_text') is-invalid @enderror"
                placeholder="Ceritakan pengalaman Anda memakai kendaraan ini, kondisi unit, dan proses layanannya."
                required
            >{{ old('review_text', ($review ?? null)?->review_text ?? '') }}</textarea>
            <div class="form-text">Minimal 10 karakter, maksimal 1000 karakter. Review akan dicek admin sebelum tampil.</div>
            @error('review_text')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ratingLabels = {
                1: 'Kurang puas',
                2: 'Masih kurang',
                3: 'Cukup oke',
                4: 'Bagus',
                5: 'Mantap sekali',
            };

            document.querySelectorAll('[data-rating-input]').forEach(function (wrapper) {
                const hint = wrapper.parentElement.querySelector('[data-rating-hint]');

                function syncHint() {
                    const selected = wrapper.querySelector('input:checked');
                    if (!hint) {
                        return;
                    }

                    hint.textContent = selected
                        ? ratingLabels[selected.value] || 'Rating dipilih.'
                        : 'Pilih 1 sampai 5 bintang untuk kendaraan ini.';
                }

                wrapper.querySelectorAll('input').forEach(function (input) {
                    input.addEventListener('change', syncHint);
                });

                syncHint();
            });
        });
    </script>
@endonce