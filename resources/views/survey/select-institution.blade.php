@extends('layouts.app')

@section('content')
<div class="container my-4">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('survey.welcome') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('survey.selectCity') }}">Pilih Lokasi</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
    </ol>
  </nav>

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h2 class="fw-semibold">Pilih Instansi</h2>
      <p class="text-muted small mb-0">Pilih instansi untuk melihat hasil Survei Kepuasan Masyarakat (SKM) atau mengisi survei.</p>
    </div>
    
  </div>

  {{-- Search --}}
  <form class="mb-3" method="GET" action="{{ route('survey.selectInstitution', $slug) }}">
    <div class="input-group">
      <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari instansi...">
      <button class="btn btn-outline-secondary" type="submit">Cari</button>
      @if($search)
        <a href="{{ route('survey.selectInstitution', $slug) }}" class="btn btn-outline-danger">Reset</a>
      @endif
    </div>
  </form>

  

  {{-- Daftar Instansi --}}
  <div class="row g-3">
    @forelse($institutions as $inst)
      <div class="col-md-6">
        <div class="card h-100 shadow-sm">
          <div class="card-body d-flex align-items-start">
            <div class="me-3">
              <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                <span class="fw-bold">{{ Str::of($inst->name)->explode(' ')->take(2)->map(fn($w)=>Str::substr($w,0,1))->join('') }}</span>
              </div>
            </div>
            <div class="flex-grow-1">
              <h6 class="card-title mb-1">{{ $inst->name }}</h6>
              <p class="text-muted small mb-2">• {{ $inst->group->name ?? '-' }}  </p>
              <div>
                <a href="{{ route('survey.form', $inst->slug) }}" class="btn btn-sm btn-primary">Isi Survey</a>
                
              </div>
            </div>
            
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">Tidak ada instansi ditemukan.</div>
      </div>
    @endforelse
  </div>

</div>

@endsection