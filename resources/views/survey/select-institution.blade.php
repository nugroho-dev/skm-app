@extends('layouts.app')

@push('styles')
<style>
  .inst-page {
    padding: 1.1rem 0 2rem;
  }

  .inst-hero {
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

  .inst-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    opacity: 0.03;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
    mix-blend-mode: multiply;
  }

  .inst-hero > * {
    position: relative;
    z-index: 1;
  }

  .inst-kicker {
    font-size: 0.72rem;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--skm-muted);
    font-weight: 700;
  }

  .inst-title {
    font-family: "Playfair Display", Georgia, serif;
    font-size: clamp(1.85rem, 3.3vw, 2.7rem);
    line-height: 1.1;
    letter-spacing: -0.02em;
    color: var(--skm-text);
    margin: 0.5rem 0 0.75rem;
  }

  .inst-copy {
    margin: 0;
    color: var(--skm-muted);
    font-size: 0.97rem;
    line-height: 1.72;
    max-width: 680px;
  }

  .inst-search-panel {
    border: 1px solid rgba(228, 221, 207, 0.92);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 18px 38px -34px rgba(16, 36, 60, 0.62);
    padding: 1rem;
  }

  .inst-search-title {
    font-family: "Playfair Display", Georgia, serif;
    font-size: 1.12rem;
    color: var(--skm-text);
    margin: 0 0 0.2rem;
  }

  .inst-search-note {
    margin: 0;
    color: var(--skm-muted);
    font-size: 0.86rem;
    line-height: 1.6;
  }

  .inst-search-input {
    border-radius: 10px;
    border-color: var(--skm-border);
    font-size: 0.92rem;
    padding: 0.62rem 0.8rem;
  }

  .inst-search-input:focus {
    border-color: rgba(15, 95, 122, 0.48);
    box-shadow: 0 0 0 0.2rem rgba(15, 95, 122, 0.14);
  }

  .inst-btn-soft {
    border: 1px solid var(--skm-border);
    background: #fff;
    color: var(--skm-text);
    border-radius: 10px;
    font-weight: 600;
    padding: 0.58rem 0.9rem;
  }

  .inst-btn-soft:hover {
    background: #f6f8fb;
    color: var(--skm-primary);
    border-color: #cfd9e6;
  }

  .inst-card {
    height: 100%;
    border: 1px solid rgba(228, 221, 207, 0.92);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.92);
    padding: 1rem;
    box-shadow: 0 16px 34px -32px rgba(16, 36, 60, 0.72);
    transition: transform 0.24s ease, border-color 0.24s ease, box-shadow 0.24s ease;
  }

  .inst-card:hover {
    transform: translateY(-2px);
    border-color: rgba(15, 95, 122, 0.35);
    box-shadow: 0 18px 36px -28px rgba(16, 36, 60, 0.68);
  }

  .inst-avatar {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    background: linear-gradient(180deg, #f2f6fa, #eaf1f9);
    color: var(--skm-primary);
    border: 1px solid rgba(15, 95, 122, 0.14);
    flex-shrink: 0;
  }

  .inst-name {
    font-size: 0.98rem;
    font-weight: 700;
    color: var(--skm-text);
    margin: 0;
    line-height: 1.4;
  }

  .inst-meta {
    margin: 0.3rem 0 0.95rem;
    color: var(--skm-muted);
    font-size: 0.84rem;
    line-height: 1.55;
  }

  .inst-empty {
    border: 1px dashed rgba(15, 95, 122, 0.28);
    border-radius: 14px;
    background: rgba(15, 95, 122, 0.04);
    color: var(--skm-muted);
    padding: 1rem;
    text-align: center;
    font-size: 0.92rem;
  }
</style>
@endpush

@section('content')
<section class="inst-page">
  <div class="mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <div class="text-uppercase small fw-semibold" style="letter-spacing:0.12em;color:var(--skm-muted);">Langkah 2 dari 5 - Instansi</div>
      <div class="small text-secondary">2/5</div>
    </div>
    <div class="row g-2">
      <div class="col"><div class="wizard-step is-done">1. Lokasi</div></div>
      <div class="col"><div class="wizard-step is-active">2. Instansi</div></div>
      <div class="col"><div class="wizard-step">3. Data Diri</div></div>
      <div class="col"><div class="wizard-step">4. Penilaian</div></div>
      <div class="col"><div class="wizard-step">5. Saran</div></div>
    </div>
  </div>

  <div class="inst-hero mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-3" style="--bs-breadcrumb-divider: '›';">
        <li class="breadcrumb-item"><a href="{{ route('survey.welcome') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('survey.selectCity') }}">Pilih Lokasi</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
      </ol>
    </nav>

    <div class="inst-kicker">Tahap Pemilihan</div>
    <h1 class="inst-title">Pilih instansi tujuan survei Anda.</h1>
    <p class="inst-copy">Cari dan pilih instansi yang baru saja Anda kunjungi untuk melanjutkan pengisian Survei Kepuasan Masyarakat secara tepat.</p>
  </div>

  <div class="inst-search-panel mb-4">
    <h2 class="inst-search-title">Temukan Instansi</h2>
    <p class="inst-search-note">Gunakan kata kunci nama instansi untuk mempercepat pencarian.</p>

    <form class="mt-3" method="GET" action="{{ route('survey.selectInstitution', $slug) }}">
      <div class="input-group">
        <input type="text" name="search" value="{{ $search }}" class="form-control inst-search-input" placeholder="Contoh: Dinas Pendidikan">
        <button class="btn skm-button-primary" type="submit">Cari</button>
        @if($search)
          <a href="{{ route('survey.selectInstitution', $slug) }}" class="btn inst-btn-soft">Reset</a>
        @endif
      </div>
    </form>
  </div>

  <div class="row g-3">
    @forelse($institutions as $inst)
      <div class="col-md-6 col-xl-4">
        <article class="inst-card">
          <div class="d-flex align-items-start gap-3">
            <div class="inst-avatar">
              {{ Str::of($inst->name)->explode(' ')->take(2)->map(fn($word) => Str::substr($word, 0, 1))->join('') }}
            </div>
            <div class="flex-grow-1">
              <h3 class="inst-name">{{ $inst->name }}</h3>
              <p class="inst-meta">{{ $inst->group->name ?? '-' }}</p>
              <a href="{{ route('survey.form', $inst->slug) }}" class="btn btn-sm skm-button-primary">Pilih Instansi</a>
            </div>
          </div>
        </article>
      </div>
    @empty
      <div class="col-12">
        <div class="inst-empty">Tidak ada instansi ditemukan untuk kata kunci ini.</div>
      </div>
    @endforelse
  </div>
</section>

@endsection