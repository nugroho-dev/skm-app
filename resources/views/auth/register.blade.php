@extends('layouts.login.main')
@section('title', 'Sign Up | ' . config('app.name'))
@section('container')
<div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <h2 class="h2 text-center mb-4">SiSUKMA</h2>
        </div>
        <form class="card card-md" action="{{ route('register') }}" method="POST" autocomplete="off" novalidate="">
          @csrf
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Buat Akun</h2>
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" name="name" value="{{ old('name') }}" required autofocus>
              @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat Email</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email" name="email" value="{{ old('email') }}" required>
              @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Kata Sandi</label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password"  autocomplete="off"  required>
               @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Kata Sandi</label>
              <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password" name="password_confirmation" autocomplete="off" required>
              @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3">
              <label for="institution_slug" class="form-label">Pilih Instansi</label>
              <select name="institution_slug" class="form-select" required>
                  <option value=" ">-- Pilih Instansi --</option>
                  @foreach ($institutions as $institution)
                      <option value="{{ $institution->slug }}" {{ old('institution_slug') == $institution->slug ? 'selected' : '' }}>
                          {{ $institution->name }}
                      </option>
                  @endforeach
              </select>
          </div>
            
             <div class="mb-3">
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
                    @error('captcha')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
            </div>
          </div>
        </form>
        <div class="text-center text-secondary mt-3">Sudah Punya Akun ? <a href="{{ route('login') }}" tabindex="-1">Sign in</a></div>
      </div>
    </div>
    <!-- Include Google reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer ></script>
@endsection