@extends('layouts.app')

@push('styles')
<style>
    /* ── Landing: buka padding/border skm-page ── */
    .skm-page {
        padding: 0 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    /* ── Grain helper ── */
    .lp-grain-bg {
        position: relative;
        isolation: isolate;
    }
    .lp-grain-bg::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.025;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
        mix-blend-mode: multiply;
        z-index: 0;
    }
    .lp-grain-bg > * { position: relative; z-index: 1; }

    /* ── Gold rule ── */
    .lp-rule {
        display: block;
        width: 46px;
        height: 2px;
        background: var(--skm-accent);
        margin-bottom: 0.65rem;
    }

    /* ── Ornament divider ── */
    .lp-ornament {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: var(--skm-muted);
        font-size: 0.72rem;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .lp-ornament::before,.lp-ornament::after {
        content: "";
        flex: 1;
        height: 1px;
        background: linear-gradient(to right,transparent,var(--skm-border),transparent);
    }

    /* ── Kicker ── */
    .lp-kicker {
        font-size: 0.72rem;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: var(--skm-muted);
        font-weight: 700;
    }

    /* ── Hero ── */
    .lp-hero {
        padding: 4.5rem 2.5rem 5rem;
        overflow: hidden;
    }
    .lp-hero-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(2.4rem, 5vw, 4.2rem);
        line-height: 1.04;
        letter-spacing: -0.025em;
        color: var(--skm-text);
    }
    .lp-hero-title em {
        font-style: italic;
        color: var(--skm-primary);
    }
    .lp-trust {
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
        color: var(--skm-muted);
        font-size: 0.88rem;
    }
    .lp-trust-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .lp-trust-icon {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
        color: var(--skm-primary);
    }
    .lp-hero-media {
        position: relative;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 28px 48px -32px rgba(16, 36, 60, 0.65);
        background:
            radial-gradient(circle at top, rgba(212, 167, 74, 0.18), transparent 42%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(247, 244, 238, 0.98));
        border: 1px solid rgba(228, 221, 207, 0.92);
    }
    .lp-hero-media::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(90deg, rgba(15, 95, 122, 0.06) 1px, transparent 1px),
            linear-gradient(rgba(15, 95, 122, 0.05) 1px, transparent 1px),
            radial-gradient(circle at 20% 22%, rgba(212, 167, 74, 0.16), transparent 26%),
            radial-gradient(circle at 82% 78%, rgba(15, 95, 122, 0.09), transparent 24%);
        background-size: 32px 32px, 32px 32px, auto, auto;
        opacity: 0.72;
        pointer-events: none;
    }
    .lp-hero-media img {
        width: 100%;
        height: 540px;
        object-fit: cover;
        display: block;
    }
    .lp-hero-media.is-emblem {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    .lp-hero-media.is-emblem > * {
        position: relative;
        z-index: 1;
    }
    .lp-hero-media.is-emblem img {
        width: min(100%, 320px);
        height: auto;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 18px 26px rgba(16, 36, 60, 0.18));
    }
    .lp-hero-media::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 95, 122, 0.28), transparent 55%);
        pointer-events: none;
    }
    .lp-hero-media.is-emblem::after {
        background:
            linear-gradient(to top, rgba(15, 95, 122, 0.06), transparent 58%),
            radial-gradient(circle at center, transparent 45%, rgba(15, 95, 122, 0.08));
    }
    /* ── Stats strip ── */
    .lp-stats {
        border-top: 1px solid var(--skm-border);
        border-bottom: 1px solid var(--skm-border);
        background: rgba(255,255,255,0.6);
    }
    .lp-stat-col {
        padding: 2rem 1.2rem;
        text-align: center;
    }
    .lp-stat-col + .lp-stat-col {
        border-left: 1px solid var(--skm-border);
    }
    .lp-stat-num {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 2.8rem;
        line-height: 1;
        letter-spacing: -0.02em;
        color: var(--skm-text);
        margin-top: 0.5rem;
    }
    /* ── Sections ── */
    .lp-section {
        padding: 5rem 2.5rem;
    }
    .lp-section-heading {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(2rem, 3.5vw, 2.8rem);
        line-height: 1.12;
        letter-spacing: -0.02em;
        color: var(--skm-text);
    }
    /* ── Step cards ── */
    .lp-step {
        border: 1px solid var(--skm-border);
        background: #fff;
        padding: 2rem;
        border-radius: 4px;
        transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
        height: 100%;
    }
    .lp-step:hover {
        transform: translateY(-5px);
        border-color: rgba(15, 95, 122, 0.38);
        box-shadow: 0 20px 32px -28px rgba(16, 36, 60, 0.7);
    }
    .lp-step-num {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 3.2rem;
        line-height: 1;
        color: var(--skm-muted);
        opacity: 0.38;
        letter-spacing: -0.02em;
    }
    .lp-step-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 1.28rem;
        margin-top: 1.4rem;
        margin-bottom: 0.5rem;
        color: var(--skm-text);
    }
    /* ── Quote ── */
    .lp-quote {
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: calc(-50vw + 0px);
        margin-right: calc(-50vw + 0px);
        background: var(--skm-primary);
        color: #fff;
        padding: 4.5rem 2.5rem;
        position: relative;
        overflow: hidden;
    }
    .lp-quote::before {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0.025;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
        mix-blend-mode: multiply;
    }
    .lp-quote-text {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(1.4rem, 3vw, 2.2rem);
        line-height: 1.4;
        font-style: italic;
        position: relative;
        z-index: 1;
    }
    .lp-quote-inner {
        width: 100%;
        max-width: 1440px;
        margin: 0 auto;
    }
    /* ── FAQ ── */
    .lp-faq .accordion-item {
        border: 0;
        background: transparent;
    }
    .lp-faq .accordion-button {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 1.15rem;
        line-height: 1.4;
        color: var(--skm-text);
        background: transparent;
        box-shadow: none;
        padding-left: 0;
        padding-right: 0;
    }
    .lp-faq .accordion-button:not(.collapsed) {
        color: var(--skm-primary);
        background: transparent;
    }
    .lp-faq .accordion-body {
        color: var(--skm-muted);
        font-size: 1rem;
        line-height: 1.75;
        padding: 0 0 1.2rem 0;
    }
    /* ── Footer ── */
    .lp-footer {
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: calc(-50vw + 0px);
        margin-right: calc(-50vw + 0px);
        border-top: 1px solid var(--skm-border);
        padding: 3.5rem 2.5rem 2rem;
        background: rgba(255,255,255,0.4);
    }
    .lp-footer-inner {
        width: 100%;
        max-width: 1440px;
        margin: 0 auto;
    }
    .lp-footer-brand {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 1.5rem;
        color: var(--skm-text);
        letter-spacing: -0.02em;
    }
    .lp-footer-link {
        color: var(--skm-text);
        text-decoration: none;
        font-size: 0.92rem;
        opacity: 0.78;
        transition: opacity 0.2s;
    }
    .lp-footer-link:hover { opacity: 1; color: var(--skm-primary); }
    /* ── CTA ── */
    .lp-btn-primary {
        background: var(--skm-primary);
        border: 1px solid var(--skm-primary);
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        padding: 0.8rem 1.8rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.22s ease;
        box-shadow: 0 8px 18px -12px rgba(15, 95, 122, 0.55);
    }
    .lp-btn-primary:hover {
        background: var(--skm-primary-strong);
        border-color: var(--skm-primary-strong);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 12px 24px -12px rgba(15, 95, 122, 0.65);
    }
    .lp-btn-primary .lp-arrow { transition: transform 0.2s; }
    .lp-btn-primary:hover .lp-arrow { transform: translateX(4px); }
    .lp-btn-ghost {
        background: transparent;
        border: none;
        color: var(--skm-text);
        font-size: 0.93rem;
        font-weight: 500;
        padding: 0.8rem 0.4rem;
        text-decoration: none;
        opacity: 0.72;
        transition: opacity 0.2s;
        position: relative;
    }
    .lp-btn-ghost::after {
        content: "";
        position: absolute;
        left: 0; bottom: 0.5rem;
        height: 1px;
        width: 100%;
        background: currentColor;
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.32s ease;
    }
    .lp-btn-ghost:hover { opacity: 1; color: var(--skm-text); }
    .lp-btn-ghost:hover::after { transform: scaleX(1); transform-origin: left; }
    /* ── Responsive ── */
    @media (max-width: 991.98px) {
        .lp-hero { padding: 3rem 1.5rem 4rem; }
        .lp-hero-media img { height: 380px; }
        .lp-hero-media.is-emblem { padding: 1.5rem; }
        .lp-hero-media.is-emblem img { width: min(100%, 260px); }
        .lp-section { padding: 3.5rem 1.5rem; }
        .lp-stat-col + .lp-stat-col { border-left: none; border-top: 1px solid var(--skm-border); }
        .lp-quote { padding: 3.5rem 1.5rem; }
        .lp-footer { padding: 3rem 1.5rem 2rem; }
    }
    @media (max-width: 575.98px) {
        .lp-hero-media img { height: 280px; }
        .lp-hero-media.is-emblem img { width: min(100%, 210px); }
        .lp-hero { padding: 2.5rem 1rem 3rem; }
        .lp-section { padding: 2.8rem 1rem; }
        .lp-footer { padding: 2.5rem 1rem 1.5rem; }
    }
