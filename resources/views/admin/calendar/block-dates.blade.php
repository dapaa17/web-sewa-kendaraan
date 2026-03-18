<div class="modal fade" id="blockDatesModal" tabindex="-1" aria-labelledby="blockDatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title" id="blockDatesModalLabel">Blok Tanggal Maintenance</h5>
                    <p class="text-muted small mb-0">Pilih kendaraan dan rentang hari yang ingin diblok untuk perawatan.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="blockDatesForm" class="calendar-form-stack">
                    @csrf
                    <div class="calendar-form-field">
                        <label for="block_vehicle_id" class="form-label">Kendaraan</label>
                        <select id="block_vehicle_id" name="vehicle_id" class="form-select" required>
                            <option value="">Pilih kendaraan</option>
                            @foreach($vehicles as $vehicleOption)
                                <option value="{{ $vehicleOption->id }}">{{ $vehicleOption->name }} • {{ $vehicleOption->plat_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="calendar-form-grid">
                        <div class="calendar-form-field">
                            <label for="block_start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" id="block_start_date" name="start_date" class="form-control" required>
                        </div>
                        <div class="calendar-form-field">
                            <label for="block_end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" id="block_end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="calendar-form-field">
                        <label for="block_reason" class="form-label">Alasan</label>
                        <input type="text" id="block_reason" name="reason" class="form-control" placeholder="Contoh: Servis berkala" required>
                    </div>
                    <div class="calendar-form-field">
                        <label for="block_notes" class="form-label">Catatan</label>
                        <textarea id="block_notes" name="notes" class="form-control" rows="3" placeholder="Catatan tambahan untuk tim operasional"></textarea>
                    </div>
                    <div class="calendar-inline-alert d-none" id="blockDatesAlert"></div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-wrench-adjustable-circle me-1"></i> Blok Tanggal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>