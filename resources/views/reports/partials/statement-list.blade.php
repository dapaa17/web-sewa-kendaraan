<style>
    .stmt-page {
        padding: 1.25rem 0 2.5rem;
    }

    .stmt-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 56%, #0891b2 100%);
        color: #fff;
        border-radius: 1.25rem;
        padding: 1.35rem 1.2rem;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.22);
        position: relative;
        overflow: hidden;
    }

    .stmt-header::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        top: -112px;
        right: -80px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(103, 232, 249, 0.3) 0%, rgba(103, 232, 249, 0) 70%);
        pointer-events: none;
    }

    .stmt-title {
        font-size: 1.55rem;
        margin: 0;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .stmt-subtitle {
        font-size: 0.9rem;
        opacity: 0.82;
        margin: 0.35rem 0 0;
    }

    .stmt-pill {
        border: 1px solid rgba(255, 255, 255, 0.34);
        border-radius: 999px;
        padding: 0.4rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255, 255, 255, 0.12);
    }

    .stmt-config {
        margin-top: 1rem;
        border: 1px solid rgba(203, 213, 225, 0.85);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        padding: 0.95rem;
    }

    .stmt-form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .stmt-date-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.65rem;
    }

    .stmt-field {
        border: 1px solid rgba(226, 232, 240, 1);
        border-radius: 0.95rem;
        background: #f8fafc;
        padding: 0.65rem 0.7rem;
    }

    .stmt-field label {
        display: block;
        margin: 0 0 0.35rem;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .stmt-field input,
    .stmt-field select {
        border: none;
        background: transparent;
        width: 100%;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        padding: 0;
    }

    .stmt-field input:focus,
    .stmt-field select:focus {
        outline: none;
    }

    .stmt-info {
        border: 1px solid rgba(245, 158, 11, 0.4);
        background: linear-gradient(135deg, rgba(254, 243, 199, 0.6) 0%, rgba(254, 249, 195, 0.72) 100%);
        border-radius: 0.9rem;
        padding: 0.75rem 0.8rem;
        color: #78350f;
        display: flex;
        gap: 0.55rem;
        align-items: flex-start;
        font-size: 0.88rem;
    }

    .stmt-action-row {
        display: flex;
        gap: 0.65rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .stmt-apply-btn {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.45);
        background: #fff;
        color: #334155;
        font-size: 0.86rem;
        font-weight: 700;
        padding: 0.55rem 1rem;
    }

    .stmt-download-btn {
        border-radius: 999px;
        border: none;
        background: #1d7be5;
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
        padding: 0.8rem 1rem;
        width: 100%;
        box-shadow: 0 10px 22px rgba(29, 123, 229, 0.28);
    }

    .stmt-summary {
        margin-top: 1rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.65rem;
    }

    .stmt-stat {
        border: 1px solid rgba(203, 213, 225, 0.72);
        border-radius: 1rem;
        background: #fff;
        padding: 0.9rem 0.95rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .stmt-stat .label {
        color: #64748b;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
    }

    .stmt-stat .value {
        margin-top: 0.4rem;
        color: #0f172a;
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .stmt-year {
        margin: 1.25rem 0 0.65rem;
        font-size: 1.1rem;
        color: #64748b;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .stmt-card {
        border: 1px solid rgba(203, 213, 225, 0.72);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 26px rgba(15, 23, 42, 0.06);
        padding: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.8rem;
        margin-bottom: 0.65rem;
        flex-wrap: wrap;
    }

    .stmt-month {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .stmt-caption {
        margin: 0.35rem 0 0;
        font-size: 0.82rem;
        color: #64748b;
    }

    .stmt-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-left: auto;
        padding-left: 0.45rem;
    }

    .stmt-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 0.8rem;
        padding: 0.5rem 0.65rem;
        text-decoration: none;
        font-weight: 800;
        color: #0284c7;
    }

    .stmt-action:hover {
        color: #0369a1;
        background: rgba(14, 116, 144, 0.08);
    }

    .stmt-sep {
        width: 1px;
        height: 26px;
        background: rgba(203, 213, 225, 0.9);
    }

    .stmt-empty {
        margin-top: 1rem;
        border: 1px solid rgba(203, 213, 225, 0.72);
        border-radius: 1rem;
        background: #fff;
        padding: 2rem 1rem;
        text-align: center;
        color: #64748b;
        box-shadow: 0 8px 26px rgba(15, 23, 42, 0.06);
    }

    @media (max-width: 768px) {
        .stmt-page {
            padding-top: 0.85rem;
        }

        .stmt-header {
            border-radius: 1rem;
            padding: 1.15rem 0.95rem;
        }

        .stmt-title {
            font-size: 1.38rem;
        }

        .stmt-date-grid {
            grid-template-columns: 1fr;
        }

        .stmt-actions {
            width: 100%;
            margin-left: 0;
            justify-content: flex-end;
            padding-left: 0;
        }

        .stmt-summary {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="stmt-page">
    <div class="stmt-header">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
            <div>
                <h1 class="stmt-title">{{ $title }}</h1>
                <p class="stmt-subtitle">{{ $subtitle }}</p>
            </div>
            <span class="stmt-pill"><i class="bi bi-calendar3"></i>{{ $periodLabel }}</span>
        </div>
    </div>

    <div class="stmt-config">
        <form method="GET" action="{{ route($indexRoute) }}" class="stmt-form">
            <div class="stmt-date-grid">
                <div class="stmt-field">
                    <label for="stmt-from">Dari</label>
                    <input id="stmt-from" type="date" name="from" value="{{ $fromDate }}" required>
                </div>
                <div class="stmt-field">
                    <label for="stmt-to">Sampai</label>
                    <input id="stmt-to" type="date" name="to" value="{{ $toDate }}" required>
                </div>
            </div>

            <div class="stmt-field">
                <label for="stmt-file-type">Jenis File</label>
                <select id="stmt-file-type" name="file_type">
                    <option value="pdf" @selected($selectedFileType === 'pdf')>PDF</option>
                    <option value="excel" @selected($selectedFileType === 'excel')>Excel</option>
                </select>
            </div>

            <div class="stmt-info">
                <i class="bi bi-lightbulb" style="font-size:1.05rem"></i>
                <div>
                    Gunakan tanggal lahir Anda (DDMMYYYY) untuk membuka file e-statement jika laporan terkunci di perangkat tujuan.
                </div>
            </div>

            <div class="stmt-action-row">
                <button type="submit" class="stmt-apply-btn">
                    <i class="bi bi-sliders me-1"></i>Terapkan Periode
                </button>
            </div>

            <button type="submit" name="download" value="1" class="stmt-download-btn">
                Download
            </button>
        </form>
    </div>

    <div class="stmt-summary">
        <div class="stmt-stat">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $totalTransactions }}</div>
        </div>
        <div class="stmt-stat">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="stmt-stat">
            <div class="label">Total Denda</div>
            <div class="value">Rp{{ number_format($totalFees, 0, ',', '.') }}</div>
        </div>
    </div>

    @if(empty($statementsByYear))
        <div class="stmt-empty">
            <div class="mb-2" style="font-size:1.5rem"><i class="bi bi-folder2-open"></i></div>
            <strong>Belum ada data laporan untuk periode ini.</strong>
        </div>
    @else
        @foreach($statementsByYear as $year => $statements)
            <h3 class="stmt-year">{{ $year }}</h3>

            @foreach($statements as $statement)
                @php
                    $statementStartDate = \Carbon\Carbon::create($statement['year'], $statement['month'], 1)->toDateString();
                    $statementEndDate = \Carbon\Carbon::create($statement['year'], $statement['month'], 1)->endOfMonth()->toDateString();
                @endphp
                <div class="stmt-card">
                    <div>
                        <p class="stmt-month">{{ ucfirst($statement['month_name']) }}</p>
                        <p class="stmt-caption">
                            {{ $statement['total_transactions'] }} transaksi
                            &middot; Pendapatan Rp{{ number_format($statement['total_revenue'], 0, ',', '.') }}
                            &middot; Denda Rp{{ number_format($statement['total_fees'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="stmt-actions">
                        <a href="{{ route($excelRoute, ['from' => $statementStartDate, 'to' => $statementEndDate]) }}" class="stmt-action">
                            <i class="bi bi-file-earmark-spreadsheet"></i>Excel
                        </a>
                        <span class="stmt-sep"></span>
                        <a href="{{ route($pdfRoute, ['from' => $statementStartDate, 'to' => $statementEndDate]) }}" class="stmt-action" target="_blank" rel="noopener">
                            <i class="bi bi-file-earmark-pdf"></i>PDF
                        </a>
                    </div>
                </div>
            @endforeach
        @endforeach
    @endif
</div>
