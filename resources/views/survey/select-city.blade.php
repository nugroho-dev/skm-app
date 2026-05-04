@extends('layouts.app')

@push('styles')
<style>
    .city-page {
        padding: 1.1rem 0 2rem;
    }

    .city-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 22px;
        background:
            radial-gradient(circle at top right, rgba(212, 167, 74, 0.14), transparent 34%),
            radial-gradient(circle at left center, rgba(15, 95, 122, 0.09), transparent 40%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(248, 246, 241, 0.98));
        padding: 1.5rem;
        box-shadow: 0 24px 48px -36px rgba(16, 36, 60, 0.62);
    }

    .city-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.03;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
        mix-blend-mode: multiply;
    }

    .city-hero > * {
        position: relative;
        z-index: 1;
    }

    .city-kicker {
        font-size: 0.72rem;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: var(--skm-muted);
        font-weight: 700;
    }

    .city-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(1.85rem, 3.3vw, 2.7rem);
        line-height: 1.1;
        letter-spacing: -0.02em;
        color: var(--skm-text);
        margin: 0.5rem 0 0.75rem;
    }

    .city-copy {
        margin: 0;
        color: var(--skm-muted);
        font-size: 0.97rem;
        line-height: 1.72;
        max-width: 680px;
    }

    .city-option {
        height: 100%;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 18px 38px -34px rgba(16, 36, 60, 0.62);
        padding: 1.35rem;
        transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
    }

    .city-option:hover {
        transform: translateY(-2px);
        border-color: rgba(15, 95, 122, 0.35);
        box-shadow: 0 20px 38px -28px rgba(16, 36, 60, 0.68);
    }

    .city-option-tag {
        font-size: 0.7rem;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--skm-muted);
        font-weight: 700;
        margin-bottom: 0.55rem;
    }

    .city-option-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 1.45rem;
        color: var(--skm-text);
        margin-bottom: 0.7rem;
    }

    .city-option-copy {
        color: var(--skm-muted);
        line-height: 1.72;
        font-size: 0.93rem;
        margin-bottom: 0.85rem;
    }

    .city-option-note {
        color: var(--skm-muted);
        line-height: 1.6;
        font-size: 0.82rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<section class="city-page">
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="text-uppercase small fw-semibold" style="letter-spacing:0.12em;color:var(--skm-muted);">Langkah 1 dari 5 - Lokasi</div>
            <div class="small text-secondary">1/5</div>
        </div>
        <div class="row g-2">
            <div class="col"><div class="wizard-step is-active">1. Lokasi</div></div>
            <div class="col"><div class="wizard-step">2. Instansi</div></div>
            <div class="col"><div class="wizard-step">3. Data Diri</div></div>
            <div class="col"><div class="wizard-step">4. Penilaian</div></div>
            <div class="col"><div class="wizard-step">5. Saran</div></div>
        </div>
    </div>

    <div class="city-hero mb-4">
        <div class="city-kicker">Alur Pengisian Survei</div>
        <h1 class="city-title">Tentukan lokasi layanan yang ingin Anda nilai.</h1>
        <p class="city-copy">Pilih kategori layanan terlebih dahulu agar daftar instansi pada langkah berikutnya lebih relevan dengan pengalaman layanan yang baru saja Anda terima.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4 align-items-stretch">
        <div class="col-md-6">
            @if($institutionGroup)
            <article class="city-option">
                <div class="city-option-tag">OPD Kota Magelang</div>
                <h2 class="city-option-title">{{ $institutionGroup->name }}</h2>
                <p class="city-option-copy">
                    Survei ini mencakup layanan publik di lingkup Pemerintah Kota Magelang,
                    termasuk dinas, badan, dan instansi lain di luar MPP.
                </p>
                <p class="city-option-note">Contoh: Dinas Pendidikan, Dinas Kesehatan, Dinas Lingkungan Hidup.</p>
                <a href="{{ route('survey.selectInstitution',  $institutionGroup->slug) }}" class="btn skm-button-primary px-4">Pilih Instansi</a>
            </article>
            @endif
        </div>
        <div class="col-md-6">
            @if($mpp)
            <article class="city-option">
                <div class="city-option-tag">Mall Pelayanan Publik</div>
                <h2 class="city-option-title">{{ $mpp->name }}</h2>
                <p class="city-option-copy">
                    Survei ini khusus untuk layanan yang diberikan melalui Mal Pelayanan Publik
                    Kota Magelang dari berbagai instansi dalam satu lokasi.
                </p>
                <p class="city-option-note">Contoh: Disdukcapil, Imigrasi, Samsat, BPJS, dan lainnya.</p>
                <a href="{{ route('survey.selectInstitution', $mpp->slug) }}" class="btn skm-button-soft px-4">Pilih Instansi</a>
            </article>
            @endif
        </div>
    </div>
</section>
@endsection