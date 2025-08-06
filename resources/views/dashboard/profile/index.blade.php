@extends('dashboard.layouts.tabler.main')

@section('container')
    <div class="page-wrapper">
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">Account Settings</h2>
              </div>
              <div class="col-12">
                @if ($errors->any())
                    <pre>{{ print_r($errors->all(), true) }}</pre>
                @endif
                @if (session('status') === 'profile-information-updated')
                    <div class="alert alert-success">Profil berhasil diperbarui.</div>
                @endif

                @if (session('status') === 'password-updated')
                    <div class="alert alert-success">Password berhasil diperbarui.</div>
                @endif
                @foreach (['success'] as $msg)
                    @if (session($msg))
                        <div class="alert alert-success">
                            {{ session($msg) }}
                        </div>
                    @endif
                @endforeach

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @foreach (['current_password', 'password'] as $field)
                    @error($field)
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
        <!-- BEGIN PAGE BODY -->
        <div class="page-body">
          <div class="container-xl">
            <div class="card">
              <div class="row g-0">
                <div class="col-12 col-md-3 border-end">
                  <div class="card-body">
                    <h4 class="subheader">Settings</h4>
                    <div class="list-group list-group-transparent">
                      <a href="#" class="list-group-item list-group-item-action d-flex align-items-center active">My Account</a>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-9 d-flex flex-column">
                  <div class="card-body">
                    <h2 class="mb-4">My Account</h2>
                    <h3 class="card-title">Profile Details</h3>
                    <form method="POST" action="{{ route('user-profile-information.update') }}">
                    @csrf
                    @method('PUT')
                    <!--<div class="row align-items-center">
                      <div class="col-auto"><span class="avatar avatar-xl" style="background-image: url(./static/avatars/000m.jpg)"> </span></div>
                      <div class="col-auto">
                        <a href="#" class="btn btn-1"> Change avatar </a>
                      </div>
                      <div class="col-auto">
                        <a href="#" class="btn btn-ghost-danger btn-3"> Delete avatar </a>
                      </div>
                    </div>
                    <h3 class="card-title mt-4">Business Profile</h3>-->
                    <div class="row g-3">
                      <div class="col-md-5 col-sm-12">
                        <div class="form-label">Nama</div>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-6 col-sm-12">
                        <div class="form-label">Instansi</div>
                        <p class="form-control-plaintext">{{ auth()->user()->institution->name }}</p>
                      </div>
                      
                    </div>
                    <h3 class="card-title mt-4">Email</h3>
                    <p class="card-subtitle">Gunakan alamat email aktif dan dapat diakses</p>
                    <div>
                      <div class="row g-2">
                        <div class="col-md-5">
                          <input type="email" name="email" class="form-control @error('name') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                          @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                        
                      </div>
                    </div>
                    <h3 class="card-title mt-4">Password</h3>
                    <p class="card-subtitle">Masukkan password baru untuk memperbarui kata sandi akun Anda.</p>
                    <div>
                      <a href="#" class="btn btn-1" data-bs-toggle="modal" data-bs-target="#modal-report"> Set password baru </a>
                    </div>
                    
                  </div>
                  <div class="card-footer bg-transparent mt-auto">
                    <div class="btn-list justify-content-end">
                      <a href="#" class="btn btn-1"> Batal</a>
                      <button type="submit" class="btn btn-primary btn-2"> Simpan </button>
                    </div>
                  </div>
                </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE BODY -->
                <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Ubah Password</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <form method="POST" action="{{ route('user-password.update') }}">
                                @csrf
                                @method('PUT')
                              <div class="modal-body">
                                <div class="mb-3">
                                  <label class="form-label">Password Lama</label>
                                  <input type="password" class="form-control" name="current_password" placeholder="Password Lama" required>
                                @error('current_password')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                                 
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Password Baru</label>
                                  <input type="password" class="form-control" name="password" placeholder="Password Baru" required>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Konfirmasi Password Baru</label>
                                  <input type="password" class="form-control" name="password_confirmation"  placeholder="Konfirmasi Password Baru" required>
                                </div>
                              </div>
                              
                              <div class="modal-footer">
                                <a href="#" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Batal </a>
                                <button type="submit" class="btn btn-primary btn-5 ms-auto" >
                                  <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2">
                                    <path d="M12 5l0 14"></path>
                                    <path d="M5 12l14 0"></path>
                                  </svg>
                                  Simpan
                                </button>
                              </div>
                            </form>
                            </div>
                          </div>
                        </div>
      </div>
@endsection