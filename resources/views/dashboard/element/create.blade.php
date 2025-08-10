
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

        <div class="col-md-6 col-sm-12">
                <form class="card" action="{{  route('unsur.store') }}" method="POST">
                    @csrf
                  <div class="card-header">
                    <h3 class="card-title">Form Tambah {{ $title }}</h3>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label required">Unsur</label>
                      <div>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" aria-describedby="namaUnsur" placeholder="Masukan Nama Unsur" value="{{ old('name', $unsur->name ?? '') }}" required>
                          @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                      </div>
                      
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