<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin - RentalHub')</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2306B6D4'><path d='M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z'/></svg>">

        <!-- Bootstrap CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=sora:600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/js/app.js'])

        <style>
            :root {
                color-scheme: light;
                --color-primary: #1F2937;
                --color-primary-rgb: 31, 41, 55;
                --color-secondary: #06B6D4;
                --color-secondary-rgb: 6, 182, 212;
                --color-secondary-strong: #0E7490;
                --color-accent: #EF4444;
                --color-accent-rgb: 239, 68, 68;
                --color-success: #10B981;
                --color-success-rgb: 16, 185, 129;
                --color-warning: #F59E0B;
                --color-warning-rgb: 245, 158, 11;
                --color-surface: #F8FAFC;
                --color-surface-alt: #EEF2F7;
                --color-card: #FFFFFF;
                --color-border: #CBD5E1;
                --color-muted: #64748B;
                --color-heading: #0F172A;
                --font-body: 'Manrope', sans-serif;
                --font-display: 'Sora', sans-serif;
                --shadow-soft: 0 10px 30px rgba(15, 23, 42, 0.08);
                --shadow-card: 0 18px 45px rgba(15, 23, 42, 0.08);
                --shadow-card-hover: 0 22px 55px rgba(6, 182, 212, 0.16);
                --gradient-brand: linear-gradient(135deg, #111827 0%, #1F2937 52%, #06B6D4 100%);
                --gradient-cyan: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%);
                --gradient-soft: linear-gradient(135deg, rgba(31, 41, 55, 0.06) 0%, rgba(6, 182, 212, 0.14) 100%);
                --gradient-info-soft: linear-gradient(135deg, rgba(31, 41, 55, 0.08) 0%, rgba(6, 182, 212, 0.16) 100%);
                --gradient-success: linear-gradient(135deg, #10B981 0%, #059669 100%);
                --gradient-warning: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
                --gradient-danger: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
                --gradient-muted: linear-gradient(135deg, #94A3B8 0%, #64748B 100%);

                --sidebar-w-expanded: 232px;
                --sidebar-w-collapsed: 88px;
                --sidebar-w: var(--sidebar-w-expanded);
                --sidebar-bg: #111827;
                --sidebar-text: rgba(255,255,255,0.65);
                --sidebar-text-hover: #ffffff;
                --sidebar-active-bg: rgba(6,182,212,0.15);
                --sidebar-active-text: #06B6D4;
                --topbar-h: 64px;
            }

            *, *::before, *::after { box-sizing: border-box; }

            body {
                margin: 0;
                color: var(--color-heading);
                font-family: var(--font-body);
                font-feature-settings: 'cv02','cv03','cv04','cv11';
                line-height: 1.65;
                letter-spacing: -0.015em;
                background: var(--color-surface);
                overflow-x: hidden;
            }

            h1,h2,h3,h4,h5,h6,.display-1,.display-2,.display-3,.display-4,.display-5,.display-6,.btn,.badge {
                font-family: var(--font-display);
                letter-spacing: -0.04em;
            }

            p,li,td,th,label,input,textarea,select,small,span { font-family: var(--font-body); }

            h1,h2,h3,h4,h5,h6 { line-height: 1.1; }

            /* ───── Layout Shell ───── */
            .adm-shell {
                display: flex;
                min-height: 100vh;
            }

            /* ───── Sidebar ───── */
            .adm-sidebar {
                width: var(--sidebar-w);
                background: var(--sidebar-bg);
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1040;
                overflow: hidden;
                transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), width 0.25s ease;
            }

            .adm-sidebar-brand {
                padding: 1.5rem 1.5rem 1.25rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-decoration: none;
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }
            .adm-sidebar-brand-copy {
                min-width: 0;
                transition: opacity 0.2s ease, width 0.2s ease;
            }
            .adm-sidebar-brand-mark {
                width: 2.4rem;
                height: 2.4rem;
                border-radius: 0.75rem;
                background: linear-gradient(135deg, var(--color-secondary) 0%, #0891B2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.15rem;
                flex-shrink: 0;
            }
            .adm-sidebar-brand-text {
                font-family: var(--font-display);
                font-weight: 800;
                font-size: 1.2rem;
                color: white;
                letter-spacing: -0.04em;
            }
            .adm-sidebar-brand-sub {
                font-size: 0.7rem;
                color: var(--color-secondary);
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.06em;
            }

            .adm-sidebar-nav {
                flex: 1;
                padding: 1.25rem 0.85rem;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: rgba(255,255,255,0.1) transparent;
            }

            .adm-nav-label {
                font-size: 0.68rem;
                font-weight: 700;
                color: rgba(255,255,255,0.3);
                text-transform: uppercase;
                letter-spacing: 0.1em;
                padding: 0.75rem 0.75rem 0.4rem;
                margin-top: 0.5rem;
            }

            .adm-nav-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.7rem 0.85rem;
                border-radius: 0.75rem;
                color: var(--sidebar-text);
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 500;
                transition: all 0.2s ease;
                margin-bottom: 0.15rem;
            }
            .adm-nav-text {
                min-width: 0;
                white-space: nowrap;
                transition: opacity 0.2s ease, width 0.2s ease;
            }
            .adm-nav-item:hover {
                background: rgba(255,255,255,0.06);
                color: var(--sidebar-text-hover);
            }
            .adm-nav-item.active {
                background: var(--sidebar-active-bg);
                color: var(--sidebar-active-text);
                font-weight: 600;
            }
            .adm-nav-item i {
                font-size: 1.15rem;
                width: 1.25rem;
                text-align: center;
                flex-shrink: 0;
            }
            .adm-nav-item .adm-nav-badge {
                margin-left: auto;
                background: var(--color-secondary);
                color: white;
                font-size: 0.7rem;
                padding: 0.15rem 0.55rem;
                border-radius: 999px;
                font-weight: 700;
            }

            .adm-sidebar-footer {
                padding: 1rem 0.85rem;
                border-top: 1px solid rgba(255,255,255,0.08);
            }
            .adm-sidebar-user {
                display: flex;
                align-items: center;
                gap: 0.7rem;
                padding: 0.65rem 0.85rem;
                border-radius: 0.75rem;
                text-decoration: none;
                color: var(--sidebar-text);
                transition: all 0.2s ease;
            }
            .adm-sidebar-user-copy {
                min-width: 0;
                white-space: nowrap;
                transition: opacity 0.2s ease, width 0.2s ease;
            }
            .adm-sidebar-user:hover {
                background: rgba(255,255,255,0.06);
                color: var(--sidebar-text-hover);
            }
            .adm-sidebar-avatar {
                width: 2.2rem;
                height: 2.2rem;
                border-radius: 0.65rem;
                background: linear-gradient(135deg, var(--color-secondary) 0%, #0891B2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-family: var(--font-display);
                font-weight: 700;
                font-size: 0.85rem;
                flex-shrink: 0;
            }
            .adm-sidebar-uname {
                font-weight: 600;
                font-size: 0.88rem;
                color: rgba(255,255,255,0.85);
                line-height: 1.2;
            }
            .adm-sidebar-urole {
                font-size: 0.72rem;
                color: rgba(255,255,255,0.4);
            }

            /* ───── Main Content ───── */
            .adm-main {
                flex: 1;
                margin-left: var(--sidebar-w);
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                max-width: calc(100vw - var(--sidebar-w));
                overflow-x: hidden;
                transition: margin-left 0.25s ease, max-width 0.25s ease;
                background:
                    radial-gradient(circle at top right, rgba(var(--color-secondary-rgb), 0.07), transparent 28%),
                    linear-gradient(180deg, var(--color-surface) 0%, var(--color-surface-alt) 100%);
            }

            /* ───── Top Bar ───── */
            .adm-topbar {
                height: var(--topbar-h);
                background: rgba(255,255,255,0.82);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(203,213,225,0.55);
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 2rem;
                position: sticky;
                top: 0;
                z-index: 1030;
            }
            .adm-topbar-title {
                font-family: var(--font-display);
                font-weight: 700;
                font-size: 1.15rem;
                color: var(--color-heading);
                letter-spacing: -0.03em;
            }
            .adm-topbar-actions {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .adm-topbar-btn {
                width: 2.4rem;
                height: 2.4rem;
                border-radius: 0.7rem;
                border: 1px solid rgba(203,213,225,0.5);
                background: white;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--color-muted);
                font-size: 1.1rem;
                cursor: pointer;
                transition: all 0.2s ease;
                text-decoration: none;
            }
            .adm-topbar-btn:hover {
                background: rgba(var(--color-secondary-rgb), 0.1);
                border-color: var(--color-secondary);
                color: var(--color-secondary-strong);
            }
            .adm-sidebar-desktop-toggle {
                display: inline-flex;
            }

            .adm-content .filter-panel,
            .adm-content .timeline-filters {
                background: rgba(255, 255, 255, 0.94);
                backdrop-filter: blur(12px);
            }

            .adm-content .filter-panel,
            .adm-content .timeline-filters,
            .adm-content .timeline-legend,
            .adm-content .bookings-list-header,
            .adm-content .timeline-board,
            .adm-content .info-card,
            .adm-content .settings-card,
            .adm-content .vf-card,
            .adm-content .user-card,
            .adm-content .media-card,
            .adm-content .panel-card,
            .adm-content .description-card,
            .adm-content .bk-card,
            .adm-content .booking-summary-card,
            .adm-content .payment-card,
            .adm-content .op-card,
            .adm-content .transfer-card,
            .adm-content .steps-card,
            .adm-content .wa-container .booking-card,
            .adm-content .whatsapp-card,
            .adm-content .empty-state,
            .adm-content .vh-empty,
            .adm-content .tbl-wrap {
                box-shadow: var(--shadow-soft);
            }

            @media (min-width: 992px) {
                .adm-shell.is-collapsed:not(.is-hover-expanded) {
                    --sidebar-w: var(--sidebar-w-collapsed);
                }

                .adm-shell.is-collapsed.is-hover-expanded {
                    --sidebar-w: var(--sidebar-w-expanded);
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-brand,
                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-user,
                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-nav-item {
                    justify-content: center;
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-brand {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-nav,
                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-footer {
                    padding-left: 0.7rem;
                    padding-right: 0.7rem;
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-nav-label {
                    opacity: 0;
                    height: 0;
                    padding: 0;
                    margin: 0;
                    overflow: hidden;
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-brand-copy,
                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-nav-text,
                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-nav-badge,
                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-user-copy {
                    opacity: 0;
                    width: 0;
                    overflow: hidden;
                    pointer-events: none;
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-nav-item {
                    gap: 0;
                    padding-left: 0.75rem;
                    padding-right: 0.75rem;
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-nav-item i {
                    width: auto;
                    font-size: 1.2rem;
                }

                .adm-shell.is-collapsed:not(.is-hover-expanded) .adm-sidebar-user {
                    padding-left: 0.55rem;
                    padding-right: 0.55rem;
                }

                .adm-shell.is-collapsed.is-hover-expanded .adm-sidebar {
                    box-shadow: 24px 0 50px rgba(15, 23, 42, 0.2);
                }

                .adm-shell.is-collapsed.is-hover-expanded .adm-sidebar-brand-copy,
                .adm-shell.is-collapsed.is-hover-expanded .adm-nav-text,
                .adm-shell.is-collapsed.is-hover-expanded .adm-nav-badge,
                .adm-shell.is-collapsed.is-hover-expanded .adm-sidebar-user-copy {
                    opacity: 1;
                    width: auto;
                    overflow: visible;
                    pointer-events: auto;
                }
            }

            @media (min-width: 1200px) {
                .adm-content .filter-panel,
                .adm-content .timeline-filters {
                    position: sticky;
                    top: calc(var(--topbar-h) + 1rem);
                    z-index: 16;
                }
            }

            /* ───── Content Area ───── */
            .adm-content {
                flex: 1;
                padding: 1.5rem;
            }

            /* ───── Mobile Sidebar Toggle ───── */
            .adm-sidebar-toggle {
                display: none;
                width: 2.4rem;
                height: 2.4rem;
                border-radius: 0.7rem;
                border: 1px solid rgba(203,213,225,0.5);
                background: white;
                align-items: center;
                justify-content: center;
                color: var(--color-heading);
                font-size: 1.2rem;
                cursor: pointer;
            }
            .adm-sidebar-close {
                display: none;
                position: absolute;
                top: 1.1rem;
                right: 1rem;
                width: 2rem;
                height: 2rem;
                border-radius: 0.5rem;
                border: none;
                background: rgba(255,255,255,0.1);
                color: rgba(255,255,255,0.6);
                font-size: 1rem;
                cursor: pointer;
                align-items: center;
                justify-content: center;
            }
            .adm-sidebar-close:hover {
                background: rgba(255,255,255,0.15);
                color: white;
            }
            .adm-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
            }
            .adm-sidebar-tooltip .tooltip-inner {
                background: rgba(15, 23, 42, 0.96);
                color: #fff;
                font-family: var(--font-body);
                font-size: 0.8rem;
                font-weight: 600;
                letter-spacing: -0.01em;
                padding: 0.45rem 0.7rem;
                border-radius: 0.65rem;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.22);
            }
            .adm-sidebar-tooltip.bs-tooltip-end .tooltip-arrow::before {
                border-right-color: rgba(15, 23, 42, 0.96);
            }

            /* ───── Reusable Components ───── */
            .card, .modal-content, .dropdown-menu {
                background: rgba(255,255,255,0.96);
                border: 1px solid rgba(203,213,225,0.75);
                box-shadow: var(--shadow-card);
            }
            .card, .modal-content { border-radius: 1.25rem; backdrop-filter: blur(10px); }
            .card-header {
                background: linear-gradient(135deg, rgba(31,41,55,0.05) 0%, rgba(6,182,212,0.08) 100%);
                border-bottom: 1px solid rgba(203,213,225,0.75);
                color: var(--color-heading);
                font-weight: 600;
            }
            .card-body, .modal-body { padding: 1.5rem; }
            .form-control, .form-select { border-color: rgba(148,163,184,0.45); }
            .form-control:focus, .form-select:focus {
                border-color: var(--color-secondary);
                box-shadow: 0 0 0 0.25rem rgba(var(--color-secondary-rgb), 0.18);
            }

            .btn { border-radius: 0.85rem; font-weight: 600; letter-spacing: -0.025em; transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }
            .btn:hover { transform: translateY(-1px); }
            .btn-primary { background-color: var(--color-primary); border-color: var(--color-primary); color: #fff; box-shadow: 0 12px 24px rgba(var(--color-primary-rgb), 0.18); }
            .btn-primary:hover, .btn-primary:focus, .btn-primary:active { background-color: var(--color-secondary); border-color: var(--color-secondary); color: var(--color-primary); box-shadow: 0 16px 30px rgba(var(--color-secondary-rgb), 0.22); }
            .btn-outline-primary { border-color: rgba(var(--color-primary-rgb), 0.22); color: var(--color-primary); }
            .btn-outline-primary:hover { background-color: rgba(var(--color-secondary-rgb), 0.12); border-color: var(--color-secondary); color: var(--color-primary); }
            .btn-info { background: var(--gradient-cyan); border-color: transparent; color: #fff; }
            .btn-info:hover { background: var(--color-primary); border-color: var(--color-primary); color: #fff; }

            .badge { border-radius: 999px; font-weight: 600; letter-spacing: -0.02em; }
            .badge.bg-primary { background-color: var(--color-primary) !important; }
            .badge.bg-info { background-color: rgba(var(--color-secondary-rgb), 0.18) !important; color: var(--color-primary) !important; }
            .badge.bg-success { background-color: var(--color-success) !important; }
            .badge.bg-warning { background-color: #FDE68A !important; color: #92400E !important; }
            .badge.bg-danger { background-color: var(--color-accent) !important; }

            .alert { border: 1px solid transparent; border-radius: 1rem; box-shadow: var(--shadow-soft); }
            .alert-info { background: linear-gradient(135deg, rgba(31,41,55,0.08) 0%, rgba(6,182,212,0.14) 100%); border-color: rgba(var(--color-secondary-rgb), 0.24); color: var(--color-primary); }
            .alert-success { background: linear-gradient(135deg, rgba(16,185,129,0.14) 0%, rgba(209,250,229,0.4) 100%); border-color: rgba(var(--color-success-rgb), 0.28); color: #065F46; }
            .alert-warning { background: linear-gradient(135deg, rgba(245,158,11,0.16) 0%, rgba(251,191,36,0.2) 100%); border-color: rgba(var(--color-warning-rgb), 0.26); color: #92400E; }
            .alert-danger { background: linear-gradient(135deg, rgba(239,68,68,0.16) 0%, rgba(254,202,202,0.4) 100%); border-color: rgba(var(--color-accent-rgb), 0.3); color: #991B1B; }
            .adm-flash-stack { display: grid; gap: 0.9rem; margin-bottom: 1.25rem; }
            .adm-flash-stack .alert { margin-bottom: 0; }
            .adm-flash-stack .btn-close { padding: 1rem; }

            .table { --bs-table-hover-bg: rgba(6,182,212,0.06); }
            .table thead th { letter-spacing: 0.02em; }

            .page-link { color: var(--color-primary); border-color: rgba(203,213,225,0.85); min-width: 2.8rem; height: 2.8rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.9rem; background: rgba(255,255,255,0.94); box-shadow: 0 10px 22px rgba(15,23,42,0.06); font-weight: 700; }
            .page-link:hover { background: rgba(var(--color-secondary-rgb), 0.12); border-color: var(--color-secondary); }
            .pagination { gap: 0.45rem; flex-wrap: wrap; }
            .page-item.active .page-link { background-color: var(--color-primary); border-color: var(--color-primary); }

            .lead { line-height: 1.8; color: var(--color-muted); }

            .dropdown-menu { border-radius: 1rem; }
            .dropdown-item { border-radius: 0.75rem; padding: 0.65rem 0.85rem; color: var(--color-primary); }
            .dropdown-item:hover { background: rgba(var(--color-secondary-rgb), 0.14); color: var(--color-primary); }

            .form-check-input:checked { background-color: var(--color-primary); border-color: var(--color-primary); }

            /* ───── Content Overrides ───── */
            .adm-content .container {
                max-width: 100%;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            /* Make gradient headers flush with content edges */
            .adm-content .dash-header,
            .adm-content .vh-header,
            .adm-content .vf-header,
            .adm-content .pf-header,
            .adm-content .booking-header,
            .adm-content .ktp-header,
            .adm-content .complete-header,
            .adm-content .settings-hero,
            .adm-content .timeline-header,
            .adm-content .gd-header,
            .adm-content .vehicle-hero,
            .adm-content .detail-header,
            .adm-content .bk-header,
            .adm-content .payment-header,
            .adm-content .op-header,
            .adm-content .wa-header {
                margin: -1.5rem -1.5rem 0;
                border-radius: 0 0 1.5rem 1.5rem;
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }
            .adm-content .rpt-header {
                margin: -1.5rem -1.5rem 0;
                border-radius: 0 0 1.5rem 1.5rem;
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }
            .adm-content .browse-header {
                margin: -1.5rem -1.5rem 2rem;
                border-radius: 0 0 1.5rem 1.5rem;
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }
            /* Inner containers inside headers get natural padding */
            .adm-content .dash-header .container,
            .adm-content .vh-header .container,
            .adm-content .vf-header .container,
            .adm-content .pf-header .container,
            .adm-content .booking-header .container,
            .adm-content .ktp-header .container,
            .adm-content .complete-header .container,
            .adm-content .settings-hero .container,
            .adm-content .timeline-header .container,
            .adm-content .browse-header .container,
            .adm-content .gd-header .container,
            .adm-content .vehicle-hero .container,
            .adm-content .detail-header .container,
            .adm-content .bk-header .container,
            .adm-content .payment-header .container,
            .adm-content .op-header .container,
            .adm-content .wa-header .container,
            .adm-content .rpt-header .container {
                padding-left: 0;
                padding-right: 0;
            }
            /* Body sections inside admin content */
            .adm-content .dash-body,
            .adm-content .vh-body,
            .adm-content .vf-body,
            .adm-content .pf-body,
            .adm-content .gd-body,
            .adm-content .bk-body,
            .adm-content .op-body,
            .adm-content .rpt-body {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            .adm-content .settings-shell,
            .adm-content .ktp-container,
            .adm-content .complete-container,
            .adm-content .vehicle-shell,
            .adm-content .detail-container,
            .adm-content .payment-container,
            .adm-content .wa-container {
                width: 100%;
                max-width: min(100%, 1280px);
            }
            .adm-content .vf-header .col-lg-8.offset-lg-2,
            .adm-content .vf-body .col-lg-8.offset-lg-2,
            .adm-content .pf-header .col-lg-8.offset-lg-2,
            .adm-content .pf-body .col-lg-8.offset-lg-2 {
                width: 100%;
                max-width: 1180px;
                margin-left: auto;
                margin-right: auto;
            }
            .adm-content .vh-tbl-wrap,
            .adm-content .tbl-wrap,
            .adm-content .timeline-board {
                max-width: 100%;
                overflow-x: auto;
            }
            .adm-content .settings-grid > *,
            .adm-content .settings-summary-grid > *,
            .adm-content .placeholder-grid > *,
            .adm-content .vf-row > *,
            .adm-content .planner-form > *,
            .adm-content .inspection-grid > *,
            .adm-content .checklist-grid > *,
            .adm-content .timeline-filters > * {
                min-width: 0;
            }
            .adm-content .timeline-field,
            .adm-content .timeline-filter-actions,
            .adm-content .timeline-problem-toggle,
            .adm-content .settings-card,
            .adm-content .placeholder-item,
            .adm-content .user-card .user-info {
                min-width: 0;
            }

            @media (max-width: 1279.98px) {
                .adm-content {
                    padding: 1.35rem;
                }

                .adm-content .dash-header,
                .adm-content .vh-header,
                .adm-content .vf-header,
                .adm-content .pf-header,
                .adm-content .booking-header,
                .adm-content .ktp-header,
                .adm-content .complete-header,
                .adm-content .settings-hero,
                .adm-content .timeline-header,
                .adm-content .gd-header,
                .adm-content .vehicle-hero,
                .adm-content .detail-header,
                .adm-content .bk-header,
                .adm-content .payment-header,
                .adm-content .op-header,
                .adm-content .wa-header,
                .adm-content .rpt-header {
                    margin: -1.35rem -1.35rem 0;
                    padding-left: 1.75rem;
                    padding-right: 1.75rem;
                }
                .adm-content .browse-header {
                    margin: -1.35rem -1.35rem 1.75rem;
                    padding-left: 1.75rem;
                    padding-right: 1.75rem;
                }

                .adm-content .timeline-filters,
                .adm-content .settings-grid,
                .adm-content .settings-summary-grid,
                .adm-content .placeholder-grid,
                .adm-content .vf-row,
                .adm-content .planner-form,
                .adm-content .inspection-grid,
                .adm-content .checklist-grid {
                    grid-template-columns: 1fr;
                }

                .adm-content .settings-tabs {
                    grid-template-columns: 1fr;
                }

                .adm-content .aside-stack {
                    position: static;
                }

                .adm-content .timeline-filter-actions {
                    justify-content: flex-start;
                    flex-wrap: wrap;
                }

                .adm-content .timeline-problem-toggle {
                    white-space: normal;
                }

                .adm-content .timeline-header-top,
                .adm-content .timeline-week-nav,
                .adm-content .timeline-header-actions {
                    justify-content: flex-start;
                }

                .adm-content .timeline-header-actions {
                    width: 100%;
                }

                .adm-content .vh-tbl {
                    min-width: 760px;
                }

                .adm-content .vh-actions {
                    flex-wrap: wrap;
                }
            }

            /* ───── Responsive ───── */
            @media (max-width: 991.98px) {
                .adm-sidebar-desktop-toggle {
                    display: none;
                }
                .adm-sidebar {
                    transform: translateX(-100%);
                }
                .adm-sidebar.open {
                    transform: translateX(0);
                }
                .adm-sidebar-close {
                    display: flex;
                }
                .adm-overlay.open {
                    display: block;
                }
                .adm-sidebar-toggle {
                    display: flex;
                }
                .adm-main {
                    margin-left: 0;
                    max-width: 100vw;
                }
                .adm-content {
                    padding: 1.25rem;
                }
                .adm-topbar {
                    padding: 0 1.25rem;
                }
            }
        </style>
        @yield('css')
    </head>
    <body>
        @php
            $admDashActive = request()->routeIs('admin.dashboard');
            $admVehiclesActive = request()->routeIs('admin.vehicles.*');
            $admBookingsActive = request()->routeIs('admin.bookings.index')
                || request()->routeIs('admin.bookings.complete-form')
                || (Auth::user()->isAdmin() && (
                    request()->routeIs('bookings.show')
                    || request()->routeIs('bookings.create')
                    || request()->routeIs('bookings.payment')
                    || request()->routeIs('payments.whatsapp-confirmation')
                ));
            $admTimelineActive = request()->routeIs('admin.bookings.timeline');
            $admCalendarActive = request()->routeIs('admin.calendar.*') || request()->routeIs('admin.api.fleet-availability');
            $admKtpActive = request()->routeIs('admin.ktp.*');
            $admReviewsActive = request()->routeIs('admin.reviews.*');
            $admSettingsActive = request()->routeIs('admin.settings.*');
            $admReportsActive = request()->routeIs('admin.reports.*');
            $admProfileActive = request()->routeIs('profile.*');
            $admBrowseActive = request()->routeIs('vehicles.browse') || request()->routeIs('vehicles.show') || request()->routeIs('vehicles.calendar');
            $admGuideActive = request()->routeIs('guide');
        @endphp

        <div class="adm-shell">
            <!-- Overlay -->
            <div class="adm-overlay" id="admOverlay" onclick="closeSidebar()"></div>

            <!-- Sidebar -->
            <aside class="adm-sidebar" id="admSidebar">
                <button class="adm-sidebar-close" onclick="closeSidebar()"><i class="bi bi-x-lg"></i></button>

                <a class="adm-sidebar-brand" href="{{ route('admin.dashboard') }}" data-adm-tooltip="Dashboard Admin" aria-label="Dashboard Admin">
                    <div class="adm-sidebar-brand-mark"><i class="bi bi-car-front-fill"></i></div>
                    <div class="adm-sidebar-brand-copy">
                        <div class="adm-sidebar-brand-text">RentalHub</div>
                        <div class="adm-sidebar-brand-sub">Admin Panel</div>
                    </div>
                </a>

                <nav class="adm-sidebar-nav">
                    <div class="adm-nav-label">Menu Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="adm-nav-item {{ $admDashActive ? 'active' : '' }}" data-adm-tooltip="Dashboard" aria-label="Dashboard">
                        <i class="bi bi-grid-1x2-fill"></i><span class="adm-nav-text">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.vehicles.index') }}" class="adm-nav-item {{ $admVehiclesActive ? 'active' : '' }}" data-adm-tooltip="Kendaraan" aria-label="Kendaraan">
                        <i class="bi bi-car-front"></i><span class="adm-nav-text">Kendaraan</span>
                    </a>
                    <a href="{{ route('vehicles.browse') }}" class="adm-nav-item {{ $admBrowseActive ? 'active' : '' }}" data-adm-tooltip="Cari Kendaraan" aria-label="Cari Kendaraan">
                        <i class="bi bi-search"></i><span class="adm-nav-text">Cari Kendaraan</span>
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="adm-nav-item {{ $admBookingsActive ? 'active' : '' }}" data-adm-tooltip="Booking" aria-label="Booking">
                        <i class="bi bi-calendar-check"></i><span class="adm-nav-text">Booking</span>
                        @if(($adminSidebarStats['booking_attention'] ?? 0) > 0)
                            <span class="adm-nav-badge">{{ $adminSidebarStats['booking_attention'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.bookings.timeline') }}" class="adm-nav-item {{ $admTimelineActive ? 'active' : '' }}" data-adm-tooltip="Timeline Booking" aria-label="Timeline Booking">
                        <i class="bi bi-calendar3"></i><span class="adm-nav-text">Timeline</span>
                    </a>
                    <a href="{{ route('admin.calendar.index') }}" class="adm-nav-item {{ $admCalendarActive ? 'active' : '' }}" data-adm-tooltip="Kalender Armada" aria-label="Kalender Armada">
                        <i class="bi bi-grid-3x3-gap"></i><span class="adm-nav-text">Kalender Armada</span>
                    </a>

                    <div class="adm-nav-label">Verifikasi</div>
                    <a href="{{ route('admin.ktp.index') }}" class="adm-nav-item {{ $admKtpActive ? 'active' : '' }}" data-adm-tooltip="Verifikasi KTP" aria-label="Verifikasi KTP">
                        <i class="bi bi-person-badge"></i><span class="adm-nav-text">Verifikasi KTP</span>
                        @if(($adminSidebarStats['ktp_pending'] ?? 0) > 0)
                            <span class="adm-nav-badge">{{ $adminSidebarStats['ktp_pending'] }}</span>
                        @endif
                    </a>

                    <div class="adm-nav-label">Moderasi</div>
                    <a href="{{ route('admin.reviews.index') }}" class="adm-nav-item {{ $admReviewsActive ? 'active' : '' }}" data-adm-tooltip="Ulasan" aria-label="Ulasan">
                        <i class="bi bi-star-half"></i><span class="adm-nav-text">Ulasan</span>
                        @if(($adminSidebarStats['review_pending'] ?? 0) > 0)
                            <span class="adm-nav-badge">{{ $adminSidebarStats['review_pending'] }}</span>
                        @endif
                    </a>

                    <div class="adm-nav-label">Laporan</div>
                    <a href="{{ route('admin.reports.transactions') }}" class="adm-nav-item {{ $admReportsActive ? 'active' : '' }}" data-adm-tooltip="Laporan Transaksi" aria-label="Laporan Transaksi">
                        <i class="bi bi-file-earmark-bar-graph"></i><span class="adm-nav-text">Laporan Transaksi</span>
                    </a>

                    <div class="adm-nav-label">Pengaturan</div>
                    <a href="{{ route('admin.settings.index') }}" class="adm-nav-item {{ $admSettingsActive ? 'active' : '' }}" data-adm-tooltip="Pengaturan" aria-label="Pengaturan">
                        <i class="bi bi-sliders"></i><span class="adm-nav-text">Pengaturan</span>
                    </a>

                    <div class="adm-nav-label">Lainnya</div>
                    <a href="{{ route('profile.edit') }}" class="adm-nav-item {{ $admProfileActive ? 'active' : '' }}" data-adm-tooltip="Profile" aria-label="Profile">
                        <i class="bi bi-person-circle"></i><span class="adm-nav-text">Profile</span>
                    </a>
                    <a href="{{ route('guide') }}" class="adm-nav-item {{ $admGuideActive ? 'active' : '' }}" data-adm-tooltip="Panduan" aria-label="Panduan">
                        <i class="bi bi-book"></i><span class="adm-nav-text">Panduan</span>
                    </a>
                </nav>

                <div class="adm-sidebar-footer">
                    <div class="adm-sidebar-user" data-adm-tooltip="{{ Auth::user()->name }}" aria-label="{{ Auth::user()->name }}">
                        <div class="adm-sidebar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        <div class="adm-sidebar-user-copy">
                            <div class="adm-sidebar-uname">{{ Auth::user()->name }}</div>
                            <div class="adm-sidebar-urole">Administrator</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="adm-nav-item" data-adm-tooltip="Logout" aria-label="Logout" style="width:100%;border:none;background:none;cursor:pointer;">
                            <i class="bi bi-box-arrow-left"></i><span class="adm-nav-text">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main -->
            <div class="adm-main">
                <!-- Top Bar -->
                <header class="adm-topbar">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="adm-sidebar-toggle" onclick="openSidebar()"><i class="bi bi-list"></i></button>
                        <button type="button" class="adm-topbar-btn adm-sidebar-desktop-toggle" id="admSidebarCollapseToggle" onclick="toggleSidebarCollapse()" title="Ciutkan sidebar" aria-label="Ciutkan sidebar">
                            <i class="bi bi-layout-sidebar"></i>
                        </button>
                        <div class="adm-topbar-title">@yield('page-title', 'Dashboard')</div>
                    </div>
                    <div class="adm-topbar-actions">
                        <a href="{{ route('profile.edit') }}" class="adm-topbar-btn" title="Profile"><i class="bi bi-person"></i></a>
                    </div>
                </header>

                <!-- Content -->
                <div class="adm-content">
                    @php
                        $adminFlashMessages = collect([
                            ['key' => 'success', 'icon' => 'bi-check-circle-fill', 'title' => 'Berhasil', 'class' => 'alert-success'],
                            ['key' => 'error', 'icon' => 'bi-exclamation-octagon-fill', 'title' => 'Ada masalah', 'class' => 'alert-danger'],
                            ['key' => 'warning', 'icon' => 'bi-exclamation-triangle-fill', 'title' => 'Perlu perhatian', 'class' => 'alert-warning'],
                            ['key' => 'info', 'icon' => 'bi-info-circle-fill', 'title' => 'Info', 'class' => 'alert-info'],
                        ])->filter(fn ($flash) => session()->has($flash['key']));
                    @endphp

                    @if($adminFlashMessages->isNotEmpty())
                        <div class="adm-flash-stack">
                            @foreach($adminFlashMessages as $flash)
                                <div class="alert {{ $flash['class'] }} alert-dismissible fade show" role="alert">
                                    <div class="d-flex align-items-start gap-3 pe-4">
                                        <i class="bi {{ $flash['icon'] }} fs-5"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold mb-1">{{ $flash['title'] }}</div>
                                            <div>{{ session($flash['key']) }}</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script>
            const admShell = document.querySelector('.adm-shell');
            const admSidebar = document.getElementById('admSidebar');
            const admSidebarCollapseToggle = document.getElementById('admSidebarCollapseToggle');
            const admSidebarCollapseIcon = admSidebarCollapseToggle?.querySelector('i');
            const admSidebarStorageKey = 'rentalhub-admin-sidebar-collapsed';
            let admSidebarTooltipInstances = [];

            function isDesktopSidebar() {
                return window.innerWidth >= 992;
            }

            function getStoredSidebarCollapsed() {
                try {
                    return localStorage.getItem(admSidebarStorageKey) === '1';
                } catch (error) {
                    return false;
                }
            }

            function setStoredSidebarCollapsed(collapsed) {
                try {
                    localStorage.setItem(admSidebarStorageKey, collapsed ? '1' : '0');
                } catch (error) {
                    // Ignore storage errors and keep the UI functional.
                }
            }

            function syncSidebarCollapseButton(collapsed) {
                if (!admSidebarCollapseToggle || !admSidebarCollapseIcon) {
                    return;
                }

                admSidebarCollapseToggle.setAttribute('title', collapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar');
                admSidebarCollapseToggle.setAttribute('aria-label', collapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar');
                admSidebarCollapseToggle.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
                admSidebarCollapseIcon.className = collapsed ? 'bi bi-layout-sidebar-inset-reverse' : 'bi bi-layout-sidebar';
            }

            function disposeSidebarTooltips() {
                admSidebarTooltipInstances.forEach((tooltip) => tooltip.dispose());
                admSidebarTooltipInstances = [];
            }

            function syncSidebarTooltips(collapsed) {
                disposeSidebarTooltips();

                if (!collapsed || !isDesktopSidebar() || typeof bootstrap === 'undefined' || admShell?.classList.contains('is-hover-expanded')) {
                    return;
                }

                document.querySelectorAll('#admSidebar [data-adm-tooltip]').forEach((element) => {
                    admSidebarTooltipInstances.push(new bootstrap.Tooltip(element, {
                        trigger: 'hover focus',
                        placement: 'right',
                        boundary: 'viewport',
                        customClass: 'adm-sidebar-tooltip',
                        title: element.getAttribute('data-adm-tooltip'),
                    }));
                });
            }

            function applySidebarCollapsedState(collapsed) {
                if (!admShell) {
                    return;
                }

                const shouldCollapse = isDesktopSidebar() && collapsed;
                admShell.classList.toggle('is-collapsed', shouldCollapse);
                admShell.classList.remove('is-hover-expanded');
                syncSidebarCollapseButton(shouldCollapse);
                syncSidebarTooltips(shouldCollapse);
            }

            function openSidebar() {
                document.getElementById('admSidebar').classList.add('open');
                document.getElementById('admOverlay').classList.add('open');
            }

            function closeSidebar() {
                document.getElementById('admSidebar').classList.remove('open');
                document.getElementById('admOverlay').classList.remove('open');
            }

            function toggleSidebarCollapse() {
                const nextCollapsed = !admShell?.classList.contains('is-collapsed');
                setStoredSidebarCollapsed(nextCollapsed);
                applySidebarCollapsedState(nextCollapsed);
            }

            admSidebar?.addEventListener('mouseenter', () => {
                if (!isDesktopSidebar() || !admShell?.classList.contains('is-collapsed')) {
                    return;
                }

                admShell.classList.add('is-hover-expanded');
                syncSidebarTooltips(false);
            });

            admSidebar?.addEventListener('mouseleave', () => {
                if (!admShell?.classList.contains('is-collapsed')) {
                    return;
                }

                admShell.classList.remove('is-hover-expanded');
                syncSidebarTooltips(true);
            });

            applySidebarCollapsedState(getStoredSidebarCollapsed());
            window.addEventListener('resize', () => {
                applySidebarCollapsedState(getStoredSidebarCollapsed());
            });
        </script>
        @yield('js')
    </body>
</html>