</style>
@endpush

@section('content')
@php
    $totalRespondents = $totalRespondents ?? 0;
    $avgIkm           = $avgIkm ?? 0;
    $institutionCount = $institutionCount ?? 0;
@endphp

{{-- ══ HERO ══ --}}
<section class="lp-hero lp-grain-bg">
    <div class="row g-4 g-xl-5 align-items-center">

        {{-- Copy --}}
        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="lp-rule" style="margin-bottom:0;"></span>
                <span class="lp-kicker">Pemerintah Kota Magelang</span>
            </div>

            <h1 class="lp-hero-title">
                Suara Anda,<br><em>wajah</em> pelayanan kami.
            </h1>

            <p class="mt-4 mb-5" style="font-size:1.1rem;line-height:1.78;color:var(--skm-muted);max-width:560px;">
                SiSUKMA — Sistem Informasi Survei Kepuasan Masyarakat — hadir untuk menjembatani
                aspirasi Anda dengan perbaikan nyata pada setiap layanan publik di Kota Magelang.
            </p>

            <div class="d-flex flex-wrap align-items-center gap-3 mb-5">
                <a href="{{ route('survey.selectCity') }}" class="lp-btn-primary">
                    Mulai Survei Sekarang
                    <svg class="lp-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#tentang" class="lp-btn-ghost">Pelajari lebih lanjut</a>
            </div>

            <div class="lp-trust">
                <div class="lp-trust-item">
                    <svg class="lp-trust-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    Anonim &amp; aman
                </div>
                <div class="lp-trust-item">
                    <svg class="lp-trust-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Hanya 3 menit
                </div>
                <div class="lp-trust-item">
                    <svg class="lp-trust-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    Permenpan RB 14/2017
                </div>
            </div>
        </div>

        {{-- Image + float card --}}
        <div class="col-lg-5">
            <div style="position:relative;">
                <div class="d-none d-lg-block" style="position:absolute;top:-1rem;right:-1rem;width:90px;height:90px;border:1px solid rgba(212,167,74,0.45);z-index:0;pointer-events:none;"></div>
                 <div class="lp-hero-media is-emblem" style="position:relative;z-index:1;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Seal_of_the_City_of_Magelang.svg/500px-Seal_of_the_City_of_Magelang.svg.png"
                        alt="Lambang resmi Kota Magelang" loading="eager">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ STATS ══ --}}
