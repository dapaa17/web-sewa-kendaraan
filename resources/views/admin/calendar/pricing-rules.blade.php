<div class="modal fade" id="pricingRulesModal" tabindex="-1" aria-labelledby="pricingRulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title" id="pricingRulesModalLabel">Kelola Aturan Harga</h5>
                    <p class="text-muted small mb-0">Atur diskon harian untuk peak season, low season, early bird, last minute, atau promo kustom.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <form id="pricingRuleForm" class="calendar-form-stack">
                            @csrf
                            <input type="hidden" name="pricing_rule_id" id="pricing_rule_id">
                            <input type="hidden" name="action" id="pricing_rule_action" value="save">
                            <div class="calendar-form-field">
                                <label for="pricing_vehicle_id" class="form-label">Kendaraan</label>
                                <select id="pricing_vehicle_id" name="vehicle_id" class="form-select" required>
                                    <option value="">Pilih kendaraan</option>
                                    @foreach($vehicles as $vehicleOption)
                                        <option value="{{ $vehicleOption->id }}" data-base-price="{{ (float) ($vehicleOption->base_price ?? $vehicleOption->daily_price) }}">
                                            {{ $vehicleOption->name }} • {{ $vehicleOption->plat_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="calendar-form-grid">
                                <div class="calendar-form-field">
                                    <label for="pricing_start_date" class="form-label">Mulai Berlaku</label>
                                    <input type="date" id="pricing_start_date" name="start_date" class="form-control" required>
                                </div>
                                <div class="calendar-form-field">
                                    <label for="pricing_end_date" class="form-label">Selesai Berlaku</label>
                                    <input type="date" id="pricing_end_date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="calendar-form-grid">
                                <div class="calendar-form-field">
                                    <label for="pricing_type" class="form-label">Tipe Rule</label>
                                    <select id="pricing_type" name="type" class="form-select" required>
                                        <option value="peak_season">Peak season</option>
                                        <option value="low_season">Low season</option>
                                        <option value="early_bird">Early bird</option>
                                        <option value="last_minute">Last minute</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                                <div class="calendar-form-field">
                                    <label for="pricing_discount_percentage" class="form-label">Diskon (%)</label>
                                    <input type="number" id="pricing_discount_percentage" name="discount_percentage" class="form-control" min="0" max="100" value="0" required>
                                </div>
                            </div>
                            <div class="calendar-form-field">
                                <label for="pricing_description" class="form-label">Deskripsi</label>
                                <input type="text" id="pricing_description" name="description" class="form-control" placeholder="Contoh: Promo akhir pekan panjang">
                            </div>
                            <div class="calendar-preview-card" id="pricingPreviewCard">
                                <div class="label">Preview Dampak</div>
                                <strong id="pricingPreviewText">Pilih kendaraan dan isi diskon untuk melihat estimasi harga setelah rule aktif.</strong>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="pricing_active" name="active" value="1" checked>
                                <label class="form-check-label" for="pricing_active">Rule aktif</label>
                            </div>
                            <div class="calendar-inline-alert d-none" id="pricingRulesAlert"></div>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-outline-secondary" id="pricingRuleResetBtn">Reset</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-stars me-1"></i> Simpan Rule
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-7">
                        <div class="calendar-table-card">
                            <div class="calendar-table-head">
                                <div>
                                    <h6 class="mb-1">Rule Aktif & Riwayat Rule</h6>
                                    <p class="text-muted small mb-0">Klik edit untuk memuat data ke form, atau hapus rule yang sudah tidak dipakai.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" id="pricingRulesTable">
                                    <thead>
                                        <tr>
                                            <th>Kendaraan</th>
                                            <th>Periode</th>
                                            <th>Tipe</th>
                                            <th>Diskon</th>
                                            <th>Status</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pricingRules as $pricingRule)
                                            <tr
                                                data-pricing-rule-id="{{ $pricingRule->id }}"
                                                data-vehicle-id="{{ $pricingRule->vehicle_id }}"
                                                data-start-date="{{ $pricingRule->start_date->toDateString() }}"
                                                data-end-date="{{ $pricingRule->end_date->toDateString() }}"
                                                data-type="{{ $pricingRule->type }}"
                                                data-discount="{{ $pricingRule->discount_percentage }}"
                                                data-description="{{ $pricingRule->description }}"
                                                data-active="{{ $pricingRule->active ? '1' : '0' }}"
                                            >
                                                <td>
                                                    <strong>{{ $pricingRule->vehicle?->name ?? 'Kendaraan dihapus' }}</strong>
                                                </td>
                                                <td>{{ $pricingRule->start_date->format('d M Y') }} - {{ $pricingRule->end_date->format('d M Y') }}</td>
                                                <td><span class="badge text-bg-light">{{ str_replace('_', ' ', $pricingRule->type) }}</span></td>
                                                <td>{{ $pricingRule->discount_percentage }}%</td>
                                                <td>
                                                    <span class="badge {{ $pricingRule->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                                        {{ $pricingRule->active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary pricing-edit-btn">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger pricing-delete-btn">Hapus</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Belum ada aturan harga. Buat rule pertama dari form di sebelah kiri.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>