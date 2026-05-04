@extends('layouts.app')

@push('styles')
<style>
    .thank-page {
        padding: 1.25rem 0 2rem;
    }

    .thank-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 22px;
        background:
            radial-gradient(circle at top right, rgba(212, 167, 74, 0.16), transparent 36%),
            radial-gradient(circle at left center, rgba(15, 95, 122, 0.12), transparent 42%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 246, 241, 0.98));
        padding: 1.4rem;
        box-shadow: 0 24px 48px -36px rgba(16, 36, 60, 0.62);
    }

    .thank-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.03;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
        mix-blend-mode: multiply;
    }

    .thank-card > * {
        position: relative;
        z-index: 1;
    }

    .thank-kicker {
        font-size: 0.72rem;
        letter-spacing: 0.26em;
        text-transform: uppercase;
        color: var(--skm-muted);
        font-weight: 700;
        margin-bottom: 0.45rem;
    }

    .thank-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(1.9rem, 3.2vw, 2.7rem);
        line-height: 1.08;
        letter-spacing: -0.02em;
        color: var(--skm-text);
        margin: 0;
    }

    .thank-copy {
        margin-top: 0.95rem;
        margin-bottom: 0;
        font-size: 0.96rem;
        line-height: 1.74;
        color: var(--skm-muted);
        max-width: 760px;
    }

    .thank-highlight {
        margin-top: 1.05rem;
        border: 1px dashed var(--skm-border);
        border-radius: 12px;
        padding: 0.7rem 0.8rem;
        background: rgba(15, 95, 122, 0.06);
        color: var(--skm-muted);
        font-size: 0.86rem;
        line-height: 1.6;
    }

    .thank-actions {
        margin-top: 1.15rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }

    .thank-auto {
        margin-top: 0.9rem;
        font-size: 0.83rem;
        color: var(--skm-muted);
    }

    @media (max-width: 767.98px) {
        .thank-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<section class="thank-page">
    <div class="thank-card">
        <div class="thank-kicker">Survei Selesai</div>
        <h1 class="thank-title">Terima kasih atas partisipasi Anda.</h1>
        <p class="thank-copy">
            Masukan Anda telah kami terima dan akan digunakan sebagai bahan evaluasi peningkatan kualitas layanan publik Kota Magelang.
        </p>

        @if($institution)
            <div class="thank-highlight">
                Survei terakhir Anda: <strong>{{ $institution->name }}</strong>
            </div>
        @endif

        <div class="thank-actions">
            <a href="{{ route('survey.welcome') }}" class="btn skm-button-primary px-4">Kembali ke Beranda</a>
            @if($institution)
                <a href="{{ route('survey.form', $institution->slug) }}" class="btn skm-button-soft px-4">Isi Survei Lagi di Instansi Ini</a>
            @else
                <a href="{{ route('survey.selectCity') }}" class="btn skm-button-soft px-4">Isi Survei Lagi</a>
            @endif
        </div>

        <p class="thank-auto">
            Anda akan diarahkan otomatis ke beranda dalam <span id="countdown">6</span> detik.
        </p>
    </div>
</section>

<script>
    (() => {
        const targetUrl = @json(route('survey.welcome'));
        const countdownEl = document.getElementById('countdown');
        let second = 6;

        const timer = setInterval(() => {
            second -= 1;
            if (countdownEl) {
                countdownEl.textContent = String(second);
            }

            if (second <= 0) {
                clearInterval(timer);
                window.location.href = targetUrl;
            }
        }, 1000);
    })();
</script>
@endsection
