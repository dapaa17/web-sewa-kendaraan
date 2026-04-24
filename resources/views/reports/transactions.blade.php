@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container py-3 py-lg-4">
    @include('reports.partials.statement-list', [
        'title' => 'Laporan Keuangan Saya',
        'subtitle' => 'Rekap transaksi booking Anda yang telah selesai per bulan',
        'indexRoute' => 'reports.transactions',
        'excelRoute' => 'reports.transactions.export.excel',
        'pdfRoute' => 'reports.transactions.export.pdf',
    ])
</div>
@endsection
