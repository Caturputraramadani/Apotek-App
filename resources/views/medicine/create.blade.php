@extends('layouts.template')

@section('content')
<form action="{{ route('medicine.store') }}" method="post" class="card p-5">
    @csrf

    @if(Session::get('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if($errors->any())
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <div class="mb-3 row">
        <label for="name" class="col-sm-2 col-form-label">Nama Obat :</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
        </div>
    </div>

    <div class="mb-3 row">
        <label for="type" class="col-sm-2 col-form-label">Jenis Obat :</label>
        <div class="col-sm-10">
            <select name="type" id="type" class="form-select">
                <option selected disabled hidden>Pilih</option>
                <option value="tablet" {{ old('type')=='tablet' ? 'selected' : '' }}>tablet</option>
                <option value="sirup" {{ old('type')=='sirup' ? 'selected' :'' }}>sirup</option>
                <option value="kapsul" {{ old('type')=='kapsul' ? 'selected' : '' }}>kapsul</option>
            </select>
        </div>
    </div>

    <div class="mb-3 row">
        <label for="price" class="col-sm-2 col-form-label">Harga Obat :</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}">
        </div>
    </div>

    <div class=" mb-3 row">
        <label for="stock" class="col-sm-2 col-form-label">Stok Tersedia :</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock') }}">
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Tambah Data</button>

</form>
@endsection