<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'RentalHub')</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=sora:600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
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
                --bs-body-bg: var(--color-surface);
                --bs-body-color: var(--color-heading);
                --bs-border-color: rgba(203, 213, 225, 0.85);
                --bs-primary: var(--color-primary);
                --bs-primary-rgb: var(--color-primary-rgb);
                --bs-info: var(--color-secondary);
                --bs-info-rgb: var(--color-secondary-rgb);
                --bs-link-color: var(--color-primary);
                --bs-link-hover-color: var(--color-secondary-strong);
                --bs-focus-ring-color: rgba(var(--color-secondary-rgb), 0.25);
            }

            body {
                background:
                    radial-gradient(circle at top right, rgba(var(--color-secondary-rgb), 0.12), transparent 28%),
                    linear-gradient(180deg, var(--color-surface) 0%, var(--color-surface-alt) 100%);
                color: var(--color-heading);
                font-family: var(--font-body);
                font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
                line-height: 1.65;
                letter-spacing: -0.015em;
            }

            main {
                min-height: calc(100vh - 84px);
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            .display-1,
            .display-2,
            .display-3,
            .display-4,
            .display-5,
            .display-6,
            .navbar-brand,
            .btn,
            .badge {
                font-family: var(--font-display);
                letter-spacing: -0.04em;
            }

            p,
            li,
            td,
            th,
            label,
            input,
            textarea,
            select,
            small,
            span {
                font-family: var(--font-body);
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                line-height: 1.1;
            }

            .lead {
                line-height: 1.8;
                color: var(--color-muted);
            }

            .container,
            .container-sm,
            .container-md,
            .container-lg,
            .container-xl,
            .container-xxl,
            .container-fluid {
                --bs-gutter-x: 1.35rem;
            }

            a {
                color: var(--color-primary);
                transition: color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            }

            a:hover {
                color: var(--color-secondary-strong);
            }

            .text-primary {
                color: var(--color-primary) !important;
            }

            .text-info {
                color: var(--color-secondary-strong) !important;
            }

            .bg-primary {
                background-color: var(--color-primary) !important;
            }

            .bg-info {
                background-color: rgba(var(--color-secondary-rgb), 0.18) !important;
                color: var(--color-primary) !important;
            }

            .border-primary {
                border-color: rgba(var(--color-primary-rgb), 0.35) !important;
            }

            .card,
            .modal-content,
            .dropdown-menu {
                background: rgba(255, 255, 255, 0.96);
                border: 1px solid rgba(203, 213, 225, 0.75);
                box-shadow: var(--shadow-card);
            }

            .card,
            .modal-content {
                border-radius: 1.25rem;
                backdrop-filter: blur(10px);
            }

            .card-header {
                background: linear-gradient(135deg, rgba(31, 41, 55, 0.05) 0%, rgba(6, 182, 212, 0.08) 100%);
                border-bottom: 1px solid rgba(203, 213, 225, 0.75);
                color: var(--color-heading);
                font-weight: 600;
            }

            .card-body,
            .modal-body {
                padding: 1.5rem;
            }

            .form-control,
            .form-select {
                border-color: rgba(148, 163, 184, 0.45);
            }

            .form-control:focus,
            .form-select:focus {
                border-color: var(--color-secondary);
                box-shadow: 0 0 0 0.25rem rgba(var(--color-secondary-rgb), 0.18);
            }

            .form-check-input:checked {
                background-color: var(--color-primary);
                border-color: var(--color-primary);
            }

            .form-check-input:focus {
                border-color: var(--color-secondary);
                box-shadow: 0 0 0 0.25rem rgba(var(--color-secondary-rgb), 0.15);
            }

            .btn {
                border-radius: 0.85rem;
                font-weight: 600;
                letter-spacing: -0.025em;
                transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            }

            .btn-lg {
                padding: 0.9rem 1.35rem;
            }

            .btn:hover {
                transform: translateY(-1px);
            }

            .btn-primary {
                background-color: var(--color-primary);
                border-color: var(--color-primary);
                color: #fff;
                box-shadow: 0 12px 24px rgba(var(--color-primary-rgb), 0.18);
            }

            .btn-primary:hover,
            .btn-primary:focus,
            .btn-primary:active,
            .btn-primary.active,
            .show > .btn-primary.dropdown-toggle {
                background-color: var(--color-secondary);
                border-color: var(--color-secondary);
                color: var(--color-primary);
                box-shadow: 0 16px 30px rgba(var(--color-secondary-rgb), 0.22);
            }

            .btn-outline-primary {
                border-color: rgba(var(--color-primary-rgb), 0.22);
                color: var(--color-primary);
            }

            .btn-outline-primary:hover,
            .btn-outline-primary:focus {
                background-color: rgba(var(--color-secondary-rgb), 0.12);
                border-color: var(--color-secondary);
                color: var(--color-primary);
            }

            .btn-outline-secondary {
                border-color: rgba(var(--color-primary-rgb), 0.14);
                color: var(--color-primary);
                background: rgba(255, 255, 255, 0.86);
            }

            .btn-outline-secondary:hover,
            .btn-outline-secondary:focus {
                background: rgba(var(--color-secondary-rgb), 0.1);
                border-color: var(--color-secondary);
                color: var(--color-primary);
                box-shadow: 0 12px 24px rgba(var(--color-secondary-rgb), 0.14);
            }

            .btn-light {
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid rgba(255, 255, 255, 0.24);
                color: var(--color-primary);
                box-shadow: 0 14px 28px rgba(15, 23, 42, 0.1);
            }

            .btn-light:hover,
            .btn-light:focus {
                background: #ffffff;
                border-color: rgba(var(--color-secondary-rgb), 0.35);
                color: var(--color-primary);
                box-shadow: 0 18px 36px rgba(var(--color-secondary-rgb), 0.18);
            }

            .btn-info {
                background: var(--gradient-cyan);
                border-color: transparent;
                color: #ffffff;
                box-shadow: 0 12px 24px rgba(var(--color-secondary-rgb), 0.2);
            }

            .btn-info:hover,
            .btn-info:focus,
            .btn-info:active {
                background: var(--color-primary);
                border-color: var(--color-primary);
                color: #ffffff;
                box-shadow: 0 16px 30px rgba(var(--color-primary-rgb), 0.22);
            }

            .alert {
                border: 1px solid transparent;
                border-radius: 1rem;
                box-shadow: var(--shadow-soft);
            }

            .alert-info {
                background: linear-gradient(135deg, rgba(31, 41, 55, 0.08) 0%, rgba(6, 182, 212, 0.14) 100%);
                border-color: rgba(var(--color-secondary-rgb), 0.24);
                color: var(--color-primary);
            }

            .alert-warning {
                background: linear-gradient(135deg, rgba(245, 158, 11, 0.16) 0%, rgba(251, 191, 36, 0.2) 100%);
                border-color: rgba(var(--color-warning-rgb), 0.26);
                color: #92400E;
            }

            .alert-danger {
                background: linear-gradient(135deg, rgba(239, 68, 68, 0.16) 0%, rgba(254, 202, 202, 0.4) 100%);
                border-color: rgba(var(--color-accent-rgb), 0.3);
                color: #991B1B;
            }

            .alert-success {
                background: linear-gradient(135deg, rgba(16, 185, 129, 0.14) 0%, rgba(209, 250, 229, 0.4) 100%);
                border-color: rgba(var(--color-success-rgb), 0.28);
                color: #065F46;
            }

            .alert-light {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.92) 0%, rgba(238, 242, 247, 0.96) 100%);
                border-color: rgba(203, 213, 225, 0.7);
                color: var(--color-primary);
            }

            .badge {
                border-radius: 999px;
                font-weight: 600;
                letter-spacing: -0.02em;
            }

            .badge.bg-primary,
            .badge.text-bg-primary {
                background-color: var(--color-primary) !important;
                color: #fff !important;
            }

            .badge.bg-info,
            .badge.text-bg-info {
                background-color: rgba(var(--color-secondary-rgb), 0.18) !important;
                color: var(--color-primary) !important;
            }

            .badge.bg-secondary,
            .badge.text-bg-secondary {
                background-color: #64748B !important;
                color: #fff !important;
            }

            .badge.bg-warning,
            .badge.text-bg-warning {
                background-color: #FDE68A !important;
                color: #92400E !important;
            }

            .badge.bg-danger,
            .badge.text-bg-danger {
                background-color: var(--color-accent) !important;
                color: #fff !important;
            }

            .badge.bg-light,
            .badge.text-bg-light {
                background-color: rgba(255, 255, 255, 0.94) !important;
                color: var(--color-primary) !important;
                border: 1px solid rgba(203, 213, 225, 0.72);
                box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            }

            .badge.bg-success,
            .badge.text-bg-success {
                background-color: var(--color-success) !important;
                color: #fff !important;
            }

            .table {
                --bs-table-hover-bg: rgba(6, 182, 212, 0.06);
            }

            .page-link {
                color: var(--color-primary);
                border-color: rgba(203, 213, 225, 0.85);
                min-width: 2.8rem;
                height: 2.8rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.65rem 0.95rem;
                border-radius: 0.9rem;
                background: rgba(255, 255, 255, 0.94);
                box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
                font-weight: 700;
            }

            .page-link:hover {
                color: var(--color-primary);
                background: rgba(var(--color-secondary-rgb), 0.12);
                border-color: var(--color-secondary);
            }

            .pagination {
                gap: 0.45rem;
                flex-wrap: wrap;
            }

            .page-item.active .page-link {
                background-color: var(--color-primary);
                border-color: var(--color-primary);
            }

            .page-item.disabled .page-link {
                color: var(--color-muted);
                background: rgba(248, 250, 252, 0.82);
                box-shadow: none;
            }

            .table thead th {
                letter-spacing: 0.02em;
            }

            .table-responsive {
                border-radius: inherit;
                scrollbar-width: thin;
                scrollbar-color: rgba(var(--color-secondary-rgb), 0.35) transparent;
            }

            .table-responsive::-webkit-scrollbar {
                height: 0.55rem;
            }

            .table-responsive::-webkit-scrollbar-track {
                background: transparent;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: rgba(var(--color-secondary-rgb), 0.3);
                border-radius: 999px;
            }

            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: rgba(var(--color-secondary-rgb), 0.42);
            }

            .bg-light {
                background-color: rgba(248, 250, 252, 0.88) !important;
            }

            code {
                background: rgba(var(--color-secondary-rgb), 0.1);
                color: var(--color-primary);
                padding: 0.2rem 0.4rem;
                border-radius: 0.45rem;
                font-size: 0.9em;
            }

            .app-navbar {
                background: var(--gradient-brand);
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.22);
                position: sticky;
                top: 0;
                z-index: 1030;
            }

            .app-navbar .navbar-brand {
                color: #fff;
                font-weight: 700;
                letter-spacing: 0.02em;
            }

            .app-navbar .brand-mark {
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 0.85rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.16);
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
            }

            .app-navbar .nav-link,
            .app-navbar .dropdown-toggle {
                color: rgba(255, 255, 255, 0.84);
                border-radius: 999px;
                padding: 0.55rem 0.9rem;
            }

            .app-navbar .nav-link:hover,
            .app-navbar .nav-link:focus,
            .app-navbar .nav-link.active,
            .app-navbar .dropdown-toggle:hover,
            .app-navbar .dropdown-toggle:focus,
            .app-navbar .dropdown-toggle.active {
                color: #fff;
                background: rgba(255, 255, 255, 0.13);
            }

            .app-navbar .dropdown-menu {
                margin-top: 0.75rem;
                border-radius: 1rem;
                padding: 0.5rem;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
                border: 1px solid rgba(203, 213, 225, 0.6);
            }

            .app-navbar .dropdown-item {
                border-radius: 0.75rem;
                padding: 0.65rem 0.85rem;
                color: var(--color-primary);
            }

            .app-navbar .dropdown-item:hover,
            .app-navbar .dropdown-item:focus,
            .app-navbar .dropdown-item.active {
                background: rgba(var(--color-secondary-rgb), 0.14);
                color: var(--color-primary);
            }

            .app-navbar .dropdown-divider {
                border-color: rgba(203, 213, 225, 0.8);
            }

            .app-navbar .navbar-toggler {
                border-color: rgba(255, 255, 255, 0.25);
                box-shadow: none;
            }

            .app-navbar .navbar-toggler:focus {
                box-shadow: 0 0 0 0.22rem rgba(var(--color-secondary-rgb), 0.25);
            }

            @media (max-width: 991.98px) {
                main {
                    min-height: calc(100vh - 76px);
                }

                .app-navbar .navbar-collapse {
                    align-items: stretch;
                    background: rgba(17, 24, 39, 0.78);
                    border: 1px solid rgba(255, 255, 255, 0.14);
                    border-radius: 1.25rem;
                    padding: 1rem;
                    margin-top: 1rem;
                    backdrop-filter: blur(14px);
                }

                .app-navbar .navbar-nav {
                    gap: 0.35rem;
                }
            }

            @media (max-width: 767.98px) {
                .page-link {
                    min-width: 2.45rem;
                    height: 2.45rem;
                    padding: 0.55rem 0.75rem;
                    font-size: 0.92rem;
                }

                .table {
                    font-size: 0.94rem;
                }

                .table thead th,
                .table tbody td {
                    white-space: nowrap;
                }
            }
        </style>
        @yield('css')
    </head>
    <body>
        <div class="min-vh-100 bg-white">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        @yield('js')
    </body>
</html>
