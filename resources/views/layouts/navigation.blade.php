@php
    $dashboardActive = request()->routeIs('dashboard');
    $browseActive = request()->routeIs('vehicles.browse') || request()->routeIs('vehicles.show');
    $bookingsActive = request()->routeIs('bookings.*');
    $reportsActive = request()->routeIs('reports.*');
    $reviewsActive = request()->routeIs('reviews.*');
    $adminActive = request()->routeIs('admin.*');
    $profileActive = request()->routeIs('profile.*');
    $aboutActive = request()->routeIs('about');
    $guideActive = request()->routeIs('guide');
@endphp

<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand d-inline-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="brand-mark"><i class="bi bi-car-front-fill"></i></span>
            <span>RentalHub</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ $dashboardActive ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $browseActive ? 'active' : '' }}" href="{{ route('vehicles.browse') }}">Cari Kendaraan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $bookingsActive ? 'active' : '' }}" href="{{ route('bookings.index') }}">Booking Saya</a>
                    </li>
                    @unless(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ $reportsActive ? 'active' : '' }}" href="{{ route('reports.transactions') }}">Laporan Keuangan</a>
                    </li>
                    @endunless
                    @unless(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ $reviewsActive ? 'active' : '' }}" href="{{ route('reviews.index') }}">Ulasan Saya</a>
                    </li>
                    @endunless
                    <li class="nav-item">
                        <a class="nav-link {{ $aboutActive ? 'active' : '' }}" href="{{ route('about') }}">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $guideActive ? 'active' : '' }}" href="{{ route('guide') }}">Panduan</a>
                    </li>
                    @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2-fill me-1"></i>Admin Panel</a>
                    </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ $profileActive ? 'active' : '' }}" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            @unless(Auth::user()->isAdmin())
                                <li><a class="dropdown-item {{ request()->routeIs('profile.ktp') ? 'active' : '' }}" href="{{ route('profile.ktp') }}"><i class="bi bi-person-badge me-2"></i>Verifikasi KTP</a></li>
                            @endunless
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vehicles.browse') || request()->routeIs('vehicles.show') ? 'active' : '' }}" href="{{ route('vehicles.browse') }}">Cari Kendaraan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $aboutActive ? 'active' : '' }}" href="{{ route('about') }}">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $guideActive ? 'active' : '' }}" href="{{ route('guide') }}">Panduan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Daftar</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
