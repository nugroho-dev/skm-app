
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

        <div class="col-md-6 col-sm-12">
                <form class="card" action="{{ auth()->user()->hasRole('super_admin') ? route('service.update', $service) : route('instansi.services.update', $service) }}" method="POST">
                    @csrf
                    @method('PUT')
                  <div class="card-header">
                    <h3 class="card-title">Form Tambah {{ $title }}</h3>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label required">Nama Layanan</label>
                      <div>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" aria-describedby="namaInstansi" placeholder="Masukan Nama Layanan Instansi" value="{{ old('name', $service->name ?? '') }}" required>
                          @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                      </div>
                      <input type="hidden" name="institution_id" value="{{  $institution_id }}">
                    </div>
                    <div class="text-end">
                      <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </div>
                </form>
              </div>
              </div>
            </div>
          </div>
              @endsection