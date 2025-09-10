@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">{{ $title }}</h2>
        <a href="{{ route('survey.selectCity') }}" class="btn btn-outline-secondary">
            ← Kembali ke Pilih Lokasi
        </a>
    </div>

    <form method="GET" class="mb-4" action="{{ route('survey.selectInstitution', $slug) }}">
        <div class="input-group shadow-sm">
            <input type="text" name="search" class="form-control" placeholder="Cari instansi..." value="{{ $search }}">
            <button class="btn btn-success" type="submit">
                <i class="bi bi-search"></i> Cari
            </button>
        </div>
    </form>

    @if($institutions->isEmpty())
        <div class="alert alert-light border text-center">
            <i class="bi bi-exclamation-circle"></i> <span class="fw-bold text-danger">Tidak ada instansi yang ditemukan.</span>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($institutions as $institution)
                <div class="col">
                    <a href="{{ route('survey.form', $institution->slug) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow transition" style="background-color: #f8fafc;">
                            <div class="card-body">
                                <h5 class="card-title text-primary fw-bold">{{ $institution->name }}</h5>
                                <p class="card-text mb-2">
                                    <span class="badge bg-light text-dark border">Induk: <span class="fw-bold">{{ $institution->group->name ?? '-' }}</span></span>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection