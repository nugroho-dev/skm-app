@extends('layouts.app')

@push('styles')
<style>
    .skm-page {
        max-width: min(1440px, calc(100vw - 2rem));
    }

    .grafik-page {
        padding: 1.2rem 0 2rem;
    }

    .grafik-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 24px;
        background:
            radial-gradient(circle at top right, rgba(212, 167, 74, 0.14), transparent 32%),
            radial-gradient(circle at left center, rgba(15, 95, 122, 0.10), transparent 38%),
            linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248,246,241,0.98));
        padding: 2rem;
        box-shadow: 0 24px 50px -36px rgba(16, 36, 60, 0.62);
    }

    .grafik-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.035;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
        mix-blend-mode: multiply;
    }

    .grafik-hero > * {
        position: relative;
        z-index: 1;
    }

    .grafik-kicker {
        font-size: 0.72rem;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: var(--skm-muted);
        font-weight: 700;
    }

    .grafik-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(2rem, 3.4vw, 3rem);
        line-height: 1.08;
        letter-spacing: -0.02em;
        color: var(--skm-text);
        margin: 0.55rem 0 0.85rem;
    }

    .grafik-lead {
        color: var(--skm-muted);
        font-size: 1rem;
        line-height: 1.75;
        max-width: 640px;
        margin: 0;
    }

    .grafik-badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: flex-start;
    }

    .grafik-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.6rem 0.85rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.82);
        border: 1px solid rgba(228, 221, 207, 0.92);
        color: var(--skm-text);
        font-size: 0.88rem;
        box-shadow: 0 10px 24px -24px rgba(16, 36, 60, 0.9);
    }

    .grafik-badge-label {
        color: var(--skm-muted);
        font-weight: 600;
    }

    .grafik-action {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.85rem 1.25rem;
        border-radius: 999px;
        border: 1px solid var(--skm-primary);
        background: var(--skm-primary);
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 14px 28px -20px rgba(15, 95, 122, 0.72);
        transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease;
    }

    .grafik-action:hover {
        background: var(--skm-primary-strong);
        border-color: var(--skm-primary-strong);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 18px 32px -20px rgba(15, 95, 122, 0.82);
    }

    .grafik-summary-grid {
        margin-top: 1.25rem;
    }

    .grafik-summary-card {
        height: 100%;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 18px;
        background: rgba(255,255,255,0.88);
        padding: 1.15rem 1.2rem;
        box-shadow: 0 18px 36px -30px rgba(16, 36, 60, 0.62);
    }

    .grafik-summary-label {
        color: var(--skm-muted);
        font-size: 0.76rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 0.55rem;
    }

    .grafik-summary-value {
        font-family: "Playfair Display", Georgia, serif;
        color: var(--skm-text);
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        line-height: 1.15;
        margin: 0;
    }

    .grafik-summary-note {
        margin: 0.5rem 0 0;
        color: var(--skm-muted);
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .grafik-section {
        margin-top: 1.4rem;
    }

    .grafik-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .grafik-section-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(1.5rem, 2.4vw, 2rem);
        color: var(--skm-text);
        line-height: 1.15;
        margin: 0;
    }

    .grafik-section-copy {
        margin: 0.35rem 0 0;
        color: var(--skm-muted);
        font-size: 0.95rem;
        line-height: 1.7;
        max-width: 720px;
    }

    .grafik-card {
        height: 100%;
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 20px;
        background: rgba(255,255,255,0.90);
        box-shadow: 0 24px 42px -34px rgba(16, 36, 60, 0.58);
        overflow: hidden;
    }

    .grafik-card-header {
        padding: 1.1rem 1.2rem 0.75rem;
        border-bottom: 1px solid rgba(228, 221, 207, 0.75);
        background: linear-gradient(180deg, rgba(255,255,255,0.72), rgba(247,244,238,0.72));
    }

    .grafik-card-kicker {
        font-size: 0.7rem;
        letter-spacing: 0.24em;
        text-transform: uppercase;
        color: var(--skm-muted);
        font-weight: 700;
    }

    .grafik-card-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 1.22rem;
        color: var(--skm-text);
        margin: 0.35rem 0 0.2rem;
    }

    .grafik-card-copy {
        margin: 0;
        color: var(--skm-muted);
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .grafik-card-body {
        padding: 1rem 1rem 1.2rem;
    }

    .grafik-modal .modal-content {
        border-radius: 18px;
        border: 1px solid rgba(228, 221, 207, 0.92);
        background:
            radial-gradient(circle at top right, rgba(212, 167, 74, 0.08), transparent 38%),
            linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,246,241,0.98));
        box-shadow: 0 28px 52px -28px rgba(16, 36, 60, 0.52);
        overflow: hidden;
    }

    .grafik-modal {
        z-index: 1080;
    }

    .grafik-modal .modal-header,
    .grafik-modal .modal-body,
    .grafik-modal .modal-footer {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
    }

    .grafik-modal .modal-header {
        border-bottom: 1px solid rgba(228, 221, 207, 0.9);
        padding-top: 1.2rem;
        padding-bottom: 1rem;
    }

    .grafik-modal .modal-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 1.45rem;
        color: var(--skm-text);
    }

    .grafik-modal .modal-subtitle {
        margin: 0.35rem 0 0;
        color: var(--skm-muted);
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .grafik-modal .modal-body {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .grafik-modal .grafik-filter-panel {
        border: 1px solid rgba(228, 221, 207, 0.92);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.88);
        box-shadow: 0 12px 24px -26px rgba(16, 36, 60, 0.72);
        padding: 1rem;
    }

    .grafik-modal .grafik-filter-panel-title {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 1.05rem;
        color: var(--skm-text);
        margin: 0 0 0.2rem;
    }

    .grafik-modal .grafik-filter-panel-note {
        margin: 0;
        font-size: 0.83rem;
        color: var(--skm-muted);
        line-height: 1.58;
    }

    .grafik-modal .form-label {
        font-weight: 600;
        color: var(--skm-text);
        font-size: 0.84rem;
        margin-bottom: 0.38rem;
    }

    .grafik-modal .form-control,
    .grafik-modal .form-select {
        border: 1px solid var(--skm-border);
        border-radius: 10px;
        padding: 0.6rem 0.78rem;
        font-size: 0.9rem;
        background-color: #fff;
        color: var(--skm-text);
    }

    .grafik-modal .form-control:focus,
    .grafik-modal .form-select:focus {
        border-color: rgba(15, 95, 122, 0.48);
        box-shadow: 0 0 0 0.2rem rgba(15, 95, 122, 0.14);
    }

    .grafik-modal .modal-footer {
        border-top: 1px solid rgba(228, 221, 207, 0.9);
        padding-top: 1rem;
        padding-bottom: 1.15rem;
        gap: 0.65rem;
    }

    .grafik-modal .btn-reset {
        background: #fff;
        color: var(--skm-text);
        border: 1px solid var(--skm-border);
        border-radius: 10px;
        font-weight: 600;
        padding: 0.58rem 1rem;
    }

    .grafik-modal .btn-reset:hover {
        background: #f6f8fb;
        border-color: #cfd9e6;
        color: var(--skm-primary);
    }

    @media (max-width: 991.98px) {
        .grafik-hero {
            padding: 1.5rem;
        }
    }

    @media (max-width: 767.98px) {
        .skm-page {
            max-width: calc(100vw - 1rem);
        }

        .grafik-page {
            padding-top: 0.5rem;
        }

        .grafik-hero,
        .grafik-card {
            border-radius: 18px;
        }
    }
</style>
@endpush

@section('content')
@php
    $selectedInstitutionLabel = $selectedInstitution ?: 'Semua Instansi';
    $hasBulanan = count($ikmBulanan) > 0;
    $hasTahunan = count($ikmTahunan) > 0;
    $latestIkm = $hasBulanan
        ? ($ikmBulanan[array_key_last($ikmBulanan)]['ikm'] ?? null)
        : ($hasTahunan ? ($ikmTahunan[array_key_last($ikmTahunan)]['ikm'] ?? null) : null);
    $yearCount = count($ikmTahunan);
@endphp

<div class="grafik-page">
    <section class="grafik-hero">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="grafik-kicker">Visualisasi SKM</div>
                <h1 class="grafik-title">Grafik kepuasan masyarakat yang selaras dengan ritme pelayanan publik.</h1>
                <p class="grafik-lead">
                    Pantau tren indeks kepuasan masyarakat berdasarkan periode dan instansi untuk membaca arah kualitas layanan secara lebih cepat dan lebih jernih.
                </p>
            </div>
            <div class="col-lg-4 d-flex justify-content-lg-end">
                <button type="button" class="grafik-action" data-open-modal="#filterModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M20 3h-16a1 1 0 0 0 -1 1v2.227l.008 .223a3 3 0 0 0 .772 1.795l4.22 4.641v8.114a1 1 0 0 0 1.316 .949l6 -2l.108 -.043a1 1 0 0 0 .576 -.906v-6.586l4.121 -4.12a3 3 0 0 0 .879 -2.123v-2.171a1 1 0 0 0 -1 -1z"/>
                    </svg>
                    Atur Filter Grafik
                </button>
            </div>
        </div>

        <div class="grafik-badge-row mt-4">
            <div class="grafik-badge">
                <span class="grafik-badge-label">Instansi</span>
                <strong>{{ $selectedInstitutionLabel }}</strong>
            </div>
            <div class="grafik-badge">
                <span class="grafik-badge-label">Tahun Fokus</span>
                <strong>{{ $selectedYear }}</strong>
            </div>
            <div class="grafik-badge">
                <span class="grafik-badge-label">Rentang Historis</span>
                <strong>{{ $yearCount }} tahun</strong>
            </div>
        </div>

        <div class="row g-3 grafik-summary-grid">
            <div class="col-md-4">
                <article class="grafik-summary-card">
                    <div class="grafik-summary-label">Fokus Laporan</div>
                    <p class="grafik-summary-value">{{ $selectedInstitutionLabel }}</p>
                    <p class="grafik-summary-note">Tampilan grafik aktif mengikuti cakupan instansi yang dipilih melalui filter publik.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="grafik-summary-card">
                    <div class="grafik-summary-label">Tahun Terpilih</div>
                    <p class="grafik-summary-value">{{ $selectedYear }}</p>
                    <p class="grafik-summary-note">Grafik bulanan, triwulan, dan semester ditampilkan khusus untuk tahun ini agar pembacaan tren lebih fokus.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="grafik-summary-card">
                    <div class="grafik-summary-label">Nilai Terbaru</div>
                    <p class="grafik-summary-value">{{ $latestIkm !== null ? number_format((float) $latestIkm, 2) : 'Belum ada data' }}</p>
                    <p class="grafik-summary-note">Nilai diambil dari periode terbaru yang tersedia dalam kumpulan data yang sedang aktif.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="grafik-section">
        <div class="grafik-section-heading">
            <div>
                <div class="grafik-kicker">Pembacaan Tren</div>
                <h2 class="grafik-section-title">Pola perubahan kualitas layanan dari waktu ke waktu.</h2>
                <p class="grafik-section-copy">Bagian ini menyajikan pembacaan periodik agar tren jangka pendek dan jangka panjang bisa dibandingkan dalam satu halaman dengan bahasa visual yang konsisten.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <article class="grafik-card">
                    <div class="grafik-card-header">
                        <div class="grafik-card-kicker">Bulanan</div>
                        <h3 class="grafik-card-title">Nilai IKM Per Bulan</h3>
                        <p class="grafik-card-copy">Menunjukkan irama perubahan kepuasan masyarakat sepanjang tahun {{ $selectedYear }}.</p>
                    </div>
                    <div class="grafik-card-body">
                        <div id="chart-bulanan" style="height: 400px;"></div>
                    </div>
                </article>
            </div>

            <div class="col-md-6">
                <article class="grafik-card">
                    <div class="grafik-card-header">
                        <div class="grafik-card-kicker">Triwulan</div>
                        <h3 class="grafik-card-title">Perbandingan Per Kuartal</h3>
                        <p class="grafik-card-copy">Membantu melihat periode yang paling stabil dan periode yang perlu perhatian lebih cepat.</p>
                    </div>
                    <div class="grafik-card-body">
                        <div id="chart-triwulan" style="height: 350px;"></div>
                    </div>
                </article>
            </div>

            <div class="col-md-6">
                <article class="grafik-card">
                    <div class="grafik-card-header">
                        <div class="grafik-card-kicker">Semester</div>
                        <h3 class="grafik-card-title">Evaluasi Semesteran</h3>
                        <p class="grafik-card-copy">Meringkas kinerja semester pertama dan kedua agar arah pembenahan layanan lebih mudah dibaca.</p>
                    </div>
                    <div class="grafik-card-body">
                        <div id="chart-semester" style="height: 350px;"></div>
                    </div>
                </article>
            </div>

            <div class="col-12">
                <article class="grafik-card">
                    <div class="grafik-card-header">
                        <div class="grafik-card-kicker">Tahunan</div>
                        <h3 class="grafik-card-title">Tren Jangka Panjang</h3>
                        <p class="grafik-card-copy">Menunjukkan perkembangan indeks kepuasan masyarakat dari tahun ke tahun pada cakupan yang sama.</p>
                    </div>
                    <div class="grafik-card-body">
                        <div id="chart-tahunan" style="height: 350px;"></div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/themes/adaptive.js"></script>
<script>
    // === DATA DARI CONTROLLER (Laravel) ===
    const dataBulanan = @json($ikmBulanan);
    const dataTriwulan = @json($ikmTriwulan);
    const dataSemester = @json($ikmSemester);
    const dataTahunan = @json($ikmTahunan);

    // Global Highcharts configuration
    Highcharts.setOptions({
        colors: ['#206bc4', '#4299e1', '#63b3ed', '#90cdf4'],
        chart: {
            backgroundColor: 'transparent',
            style: {
                fontFamily: 'inherit'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            backgroundColor: '#1e293b',
            style: {
                color: '#ffffff'
            },
            borderRadius: 8,
            borderWidth: 0
        }
    });

    // === CHART BULANAN ===
    Highcharts.chart('chart-bulanan', {
        chart: { 
            type: 'area'
        },
        title: { 
            text: 'Nilai IKM Per Bulan',
            style: {
                fontSize: '18px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Tren bulanan dengan indikator kategori mutu'
        },
        xAxis: { 
            categories: dataBulanan.map(d => d.label),
            gridLineWidth: 1,
            gridLineDashStyle: 'Dash'
        },
        yAxis: { 
            title: { 
                text: 'Nilai IKM',
                style: {
                    fontWeight: '600'
                }
            },
            max: 100,
            min: 0,
            gridLineDashStyle: 'Dash',
            plotLines: [
                {
                    value: 88.31,
                    color: '#10b981',
                    dashStyle: 'ShortDash',
                    width: 2,
                    label: {
                        text: 'A (Sangat Baik)',
                        align: 'right',
                        style: {
                            color: '#10b981',
                            fontWeight: '600'
                        }
                    },
                    zIndex: 5
                },
                {
                    value: 76.61,
                    color: '#3b82f6',
                    dashStyle: 'ShortDash',
                    width: 2,
                    label: {
                        text: 'B (Baik)',
                        align: 'right',
                        style: {
                            color: '#3b82f6',
                            fontWeight: '600'
                        }
                    },
                    zIndex: 5
                },
                {
                    value: 65.00,
                    color: '#f59e0b',
                    dashStyle: 'ShortDash',
                    width: 2,
                    label: {
                        text: 'C (Kurang Baik)',
                        align: 'right',
                        style: {
                            color: '#f59e0b',
                            fontWeight: '600'
                        }
                    },
                    zIndex: 5
                }
            ]
        },
        plotOptions: {
            area: {
                fillOpacity: 0.2,
                marker: {
                    enabled: true,
                    radius: 4
                },
                dataLabels: {
                    enabled: true,
                    format: '{y:.2f}',
                    style: {
                        fontWeight: '600',
                        textOutline: 'none'
                    }
                }
            }
        },
        series: [{
            name: 'Nilai IKM',
            data: dataBulanan.map(d => d.ikm),
            color: '#206bc4'
        }]
    });

    // === CHART TRIWULAN ===
    Highcharts.chart('chart-triwulan', {
        chart: { type: 'column' },
        title: { 
            text: 'Nilai IKM Per Triwulan',
            style: {
                fontSize: '18px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Perbandingan kinerja per kuartal'
        },
        xAxis: { 
            categories: dataTriwulan.map(d => d.label),
            gridLineWidth: 1,
            gridLineDashStyle: 'Dash'
        },
        yAxis: { 
            title: { 
                text: 'Nilai IKM',
                style: {
                    fontWeight: '600'
                }
            },
            max: 100,
            min: 0,
            gridLineDashStyle: 'Dash'
        },
        plotOptions: {
            column: {
                borderRadius: 4,
                borderWidth: 0,
                colorByPoint: true,
                colors: ['#206bc4', '#4299e1', '#63b3ed', '#90cdf4'],
                dataLabels: {
                    enabled: true,
                    format: '{y:.2f}',
                    style: {
                        fontWeight: '600',
                        textOutline: 'none'
                    }
                }
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'Nilai IKM',
            data: dataTriwulan.map(d => d.ikm)
        }]
    });

    // === CHART SEMESTER ===
    Highcharts.chart('chart-semester', {
        chart: { type: 'column' },
        title: { 
            text: 'Nilai IKM Per Semester',
            style: {
                fontSize: '18px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Evaluasi semesteran'
        },
        xAxis: { 
            categories: dataSemester.map(d => d.label),
            gridLineWidth: 1,
            gridLineDashStyle: 'Dash'
        },
        yAxis: { 
            title: { 
                text: 'Nilai IKM',
                style: {
                    fontWeight: '600'
                }
            },
            max: 100,
            min: 0,
            gridLineDashStyle: 'Dash'
        },
        plotOptions: {
            column: {
                borderRadius: 4,
                borderWidth: 0,
                colorByPoint: true,
                colors: ['#206bc4', '#4299e1'],
                dataLabels: {
                    enabled: true,
                    format: '{y:.2f}',
                    style: {
                        fontWeight: '600',
                        textOutline: 'none'
                    }
                }
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'Nilai IKM',
            data: dataSemester.map(d => d.ikm)
        }]
    });

    // === CHART TAHUNAN ===
    Highcharts.chart('chart-tahunan', {
        chart: { type: 'line' },
        title: { 
            text: 'Tren Nilai IKM Per Tahun',
            style: {
                fontSize: '18px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Perkembangan kinerja tahunan'
        },
        xAxis: { 
            categories: dataTahunan.map(d => d.year),
            gridLineWidth: 1,
            gridLineDashStyle: 'Dash'
        },
        yAxis: { 
            title: { 
                text: 'Nilai IKM',
                style: {
                    fontWeight: '600'
                }
            },
            max: 100,
            min: 0,
            gridLineDashStyle: 'Dash'
        },
        plotOptions: {
            line: {
                lineWidth: 3,
                marker: {
                    enabled: true,
                    radius: 6
                },
                dataLabels: {
                    enabled: true,
                    format: '{y:.2f}',
                    style: {
                        fontWeight: '600',
                        textOutline: 'none'
                    }
                }
            }
        },
        series: [{
            name: 'Nilai IKM',
            data: dataTahunan.map(d => d.ikm),
            color: '#10b981'
        }]
    });
</script>
    <div class="modal fade grafik-modal" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{route('survey.grafik')}}"  method="GET">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="filterModalLabel">Atur Fokus Grafik</h5>
                        <p class="modal-subtitle">Pilih tahun dan cakupan instansi untuk menyesuaikan visualisasi data yang ingin dibaca.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="grafik-filter-panel mb-3">
                        <h6 class="grafik-filter-panel-title">Periode Fokus</h6>
                        <p class="grafik-filter-panel-note">Tahun yang dipilih akan mengatur tampilan grafik bulanan, triwulan, dan semester agar pembacaan tren tetap fokus.</p>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Tahun</label>
                                <select name="year" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ (string) $selectedYear === (string) $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grafik-filter-panel">
                        <h6 class="grafik-filter-panel-title">Cakupan Instansi</h6>
                        <p class="grafik-filter-panel-note">Pilih agregat kota, agregat MPP, atau satu instansi tertentu untuk melihat pola grafik pada cakupan yang diinginkan.</p>
                        <div class="row g-3 mt-1">
                        @if($institutionsall->isNotEmpty())
                            <div class="col-md-12">
                                <label class="form-label">Instansi</label>
                                <select name="institution_id" class="form-select">
                                    <option value="">-- Semua --</option>
                                    <option value="kota_ikm" {{ request('institution_id') === 'kota_ikm' ? 'selected' : '' }}>Nilai IKM Kota Magelang</option>
                                    <option value="mpp_ikm" {{ request('institution_id') === 'mpp_ikm' ? 'selected' : '' }}>Nilai IKM MPP</option>
                                    @foreach($institutionsall as $inst)
                                        @if(!empty($inst->slug))
                                            @php $instFilterValue = 'inst:' . $inst->slug; @endphp
                                            <option value="{{ $instFilterValue }}" {{ request('institution_id') === $instFilterValue ? 'selected' : '' }}>
                                                {{ $inst->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('survey.grafik') }}" class="btn btn-reset">Reset Filter</a>
                    <button type="submit" class="btn skm-button-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection