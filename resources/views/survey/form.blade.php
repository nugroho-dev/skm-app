@extends('layouts.app')

@push('styles')
<style>
    .form-page {
        padding: 1.1rem 0 2rem;
    }

    .form-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 22px;
        background:
            radial-gradient(circle at top right, rgba(212, 167, 74, 0.14), transparent 34%),
            radial-gradient(circle at left center, rgba(15, 95, 122, 0.09), transparent 40%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(248, 246, 241, 0.98));
        padding: 1.4rem;
        box-shadow: 0 24px 48px -36px rgba(16, 36, 60, 0.62);
    }

    .form-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.03;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
        mix-blend-mode: multiply;
    }

    .form-hero > * {
        position: relative;
        z-index: 1;
    }

    .form-kicker {
        font-size: 0.72rem;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: var(--skm-muted);
        font-weight: 700;
    }

    .form-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(1.7rem, 3vw, 2.4rem);
        line-height: 1.12;
        letter-spacing: -0.02em;
        color: var(--skm-text);
        margin: 0.48rem 0 0.55rem;
    }

    .form-copy {
        color: var(--skm-muted);
        margin: 0;
        line-height: 1.7;
        font-size: 0.94rem;
        max-width: 760px;
    }

    .form-step-panel {
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 18px 38px -34px rgba(16, 36, 60, 0.62);
        padding: 1rem;
    }

    .form-section {
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 16px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 20px 38px -34px rgba(16, 36, 60, 0.62);
    }

    .form-section-header {
        padding: 0.9rem 1rem;
        color: #fff;
        font-weight: 700;
        font-size: 0.92rem;
        letter-spacing: 0.02em;
    }

    .form-section-header.is-primary { background: #1f6feb; }
    .form-section-header.is-success { background: #0f9d74; }
    .form-section-header.is-info { background: #0ea5c6; }

    .form-section-body {
        padding: 1rem;
    }

    .form-control,
    .form-select {
        border-color: var(--skm-border);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: rgba(15, 95, 122, 0.48);
        box-shadow: 0 0 0 0.2rem rgba(15, 95, 122, 0.14);
    }

    .form-hint {
        color: var(--skm-muted);
        font-size: 0.82rem;
        margin-top: 0.35rem;
        line-height: 1.5;
    }

    .form-field-card {
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 12px;
        padding: 0.85rem;
        background: rgba(255, 255, 255, 0.88);
        height: 100%;
    }

    .gender-options {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.55rem;
    }

    .gender-pill {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        border-radius: 10px;
        border: 1px solid var(--skm-border);
        background: #fff;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--skm-text);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .gender-pill:hover {
        border-color: rgba(15, 95, 122, 0.45);
        transform: translateY(-1px);
    }

    .gender-pill input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .gender-pill:has(input:checked) {
        border-color: rgba(15, 95, 122, 0.7);
        background: rgba(15, 95, 122, 0.08);
        color: #0d4f66;
    }

    .question-card {
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.9);
        padding: 0.9rem;
        height: 100%;
    }

    .question-meta {
        font-size: 0.76rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--skm-muted);
        margin-bottom: 0.45rem;
    }

    .question-title {
        color: var(--skm-text);
        font-size: 0.93rem;
        font-weight: 600;
        line-height: 1.55;
        margin-bottom: 0.65rem;
        display: block;
    }

    .answer-guide {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-top: 0.55rem;
    }

    .answer-guide span {
        border: 1px solid var(--skm-border);
        border-radius: 999px;
        padding: 0.22rem 0.55rem;
        font-size: 0.73rem;
        color: var(--skm-muted);
        background: #fff;
    }

    .suggestion-note {
        border: 1px dashed var(--skm-border);
        background: rgba(15, 95, 122, 0.05);
        color: var(--skm-muted);
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
        font-size: 0.84rem;
        line-height: 1.6;
        margin-bottom: 0.8rem;
    }

    .form-action-bar {
        position: sticky;
        bottom: 0;
        z-index: 20;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(6px);
        box-shadow: 0 -6px 20px -18px rgba(16, 36, 60, 0.62);
        padding: 0.75rem;
    }

    @media (max-width: 767.98px) {
        .gender-options {
            grid-template-columns: 1fr;
        }

        .form-action-bar {
            border-radius: 12px;
            margin-top: 1rem;
        }

        .form-action-bar .btn {
            min-width: 120px;
        }
    }
</style>
@endpush

@section('content')
{!! NoCaptcha::renderJs() !!}
@php
    $errorKeys = collect($errors->keys());
    $hasAnswerErrors = $errorKeys->contains(fn($k) => str_starts_with($k, 'answers.'));
    $hasFinalStepErrors = $errors->has('captcha') || $errors->has('suggestion');
    $initialStep = $hasAnswerErrors ? 1 : ($hasFinalStepErrors ? 2 : 0);
@endphp
<div class="form-page">
    <div class="form-hero mb-4">
        <div class="form-kicker">Langkah 3 sampai 5</div>
        <h1 class="form-title">{{ $institution->name }}</h1>
        <p class="form-copy">Lengkapi data diri, berikan penilaian atas seluruh unsur layanan, lalu tuliskan saran agar hasil survei membantu perbaikan layanan secara berkelanjutan.</p>
    </div>

    <div class="form-step-panel mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="text-uppercase small fw-semibold" style="letter-spacing:0.12em;color:var(--skm-muted);" id="wizard-label">
                Langkah 3 dari 5 - Data Diri
            </div>
            <div class="small text-secondary" id="wizard-progress">3/5</div>
        </div>
        <div class="row g-2">
            <div class="col-4">
                <div class="wizard-step is-active" data-indicator="0">3. Data Diri</div>
            </div>
            <div class="col-4">
                <div class="wizard-step" data-indicator="1">4. Penilaian</div>
            </div>
            <div class="col-4">
                <div class="wizard-step" data-indicator="2">5. Saran</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('survey.submit', $institution->slug) }}">
    @csrf

    <div id="wizardAlert" class="alert alert-danger d-none"></div>

    @if ($errors->any())
        <div class="alert alert-danger" id="server-errors">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div data-step="0">
        <div class="form-section mb-4">
            <div class="form-section-header is-primary">
                <strong>Data Responden</strong>
            </div>
            <div class="form-section-body row g-3">
                <div class="col-12">
                    <div class="suggestion-note mb-2">
                        Data digunakan sebagai agregat statistik kepuasan layanan. Tidak ada identitas pribadi seperti nama atau nomor telepon yang diminta pada formulir ini.
                    </div>
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="form-field-card">
                        <label for="age" class="form-label fw-semibold">Umur</label>
                        <input type="number" name="age" id="age" class="form-control form-control-sm @error('age') is-invalid @enderror" value="{{ old('age') }}" required placeholder="Masukkan umur Anda">
                        <div class="form-hint">Contoh: 25</div>
                        @error('age')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="form-field-card">
                        <label class="form-label fw-semibold d-block">Jenis Kelamin</label>
                        <div class="gender-options">
                            <label class="gender-pill" for="gender_l">
                                <input class="@error('gender') is-invalid @enderror" type="radio" name="gender" id="gender_l" value="L" {{ old('gender') == 'L' ? 'checked' : '' }}>
                                <span>Laki-laki</span>
                            </label>
                            <label class="gender-pill" for="gender_p">
                                <input class="@error('gender') is-invalid @enderror" type="radio" name="gender" id="gender_p" value="P" {{ old('gender') == 'P' ? 'checked' : '' }}>
                                <span>Perempuan</span>
                            </label>
                        </div>
                        <div class="form-hint">Pilih salah satu kategori.</div>
                        @error('gender')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="form-field-card">
                        <label for="education_id" class="form-label fw-semibold">Pendidikan Terakhir</label>
                        <select name="education_id" id="education_id" class="form-select @error('education_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pendidikan --</option>
                            @foreach ($educations as $education)
                                <option value="{{ $education->id}}" {{ old('education_id') == $education->id ? 'selected' : '' }}>{{ $education->level }}</option>
                            @endforeach
                        </select>
                        <div class="form-hint">Pilih tingkat pendidikan terakhir Anda.</div>
                        @error('education_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class="form-field-card">
                        <label for="occupation_id" class="form-label fw-semibold">Pekerjaan</label>
                        <select name="occupation_id" id="occupation_id" class="form-select @error('occupation_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach ($occupations as $occupation)
                                <option value="{{ $occupation->id }}" {{ old('occupation_id') == $occupation->id ? 'selected' : '' }}>{{ $occupation->type }}</option>
                            @endforeach
                        </select>
                        <div class="form-hint">Pilih pekerjaan yang paling sesuai.</div>
                        @error('occupation_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 mt-3">
                    <div class="form-field-card">
                        <label for="service_id" class="form-label fw-semibold">Layanan yang digunakan</label>
                        <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($institution->services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-hint">Pastikan layanan sesuai dengan kunjungan Anda hari ini.</div>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div data-step="1" class="d-none">
        <div class="form-section mb-4">
            <div class="form-section-header is-success">
                <strong>Pertanyaan</strong>
            </div>
            <div class="form-section-body row g-3">
                @foreach($questions as $question)
                    <div class="col-md-6 col-sm-12 mb-3">
                        <div class="question-card">
                            <div class="question-meta">Pertanyaan {{ $loop->iteration }} • Unsur {{ $question->unsur->name ?? '-' }}</div>
                            <label class="question-title">{{ $question->text }}</label>
                            <select name="answers[{{ $question->id }}]" class="form-select question-answer @error('answers.'.$question->id) is-invalid @enderror" required>
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
                            <div class="answer-guide">
                                <span>1 sangat tidak puas</span>
                                <span>2 kurang puas</span>
                                <span>3 puas</span>
                                <span>4 sangat puas</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div data-step="2" class="d-none">
        <div class="form-section mb-4">
            <div class="form-section-header is-info">
                <strong>Saran / Masukan</strong>
            </div>
            <div class="form-section-body">
                <div class="suggestion-note">
                    Tulis masukan yang spesifik agar instansi dapat menindaklanjuti dengan jelas, misalnya terkait waktu pelayanan, keramahan petugas, atau kejelasan prosedur.
                </div>
                <div class="mb-3">
                    <label for="suggestion" class="form-label fw-semibold">Saran / Masukan</label>
                    <textarea name="suggestion" id="suggestion" class="form-control @error('suggestion') is-invalid @enderror" rows="4" placeholder="Tulis saran Anda di sini...">{{ old('suggestion') }}</textarea>
                    @error('suggestion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

       <div class="g-recaptcha mb-2" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
       @error('captcha')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-action-bar d-flex justify-content-between align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary" id="btnPrev" disabled>Kembali</button>
        <div class="d-flex gap-2">
            <button type="button" class="btn skm-button-primary" id="btnNext">Lanjut</button>
            <button type="submit" class="btn skm-button-primary d-none" id="btnSubmit">Kirim Survei</button>
        </div>
    </div>

    <script>
        (() => {
            const totalSteps = 3;
            const labels = ["Data Diri", "Penilaian", "Saran"];
            let currentStep = {{ $initialStep }};

            const stepElements = Array.from(document.querySelectorAll('[data-step]'));
            const indicators = Array.from(document.querySelectorAll('[data-indicator]'));
            const btnPrev = document.getElementById('btnPrev');
            const btnNext = document.getElementById('btnNext');
            const btnSubmit = document.getElementById('btnSubmit');
            const wizardLabel = document.getElementById('wizard-label');
            const wizardProgress = document.getElementById('wizard-progress');
            const wizardAlert = document.getElementById('wizardAlert');

            function showAlert(message) {
                wizardAlert.textContent = message;
                wizardAlert.classList.remove('d-none');
            }

            function hideAlert() {
                wizardAlert.classList.add('d-none');
                wizardAlert.textContent = '';
            }

            function setStep(stepIndex) {
                currentStep = stepIndex;
                stepElements.forEach((el, idx) => {
                    el.classList.toggle('d-none', idx !== currentStep);
                });
                indicators.forEach((el, idx) => {
                    el.classList.toggle('is-active', idx === currentStep);
                    el.classList.toggle('is-done', idx < currentStep);
                });

                wizardLabel.textContent = `Langkah ${currentStep + 3} dari 5 - ${labels[currentStep]}`;
                wizardProgress.textContent = `${currentStep + 3}/5`;

                btnPrev.disabled = currentStep === 0;
                btnNext.classList.toggle('d-none', currentStep === totalSteps - 1);
                btnSubmit.classList.toggle('d-none', currentStep !== totalSteps - 1);
            }

            function validateCurrentStep() {
                hideAlert();

                if (currentStep === 0) {
                    const age = document.getElementById('age');
                    const education = document.getElementById('education_id');
                    const occupation = document.getElementById('occupation_id');
                    const service = document.getElementById('service_id');
                    const genderChecked = document.querySelector('input[name="gender"]:checked');

                    let valid = true;

                    [age, education, occupation, service].forEach((field) => {
                        if (!field.value) {
                            field.classList.add('is-invalid');
                            valid = false;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (!genderChecked) {
                        valid = false;
                    }

                    if (!valid) {
                        showAlert('Lengkapi data responden terlebih dahulu sebelum lanjut.');
                    }

                    return valid;
                }

                if (currentStep === 1) {
                    const answers = Array.from(document.querySelectorAll('select.question-answer'));
                    const invalid = answers.filter((el) => !el.value);

                    answers.forEach((el) => el.classList.remove('is-invalid'));

                    if (invalid.length > 0) {
                        invalid.forEach((el) => el.classList.add('is-invalid'));
                        showAlert('Semua pertanyaan penilaian wajib diisi sebelum lanjut.');
                        return false;
                    }
                }

                return true;
            }

            btnNext.addEventListener('click', () => {
                if (!validateCurrentStep()) return;
                if (currentStep < totalSteps - 1) {
                    setStep(currentStep + 1);
                }
            });

            btnPrev.addEventListener('click', () => {
                hideAlert();
                if (currentStep > 0) {
                    setStep(currentStep - 1);
                }
            });

            setStep(currentStep);
        })();
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </form>
@endsection