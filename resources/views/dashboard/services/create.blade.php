
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

        <div class="col-md-6 col-sm-12">
                <form class="card" action="{{ auth()->user()->hasRole('super_admin') ? route('services.store') : route('instansi.services.store') }}" method="POST">
                    @csrf
                  <div class="card-header">
                    <h3 class="card-title">Form Tambah {{ $title }}</h3>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label required">Nama Layanan</label>
                      <div>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" aria-describedby="namaInstansi" placeholder="Masukan Nama Instansi" value="{{ old('name', $institution->name ?? '') }}" required>
                          @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                      </div>
                      <input type="hidden" name="institution" value="{{ auth()->user()->hasRole('super_admin') ? $institution_slug : '' }}">
                    </div>
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </div>
                </form>
              </div>
              </div>
            </div>
          </div>
              @endsection