
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

        <div class="col-md-6 col-sm-12">
                <form class="card" action="{{  route('pekerjaan.store') }}" method="POST">
                    @csrf
                  <div class="card-header">
                    <h3 class="card-title">Form Tambah {{ $title }}</h3>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label required">Nama Pekerjaan</label>
                      <div>
                        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" aria-describedby="namaPekerjaan" placeholder="Masukan Nama Pekerjaan" value="{{ old('type', $pekerjaan->type ?? '') }}" required>
                          @error('type')
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