@php($usesAdminLayout = (bool) auth()->user()?->isAdmin())
@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.app')

@section('title', 'Booking - ' . $vehicle->name)
@if($usesAdminLayout)
@section('page-title', 'Form Booking')
@endif

@section('css')
<style>
    .bk-header{background:var(--gradient-brand);color:#fff;padding:3rem 0 5.5rem;position:relative;overflow:hidden}
    .bk-header::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%),radial-gradient(circle at 10% 75%,rgba(255,255,255,.05),transparent 45%);pointer-events:none}
    .bk-header h1{font-weight:800;font-size:1.85rem;letter-spacing:-.06em;margin:0}
    .bk-header .subtitle{opacity:.7;font-size:.92rem;margin-top:.35rem}
    .bk-back{display:inline-flex;align-items:center;gap:.4rem;color:rgba(255,255,255,.7);text-decoration:none;font-size:.85rem;font-weight:500;margin-bottom:.75rem;transition:color .2s}
    .bk-back:hover{color:#fff}
    .bk-body{margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem}

    /* Cards */
    .bk-card{background:#fff;border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.07);border:1px solid rgba(203,213,225,.45);overflow:hidden;margin-bottom:1.25rem}
    .bk-card-head{padding:1rem 1.5rem;border-bottom:1px solid rgba(203,213,225,.35);display:flex;align-items:center;gap:.6rem}
    .bk-card-head i{color:var(--color-secondary);font-size:1rem}
    .bk-card-head h2{font-size:.95rem;font-weight:700;margin:0;color:#0f172a;letter-spacing:-.03em}
    .bk-card-body{padding:1.5rem}

    /* Form */
    .bk-group{margin-bottom:1.25rem}
    .bk-label{display:block;font-size:.82rem;font-weight:700;color:#334155;margin-bottom:.4rem;letter-spacing:.02em}
    .bk-control{display:block;width:100%;padding:.65rem .9rem;border:1.5px solid rgba(203,213,225,.8);border-radius:.75rem;font-size:.88rem;color:#0f172a;background:#fff;transition:all .2s}
    .bk-control:focus{outline:none;border-color:var(--color-secondary);box-shadow:0 0 0 3px rgba(6,182,212,.12)}
    .bk-control.is-invalid{border-color:#ef4444}
    .bk-hint{font-size:.78rem;color:#94a3b8;margin-top:.35rem;display:flex;align-items:flex-start;gap:.35rem}
    .bk-hint i{margin-top:1px;flex-shrink:0}

    /* Time row */
    .bk-time-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    @media(max-width:575px){.bk-time-row{grid-template-columns:1fr}}

    /* Submit */
    .bk-submit{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border:none;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:700;font-size:.92rem;cursor:pointer;transition:all .25s}
    .bk-submit:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 8px 24px rgba(15,23,42,.15)}
    .bk-submit:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}

    /* Vehicle preview */
    .vehicle-preview-media,
    .vehicle-preview-placeholder{width:100%;height:220px;border-radius:.85rem;margin-bottom:1rem}
    .vehicle-preview-media{object-fit:cover;display:block}
    .vehicle-preview-placeholder{background:radial-gradient(circle at top,rgba(255,255,255,.14),transparent 38%),var(--gradient-brand);display:flex;align-items:center;justify-content:center}
    .vehicle-preview-placeholder i{font-size:4rem;color:#fff;filter:drop-shadow(0 12px 24px rgba(15,23,42,.24))}

    /* Vehicle specs */
    .bk-specs{list-style:none;padding:0;margin:0}
    .bk-specs li{display:flex;align-items:center;gap:.5rem;padding:.5rem 0;border-bottom:1px solid rgba(203,213,225,.35);font-size:.86rem;color:#334155}
    .bk-specs li:last-child{border-bottom:none}
    .bk-specs li i{color:#94a3b8;width:1rem;text-align:center;flex-shrink:0}

    /* Price summary */
    .bk-summary{background:linear-gradient(135deg,rgba(248,250,252,.9) 0%,rgba(6,182,212,.06) 100%);border:1px solid rgba(203,213,225,.55);border-radius:1rem;padding:1.25rem 1.5rem}
    .bk-summary-row{display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid rgba(203,213,225,.4);font-size:.88rem;color:#475569}
    .bk-summary-row:last-child{border-bottom:none}
    .bk-summary-row strong{color:#0f172a}
    .bk-summary-total{font-size:1.5rem;font-weight:800;color:var(--color-primary);letter-spacing:-.04em}
    .bk-breakdown{display:grid;gap:.55rem;margin-top:1rem}
    .bk-breakdown-row{display:flex;justify-content:space-between;align-items:center;gap:.75rem;padding:.7rem .8rem;border-radius:.8rem;background:rgba(255,255,255,.85);border:1px solid rgba(203,213,225,.45);font-size:.82rem;color:#334155}
    .bk-breakdown-row strong{color:#0f172a}

    /* Alerts */
    .bk-alert{border-radius:.85rem;border:none;padding:.85rem 1.15rem;font-size:.86rem;margin-bottom:1rem}
    .bk-alert.warn{background:rgba(245,158,11,.08);color:#92400e;border-left:3px solid #f59e0b}
    .bk-alert.danger{background:rgba(239,68,68,.06);color:#dc2626;border-left:3px solid #ef4444}
    .bk-alert.info{background:rgba(6,182,212,.06);color:#0e7490;border-left:3px solid #06b6d4}

    @media(max-width:767px){
        .bk-header{padding:2rem 0 4.5rem}
        .bk-header h1{font-size:1.4rem}
        .bk-body{margin-top:-2.5rem}
    }
</style>
@endsection

@section('content')
{{-- ── Header ── --}}
<div class="bk-header">
    <div class="container">
        <a href="{{ route('vehicles.show', $vehicle) }}" class="bk-back"><i class="bi bi-arrow-left"></i> Kembali</a>
        <h1><i class="bi bi-calendar-check me-2" style="font-size:1.5rem"></i>Booking Kendaraan</h1>
        <p class="subtitle mb-0">{{ $vehicle->name }} — {{ $vehicle->plat_number }}</p>
    </div>
</div>

{{-- ── Body ── --}}
<div class="bk-body">
    <div class="container">
        <div class="row g-4">
            {{-- Left: Form --}}
            <div class="col-lg-6">
                <div class="bk-card">
                    <div class="bk-card-head">
                        <i class="bi bi-calendar3"></i>
                        <h2>Pilih Jadwal Sewa</h2>
                    </div>
                    <div class="bk-card-body">
                        <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                            <div class="bk-group">
                                <label for="start_date" class="bk-label">Tanggal Mulai Sewa</label>
                                <input type="date" class="bk-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date') }}"
                                       min="{{ date('Y-m-d') }}" required>
                                @error('start_date')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>

                            <div class="bk-group">
                                <label for="end_date" class="bk-label">Tanggal Selesai Sewa</label>
                                <input type="date" class="bk-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}"
                                       min="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback" id="dateRangeFeedback" style="display: none;">
                                    Tanggal selesai tidak boleh sebelum tanggal mulai.
                                </div>
                                @error('end_date')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                            </div>

                            <div class="bk-time-row">
                                <div class="bk-group">
                                    <label for="pickup_time" class="bk-label">Jam Ambil</label>
                                    <input type="time" class="bk-control @error('pickup_time') is-invalid @enderror"
                                           id="pickup_time" name="pickup_time" value="{{ old('pickup_time', $bookingScheduleDefaults['pickup_time']) }}" required>
                                    @error('pickup_time')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                                </div>
                                <div class="bk-group">
                                    <label for="return_time" class="bk-label">Jam Kembali</label>
                                    <input type="time" class="bk-control @error('return_time') is-invalid @enderror"
                                           id="return_time" name="return_time" value="{{ old('return_time', $bookingScheduleDefaults['return_time']) }}" required>
                                    @error('return_time')<div class="text-danger" style="font-size:.8rem;margin-top:.35rem">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="bk-hint mb-3">
                                <i class="bi bi-info-circle"></i>
                                <span>Tanggal selesai boleh sama dengan tanggal mulai untuk sewa 1 hari. Harga tetap dihitung per hari.</span>
                            </div>

                            <div id="dateValidationAlert" style="display: none;">
                                <div class="bk-alert warn">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Rentang tanggal belum valid.</strong>
                                    <p class="mb-0 small mt-1">Tanggal selesai tidak boleh sebelum tanggal mulai.</p>
                                </div>
                            </div>

                            <div id="availabilityAlert" style="display: none;">
                                <div class="bk-alert danger">
                                    <i class="bi bi-exclamation-circle"></i>
                                    <strong>Kendaraan tidak tersedia untuk tanggal yang dipilih!</strong>
                                    <p id="conflictingDates" class="mb-0 small mt-1"></p>
                                </div>
                            </div>

                            <div id="queueAlert" style="display: none;">
                                <div class="bk-alert info">
                                    <i class="bi bi-hourglass-split"></i>
                                    <strong>Kendaraan masih bisa dibooking lewat antrean.</strong>
                                    <p id="queueDates" class="mb-0 small mt-1"></p>
                                </div>
                            </div>

                            <button type="submit" class="bk-submit" id="submitBtn">
                                <i class="bi bi-check-lg"></i> Konfirmasi Booking
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right: Vehicle info + Price --}}
            <div class="col-lg-6">
                <div class="bk-card">
                    <div class="bk-card-head">
                        <i class="bi bi-truck"></i>
                        <h2>Detail Kendaraan</h2>
                    </div>
                    <div class="bk-card-body">
                        @if($vehicle->image)
                            <img src="{{ Storage::url($vehicle->image) }}"
                                 class="vehicle-preview-media" alt="{{ $vehicle->name }}">
                        @else
                            <div class="vehicle-preview-placeholder">
                                <i class="bi {{ $vehicle->vehicle_type === 'motor' ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                            </div>
                        @endif

                        <h5 style="font-weight:700;color:#0f172a;margin-bottom:.75rem">{{ $vehicle->name }}</h5>

                        <ul class="bk-specs">
                            <li><i class="bi bi-tag"></i><span class="text-muted">Plat:</span> {{ $vehicle->plat_number }}</li>
                            <li><i class="bi bi-calendar3"></i><span class="text-muted">Tahun:</span> {{ $vehicle->year }}</li>
                            <li><i class="bi bi-gear"></i><span class="text-muted">Transmisi:</span> {{ $vehicle->transmission }}</li>
                        </ul>
                    </div>
                </div>

                {{-- Price Summary --}}
                <div class="bk-summary">
                    <h6 style="font-weight:700;color:#0f172a;margin-bottom:.85rem;font-size:.92rem"><i class="bi bi-receipt me-1"></i>Ringkasan Harga</h6>

                    <div class="bk-summary-row">
                        <span>Harga Per Hari</span>
                        <strong>Rp{{ number_format($vehicle->daily_price, 0, ',', '.') }}</strong>
                    </div>

                    <div class="bk-summary-row">
                        <span>Durasi Sewa</span>
                        <strong id="durationText">-</strong>
                    </div>

                    <div class="bk-summary-row">
                        <span>Subtotal</span>
                        <strong id="subtotalText">-</strong>
                    </div>

                    <div class="bk-summary-row" style="border-top:2px solid rgba(203,213,225,.4);padding-top:.75rem;margin-top:.25rem">
                        <span style="font-weight:700;color:#0f172a">Total Harga</span>
                        <span class="bk-summary-total" id="totalText">-</span>
                    </div>

                    <div id="dailyBreakdown" class="bk-breakdown">
                        <div class="bk-breakdown-row">
                            <span>Pilih tanggal untuk melihat tarif harian dinamis.</span>
                        </div>
                    </div>
                </div>

                <div class="bk-hint mt-3">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>Pastikan tanggal dan durasi sewa sudah benar sebelum mengkonfirmasi.</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const vehicleId = {{ $vehicle->id }};
    const priceApiUrl = @json(route('api.vehicle.price', $vehicle));
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationText = document.getElementById('durationText');
    const subtotalText = document.getElementById('subtotalText');
    const totalText = document.getElementById('totalText');
    const availabilityAlert = document.getElementById('availabilityAlert');
    const conflictingDates = document.getElementById('conflictingDates');
    const queueAlert = document.getElementById('queueAlert');
    const queueDates = document.getElementById('queueDates');
    const submitBtn = document.getElementById('submitBtn');
    const bookingForm = document.getElementById('bookingForm');
    const dateValidationAlert = document.getElementById('dateValidationAlert');
    const dateRangeFeedback = document.getElementById('dateRangeFeedback');
    const dailyBreakdown = document.getElementById('dailyBreakdown');
    
    let isAvailable = true;
    let hasValidDateRange = true;

    function getDateValue(input) {
        return input.value ? new Date(`${input.value}T00:00:00`) : null;
    }

    function getMinimumEndDateValue() {
        if (!startDateInput.value) {
            return new Date().toISOString().split('T')[0];
        }

        return startDateInput.value;
    }

    function syncDateConstraints() {
        endDateInput.min = getMinimumEndDateValue();

        if (startDateInput.value && endDateInput.value && endDateInput.value < startDateInput.value) {
            endDateInput.value = startDateInput.value;
        }
    }

    function updateSubmitState() {
        submitBtn.disabled = !hasValidDateRange || !isAvailable;
    }

    function validateDateRange() {
        const startDate = getDateValue(startDateInput);
        const endDate = getDateValue(endDateInput);

        if (!startDateInput.value || !endDateInput.value) {
            hasValidDateRange = true;
            dateValidationAlert.style.display = 'none';
            endDateInput.classList.remove('is-invalid');
            dateRangeFeedback.style.display = 'none';
            updateSubmitState();
            return true;
        }

        hasValidDateRange = endDate >= startDate;

        if (!hasValidDateRange) {
            dateValidationAlert.style.display = 'block';
            availabilityAlert.style.display = 'none';
            endDateInput.classList.add('is-invalid');
            dateRangeFeedback.style.display = 'block';
            isAvailable = false;
            updateSubmitState();
            return false;
        }

        dateValidationAlert.style.display = 'none';
        endDateInput.classList.remove('is-invalid');
        dateRangeFeedback.style.display = 'none';
        updateSubmitState();
        return true;
    }

    function resetPriceBreakdown() {
        durationText.textContent = '-';
        subtotalText.textContent = '-';
        totalText.textContent = '-';
        dailyBreakdown.innerHTML = '<div class="bk-breakdown-row"><span>Pilih tanggal untuk melihat tarif harian dinamis.</span></div>';
    }

    function updatePricePreview() {
        if (!startDateInput.value || !endDateInput.value || !validateDateRange()) {
            resetPriceBreakdown();
            return;
        }

        fetch(`${priceApiUrl}?start_date=${startDateInput.value}&end_date=${endDateInput.value}`)
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                return data;
            })
            .then(data => {
                durationText.textContent = `${data.duration_days} hari`;
                subtotalText.textContent = 'Rp' + Number(data.subtotal).toLocaleString('id-ID');
                totalText.textContent = 'Rp' + Number(data.total).toLocaleString('id-ID');

                dailyBreakdown.innerHTML = (data.daily_prices || []).map(item => `
                    <div class="bk-breakdown-row">
                        <span>${new Date(`${item.date}T00:00:00`).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
                        <strong>Rp${Number(item.price).toLocaleString('id-ID')}</strong>
                    </div>
                `).join('') || '<div class="bk-breakdown-row"><span>Belum ada rincian harga.</span></div>';
            })
            .catch(() => {
                resetPriceBreakdown();
            });
    }

    function checkAvailability() {
        if (!startDateInput.value || !endDateInput.value) {
            availabilityAlert.style.display = 'none';
            queueAlert.style.display = 'none';
            isAvailable = true;
            updateSubmitState();
            return;
        }

        if (!validateDateRange()) {
            return;
        }

        fetch(`/api/vehicles/${vehicleId}/availability?start_date=${startDateInput.value}&end_date=${endDateInput.value}`)
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                return data;
            })
            .then(data => {
                if (data.queue_available === true) {
                    availabilityAlert.style.display = 'none';
                    queueAlert.style.display = 'block';
                    isAvailable = true;

                    let queueMsg = 'Kendaraan sedang dipakai pada tanggal: ';
                    if (data.queue_bookings && data.queue_bookings.length > 0) {
                        queueMsg += data.queue_bookings.map(b => {
                            const start = new Date(b.start_date).toLocaleDateString('id-ID');
                            const end = new Date(b.end_date).toLocaleDateString('id-ID');
                            return `${start} - ${end}`;
                        }).join(', ');
                    }
                    queueMsg += '. Anda tetap bisa booking. Setelah pembayaran diverifikasi, status booking akan masuk antrean sampai kendaraan tersedia.';
                    queueDates.textContent = queueMsg;
                } else if (!data.available || data.available === false) {
                    availabilityAlert.style.display = 'block';
                    queueAlert.style.display = 'none';
                    isAvailable = false;
                    
                    let conflictMsg = '';
                    if (data.blocking_bookings && data.blocking_bookings.length > 0) {
                        conflictMsg = 'Kendaraan sudah dipesan pada tanggal: ';
                        conflictMsg += data.blocking_bookings.map(b => {
                            const start = new Date(b.start_date).toLocaleDateString('id-ID');
                            const end = new Date(b.end_date).toLocaleDateString('id-ID');
                            return `${start} - ${end}`;
                        }).join(', ');
                    }
                    conflictingDates.textContent = conflictMsg;
                } else {
                    availabilityAlert.style.display = 'none';
                    queueAlert.style.display = 'none';
                    isAvailable = true;
                }

                updateSubmitState();
            })
            .catch(error => {
                availabilityAlert.style.display = 'block';
                conflictingDates.textContent = error.error || 'Periksa kembali rentang tanggal yang dipilih.';
                isAvailable = false;
                updateSubmitState();
            });
    }

    // Prevent form submission if vehicle is not available
    bookingForm.addEventListener('submit', function(e) {
        if (!validateDateRange()) {
            e.preventDefault();
            endDateInput.focus();
            return false;
        }

        if (!isAvailable) {
            e.preventDefault();
            alert('Kendaraan tidak tersedia untuk tanggal yang dipilih. Silakan pilih tanggal lain.');
            return false;
        }
    });

    startDateInput.addEventListener('change', function() {
        syncDateConstraints();
        validateDateRange();
        updatePricePreview();
        checkAvailability();
    });
    endDateInput.addEventListener('change', function() {
        validateDateRange();
        updatePricePreview();
        checkAvailability();
    });

    // Calculate on page load if dates are pre-filled
    syncDateConstraints();
    validateDateRange();
    updatePricePreview();
    checkAvailability();
</script>
@endsection