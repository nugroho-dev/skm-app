
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

        <div class="col-md-6 col-sm-12">
                <form class="card" action="{{ route('institutions.store') }}" method="POST">
                    @csrf
                  <div class="card-header">
                    <h3 class="card-title">Form Tambah Instansi</h3>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label required">Nama Instansi</label>
                      <div>
                        <input type="email" class="form-control" aria-describedby="emailHelp" placeholder="Enter email">
                        <small class="form-hint">We'll never share your email with anyone else.</small>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">Instansi Induk</label>
                      <div>
                         <select class="form-select" name="institution_group">
                            <option value="">-- Pilih --</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->slug }}" {{ old('institution_group', $institution->institution_group->slug ?? '') == $group->slug ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                      
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Tenan di MPP</label>
                      <div>
                        <select class="form-select"  name="mpp">
                            <option value="">-- Pilih --</option>
                            @foreach($mpps as $mpp)
                            <option value="{{ $mpp->slug }}" {{ old('mpp', $institution->mpp->slug ?? '') == $mpp->slug ? 'selected' : '' }}>{{ $mpp->name }}</option>
                            @endforeach
                          
                          
                        </select>
                      </div>
                    </div>
                    
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
              </div>
            </div>
          </div>
              @endsection