<section id="statistik" class="lp-stats">
    <div class="row g-0">
        <div class="col-md-4 lp-stat-col">
            <span class="lp-rule"></span>
            <div class="lp-stat-num">{{ number_format((int)$totalRespondents) }}+</div>
            <div class="lp-kicker mt-2">Total Responden</div>
        </div>
        <div class="col-md-4 lp-stat-col">
            <span class="lp-rule"></span>
            <div class="lp-stat-num">{{ number_format((float)$avgIkm, 2) }} <span style="font-size:1.1rem;color:var(--skm-muted);">/ 4.0</span></div>
            <div class="lp-kicker mt-2">Rata-Rata IKM</div>
        </div>
        <div class="col-md-4 lp-stat-col">
            <span class="lp-rule"></span>
            <div class="lp-stat-num">{{ number_format((int)$institutionCount) }}</div>
            <div class="lp-kicker mt-2">Instansi Terdaftar</div>
        </div>
    </div>
</section>

{{-- ══ TENTANG ══ --}}
<section id="tentang" class="lp-section">
    <div class="row g-4 g-lg-5 align-items-start">
        <div class="col-lg-5">
            <div class="lp-kicker mb-3">Tentang</div>
            <h2 class="lp-section-heading">Transparansi dimulai dari mendengar.</h2>
        </div>
        <div class="col-lg-7">
            <p style="font-size:1.08rem;line-height:1.8;color:var(--skm-muted);margin-bottom:1.2rem;">
                SiSUKMA adalah portal resmi yang dirancang untuk mengukur tingkat kepuasan masyarakat terhadap
                layanan publik yang diselenggarakan oleh instansi Pemerintah Kota Magelang.
            </p>
            <p style="font-size:1.08rem;line-height:1.8;color:var(--skm-muted);margin:0;">
                Kami percaya setiap suara warga adalah dasar dari perbaikan pelayanan publik. Melalui SiSUKMA,
                Anda dapat memberikan penilaian dan saran secara langsung, cepat, dan transparan.
            </p>
        </div>
    </div>
