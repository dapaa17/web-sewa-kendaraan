@if ($paginator->hasPages())
<style>
    .custom-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.4rem;
        margin-top: 2.5rem;
        flex-wrap: wrap;
    }
    .custom-pagination .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.6rem;
        height: 2.6rem;
        padding: 0.4rem 0.85rem;
        border-radius: 0.85rem;
        font-size: 0.92rem;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid #e2e8f0;
        background: white;
        color: #475569;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .custom-pagination .page-btn:hover:not(.active):not(.disabled) {
        background: linear-gradient(135deg, rgba(6,182,212,0.08), rgba(15,23,42,0.06));
        border-color: var(--color-secondary, #06b6d4);
        color: var(--color-primary, #0f172a);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(6,182,212,0.18);
    }
    .custom-pagination .page-btn.active {
        background: var(--gradient-brand, linear-gradient(135deg, #0f172a, #1e3a5f));
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 14px rgba(15,23,42,0.3);
        transform: scale(1.08);
    }
    .custom-pagination .page-btn.disabled {
        background: #f8fafc;
        border-color: #f1f5f9;
        color: #cbd5e1;
        cursor: not-allowed;
        box-shadow: none;
    }
    .custom-pagination .page-btn.nav-btn {
        padding: 0.4rem 1rem;
        font-size: 1rem;
    }
    .custom-pagination .page-dots {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.6rem;
        height: 2.6rem;
        color: #94a3b8;
        font-weight: 700;
        letter-spacing: 0.15em;
        font-size: 0.9rem;
    }
    .custom-pagination .page-info {
        width: 100%;
        text-align: center;
        margin-top: 0.75rem;
        color: #94a3b8;
        font-size: 0.82rem;
        font-weight: 500;
    }
</style>
<nav aria-label="Pagination">
    <div class="custom-pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="page-btn nav-btn disabled" aria-disabled="true">
                <i class="bi bi-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn nav-btn" rel="prev" aria-label="Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn nav-btn" rel="next" aria-label="Selanjutnya">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="page-btn nav-btn disabled" aria-disabled="true">
                <i class="bi bi-chevron-right"></i>
            </span>
        @endif

        {{-- Page Info --}}
        <div class="page-info">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </div>
    </div>
</nav>
@endif
