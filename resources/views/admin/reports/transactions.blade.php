@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan')

@section('content')
<div class="container pb-4">
    @include('reports.partials.statement-list', [
        'title' => 'Laporan Keuangan Admin',
        'subtitle' => 'Rekap transaksi booking yang telah selesai per bulan',
        'indexRoute' => 'admin.reports.transactions',
        'excelRoute' => 'admin.reports.transactions.export.excel',
        'pdfRoute' => 'admin.reports.transactions.export.pdf',
    ])
</div>
@endsection