</section>

{{-- ══ ALUR ══ --}}
<section id="alur" class="lp-section" style="padding-top:0;">
    <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-5">
        <div>
            <div class="lp-ornament mb-4" style="max-width:160px;"><span>Alur</span></div>
            <h2 class="lp-section-heading mb-0">Empat langkah sederhana.</h2>
        </div>
    </div>

    <div class="row g-3 g-lg-4">
        @foreach([
            ['n'=>'01','title'=>'Pilih Lokasi',    'desc'=>'Tentukan apakah Anda ingin mensurvei layanan OPD atau Mal Pelayanan Publik.'],
            ['n'=>'02','title'=>'Pilih Instansi',  'desc'=>'Cari instansi yang baru saja Anda kunjungi dari daftar yang tersedia.'],
            ['n'=>'03','title'=>'Isi 9 Pertanyaan','desc'=>'Berikan penilaian 1-4 untuk setiap aspek pelayanan sesuai pengalaman Anda.'],
            ['n'=>'04','title'=>'Tulis Saran',      'desc'=>'Bagikan masukan opsional agar pelayanan ke depan semakin baik.'],
        ] as $s)
        <div class="col-sm-6 col-xl-3">
            <article class="lp-step">
                <div class="lp-step-num">{{ $s['n'] }}</div>
                <h3 class="lp-step-title">{{ $s['title'] }}</h3>
                <p style="font-size:0.9rem;color:var(--skm-muted);line-height:1.65;margin:0;">{{ $s['desc'] }}</p>
            </article>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('survey.selectCity') }}" class="lp-btn-primary" style="font-size:1.05rem;padding:0.9rem 2.4rem;">
            Mulai Sekarang
            <svg class="lp-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>

{{-- ══ QUOTE ══ --}}
<section class="lp-quote text-center">
    <div class="lp-quote-inner">
        <div style="max-width:700px;margin:0 auto;position:relative;z-index:1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="rgba(212,167,74,0.9)" stroke-width="1.5" style="display:block;margin:0 auto 1.5rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
            </svg>
            <p class="lp-quote-text mb-4">"Pemerintah yang baik adalah pemerintah yang mau mendengar — dan bertindak atas apa yang didengar."</p>
            <div class="lp-kicker" style="color:rgba(255,255,255,0.55);">Pemerintah Kota Magelang</div>
        </div>
    </div>
</section>

