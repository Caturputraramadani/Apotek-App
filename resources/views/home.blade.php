@extends('layouts.template')

@section('content')
<div class="jumbotron py-4 px-5">
    @if(session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    <h1 class="display-4">
        Selamat Datang {{ Auth::user()->name }}!
    </h1>
    <hr class="my-4">
    <p>
        Aplikasi ini digunakan oleh pegawai administrator APOTEK. Digunakan untuk mengisi data obat, penyetokan, juga
        pembelian (kasir).
    </p>
</div>
@endsection