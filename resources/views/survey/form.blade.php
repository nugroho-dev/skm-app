@extends('layouts.app')

@section('content')
{!! NoCaptcha::renderJs() !!}
<div class="container py-4">
    <h3 class="mb-4">Survey Kepuasan - {{ $institution->name }}</h3>

    <form method="POST" action="{{ route('survey.submit', $institution->slug) }}">
    @csrf

    {{-- Tampilkan Semua Error Sekaligus (Opsional) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-lg mb-4 border-0">
        <div class="card-header bg-primary text-white">
            <strong><i class="bi bi-person-lines-fill me-2"></i>Data Responden</strong>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-3 col-sm-12">
                <label for="age" class="form-label fw-semibold">Umur</label>
                <input type="number" name="age" id="age" class="form-control form-control-sm @error('age') is-invalid @enderror" value="{{ old('age') }}" required placeholder="Masukkan umur Anda">
                @error('age')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3 col-sm-12">
                <label class="form-label fw-semibold">Jenis Kelamin</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="gender_l" value="L" {{ old('gender') == 'L' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gender_l">Laki-laki</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="gender_p" value="P" {{ old('gender') == 'P' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gender_p">Perempuan</label>
                    </div>
                </div>
                @error('gender')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3 col-sm-12">
                <label for="education_id" class="form-label fw-semibold">Pendidikan Terakhir</label>
                <select name="education_id" id="education_id" class="form-select @error('education_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Pendidikan --</option>
                    @foreach ($educations as $education)
                        <option value="{{ $education->id}}" {{ old('education_id') == $education->id ? 'selected' : '' }}>{{ $education->level }}</option>
                    @endforeach
                </select>
                @error('education_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3 col-sm-12">
                <label for="occupation_id" class="form-label fw-semibold">Pekerjaan</label>
                <select name="occupation_id" id="occupation_id" class="form-select @error('occupation_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Pekerjaan --</option>
                    @foreach ($occupations as $occupation)
                        <option value="{{ $occupation->id }}" {{ old('occupation_id') == $occupation->id ? 'selected' : '' }}>{{ $occupation->type }}</option>
                    @endforeach
                </select>
                @error('occupation_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 col-sm-12 mt-3">
                <label for="service_id" class="form-label fw-semibold">Layanan yang digunakan</label>
                <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($institution->services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                    @endforeach
                </select>
                @error('service_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <hr class="my-4">
    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-success text-white">
            <strong><i class="bi bi-question-circle me-2"></i>Pertanyaan</strong>
        </div>
        <div class="card-body row g-3">
            @foreach($questions as $question)
                <div class="col-md-6 col-sm-12 mb-3">
                    <label class="fw-semibold">{{ $question->text }} <br> <small class="text-muted">(Unsur: {{ $question->unsur->name ?? '-' }})</small></label>
                    <select name="answers[{{ $question->id }}]" class="form-select @error('answers.'.$question->id) is-invalid @enderror" required>
                        <option value="">-- Pilih Jawaban --</option>
                        @foreach ($question->choices as $choice)
                            <option value="{{ $choice->score }}" {{ old("answers.$question->id") == $choice->score ? 'selected' : '' }}>
                                {{ $choice->score }}. {{ $choice->label }}
                            </option>
                        @endforeach
                    </select>
                    @error('answers.'.$question->id)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-info text-white">
            <strong><i class="bi bi-chat-dots me-2"></i>Saran / Masukan</strong>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="suggestion" class="form-label fw-semibold">Saran / Masukan</label>
                <textarea name="suggestion" id="suggestion" class="form-control @error('suggestion') is-invalid @enderror" rows="4" placeholder="Tulis saran Anda di sini...">{{ old('suggestion') }}</textarea>
                @error('suggestion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

   <div class="g-recaptcha mb-4" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
   @error('captcha')
        <small class="text-danger">{{ $message }}</small>
    @enderror
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-lg btn-primary shadow"><i class="bi bi-send me-2"></i>Kirim Survey</button>
    </div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection