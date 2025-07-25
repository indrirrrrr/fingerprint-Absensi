@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Karyawan</h6>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('karyawan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nomor Karyawan (Harus sama dengan ID di Mesin)</label>
                <input type="text" name="nomor_karyawan" class="form-control" value="{{ old('nomor_karyawan') }}" required>
            </div>
            <div class="form-group">
                <label>Nama Karyawan</label>
                <input type="text" name="nama_karyawan" class="form-control" value="{{ old('nama_karyawan') }}" required>
            </div>
             
            <div class="form-group">
                <label>Shift</label>
                <select name="shift" class="form-control" required>
                    <option value="" disabled selected>Pilih Shift</option>
                    <option value="1">Shift 1</option>
                    <option value="2">Shift 2</option>
                    <option value="3">Shift 3</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection