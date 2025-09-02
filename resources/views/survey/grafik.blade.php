@extends('layouts.app')

@section('content')
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
            <div class="card-header">
                <h3 class="card-title">Grafik SKM</h3>
            </div>
            <div class="card-body" id="tabel-ikm">
                 <div class="row text-center">
                    <h2>Grafik Nilai Survei Kepuasan Masyarakat <br> {{ $selectedInstitution }}</h2>
                    <h3>Tahun {{ $selectedYear }}</h3>
                    <!-- Grafik Bulanan -->
                    <div class="col-md-12 col-sm-12">
                        <div id="chart-bulanan" class="w-full h-80"></div>
                    </div>
                    <!-- Grafik Triwulan -->
                    <div class="col-md-6 col-sm-12">
                        <div id="chart-triwulan" class="w-full h-80"></div>
                    </div>
                    <!-- Grafik Semester -->
                    <div class="col-md-6 col-sm-12">
                        <div id="chart-semester" class="w-full h-80"></div>
                    </div>
                    <!-- Grafik Tahunan -->
                    <div class="col-md-12 col-sm-12">
                        <div id="chart-tahunan" class="w-full h-80"></div>
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

    // === CHART BULANAN ===
    Highcharts.chart('chart-bulanan', {
        chart: { type: 'line' },
        title: { text: 'IKM Per Bulan' },
        xAxis: { categories: dataBulanan.map(d => d.label) },
        yAxis: { title: { text: 'Nilai IKM' }, max: 100, min: 0 },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: 'IKM',
            data: dataBulanan.map(d => d.ikm)
        }]
    });

    // === CHART TRIWULAN ===
    Highcharts.chart('chart-triwulan', {
        chart: { type: 'column' },
        title: { text: 'IKM Per Triwulan' },
        xAxis: { categories: dataTriwulan.map(d => d.label) },
        yAxis: { title: { text: 'Nilai IKM' }, max: 100, min: 0 },
        
       plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
            }
        }
    },
        series: [{
            name: 'IKM',
            data: dataTriwulan.map(d => d.ikm)
        }]
    });

    // === CHART SEMESTER ===
    Highcharts.chart('chart-semester', {
        chart: { type: 'column' },
        title: { text: 'IKM Per Semester' },
        xAxis: { categories: dataSemester.map(d => d.label) },
        yAxis: { title: { text: 'Nilai IKM' }, max: 100, min: 0 },
        plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
            }
        }
    },
        series: [{
            name: 'IKM',
            data: dataSemester.map(d => d.ikm)
        }]
    });

    // === CHART TAHUNAN ===
    Highcharts.chart('chart-tahunan', {
        chart: { type: 'line' },
        title: { text: 'IKM Per Tahun' },
        xAxis: { categories: dataTahunan.map(d => d.year) },
        yAxis: { title: { text: 'Nilai IKM' }, max: 100, min: 0 },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: 'IKM',
            data: dataTahunan.map(d => d.ikm)
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
<div class="modal fade" id="filterModal2" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{  route('survey.publikasi') }}" target="_blank" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Laporan SKM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Filter Rentang Tanggal -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>

                        <!-- Filter Triwulan -->
                        <div class="col-md-6">
                            <label class="form-label">Triwulan</label>
                            <select name="quarter" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($quarters as $q => $label)
                                    <option value="{{ $q }}" {{ request('quarter') == $q ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Semester -->
                        <div class="col-md-6">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($semesters as $s => $label)
                                    <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Bulan -->
                        <div class="col-md-6">
                            <label class="form-label">Bulan</label>
                            <select name="month" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

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