{{-- ══ FAQ ══ --}}
<section id="faq" class="lp-section lp-faq" style="max-width:760px;margin:0 auto;">
    <div class="text-center mb-5">
        <div class="lp-ornament mb-4" style="max-width:120px;margin:0 auto;"><span>FAQ</span></div>
        <h2 class="lp-section-heading mb-0">Pertanyaan umum.</h2>
    </div>

    <div class="accordion" id="faqAccordion">
        @foreach([
            ['id'=>'faq1','q'=>'Apa itu SISUKMA?',                   'a'=>'SISUKMA adalah Sistem Informasi Survei Kepuasan Masyarakat milik Pemerintah Kota Magelang untuk mengukur kualitas pelayanan publik secara berkala dan transparan.','open'=>true],
            ['id'=>'faq2','q'=>'Apakah data saya aman?',             'a'=>'Ya. Data digunakan secara agregat untuk evaluasi layanan dan tidak dipublikasikan sebagai data personal individu.'],
            ['id'=>'faq3','q'=>'Berapa lama waktu mengisi survei?',   'a'=>'Rata-rata hanya sekitar 3 menit — 9 pertanyaan utama dan 1 kolom saran opsional.'],
            ['id'=>'faq4','q'=>'Apakah saya perlu mendaftar?',       'a'=>'Tidak. Anda dapat langsung mengisi survei tanpa membuat akun atau login.'],
            ['id'=>'faq5','q'=>'Bagaimana hasil survei digunakan?',  'a'=>'Hasil survei digunakan untuk evaluasi, perencanaan peningkatan layanan, dan publikasi kinerja sesuai pedoman IKM nasional.'],
        ] as $item)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ empty($item['open']) ? 'collapsed' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#{{ $item['id'] }}"
                    aria-expanded="{{ !empty($item['open']) ? 'true' : 'false' }}" aria-controls="{{ $item['id'] }}">
                    {{ $item['q'] }}
                </button>
            </h2>
            <div id="{{ $item['id'] }}" class="accordion-collapse collapse {{ !empty($item['open']) ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                <div class="accordion-body">{{ $item['a'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ FOOTER ══ --}}
<footer class="lp-footer">
    <div class="lp-footer-inner">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="d-inline-flex align-items-center justify-content-center"
                        style="width:34px;height:34px;border-radius:8px;background:var(--skm-primary);color:#fff;font-size:0.68rem;font-weight:700;flex-shrink:0;">SKM</span>
                    <span class="lp-footer-brand">SiSUKMA</span>
                </div>
                <div class="lp-kicker mb-3">Pemerintah Kota Magelang</div>
                <p style="font-size:0.9rem;color:var(--skm-muted);max-width:320px;line-height:1.7;margin:0;">
                    Portal resmi Survei Kepuasan Masyarakat berdasarkan Peraturan Menteri PANRB No. 14 Tahun 2017.
                </p>
            </div>
            <div class="col-lg-3 col-6">
                <div class="lp-kicker mb-3">Tautan</div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#tentang" class="lp-footer-link">Tentang SISUKMA</a></li>
                    <li class="mb-2"><a href="#alur" class="lp-footer-link">Alur Survei</a></li>
                    <li><a href="#faq" class="lp-footer-link">Pertanyaan Umum</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-6">
                <div class="lp-kicker mb-3">Kontak</div>
                <p style="font-size:0.9rem;color:var(--skm-muted);line-height:1.7;margin-bottom:0.6rem;">
                    Jl. Jendral Sudirman No. 46<br>
                    Kota Magelang, Jawa Tengah 56125
                </p>
                <p style="font-size:0.9rem;color:var(--skm-muted);line-height:1.7;margin:0;">
                    (0293) 362 111<br>
                    sisukma@magelangkota.go.id
                </p>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 mt-5 pt-3"
             style="border-top:1px solid var(--skm-border);">
            <span style="font-size:0.82rem;color:var(--skm-muted);">© {{ now()->year }} Pemerintah Kota Magelang. Hak Cipta Dilindungi.</span>
            <span class="lp-kicker">Permenpan RB 14 / 2017</span>
        </div>
    </div>
</footer>
@endsection
