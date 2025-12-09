@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Monitoring & Evaluasi
                </div>
                <h2 class="page-title">
                    Dashboard SKM
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 21l18 0" />
                        <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" />
                        <path d="M5 21l0 -10.15" />
                        <path d="M19 21l0 -10.15" />
                        <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                    </svg>
                    {{ $user->hasRole('super_admin') ? 'Semua Instansi' : $user->institution->name }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Statistik Cards -->
        <div class="row row-deck row-cards mb-4">
            <!-- Total Responden -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ number_format($totalResponses) }}
                                </div>
                                <div class="text-muted">
                                    Total Responden
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Layanan -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-info text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                        <path d="M12 12l8 -4.5" />
                                        <path d="M12 12l0 9" />
                                        <path d="M12 12l-8 -4.5" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ number_format($totalServices) }}
                                </div>
                                <div class="text-muted">
                                    Total Layanan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rata-rata SKM -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ number_format($averageSKM, 2) }}
                                </div>
                                <div class="text-muted">
                                    Rata-rata SKM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Mutu -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-{{ $kategoriMutu['color'] }} text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 6l4 6l5 -4l-2 10h-14l-2 -10l5 4z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Kategori {{ $kategoriMutu['kategori'] }}
                                </div>
                                <div class="text-muted">
                                    {{ $kategoriMutu['mutu'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris Kedua Cards -->
        <div class="row row-deck row-cards mb-4">
            <!-- SKM Bulan Ini -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-cyan text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <rect x="4" y="5" width="16" height="16" rx="2" />
                                        <line x1="16" y1="3" x2="16" y2="7" />
                                        <line x1="8" y1="3" x2="8" y2="7" />
                                        <line x1="4" y1="11" x2="20" y2="11" />
                                        <line x1="10" y1="16" x2="14" y2="16" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ number_format($currentMonthSKM, 2) }}
                                </div>
                                <div class="text-muted">
                                    SKM Bulan Ini
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Section -->
        <div class="row row-deck row-cards">
            <!-- Grafik SKM per Bulan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">Tren Nilai SKM per Bulan</div>
                    </div>
                    <div class="card-body card-body-scrollable card-body-scrollable-shadow">
                        <div id="chart-skm-monthly" style="min-height: 300px;"></div>
                        <div class="text-muted mt-3 small">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                                <polyline points="11 12 12 12 12 16 13 16" />
                            </svg>
                            Perbandingan nilai SKM 6 bulan terakhir
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Responden per Bulan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">Tren Responden</div>
                    </div>
                    <div class="card-body card-body-scrollable card-body-scrollable-shadow">
                        <div id="chart-responden" style="min-height: 300px;"></div>
                        <div class="text-muted mt-3 small">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                                <polyline points="11 12 12 12 12 16 13 16" />
                            </svg>
                            Data responden 6 bulan terakhir
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik SKM per Unsur -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">Evaluasi per Unsur Pelayanan</div>
                    </div>
                    <div class="card-body card-body-scrollable card-body-scrollable-shadow">
                        <div id="chart-unsur" style="min-height: 300px;"></div>
                        <div class="text-muted mt-3 small">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                                <polyline points="11 12 12 12 12 16 13 16" />
                            </svg>
                            Nilai SKM per unsur pelayanan (skala 0-100)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik SKM per Layanan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">Top Layanan Terbaik</div>
                        <div class="card-actions">
                            <a href="#" class="btn btn-sm btn-primary">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart-layanan" style="min-height: 300px;"></div>
                        <div class="text-muted mt-3 small">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                                <polyline points="11 12 12 12 12 16 13 16" />
                            </svg>
                            5 Layanan dengan nilai SKM tertinggi
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="row row-cards mt-4">
            <div class="col-12">
                <div class="card card-md">
                    <div class="card-stamp card-stamp-lg">
                        <div class="card-stamp-icon bg-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                            </svg>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h3 class="h1">Kategori Mutu Pelayanan</h3>
                                <div class="markdown text-muted">
                                    <p>Nilai SKM Anda saat ini: <strong class="text-{{ $kategoriMutu['color'] }}">{{ number_format($averageSKM, 2) }}</strong></p>
                                    <p class="mb-0">Kategori: <span class="badge badge-outline text-{{ $kategoriMutu['color'] }}">{{ $kategoriMutu['kategori'] }} - {{ $kategoriMutu['mutu'] }}</span></p>
                                </div>
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-md-6 col-xl-3">
                                            <div class="mb-2">
                                                <span class="badge bg-success">A</span>
                                                <span class="text-muted ms-2">88.31 - 100 (Sangat Baik)</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <div class="mb-2">
                                                <span class="badge bg-info">B</span>
                                                <span class="text-muted ms-2">76.61 - 88.30 (Baik)</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <div class="mb-2">
                                                <span class="badge bg-warning">C</span>
                                                <span class="text-muted ms-2">65.00 - 76.60 (Kurang Baik)</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <div class="mb-2">
                                                <span class="badge bg-danger">D</span>
                                                <span class="text-muted ms-2">< 65.00 (Tidak Baik)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Data dari Controller
    const monthlyData = @json($monthlyData);
    const monthlySKMData = @json($monthlySKMData);
    const unsurData = @json($unsurData);
    const servicesData = @json($servicesData);

    // Nama bulan
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    // Grafik SKM per Bulan
    const skmMonthlyCategories = monthlySKMData.map(item => monthNames[item.month - 1] + ' ' + item.year);
    const skmMonthlySeries = monthlySKMData.map(item => item.skm);

    const chartSKMMonthly = new ApexCharts(document.getElementById('chart-skm-monthly'), {
        chart: {
            type: 'line',
            fontFamily: 'inherit',
            height: 300,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: true
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1);
            },
            style: {
                fontSize: '11px',
            },
            background: {
                enabled: true,
                foreColor: '#fff',
                borderRadius: 2,
                padding: 4,
                opacity: 0.9,
            },
        },
        fill: {
            opacity: 0.2,
            type: 'solid'
        },
        stroke: {
            width: 3,
            lineCap: "round",
            curve: "smooth",
        },
        series: [{
            name: "Nilai SKM",
            data: skmMonthlySeries
        }],
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return val.toFixed(2);
                }
            }
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            categories: skmMonthlyCategories,
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false,
            },
        },
        yaxis: {
            max: 100,
            min: 0,
            labels: {
                padding: 4
            },
        },
        colors: ['#2fb344'],
        markers: {
            size: 5,
            colors: ['#2fb344'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        legend: {
            show: false,
        },
    });
    chartSKMMonthly.render();

    // Grafik Responden per Bulan
    const monthlyCategories = monthlyData.map(item => monthNames[item.month - 1] + ' ' + item.year);
    const monthlySeries = monthlyData.map(item => item.total);

    const chartResponden = new ApexCharts(document.getElementById('chart-responden'), {
        chart: {
            type: 'area',
            fontFamily: 'inherit',
            height: 300,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: true
            },
        },
        dataLabels: {
            enabled: false,
        },
        fill: {
            opacity: 0.16,
            type: 'solid'
        },
        stroke: {
            width: 2,
            lineCap: "round",
            curve: "smooth",
        },
        series: [{
            name: "Responden",
            data: monthlySeries
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            categories: monthlyCategories,
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                padding: 4
            },
        },
        colors: ['#206bc4'],
        legend: {
            show: false,
        },
    });
    chartResponden.render();

    // Grafik SKM per Unsur
    const unsurCategories = unsurData.map(item => item.name);
    const unsurSeries = unsurData.map(item => item.score);

    const chartUnsur = new ApexCharts(document.getElementById('chart-unsur'), {
        chart: {
            type: 'bar',
            fontFamily: 'inherit',
            height: 300,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '60%',
                distributed: false,
                borderRadius: 4,
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1);
            },
            style: {
                fontSize: '12px',
                colors: ['#fff']
            },
        },
        series: [{
            name: 'Nilai SKM',
            data: unsurSeries
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: true
                }
            },
        },
        xaxis: {
            categories: unsurCategories,
            max: 100,
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '11px'
                }
            }
        },
        colors: ['#2fb344'],
    });
    chartUnsur.render();

    // Grafik SKM per Layanan
    const serviceCategories = servicesData.map(item => item.name);
    const serviceSeries = servicesData.map(item => item.skm);

    const chartLayanan = new ApexCharts(document.getElementById('chart-layanan'), {
        chart: {
            type: 'bar',
            fontFamily: 'inherit',
            height: 350,
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            bar: {
                columnWidth: '60%',
                distributed: false,
                borderRadius: 4,
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1);
            },
            style: {
                fontSize: '12px',
                colors: ['#fff']
            },
        },
        series: [{
            name: 'Nilai SKM',
            data: serviceSeries
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: false
                }
            },
        },
        xaxis: {
            categories: serviceCategories,
            labels: {
                rotate: -45,
                rotateAlways: false,
            },
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false,
            },
        },
        yaxis: {
            max: 100,
            labels: {
                padding: 4
            },
        },
        colors: ['#d63939'],
    });
    chartLayanan.render();
});
</script>
@endpush
@endsection