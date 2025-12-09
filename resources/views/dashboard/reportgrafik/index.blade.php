@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body">
    <div class="container-xl ">
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none mb-3" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">{{ $title }}</h2>
              </div>
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div class="d-flex btn-list">
                  
                   {{-- Form Filter --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-filter"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 3h-16a1 1 0 0 0 -1 1v2.227l.008 .223a3 3 0 0 0 .772 1.795l4.22 4.641v8.114a1 1 0 0 0 1.316 .949l6 -2l.108 -.043a1 1 0 0 0 .576 -.906v-6.586l4.121 -4.12a3 3 0 0 0 .879 -2.123v-2.171a1 1 0 0 0 -1 -1z" /></svg> Filter Laporan</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Visualisasi Data SKM</h3>
                <div class="card-actions">
                    <span class="badge bg-primary">{{ $selectedInstitution }}</span>
                    <span class="badge bg-info ms-2">Tahun {{ $selectedYear }}</span>
                </div>
            </div>
            <div class="card-body" id="tabel-ikm">
                <div class="row g-4">
                    <!-- Grafik Bulanan -->
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div id="chart-bulanan" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grafik Triwulan -->
                    <div class="col-lg-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div id="chart-triwulan" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grafik Semester -->
                    <div class="col-lg-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div id="chart-semester" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grafik Tahunan -->
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div id="chart-tahunan" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    // Konfigurasi global Highcharts
    Highcharts.setOptions({
        colors: ['#206bc4', '#2fb344', '#d63939', '#f59f00', '#4299e1', '#9333ea'],
        chart: {
            style: {
                fontFamily: 'inherit'
            }
        },
        credits: {
            enabled: false
        }
    });

    // === CHART BULANAN ===
    Highcharts.chart('chart-bulanan', {
        chart: { 
            type: 'area',
            backgroundColor: 'transparent'
        },
        title: { 
            text: 'Tren SKM Per Bulan',
            style: {
                fontSize: '18px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Perbandingan nilai SKM setiap bulan dalam setahun'
        },
        xAxis: { 
            categories: dataBulanan.map(d => d.label),
            gridLineWidth: 1,
            gridLineDashStyle: 'Dash'
        },
        yAxis: { 
            title: { text: 'Nilai SKM' }, 
            max: 100, 
            min: 0,
            gridLineDashStyle: 'Dash',
            plotLines: [{
                value: 88.31,
                color: '#2fb344',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'Sangat Baik (88.31)',
                    align: 'right',
                    style: {
                        color: '#2fb344'
                    }
                }
            }, {
                value: 76.61,
                color: '#4299e1',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'Baik (76.61)',
                    align: 'right',
                    style: {
                        color: '#4299e1'
                    }
                }
            }, {
                value: 65.00,
                color: '#f59f00',
                dashStyle: 'shortdash',
                width: 2,
                label: {
                    text: 'Kurang Baik (65.00)',
                    align: 'right',
                    style: {
                        color: '#f59f00'
                    }
                }
            }]
        },
        tooltip: {
            shared: true,
            valueSuffix: ' poin',
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            style: {
                color: '#fff'
            }
        },
        plotOptions: {
            area: {
                fillOpacity: 0.2,
                marker: {
                    enabled: true,
                    radius: 5,
                    symbol: 'circle'
                },
                dataLabels: {
                    enabled: true,
                    format: '{y:.1f}',
                    style: {
                        fontSize: '11px',
                        fontWeight: 'bold'
                    }
                }
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'SKM',
            data: dataBulanan.map(d => d.ikm)
        }]
    });

    // === CHART TRIWULAN ===
    Highcharts.chart('chart-triwulan', {
        chart: { 
            type: 'column',
            backgroundColor: 'transparent'
        },
        title: { 
            text: 'SKM Per Triwulan',
            style: {
                fontSize: '16px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Agregasi per 3 bulan'
        },
        xAxis: { 
            categories: dataTriwulan.map(d => d.label),
            crosshair: true
        },
        yAxis: { 
            title: { text: 'Nilai SKM' }, 
            max: 100, 
            min: 0,
            gridLineDashStyle: 'Dash'
        },
        tooltip: {
            valueSuffix: ' poin',
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            style: {
                color: '#fff'
            }
        },
        plotOptions: {
            column: {
                borderWidth: 0,
                borderRadius: 4,
                dataLabels: {
                    enabled: true,
                    format: '{y:.1f}',
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold',
                        textOutline: 'none'
                    }
                },
                colorByPoint: true,
                colors: ['#206bc4', '#2fb344', '#f59f00', '#d63939']
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'SKM',
            data: dataTriwulan.map(d => d.ikm)
        }]
    });

    // === CHART SEMESTER ===
    Highcharts.chart('chart-semester', {
        chart: { 
            type: 'column',
            backgroundColor: 'transparent'
        },
        title: { 
            text: 'SKM Per Semester',
            style: {
                fontSize: '16px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Agregasi per 6 bulan'
        },
        xAxis: { 
            categories: dataSemester.map(d => d.label),
            crosshair: true
        },
        yAxis: { 
            title: { text: 'Nilai SKM' }, 
            max: 100, 
            min: 0,
            gridLineDashStyle: 'Dash'
        },
        tooltip: {
            valueSuffix: ' poin',
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            style: {
                color: '#fff'
            }
        },
        plotOptions: {
            column: {
                borderWidth: 0,
                borderRadius: 4,
                dataLabels: {
                    enabled: true,
                    format: '{y:.1f}',
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold',
                        textOutline: 'none'
                    }
                },
                colorByPoint: true,
                colors: ['#4299e1', '#9333ea']
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'SKM',
            data: dataSemester.map(d => d.ikm)
        }]
    });

    // === CHART TAHUNAN ===
    Highcharts.chart('chart-tahunan', {
        chart: { 
            type: 'line',
            backgroundColor: 'transparent'
        },
        title: { 
            text: 'Tren SKM Per Tahun',
            style: {
                fontSize: '18px',
                fontWeight: '600'
            }
        },
        subtitle: {
            text: 'Perbandingan nilai SKM antar tahun'
        },
        xAxis: { 
            categories: dataTahunan.map(d => d.year),
            gridLineWidth: 1,
            gridLineDashStyle: 'Dash'
        },
        yAxis: { 
            title: { text: 'Nilai SKM' }, 
            max: 100, 
            min: 0,
            gridLineDashStyle: 'Dash'
        },
        tooltip: {
            shared: true,
            valueSuffix: ' poin',
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            style: {
                color: '#fff'
            }
        },
        plotOptions: {
            line: {
                marker: {
                    enabled: true,
                    radius: 6,
                    symbol: 'circle'
                },
                lineWidth: 3,
                dataLabels: {
                    enabled: true,
                    format: '{y:.1f}',
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }
                }
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            name: 'SKM',
            data: dataTahunan.map(d => d.ikm),
            color: '#2fb344'
        }]
    });
</script>
<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ auth()->user()->hasRole('super_admin') ? route('laporan.grafik') : route('instansi.laporan.grafik') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Laporan SKM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                      
                        <!-- Filter Tahun -->
                        <div class="col-md-6">
                            <label class="form-label">Tahun</label>
                            <select name="year" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($institutions->isNotEmpty())
                        <!-- Filter Instansi -->
                        <div class="col-md-12">
                            <label class="form-label">Instansi</label>
                            <select name="institution_id" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="kota_ikm">Nilai IKM Kota Magelang</option>
                                <option value="mpp_ikm">Nilai IKM MPP</option>
                                @foreach($institutions as $inst)
                                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection