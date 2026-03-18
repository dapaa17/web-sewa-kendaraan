<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --color-primary: #1F2937;
                --color-primary-rgb: 31, 41, 55;
                --color-secondary: #06B6D4;
                --color-secondary-rgb: 6, 182, 212;
                --color-secondary-strong: #0E7490;
                --color-heading: #0F172A;
                --color-muted: #64748B;
                --gradient-brand: linear-gradient(135deg, #111827 0%, #1F2937 52%, #06B6D4 100%);
            }

            * { box-sizing: border-box; }

            body {
                font-family: 'Manrope', sans-serif;
                margin: 0;
                min-height: 100vh;
            }

            .auth-shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
                position: relative;
                background:
                    radial-gradient(circle at 80% 10%, rgba(6,182,212,0.1), transparent 40%),
                    radial-gradient(circle at 20% 90%, rgba(31,41,55,0.06), transparent 40%),
                    linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            }

            .auth-container {
                width: 100%;
                max-width: 440px;
            }

            .auth-brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.6rem;
                text-decoration: none;
                color: var(--color-heading);
                margin-bottom: 2rem;
            }
            .auth-brand-mark {
                width: 3.6rem; height: 3.6rem;
                border-radius: 1rem;
                display: flex; align-items: center; justify-content: center;
                background: var(--gradient-brand);
                color: white;
                font-weight: 800;
                font-size: 1rem;
                letter-spacing: 0.06em;
                box-shadow: 0 10px 28px rgba(15,23,42,0.18);
            }
            .auth-brand-text {
                font-weight: 800;
                font-size: 1.5rem;
                letter-spacing: -0.05em;
                background: var(--gradient-brand);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .auth-card {
                background: white;
                border: 1px solid rgba(203,213,225,0.5);
                border-radius: 1.25rem;
                box-shadow: 0 12px 40px rgba(15,23,42,0.08);
                padding: 2.2rem 2rem;
            }

            .auth-card h2 {
                font-weight: 800;
                font-size: 1.45rem;
                color: var(--color-heading);
                letter-spacing: -0.05em;
                margin: 0 0 0.3rem;
            }
            .auth-card .auth-subtitle {
                color: var(--color-muted);
                font-size: 0.88rem;
                margin-bottom: 1.75rem;
                line-height: 1.6;
            }

            .auth-card .form-label {
                display: block;
                width: 100%;
                font-weight: 600;
                font-size: 0.82rem;
                color: var(--color-heading);
                margin-bottom: 0.4rem;
            }
            .auth-card .form-control {
                border-radius: 0.7rem;
                padding: 0.7rem 0.9rem;
                border: 1px solid rgba(203,213,225,0.7);
                font-size: 0.88rem;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .auth-card .form-control:focus {
                border-color: var(--color-secondary);
                box-shadow: 0 0 0 3px rgba(var(--color-secondary-rgb), 0.15);
            }

            /* Input with icon */
            .input-icon-wrap {
                position: relative;
            }
            .input-icon-wrap .form-control {
                padding-left: 2.6rem;
            }
            .input-icon-wrap .input-icon {
                position: absolute;
                left: 0.85rem;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                font-size: 1rem;
                pointer-events: none;
                transition: color 0.2s;
            }
            .input-icon-wrap .form-control:focus ~ .input-icon,
            .input-icon-wrap .form-control:focus + .input-icon {
                color: var(--color-secondary);
            }
            /* Password toggle */
            .input-icon-wrap .pw-toggle {
                position: absolute;
                right: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                font-size: 1rem;
                cursor: pointer;
                pointer-events: auto;
                background: none;
                border: none;
                padding: 0;
                line-height: 1;
                transition: color 0.2s;
            }
            .input-icon-wrap .pw-toggle:hover {
                color: var(--color-secondary-strong);
            }

            .auth-card .form-check-input:checked {
                background-color: var(--color-primary);
                border-color: var(--color-primary);
            }

            .btn-auth {
                width: 100%;
                padding: 0.72rem;
                border-radius: 0.7rem;
                font-weight: 700;
                font-size: 0.9rem;
                border: none;
                color: white;
                background: var(--gradient-brand);
                box-shadow: 0 6px 20px rgba(var(--color-primary-rgb), 0.18);
                transition: all 0.25s ease;
                cursor: pointer;
            }
            .btn-auth:hover {
                box-shadow: 0 10px 28px rgba(var(--color-secondary-rgb), 0.22);
                transform: translateY(-1px);
                color: white;
            }

            .btn-auth-secondary {
                width: 100%;
                padding: 0.65rem;
                border-radius: 0.7rem;
                font-weight: 600;
                font-size: 0.85rem;
                border: 1px solid rgba(203,213,225,0.6);
                color: var(--color-muted);
                background: white;
                transition: all 0.2s ease;
                cursor: pointer;
                text-decoration: none;
                display: block;
                text-align: center;
            }
            .btn-auth-secondary:hover {
                border-color: rgba(var(--color-secondary-rgb), 0.4);
                color: var(--color-heading);
                background: #f8fafc;
            }

            .auth-footer {
                text-align: center;
                margin-top: 1.5rem;
                font-size: 0.84rem;
                color: var(--color-muted);
            }
            .auth-footer a {
                color: var(--color-secondary-strong);
                font-weight: 600;
                text-decoration: none;
            }
            .auth-footer a:hover {
                text-decoration: underline;
            }

            .auth-alert {
                padding: 0.75rem 1rem;
                border-radius: 0.7rem;
                font-size: 0.84rem;
                margin-bottom: 1.25rem;
                line-height: 1.55;
            }
            .auth-alert.success {
                background: rgba(16,185,129,0.1);
                border: 1px solid rgba(16,185,129,0.2);
                color: #065f46;
            }
            .auth-alert.danger {
                background: rgba(239,68,68,0.08);
                border: 1px solid rgba(239,68,68,0.18);
                color: #991b1b;
            }
            .auth-alert ul {
                margin: 0;
                padding-left: 1.1rem;
            }

            .auth-divider {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin: 1.25rem 0;
                color: #cbd5e1;
                font-size: 0.78rem;
            }
            .auth-divider::before,
            .auth-divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: rgba(203,213,225,0.6);
            }
        </style>
    </head>
    <body>
        <div class="auth-shell">
            <div class="auth-container">
                <a href="/" class="auth-brand">
                    <span class="auth-brand-mark">RH</span>
                    <span class="auth-brand-text">RentalHub</span>
                </a>

                <div class="auth-card">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
