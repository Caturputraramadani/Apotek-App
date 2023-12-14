@extends('layouts.template')

@section('content')
<div class="my-5 d-flex justify-content-end">
    <a href="{{ route('order.export-excel') }}" class="btn btn-primary">Export Data (excel)</a>
</div>
<!-- Form Filter Tanggal -->
<form action="{{ route('admin.order.filter') }}" method="GET" class="my-3">
    <div class="row">
        <div class="col-md-4">
            <input type="date" name="tanggal_pembelian" class="form-control" placeholder="Pilih Tanggal">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Cari Data</button>
            <a href="{{ route('admin.order.data') }}" class="btn btn-secondary">Clear</a>
        </div>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th>Pembeli</th>
            <th>Obat</th>
            <th>Kasir</th>
            <th>Tanggal Beli</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
        <tr>
            {{-- menampilkan angka urutan berdasarkan page pagination (digunakan ketika mengambil data dengan
            paginate/simplepaginate) --}}
            <td>{{ ($orders->currentpage()-1) * $orders->perpage() + $loop->index + 1 }}</td>
            <td>{{ $order->name_customer }}</td>
            <td>
                {{-- nested loop: didalam looping ada looping --}}
                {{-- karna column medicines tipe datanya array json, maka untk akesesnya perlu di looping --}}
                <ol>
                    @foreach ($order['medicines'] as $medicine)
                    <li>
                        {{-- hasil yg diinginkan --}}
                        {{-- 1. nama obat (Rp.3000) : RP. 15000 qty 5 --}}
                        {{ $medicine['name_medicine'] }}
                        ( Rp.{{ number_format($medicine['price'], 0, ',', '.') }}) :
                        Rp. {{ number_format($medicine['sub_price'], 0, ',', '.') }}
                        <small>qty {{ $medicine['qty'] }}</small>
                    </li>
                    @endforeach
                </ol>
            </td>
            <td>{{ $order['user']['name'] }}</td>
            {{-- carbon:package bawaan laravel untuk mengatur hal yg berkaitan tipe data date/datetime --}}
            @php
            // setting lokal time sbg wil indonesia
            setlocale(LC_ALL, 'IND');
            @endphp
            <td>
                {{ Carbon\Carbon::parse($order->created_at)->formatLocalized('%d %B %Y') }}
            </td>
            <td>
                {{-- @if(Auth::user()->isAdmin()) --}}
                <a href="{{ route('admin.order.download', $order['id']) }}" class="btn btn-secondary">Unduh </a>
                {{-- @endif --}}
            </td>

        </tr>
        @endforeach
    </tbody>
</table>

@endsection