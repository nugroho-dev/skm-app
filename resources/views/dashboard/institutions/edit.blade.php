
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

        <div class="col-md-6 col-sm-12">
                <form class="card" action="{{ route('institutions.update', $institution) }}" method="POST">
                    @csrf
                     @method('PUT')
                  <div class="card-header">
                    <h3 class="card-title">Form Edit Instansi</h3>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label required">Nama Instansi {{ $institution->name }}</label>
                      <div>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" aria-describedby="namaInstansi" placeholder="Masukan Nama Instansi" value="{{ old('name', $institution->name) }}" required>
                          @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">Instansi Induk</label>
                      <div>
                         <select class="form-select @error('institution_group') is-invalid @enderror" name="institution_group">
                            <option value="">-- Pilih --</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->slug }}" {{ old('institution_group', $institution->group->slug ?? '') == $group->slug ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('institution_group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Tenan di MPP</label>
                      <div>
                        <select class="form-select @error('mpp') is-invalid @enderror"  name="mpp">
                            <option value="">-- Pilih --</option>
                            @foreach($mpps as $mpp)
                            <option value="{{ $mpp->slug }}" {{ old('mpp', $institution->mpp->slug ?? '') == $mpp->slug ? 'selected' : '' }}>{{ $mpp->name }}</option>
                            @endforeach
                        </select>
                        @error('mpp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
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