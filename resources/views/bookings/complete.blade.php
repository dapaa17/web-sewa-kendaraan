@extends('layouts.admin')

@section('title', 'Konfirmasi Pengembalian Kendaraan')
@section('page-title', 'Pengembalian Kendaraan')

@section('content')
<style>
    .complete-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 2rem 2rem;
    }
    .complete-header h1 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .back-link {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .back-link:hover {
        color: white;
    }
    .complete-container {
        max-width: 1120px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    .info-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: var(--shadow-card);
        padding: 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(203,213,225,0.65);
    }
    .info-card h5 {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-card h5 i {
        color: var(--color-secondary-strong);
    }
    .info-table {
        width: 100%;
    }
    .info-table tr {
        border-bottom: 1px solid #f3f4f6;
    }
    .info-table tr:last-child {
        border-bottom: none;
    }
    .info-table td {
        padding: 0.75rem 0;
    }
    .info-table td:first-child {
        color: #6b7280;
        width: 40%;
    }
    .info-table td:last-child {
        font-weight: 600;
        color: #1f2937;
    }
    .alert-late {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: none;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .alert-late h6 {
        color: #92400e;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .alert-late .text-danger {
        color: #dc2626 !important;
    }
    .alert-ontime {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border: none;
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
    }
    .alert-ontime i {
        font-size: 2rem;
        color: #059669;
    }
    .alert-ontime p {
        color: #065f46;
        font-weight: 600;
        margin-bottom: 0;
    }
    .summary-card {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 34%), var(--gradient-brand);
        border-radius: 1rem;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .summary-card h6 {
        font-weight: 600;
        margin-bottom: 1rem;
        opacity: 0.9;
    }
    .summary-table {
        width: 100%;
    }
    .summary-table td {
        padding: 0.5rem 0;
    }
    .summary-table td:last-child {
        text-align: right;
    }
    .summary-table .total-row td {
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.3);
        font-size: 1.25rem;
        font-weight: 700;
    }
    .planner-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 1rem;
        align-items: end;
    }
    .field-label {
        display: block;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    .helper-text {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }
    .inspection-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .checklist-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
        margin-top: 1rem;
    }
    .check-item {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.95rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        background: #f8fafc;
        cursor: pointer;
    }
    .check-item input {
        margin-top: 0.25rem;
    }
    .check-item span {
        color: #1f2937;
        font-weight: 500;
    }
    .textarea-control {
        min-height: 120px;
        resize: vertical;
    }
    .summary-note {
        margin-top: 0.75rem;
        color: rgba(255,255,255,0.82);
        font-size: 0.9rem;
    }
    .error-text {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.4rem;
        display: block;
    }
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #ef4444;
    }
    .summary-charge-row {
        display: none;
    }
    .summary-charge-row.is-visible {
        display: table-row;
    }
    .form-control {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
    }
    .form-select {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
    }
    .form-select:focus {
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 3px rgba(var(--color-secondary-rgb), 0.14);
    }
    .form-control:focus {
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 3px rgba(var(--color-secondary-rgb), 0.14);
    }
    .btn-secondary-soft {
        background: rgba(var(--color-secondary-rgb), 0.1);
        color: var(--color-primary);
        border: 1px solid rgba(var(--color-secondary-rgb), 0.12);
        padding: 0.85rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
    }
    .btn-secondary-soft:hover {
        background: rgba(var(--color-secondary-rgb), 0.18);
        color: var(--color-primary);
    }
    .btn-confirm {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        color: white;
    }
    .btn-back {
        background: white;
        color: var(--color-primary);
        border: 1px solid rgba(var(--color-primary-rgb), 0.12);
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-back:hover {
        background: rgba(var(--color-secondary-rgb), 0.08);
        color: var(--color-primary);
    }
    @media (max-width: 768px) {
        .planner-form,
        .inspection-grid,
        .checklist-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="complete-header">
    <div class="complete-container">
        <a href="{{ route('admin.bookings.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Booking
        </a>
        <h1><i class="bi bi-box-arrow-in-left me-2"></i>Konfirmasi Pengembalian</h1>
        <p class="mb-0 opacity-75">Booking #{{ $booking->id }} - {{ $booking->vehicle->name }}</p>
    </div>
</div>

<div class="complete-container">
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <strong>Data inspeksi belum lengkap.</strong>
            <div class="small mt-2">Periksa kembali field yang ditandai lalu kirim ulang.</div>
        </div>
    @endif

    <!-- Booking Info -->
    <div class="info-card">
        <h5><i class="bi bi-info-circle"></i> Informasi Booking</h5>
        <table class="info-table">
            <tr>
                <td>Kendaraan</td>
                <td>{{ $booking->vehicle->name }} ({{ $booking->vehicle->plat_number }})</td>
            </tr>
            <tr>
                <td>Customer</td>
                <td>{{ $booking->user->name }}</td>
            </tr>
            <tr>
                <td>Periode Sewa</td>
                <td>{{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}</td>
            </tr>
            <tr>
                <td>Durasi</td>
                <td>{{ $booking->duration_days }} hari</td>
            </tr>
            <tr>
                <td>Harga Sewa</td>
                <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="info-card">
        <h5><i class="bi bi-calendar-check"></i> 1. Tentukan Tanggal Pengembalian</h5>
        <form method="GET" action="{{ route('admin.bookings.complete-form', $booking) }}" class="planner-form">
            <div>
                <label for="planner_return_date" class="field-label">Tanggal aktual kendaraan kembali</label>
                <input
                    type="date"
                    class="form-control @error('return_date') is-invalid @enderror"
                    id="planner_return_date"
                    name="return_date"
                    value="{{ old('return_date', $returnDate ?? now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                >
                <small class="helper-text">Atur tanggal lebih dulu supaya ringkasan denda dan total di bawah akurat sebelum admin mengisi inspeksi.</small>
            </div>
            <button type="submit" class="btn btn-secondary-soft">
                <i class="bi bi-arrow-repeat me-2"></i>Update Ringkasan
            </button>
        </form>
        @error('return_date')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <!-- Late Fee Alert -->
    @if($lateDays > 0)
        <div class="alert-late">
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Keterlambatan Pengembalian</h6>
            <table class="info-table">
                <tr>
                    <td>Tanggal Seharusnya Kembali</td>
                    <td>{{ $booking->end_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td>Tanggal Pengembalian Dipilih</td>
                    <td>{{ \Carbon\Carbon::parse($returnDate)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td>Jumlah Hari Terlambat</td>
                    <td class="text-danger">{{ $lateDays }} hari</td>
                </tr>
                <tr>
                    <td>Denda per Hari</td>
                    <td>Rp {{ number_format($booking->daily_price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Denda</strong></td>
                    <td class="text-danger"><strong>Rp {{ number_format($lateFee, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
    @else
        <div class="alert-ontime">
            <i class="bi bi-check-circle-fill mb-2"></i>
            <p>Pengembalian tepat waktu. Tidak ada denda keterlambatan.</p>
        </div>
    @endif

    <!-- Complete Form -->
    <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
        @csrf
        <input type="hidden" name="return_date" value="{{ old('return_date', $returnDate ?? now()->toDateString()) }}">
        
        <div class="info-card">
            <h5><i class="bi bi-clipboard2-check"></i> 2. Isi Checklist Pengembalian</h5>
            <div class="inspection-grid">
                <div>
                    <label for="return_condition_status" class="field-label">Kondisi unit saat kembali</label>
                    <select id="return_condition_status" name="return_condition_status" class="form-select @error('return_condition_status') is-invalid @enderror" required>
                        <option value="">Pilih kondisi unit</option>
                        @foreach($returnConditionOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('return_condition_status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('return_condition_status')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="return_fuel_level" class="field-label">Level BBM saat kembali</label>
                    <select id="return_fuel_level" name="return_fuel_level" class="form-select @error('return_fuel_level') is-invalid @enderror">
                        <option value="">Belum dicatat</option>
                        @foreach($returnFuelOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('return_fuel_level') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('return_fuel_level')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="return_odometer" class="field-label">Odometer saat kembali</label>
                    <input type="number" min="0" step="1" class="form-control @error('return_odometer') is-invalid @enderror" id="return_odometer" name="return_odometer" value="{{ old('return_odometer') }}" placeholder="Contoh: 15432">
                    <small class="helper-text">Opsional, dipakai untuk tracking kondisi kendaraan.</small>
                    @error('return_odometer')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="return_damage_fee" class="field-label">Biaya tambahan kerusakan / cleaning</label>
                    <input type="number" min="0" step="1000" class="form-control @error('return_damage_fee') is-invalid @enderror" id="return_damage_fee" name="return_damage_fee" value="{{ old('return_damage_fee', 0) }}" placeholder="0">
                    <small class="helper-text">Isi `0` bila tidak ada biaya tambahan selain denda keterlambatan.</small>
                    @error('return_damage_fee')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="field-label">Checklist penerimaan unit</label>
                <div class="checklist-grid">
                    @foreach($returnChecklistOptions as $value => $label)
                        <label class="check-item">
                            <input type="checkbox" name="return_checklist[]" value="{{ $value }}" @checked(in_array($value, old('return_checklist', []), true))>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('return_checklist')
                    <span class="error-text">{{ $message }}</span>
                @enderror
                @error('return_checklist.*')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="info-card">
            <h5><i class="bi bi-journal-text"></i> 3. Catatan Pengembalian</h5>
            <div class="mb-4">
                <label for="return_notes" class="field-label">Catatan admin</label>
                <textarea class="form-control textarea-control @error('return_notes') is-invalid @enderror" id="return_notes" name="return_notes" placeholder="Tulis catatan kondisi kendaraan, keluhan customer, atau tindak lanjut yang perlu dilakukan...">{{ old('return_notes') }}</textarea>
                <small class="helper-text">Gunakan catatan ini untuk menjelaskan lecet, aksesoris kurang, atau alasan biaya tambahan.</small>
                @error('return_notes')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-card">
            <h6><i class="bi bi-receipt me-2"></i>Ringkasan Biaya</h6>
            <table class="summary-table">
                <tr>
                    <td>Biaya Sewa ({{ $booking->duration_days }} hari)</td>
                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                </tr>
                @if($lateDays > 0)
                <tr style="color: #fcd34d;">
                    <td>Denda Keterlambatan ({{ $lateDays }} hari)</td>
                    <td>Rp {{ number_format($lateFee, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr id="damage-fee-row" class="summary-charge-row {{ (float) old('return_damage_fee', 0) > 0 ? 'is-visible' : '' }}" style="color: #fde68a;">
                    <td>Biaya Tambahan Inspeksi</td>
                    <td id="damage-fee-value">Rp {{ number_format((float) old('return_damage_fee', 0), 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td id="summary-total-value">Rp {{ number_format($booking->total_price + $lateFee + (float) old('return_damage_fee', 0), 0, ',', '.') }}</td>
                </tr>
            </table>
            <p class="summary-note">Total ini menggabungkan biaya sewa, denda telat bila ada, dan biaya tambahan hasil inspeksi pengembalian.</p>
        </div>

        <div class="d-flex gap-3 justify-content-center">
            <button type="submit" class="btn btn-confirm">
                <i class="bi bi-check-circle me-2"></i>Konfirmasi Pengembalian
            </button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-back">
                <i class="bi bi-arrow-left me-2"></i>Batal
            </a>
        </div>
    </form>
</div>

@section('js')
<script>
    const damageFeeInput = document.getElementById('return_damage_fee');
    const damageFeeRow = document.getElementById('damage-fee-row');
    const damageFeeValue = document.getElementById('damage-fee-value');
    const summaryTotalValue = document.getElementById('summary-total-value');
    const baseTotal = {{ (float) $booking->total_price + (float) $lateFee }};

    const formatRupiah = (value) => {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    };

    const syncSummary = () => {
        const damageFee = Number(damageFeeInput.value || 0);
        damageFeeRow.classList.toggle('is-visible', damageFee > 0);
        damageFeeValue.textContent = formatRupiah(damageFee);
        summaryTotalValue.textContent = formatRupiah(baseTotal + damageFee);
    };

    damageFeeInput.addEventListener('input', syncSummary);
    syncSummary();
</script>
@endsection
@endsection
