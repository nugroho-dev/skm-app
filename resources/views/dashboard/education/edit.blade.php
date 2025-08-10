
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

        <div class="col-md-6 col-sm-12">
                <form class="card" action="{{ route('pendidikan.update', $pendidikan) }}" method="POST">
                    @csrf
                    @method('PUT')
                  <div class="card-header">
                    <h3 class="card-title">Form {{ $title }}</h3>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label required">Nama Level Pendidikan</label>
                      <div>
                        <input type="text" name="level" class="form-control @error('level') is-invalid @enderror" aria-describedby="namaInstansi" placeholder="Masukan Nama Pendidikan" value="{{ old('level', $pendidikan->level?? '') }}" required>
                          @error('level')
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