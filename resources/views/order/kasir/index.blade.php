@extends('layouts.template')

@section('content')
<div class="container mt-3">
    <div class="d-flex justify-content-end">
        <a href="{{ route('kasir.order.create') }}" class="btn btn-primary"> Pembelian Baru</a>
    </div>
    <!-- Form Filter Tanggal -->
    <form action="{{ route('kasir.order.filter') }}" method="GET" class="my-3">
        <div class="row">
            <div class="col-md-4">
                <input type="date" name="tanggal_pembelian" class="form-control" placeholder="Pilih Tanggal">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Cari Data</button>
                <a href="{{ route('kasir.order.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </form>

    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Pembeli</th>
                <th>Obat</th>
                <th>Total Bayar</th>
                <th>Kasir</th>
                <th>Tanggal Beli</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($orders as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name_customer'] }}</td>
                <td>
                    {{-- karna columnn medicines pada table orders bertipe json yg diubah formatnya menjadi array, maka
                    dari itu untk mengakses/menampilkan itemnya perlu menggunakan looping --}}
                    @foreach ($item['medicines'] as $medicine)
                    <ol>
                        <li>
                            {{-- mengakses key array assoc dari tiap item array value column medicines--}}
                            {{ $medicine['name_medicine'] }} ({{ number_format($medicine['price'], 0, ',', '.') }}) :
                            Rp. {{ number_format($medicine['sub_price'], 0, ',', '.') }}
                            <small>qty {{ $medicine['qty'] }}</small>
                        </li>
                    </ol>
                    @endforeach
                </td>
                <td>
                    Rp. {{ number_format($item['total_price'],0, ',', '.') }}
                </td>
                {{-- karena nama kasir terdapat pada table users, dan relasi antara order dan users telah didefinisikan
                pada function relasi bernama user.
                maka, untuk mengakses column pada table users melalui relasi antara keduanya dapt dilakukan dengan
                $var['namaFuncRelasi']['columnDariTableLainnya'] --}}
                <td>{{ $item['user']['name'] }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($item['created_at'])->translatedFormat('j F Y') }}
                </td>
                <td>
                    <a href="{{ route('kasir.order.download', $item['id']) }}" class="btn btn-secondary">Download
                        Setruk</a>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        {{-- jika data ada atau > 0 --}}
        @if ($orders->count())
        {{-- munculkan tampilan pagination --}}
        {{ $orders->links() }}
        @endif
    </div>
</div>

@endsection