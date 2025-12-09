@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-xl ">
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none mb-4" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <div class="page-pretitle">Visualisasi Data</div>
                <h2 class="page-title">{{ $title }}</h2>
              </div>
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div class="d-flex btn-list">
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#filterModal">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-filter">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M20 3h-16a1 1 0 0 0 -1 1v2.227l.008 .223a3 3 0 0 0 .772 1.795l4.22 4.641v8.114a1 1 0 0 0 1.316 .949l6 -2l.108 -.043a1 1 0 0 0 .576 -.906v-6.586l4.121 -4.12a3 3 0 0 0 .879 -2.123v-2.171a1 1 0 0 0 -1 -1z"/>
                  </svg>
                  Filter Laporan
                </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    Grafik SKM
                </h3>
            </div>
            <div class="card-body" id="tabel-ikm">
                <!-- Header Info -->
                <div class="text-center mb-4">
                    <h2 class="mb-2">Grafik Nilai Survei Kepuasan Masyarakat</h2>
                    <h3 class="text-primary">{{ $selectedInstitution }}</h3>
                    <div class="badge bg-azure-lt fs-4 mt-2">Tahun {{ $selectedYear }}</div>
                </div>
                
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
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div id="chart-triwulan" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grafik Semester -->
                    <div class="col-md-6">
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
                                <div id="chart-tahunan" style="height: 350px;"></div>
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
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{route('survey.grafik')}}"  method="GET">
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
                        @if($institutionsall->isNotEmpty())
                        <!-- Filter Instansi -->
                        <div class="col-md-12">
                            <label class="form-label">Instansi</label>
                            <select name="institution_id" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="kota_ikm">Nilai IKM Kota Magelang</option>
                                <option value="mpp_ikm">Nilai IKM MPP</option>
                                @foreach($institutionsall as $inst)
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