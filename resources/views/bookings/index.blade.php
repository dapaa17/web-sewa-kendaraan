@extends(Auth::user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', Auth::user()->isAdmin() ? 'Semua Booking' : 'Booking Saya')
@if(Auth::user()->isAdmin())
@section('page-title', 'Booking')
@endif

@section('content')
<style>
    .booking-header {
        background: radial-gradient(circle at top right, rgba(255,255,255,0.14), transparent 34%), var(--gradient-brand);
        color: white;
        padding: 3rem 0 2.5rem;
        margin-bottom: 1.75rem;
        border-radius: 0 0 2rem 2rem;
    }
    .booking-header h1 {
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.055em;
    }
    .booking-header p {
        max-width: 36rem;
        line-height: 1.75;
    }
    .booking-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .filter-panel {
        display: grid;
        gap: 0.9rem;
        margin-bottom: 1.25rem;
        padding: 1rem;
        border-radius: 1.15rem;
        background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.98) 100%);
        border: 1px solid rgba(203,213,225,0.75);
        box-shadow: var(--shadow-soft);
    }
    .filter-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .filter-panel-header .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #0f766e;
        margin-bottom: 0.35rem;
    }
    .filter-panel-header h2 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: -0.04em;
        color: #0f172a;
    }
    .filter-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 0.95rem;
        border-radius: 0.95rem;
        border: 1px solid rgba(203,213,225,0.95);
        background: white;
        color: #334155;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        transition: all 0.25s ease;
    }
    .filter-reset:hover {
        color: var(--color-primary);
        border-color: rgba(var(--color-secondary-rgb), 0.35);
        transform: translateY(-1px);
    }
    .filter-groups {
        display: grid;
        gap: 0.75rem;
    }
    .filter-group {
        padding: 0.85rem;
        border-radius: 1rem;
        background: rgba(248,250,252,0.92);
        border: 1px solid rgba(226,232,240,0.95);
    }
    .filter-group-header {
        margin-bottom: 0.75rem;
    }
    .filter-group-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #0f172a;
    }
    .filter-tabs {
        display: flex;
        gap: 0.55rem;
        flex-wrap: wrap;
    }
    .filter-tab {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.7rem;
        padding: 0.68rem 0.82rem;
        border-radius: 0.9rem;
        border: 1px solid rgba(203,213,225,0.95);
        background: white;
        color: #334155;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.84rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.04);
        transition: all 0.25s ease;
    }
    .filter-tab:hover {
        border-color: rgba(var(--color-secondary-rgb), 0.32);
        color: var(--color-primary);
        transform: translateY(-1px);
    }
    .filter-tab.active {
        background: var(--gradient-brand);
        border-color: transparent;
        color: white;
        box-shadow: 0 14px 24px rgba(var(--color-secondary-rgb), 0.2);
    }
    .filter-tab-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .filter-count {
        min-width: 1.8rem;
        height: 1.8rem;
        padding: 0 0.5rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid transparent;
    }
    .filter-count.neutral {
        background: #e2e8f0;
        color: #334155;
    }
    .filter-count.warning {
        background: rgba(245, 158, 11, 0.14);
        color: #92400e;
    }
    .filter-count.info {
        background: rgba(14, 165, 233, 0.12);
        color: #0c4a6e;
    }
    .filter-count.primary {
        background: rgba(var(--color-secondary-rgb), 0.12);
        color: var(--color-primary);
    }
    .filter-count.success {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }
    .filter-count.danger {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
    }
    .filter-tab.active .filter-count {
        background: rgba(255,255,255,0.18);
        color: white;
        border-color: rgba(255,255,255,0.18);
    }
    .stats-row {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        margin-bottom: 1.1rem;
    }
    .stat-pill {
        background: white;
        border-radius: 1rem;
        padding: 0.9rem 0.95rem;
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        border: 1px solid rgba(203,213,225,0.6);
        box-shadow: var(--shadow-soft);
    }
    .stat-pill-icon {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 0.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.06);
        color: #0f172a;
        flex-shrink: 0;
    }
    .stat-pill .number {
        font-size: 1.45rem;
        font-weight: 700;
        color: #1a202c;
        line-height: 1;
        letter-spacing: -0.05em;
    }
    .stat-pill .label {
        color: #718096;
        font-size: 0.88rem;
        margin-top: 0.1rem;
    }
    .stat-pill .helper {
        margin-top: 0.28rem;
        color: #94a3b8;
        font-size: 0.76rem;
        line-height: 1.4;
    }
    .bookings-list-header {
        margin-bottom: 0.95rem;
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        background: white;
        border: 1px solid rgba(203,213,225,0.65);
        box-shadow: var(--shadow-soft);
    }
    .bookings-list-header h3 {
        margin: 0;
        font-size: 1.02rem;
        font-weight: 700;
        color: #0f172a;
    }
    .bookings-list-header p {
        margin: 0.28rem 0 0;
        color: #64748b;
        line-height: 1.6;
    }
    .booking-explainer {
        margin-bottom: 1rem;
        padding: 0.95rem 1rem;
        background: linear-gradient(135deg, rgba(224,242,254,0.92) 0%, rgba(240,249,255,0.96) 100%);
        border: 1px solid rgba(125, 211, 252, 0.55);
        border-radius: 1rem;
        box-shadow: 0 12px 24px rgba(14, 165, 233, 0.08);
    }
    .booking-explainer summary {
        list-style: none;
        cursor: pointer;
    }
    .booking-explainer summary::-webkit-details-marker {
        display: none;
    }
    .booking-explainer .title {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-weight: 700;
        color: #0f172a;
    }
    .booking-explainer .title i {
        color: #0284c7;
    }
    .booking-explainer .items {
        display: grid;
        gap: 0.55rem;
    }
    .booking-explainer .item {
        color: #334155;
        line-height: 1.6;
        font-size: 0.9rem;
    }
    .booking-explainer .item strong {
        color: #0f172a;
    }
    .admin-table-wrap {
        background: transparent;
        border: none;
        border-radius: 1rem;
        box-shadow: none;
        overflow: visible;
        padding: 0 0.25rem;
    }
    .admin-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
    }
    .admin-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(226,232,240,0.9);
        white-space: nowrap;
    }
    .admin-table thead th:first-child { border-radius: 0.75rem 0 0 0.75rem; }
    .admin-table thead th:last-child { border-radius: 0 0.75rem 0.75rem 0; }
    .admin-table tbody tr {
        background: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .admin-table tbody tr:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-1px);
    }
    .admin-table tbody td {
        padding: 1.1rem 1rem;
        vertical-align: top;
        border-top: 1px solid rgba(226,232,240,0.5);
        border-bottom: 1px solid rgba(226,232,240,0.5);
    }
    .admin-table tbody td:first-child {
        border-left: 1px solid rgba(226,232,240,0.5);
        border-radius: 0.75rem 0 0 0.75rem;
    }
    .admin-table tbody td:last-child {
        border-right: 1px solid rgba(226,232,240,0.5);
        border-radius: 0 0.75rem 0.75rem 0;
    }
    .admin-table tbody tr:last-child td {
        border-bottom: 1px solid rgba(226,232,240,0.5);
    }
    .admin-table tbody tr.is-overdue {
        background: rgba(254, 242, 242, 0.75);
    }
    .admin-table tbody tr.is-awaiting-proof {
        background: rgba(255, 251, 235, 0.75);
    }
    .admin-cell-title {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.45;
    }
    .admin-cell-sub {
        margin-top: 0.22rem;
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.5;
    }
    .admin-cell-stack {
        display: grid;
        gap: 0.35rem;
    }
    .admin-payment-note {
        color: #92400e;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .admin-status-stack {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
    .admin-actions {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
    .admin-actions form {
        display: inline-flex;
        margin: 0;
    }
    .bookings-stack {
        display: grid;
        gap: 0.8rem;
    }
    .booking-card {
        background: white;
        border-radius: 1rem;
        padding: 1.05rem;
        box-shadow: var(--shadow-soft);
        transition: all 0.25s ease;
        border-left: 4px solid var(--color-secondary);
        border: 1px solid rgba(203,213,225,0.5);
        border-left-width: 4px;
    }
    .booking-card:hover {
        box-shadow: var(--shadow-card-hover);
        transform: translateY(-1px);
    }
    .booking-card.pending { border-left-color: #f59e0b; }
    .booking-card.payment_failed { border-left-color: #ef4444; }
    .booking-card.waiting_list { border-left-color: #f97316; }
    .booking-card.confirmed { border-left-color: var(--color-secondary); }
    .booking-card.completed { border-left-color: #10b981; }
    .booking-card.cancelled { border-left-color: #ef4444; }
    .booking-card.payment-overdue {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, rgba(254,242,242,0.95) 0%, rgba(255,255,255,0.98) 16%);
    }
    .booking-card .header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.8rem;
        margin-bottom: 0.75rem;
    }
    .booking-card .vehicle-info h5 {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 0.2rem;
        font-size: 1.02rem;
        letter-spacing: -0.03em;
    }
    .booking-card .vehicle-info .meta {
        color: #718096;
        font-size: 0.79rem;
    }
    .booking-card .badges {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
    .booking-deadline-row {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.7rem;
        color: #92400e;
        font-size: 0.79rem;
        font-weight: 600;
        padding: 0.42rem 0.68rem;
        border-radius: 999px;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.16);
    }
    .booking-card .details-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.65rem;
        margin-bottom: 0.8rem;
        padding: 0.8rem;
        background: #f8fafc;
        border: 1px solid rgba(226,232,240,0.9);
        border-radius: 0.75rem;
    }
    .booking-card .detail-item {
        display: flex;
        flex-direction: column;
        padding: 0.62rem 0.68rem;
        border-radius: 0.68rem;
        background: white;
        border: 1px solid rgba(226,232,240,0.75);
    }
    .booking-card .detail-item .label {
        color: #718096;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.18rem;
    }
    .booking-card .detail-item .value {
        color: #1a202c;
        font-weight: 600;
        font-size: 0.87rem;
    }
    .booking-card .detail-item .value.price {
        color: var(--color-primary);
        font-size: 0.98rem;
    }
    .booking-card .actions {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }
    .badge-status {
        padding: 0.42rem 0.78rem;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.74rem;
    }
    .badge-method {
        padding: 0.42rem 0.78rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.73rem;
        border: 1px solid transparent;
    }
    .badge-method.whatsapp {
        background: rgba(16, 185, 129, 0.14);
        color: #047857;
        border-color: rgba(16, 185, 129, 0.24);
    }
    .badge-method.transfer {
        background: rgba(14, 165, 233, 0.12);
        color: #0f766e;
        border-color: rgba(14, 165, 233, 0.22);
    }
    .badge-priority {
        padding: 0.42rem 0.78rem;
        border-radius: 2rem;
        font-weight: 700;
        font-size: 0.73rem;
        border: 1px solid transparent;
    }
    .badge-priority.urgent {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
        border-color: rgba(239, 68, 68, 0.22);
    }
    .badge-priority.expired {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border-color: transparent;
    }
    .badge-deadline {
        padding: 0.36rem 0.68rem;
        border-radius: 2rem;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .btn-action {
        border-radius: 0.72rem;
        padding: 0.44rem 0.86rem;
        font-size: 0.79rem;
        font-weight: 500;
        transition: all 0.25s ease;
    }
    .btn-action:hover {
        transform: translateY(-1px);
    }
    .btn-view {
        background: var(--color-primary);
        border: none;
        color: white;
    }
    .btn-view:hover {
        background: var(--color-secondary);
        box-shadow: 0 5px 15px rgba(var(--color-secondary-rgb), 0.24);
        color: var(--color-primary);
    }
    .btn-complete {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
    }
    .btn-complete:hover {
        box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
        color: white;
    }
    .btn-reschedule {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        border: none;
        color: white;
    }
    .btn-reschedule:hover {
        box-shadow: 0 5px 15px rgba(249, 115, 22, 0.32);
        color: white;
    }
    .btn-cancel {
        background: white;
        border: 2px solid #ef4444;
        color: #ef4444;
    }
    .btn-cancel:hover {
        background: #ef4444;
        color: white;
    }
    .btn-pending-review {
        background: linear-gradient(135deg, rgba(245,158,11,0.16) 0%, rgba(251,191,36,0.22) 100%);
        border: 1px solid rgba(245,158,11,0.35);
        color: #92400e;
        cursor: default;
        box-shadow: none;
    }
    .btn-pending-review:hover {
        transform: none;
        color: #92400e;
        box-shadow: none;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2.5rem;
        background: white;
        border-radius: 1rem;
        box-shadow: var(--shadow-card);
        border: 1px solid rgba(203,213,225,0.6);
    }
    .empty-state i {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }
    .empty-state h4 {
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    .empty-state p {
        color: #718096;
    }
    @media (max-width: 768px) {
        .booking-header {
            padding: 2.5rem 0 2.1rem;
            margin-bottom: 1.2rem;
            border-radius: 0 0 1.35rem 1.35rem;
        }
        .booking-header h1 {
            font-size: 1.4rem;
            line-height: 1.35;
        }
        .booking-header p {
            font-size: 0.86rem;
            line-height: 1.6;
        }
        .booking-header-actions {
            width: 100%;
        }
        .booking-header-actions .btn {
            width: 100%;
            justify-content: center;
        }
        .booking-page-body {
            padding-inline: 1rem;
        }
        .filter-panel {
            padding: 0.9rem;
            border-radius: 1rem;
        }
        .filter-panel-header {
            align-items: stretch;
            gap: 0.7rem;
        }
        .filter-panel-header h2 {
            font-size: 1rem;
        }
        .filter-reset {
            width: 100%;
            justify-content: center;
        }
        .filter-group {
            padding: 0.72rem;
            border-radius: 0.85rem;
        }
        .filter-group-title {
            font-size: 0.86rem;
        }
        .filter-tab {
            flex: 1 1 calc(50% - 0.55rem);
            min-height: 44px;
            font-size: 0.8rem;
            padding: 0.6rem 0.7rem;
        }
        .filter-count {
            min-width: 1.6rem;
            height: 1.6rem;
            font-size: 0.7rem;
        }
        .stats-row {
            grid-template-columns: 1fr;
            gap: 0.65rem;
        }
        .stat-pill {
            border-radius: 0.85rem;
            padding: 0.75rem 0.8rem;
        }
        .stat-pill .number {
            font-size: 1.2rem;
        }
        .bookings-list-header,
        .booking-explainer {
            border-radius: 0.9rem;
            padding: 0.8rem;
        }
        .booking-card {
            border-radius: 0.9rem;
            padding: 0.9rem;
        }
        .admin-status-stack,
        .booking-card .badges {
            gap: 0.3rem;
        }
        .badge-status,
        .badge-method,
        .badge-priority,
        .badge-deadline {
            font-size: 0.68rem;
        }
        .booking-card .header-row {
            flex-direction: column;
            align-items: stretch;
        }
        .booking-card .details-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .booking-card .actions,
        .admin-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.45rem;
        }
        .booking-card .actions form,
        .admin-actions form {
            width: 100%;
            display: block;
        }
        .booking-card .actions .btn,
        .admin-actions .btn,
        .booking-card .actions form .btn,
        .admin-actions form .btn {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
        }
        .admin-table-wrap {
            overflow-x: auto;
        }
        .admin-table {
            min-width: 820px;
        }
        .admin-table thead th,
        .admin-table tbody td {
            padding: 0.8rem 0.72rem;
        }
        .admin-table tbody tr {
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
    }
    @media (max-width: 576px) {
        .booking-header h1 {
            font-size: 1.25rem;
        }
        .booking-header p {
            font-size: 0.82rem;
        }
        .filter-tab {
            flex: 1 1 100%;
        }
        .booking-card .details-row {
            grid-template-columns: 1fr;
        }
        .empty-state {
            padding: 2.4rem 1rem;
        }
        .empty-state i {
            font-size: 3rem;
        }
    }
</style>

@php
    $isAdmin = Auth::user()->isAdmin();
    $currentStatus = $status ?: 'all';
    $bookingIndexRouteName = $isAdmin ? 'admin.bookings.index' : 'bookings.index';
    $bookingIndexUrl = fn (?string $filterStatus = null) => $filterStatus
        ? route($bookingIndexRouteName, ['status' => $filterStatus])
        : route($bookingIndexRouteName);

    $filterGroups = [
        [
            'title' => 'Status Utama',
            'copy' => $isAdmin
                ? 'Pantau alur booking utama dari yang baru masuk sampai yang sedang berjalan.'
                : 'Cek progres booking aktif Anda dari pengajuan sampai kendaraan dipakai.',
            'items' => [
                ['key' => 'all', 'label' => 'Semua', 'icon' => 'bi-grid', 'count' => $counts['all'], 'tone' => 'neutral', 'url' => $bookingIndexUrl()],
                ['key' => 'pending', 'label' => 'Pending', 'icon' => 'bi-hourglass-split', 'count' => $counts['pending'], 'tone' => 'warning', 'url' => $bookingIndexUrl('pending')],
                ['key' => 'scheduled', 'label' => 'Terjadwal', 'icon' => 'bi-calendar-event', 'count' => $counts['scheduled'], 'tone' => 'info', 'url' => $bookingIndexUrl('scheduled')],
                ['key' => 'awaiting_return', 'label' => 'Menunggu Unit', 'icon' => 'bi-truck-flatbed', 'count' => $counts['awaiting_return'], 'tone' => 'warning', 'url' => $bookingIndexUrl('awaiting_return')],
                ['key' => 'waiting_list', 'label' => 'Antrean', 'icon' => 'bi-list-ol', 'count' => $counts['waiting_list'], 'tone' => 'warning', 'url' => $bookingIndexUrl('waiting_list')],
                ['key' => 'active', 'label' => 'Aktif', 'icon' => 'bi-car-front', 'count' => $counts['active'], 'tone' => 'primary', 'url' => $bookingIndexUrl('active')],
            ],
        ],
        [
            'title' => 'Riwayat',
            'copy' => 'Lihat booking yang sudah selesai atau dibatalkan tanpa bercampur dengan proses yang masih berjalan.',
            'items' => [
                ['key' => 'completed', 'label' => 'Selesai', 'icon' => 'bi-check-circle', 'count' => $counts['completed'], 'tone' => 'success', 'url' => $bookingIndexUrl('completed')],
                ['key' => 'cancelled', 'label' => 'Batal', 'icon' => 'bi-x-circle', 'count' => $counts['cancelled'], 'tone' => 'danger', 'url' => $bookingIndexUrl('cancelled')],
            ],
        ],
    ];

    if ($isAdmin) {
        array_splice($filterGroups, 1, 0, [[
            'title' => 'Butuh Tindakan Admin',
            'copy' => 'Fokus ke booking yang perlu follow up manual supaya verifikasi dan pembatalan tidak telat.',
            'items' => [
                ['key' => 'maintenance_hold', 'label' => 'Tertahan Maintenance', 'icon' => 'bi-tools', 'count' => $counts['maintenance_hold'], 'tone' => 'danger', 'url' => $bookingIndexUrl('maintenance_hold')],
                ['key' => 'overdue_payment', 'label' => 'Lewat Deadline', 'icon' => 'bi-exclamation-octagon', 'count' => $counts['overdue_payment'], 'tone' => 'danger', 'url' => $bookingIndexUrl('overdue_payment')],
                ['key' => 'awaiting_proof', 'label' => 'Menunggu Konfirmasi', 'icon' => 'bi-chat-dots', 'count' => $counts['awaiting_proof'], 'tone' => 'warning', 'url' => $bookingIndexUrl('awaiting_proof')],
            ],
        ]]);
    }

    $filterLabels = collect($filterGroups)
        ->flatMap(fn ($group) => collect($group['items'])->mapWithKeys(fn ($item) => [$item['key'] => $item['label']]))
        ->all();

    $activeFilterLabel = $filterLabels[$currentStatus] ?? 'Semua';
    $activeFilterCount = $counts[$currentStatus] ?? $counts['all'];
    $heroStats = $isAdmin
        ? [
            ['label' => 'Total Booking', 'value' => $counts['all'], 'icon' => 'bi-collection', 'helper' => 'Semua data booking'],
            ['label' => 'Perlu Tindakan', 'value' => $counts['maintenance_hold'] + $counts['overdue_payment'] + $counts['awaiting_proof'], 'icon' => 'bi-lightning-charge', 'helper' => 'Perlu follow up admin'],
            ['label' => 'Sedang Berjalan', 'value' => $counts['active'] + $counts['awaiting_return'] + $counts['scheduled'], 'icon' => 'bi-activity', 'helper' => 'Masih aktif atau terjadwal'],
        ]
        : [
            ['label' => 'Total Booking', 'value' => $counts['all'], 'icon' => 'bi-collection', 'helper' => 'Semua riwayat booking Anda'],
            ['label' => 'Perlu Dibayar', 'value' => $counts['pending'], 'icon' => 'bi-credit-card', 'helper' => 'Belum lunas atau diverifikasi'],
            ['label' => 'Sedang Berjalan', 'value' => $counts['active'] + $counts['awaiting_return'] + $counts['scheduled'], 'icon' => 'bi-car-front', 'helper' => 'Masih aktif atau terjadwal'],
        ];

    $activeFilterDescription = match ($currentStatus) {
        'pending' => 'Booking yang masih menunggu pembayaran atau verifikasi awal.',
        'maintenance_hold' => 'Booking berbayar yang tertahan karena unit masuk maintenance dan perlu dijadwalkan ulang oleh admin.',
        'overdue_payment' => 'Booking yang sudah melewati batas pembayaran dan belum ada konfirmasi masuk.',
        'awaiting_proof' => 'Booking yang masih menunggu konfirmasi pembayaran dari customer.',
        'scheduled' => 'Booking yang sudah aman dan tinggal menunggu jadwal mulai.',
        'awaiting_return' => 'Booking yang jadwalnya sudah dekat atau sudah masuk, tetapi unit masih tertahan.',
        'waiting_list' => 'Booking yang masuk antrean karena jadwal kendaraan masih bentrok.',
        'active' => 'Booking yang sedang berjalan sekarang.',
        'completed' => 'Riwayat booking yang telah selesai.',
        'cancelled' => 'Booking yang batal atau gugur karena syarat pembayaran tidak terpenuhi.',
        default => $isAdmin
            ? 'Pantau semua booking pelanggan dari satu halaman kerja yang lebih rapi.'
            : 'Lihat semua riwayat booking Anda, termasuk yang aktif, terjadwal, dan sudah selesai.',
    };
@endphp

<!-- Booking Header -->
<div class="booking-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1>
                    <i class="bi bi-calendar-check me-2"></i>
                    {{ Auth::user()->isAdmin() ? 'Semua Booking' : 'Booking Saya' }}
                </h1>
                <p class="mb-0 opacity-75">
                    {{ Auth::user()->isAdmin() ? 'Kelola semua booking pelanggan' : 'Lihat dan kelola booking Anda' }} 📋
                </p>
            </div>
            <div class="booking-header-actions">
                @if($isAdmin)
                    <a href="{{ route('admin.bookings.timeline') }}" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-calendar3 me-2"></i>Timeline Mingguan
                    </a>
                @endif
                @if(!$isAdmin)
                    <a href="{{ route('vehicles.browse') }}" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-plus-circle me-2"></i>Booking Baru
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container pb-5 booking-page-body">
    <div class="filter-panel">
        <div class="filter-panel-header">
            <div>
                <span class="eyebrow">
                    <i class="bi bi-sliders"></i>Filter Booking
                </span>
                <h2>{{ $activeFilterLabel }} • {{ $activeFilterCount }} booking</h2>
            </div>
            @if($currentStatus !== 'all')
                <a href="{{ $bookingIndexUrl() }}" class="filter-reset">
                    <i class="bi bi-arrow-counterclockwise"></i>Reset Filter
                </a>
            @endif
        </div>

        <div class="filter-groups">
            @foreach($filterGroups as $group)
                <div class="filter-group">
                    <div class="filter-group-header">
                        <div class="filter-group-title">{{ $group['title'] }}</div>
                    </div>
                    <div class="filter-tabs">
                        @foreach($group['items'] as $item)
                            <a href="{{ $item['url'] }}" class="filter-tab {{ $currentStatus === $item['key'] ? 'active' : '' }}">
                                <span class="filter-tab-label">
                                    <i class="bi {{ $item['icon'] }}"></i>{{ $item['label'] }}
                                </span>
                                <span class="filter-count {{ $item['tone'] }}">{{ $item['count'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="stats-row mb-4">
        @foreach($heroStats as $stat)
            <div class="stat-pill">
                <span class="stat-pill-icon"><i class="bi {{ $stat['icon'] }}"></i></span>
                <div>
                    <div class="number">{{ $stat['value'] }}</div>
                    <div class="label">{{ $stat['label'] }}</div>
                    <div class="helper">{{ $stat['helper'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    @if($isAdmin)
        @include('bookings.partials.admin-index')
    @else
        @include('bookings.partials.customer-index')
    @endif
</div>
@endsection
