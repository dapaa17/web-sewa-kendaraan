@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="background:var(--gradient-brand);padding:3rem 0 5.5rem;position:relative;overflow:hidden">
    <div style="position:absolute;inset:0;background:radial-gradient(circle at 80% 25%,rgba(6,182,212,.22),transparent 55%);pointer-events:none"></div>
</div>
<div style="margin-top:-3.5rem;position:relative;z-index:2;padding-bottom:3rem">
    <div class="container">
        <div style="max-width:480px;margin:0 auto;text-align:center;background:#fff;border-radius:1.15rem;box-shadow:0 4px 24px rgba(15,23,42,.07);border:1px solid rgba(203,213,225,.45);padding:3rem 2rem">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(245,158,11,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;color:#d97706">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <h5 style="font-weight:700;color:#0f172a;margin-bottom:.5rem">Memuat Dashboard…</h5>
            <p style="font-size:.88rem;color:#64748b;margin-bottom:1.25rem">Halaman ini akan dialihkan secara otomatis.</p>
            <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.4rem;border-radius:.85rem;background:var(--gradient-brand);color:#fff;font-weight:600;font-size:.88rem;text-decoration:none;transition:all .25s">
                <i class="bi bi-arrow-clockwise"></i> Muat Ulang
            </a>
        </div>
    </div>
</div>
@endsection
