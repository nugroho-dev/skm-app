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
                  
                 <button onclick="printTable('tabel-ikm')" class="btn btn-dark ">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg></i> Print Browser
                 </button>
                
                 
                   {{-- Form Filter --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-filter"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 3h-16a1 1 0 0 0 -1 1v2.227l.008 .223a3 3 0 0 0 .772 1.795l4.22 4.641v8.114a1 1 0 0 0 1.316 .949l6 -2l.108 -.043a1 1 0 0 0 .576 -.906v-6.586l4.121 -4.12a3 3 0 0 0 .879 -2.123v-2.171a1 1 0 0 0 -1 -1z" /></svg> Filter Laporan</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
        {{-- CSS untuk print --}}
<style>

</style>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Rekap Per Jenis Layanan</h3>
            </div>
            <div class="card-body" id="tabel-ikm">
               
                <div class="text-center mb-4 ">
                    <h2 class="h2 text-uppercase m-0">
                        LAPORAN HASIL SURVEI KEPUASAN MASYARAKAT PER JENIS LAYANAN <br>
                     
                        {{ $selectedInstitution ? $selectedInstitution : 'Semua Instansi' }}
                    </h2>
                    <h3 class="h3 text-uppercase m-0">
                        Periode:
                        @if(request('quarter'))
                            Triwulan {{ request('quarter') }} Tahun {{ request('year') }}
                        @elseif(request('semester'))
                            Semester {{ request('semester') }} tahun {{ request('year') }}
                        @elseif(request('month') && request('year'))
                            {{ \Carbon\Carbon::createFromDate(request('year'), request('month'), 1)->locale('id')->translatedFormat('F Y') }}
                        @elseif(request('start_date') && request('end_date'))
                            {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse(request('end_date'))->locale('id')->translatedFormat('d F Y') }}
                        @else
                            Tahun {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('Y') }}
                        @endif
                    </h3>
                </div>
                {{-- Tabel Laporan --}}
                <div class="table-responsive" >
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Jenis Layanan</th>
                                <th>Jumlah Responden</th>
                                <th>Nilai IKM</th>
                                <th>Mutu Layanan</th>
                            </tr>
                        </thead>
                        <tbody>
                             @forelse($reportPerService as $report)
                                <tr>
                                   <td><a href="{{ auth()->user()->hasRole('super_admin') ? route('laporan.service', array_merge(['service_id' => $report['service']->id], request()->all())) : route('instansi.laporan.service', array_merge(['service_id' => $report['service']->id], request()->all())) }}">{{ $report['service']->name }}</a></td>
                                   <td>{{ $report['respondents_count'] }}</td>
                                   <td>{{ number_format($report['nilaiSKM'],2) }}</td>
                                   <td>{{ $report['kategoriMutu'][0] }} ({{ $report['kategoriMutu'][1] }})</td>
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Tidak ada data
                                    </td>
                                </tr>
                             @endforelse
                        </tbody>
                      
                    </table>
                </div>
               
            </div>
        </div>
    </div>
</div>
{{-- JavaScript Print Khusus Tabel --}}
<script>
function printTable(tableId) {
    // Ambil elemen tabel
    let table = document.getElementById(tableId).outerHTML;

    // Buat jendela print baru
    let win = window.open('', '', 'width=900,height=700');
    win.document.write(`
        <html>
        <head>
            <title>Print Tabel</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                table, th, td { border: 1px solid black; }
                th, td { padding: 6px; text-align: left; }
                @page { size: landscape; }
            </style>
        </head>
        <body>
            ${table}
        </body>
        </html>
    `);

    // Cetak dan tutup
    win.document.close();
    win.print();
    win.close();
}
</script>
<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ auth()->user()->hasRole('super_admin') ? route('reports.per_layanan') : route('instansi.reports.per_layanan') }}" method="GET">
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
                    <a href="{{ route('reports.per_layanan') }}" class="btn btn-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
