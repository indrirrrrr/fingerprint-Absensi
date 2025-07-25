@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Edit Karyawan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nomor Karyawan</label>
                <input type="text" name="nomor_karyawan" class="form-control" value="{{ $karyawan->nomor_karyawan }}" required>
            </div>
            <div class="form-group">
                <label>Nama Karyawan</label>
                <input type="text" name="nama_karyawan" class="form-control" value="{{ $karyawan->nama_karyawan }}" required>
            </div>

            <div class="form-group">
                <label>Shift</label>
                <select name="shift" class="form-control" required>
                    <option value="1" {{ $karyawan->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                    <option value="2" {{ $karyawan->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                    <option value="3" {{ $karyawan->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection