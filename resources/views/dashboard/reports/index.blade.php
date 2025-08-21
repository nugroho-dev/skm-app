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
                
                  <a href="{{ route('ikm.cetak.publikasi.pdf', request()->all()) }}" target="_blank" class="btn btn-danger">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg> Cetak Publikasi PDF</a>
                  <a href="{{ route('ikm.cetak.pdf', request()->all()) }}" target="_blank" class="btn btn-danger">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" /><path d="M17 18h2" /><path d="M20 15h-3v6" /><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" /></svg> Cetak PDF</a>
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
                <h3 class="card-title">Laporan Responden & SKM</h3>
            </div>
            <div class="card-body" id="tabel-ikm">
               
                <div class="text-center mb-4 ">
                    <h2 class="h2 text-uppercase m-0">
                        LAPORAN HASIL SURVEI KEPUASAN MASYARAKAT <br>
                     
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
                            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('Y') }}
                        @endif
                    </h3>
                    
                </div>
                {{-- Tabel Laporan --}}
                <div class="table-responsive" >
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu Survei</th>
                                <th>Umur</th>
                                <th>Pendidikan</th>
                                <th>Pekerjaan</th>
                                <th>Instansi</th>
                                <th>Jenis Layanan</th>
                                @foreach($unsurs as $unsur)
                                    <th class="text-center">U{{ $unsur->label_order }}</th>
                                @endforeach
                                <th>Nilai SKM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($respondents as $i => $res)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td> {{ $res->created_at->locale('id')->translatedFormat('d F Y H:i:s') }}</td>
                                    <td>{{ $res->age }}</td>
                                    <td>{{ $res->education->level ?? '-' }}</td>
                                    <td>{{ $res->occupation->type ?? '-' }}</td>
                                    <td>{{ $res->institution->name ?? '-' }}</td>
                                    <td>{{ $res->service->name ?? '-' }}</td>
                                    @foreach($unsurs as $unsur)
                                        <td class="text-center">
                                            {{ $respondentScores[$res->id][$unsur->id] ?? 0 }}
                                        </td>
                                    @endforeach
                                    <td></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 7 + $unsurs->count() }}" class="text-center">
                                        Tidak ada data
                                    </td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-end"> Jumlah Nilai Per Unsur </th>
                                @foreach($unsurs as $unsur)
                                <th class="text-center">{{ $totalPerUnsur[$unsur->id] ?? 0 }}</th>
                                @endforeach
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end">Nilai Rata Rata Per Unsur </th>
                                @foreach($unsurs as $unsur)
                                <th class="text-center">{{ number_format($averagePerUnsur[$unsur->id] ?? 0, 2) }}</th>
                                @endforeach
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end">Mutu Pelayanan </th>
                                @foreach($unsurs as $unsur)
                                <th class="text-center">
                                    @if(isset($averagePerUnsur[$unsur->id]))
                                                @if($averagePerUnsur[$unsur->id] >= 3.53)
                                                    A
                                                @elseif($averagePerUnsur[$unsur->id] >= 3.06)
                                                    B
                                                @elseif($averagePerUnsur[$unsur->id] >= 2.60)
                                                    C
                                                @else
                                                    D
                                                @endif
                                            @else
                                                -
                                            @endif
                                </th>
                                @endforeach
                            
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end">Nilai Rata Rata Tertimbang </th>
                                @foreach($unsurs as $unsur)
                                <th class="text-center">{{ number_format($weightedPerUnsur[$unsur->id] ?? 0, 3) }}</th>
                                @endforeach
                                <th class="text-center">*) {{ number_format($totalBobot, 3) }}</th>
                            </tr>
                            <tr>
                                <th colspan="16" class="text-end"> IKM Unit Pelayanan </th>
                                <th class="text-center">**) {{ number_format($nilaiSKM, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="16" class="text-end">Kategori Mutu Layanan</th>
                                <th class="text-center text-nowrap">{{ $kategoriMutu[0] }} <br> ({{ $kategoriMutu[1] }})</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="tabler-responsive col-md-6 col-sm-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Unsur</th>
                                    <th>Nilai Rata Rata</th>
                                    <th>Mutu Pelayanan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unsurs as $i => $unsur)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $unsur->name }}</td>
                                        <td class="text-center">{{ number_format($averagePerUnsur[$unsur->id] ?? 0, 2) }}</td>
                                        <td class="text-center">
                                            @if(isset($averagePerUnsur[$unsur->id]))
                                                @if($averagePerUnsur[$unsur->id] >= 3.53)
                                                    (A) Sangat Baik
                                                @elseif($averagePerUnsur[$unsur->id] >= 3.06)
                                                    (B) Baik
                                                @elseif($averagePerUnsur[$unsur->id] >= 2.60)
                                                    (C) Kurang Baik
                                                @else
                                                    (D) Tidak Baik
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="tabler-responsive col-md-12 col-sm-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Keterangan</th>
                                        <th>:</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>U1 - U9</td>
                                        <td>:</td>
                                        <td>Unsur - Unsur Pelayanan</td>
                                    </tr>
                                    <tr>
                                        <td>NRR</td>
                                        <td>:</td>
                                        <td>Nilai Rata Rata</td>
                                    </tr>
                                    <tr>
                                        <td>IKM</td>
                                        <td>:</td>
                                        <td>Indeks Kepuasan Masyarakat</td>
                                    </tr>
                                    <tr>
                                        <td>_*)</td>
                                        <td>:</td>
                                        <td>Jumlah NRR IKM Tertimbang</td>
                                    </tr>
                                    <tr>
                                        <td>_**)</td>
                                        <td>:</td>
                                        <td>Jumlah NRR Tertimbang x 25</td>
                                    </tr>
                                    <tr>
                                        <td>NRR Per Unsur</td>
                                        <td>:</td>
                                        <td>Jumlah Nilai Per Unsur dibagi Jumlah Kuesioner Yang Terisi</td>
                                    </tr>
                                    <tr>
                                        <td>NRR Tertimbang</td>
                                        <td>:</td>
                                        <td>NRR per unsur x 0,111 per unsur</td>
                                    </tr>
                            </table>
                        </div>
                        <div class="tabler-responsive col-md-12 col-sm-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>IKM UNIT PELAYANAN</th>
                                        <th>:</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Mutu Pelayanan</td>
                                        <td>:</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>A (Sangat Baik)</td>
                                        <td>:</td>
                                        <td>88.31 - 100.00</td>
                                    </tr>
                                    <tr>
                                        <td>B (Baik)</td>
                                        <td>:</td>
                                        <td>76.61 - 88.30</td>
                                    </tr>
                                    <tr>
                                        <td>C (Kurang Baik)</td>
                                        <td>:</td>
                                        <td>65.00 - 76.60</td>
                                    </tr>
                                    <tr>
                                        <td>D (Tidak Baik)</td>
                                        <td>:</td>
                                        <td>25.00 - 64.99</td>
                                    </tr>
                                    
                            </table>
                        </div>
                    </div>
                    
                    
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
            <form action="{{ route('reports.index') }}" method="GET">
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