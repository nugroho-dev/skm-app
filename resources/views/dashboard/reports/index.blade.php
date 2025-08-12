@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body">
    <div class="container-xl ">
        <div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Responden & SKM</h3>
    </div>
    <div class="card-body">
        {{-- Form Filter --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="month" class="form-control">
                    <option value="">-- Pilih Bulan --</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="year" class="form-control">
                    <option value="">-- Pilih Tahun --</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>
                            {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="institution_id" class="form-control">
                    <option value="">-- Pilih Instansi --</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                            {{ $inst->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Filter</button>
            </div>
        </form>

        {{-- Tabel Laporan --}}
        <div class="table-responsive">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 7 + $unsurs->count() }}" class="text-center">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-end"> Jumlah Nilai Per Unsur </th>
                        @foreach($unsurs as $unsur)
                        <th class="text-center">{{ $totalPerUnsur[$unsur->id] ?? 0 }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th colspan="7" class="text-end">Nilai Rata Rata Per Unsur </th>
                        @foreach($unsurs as $unsur)
                        <th class="text-center">{{ number_format($averagePerUnsur[$unsur->id] ?? 0, 2) }}</th>
                        @endforeach
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
                    </tr>
                    <tr>
                        <th colspan="7" class="text-end">Nilai Rata Rata Tertimbang </th>
                        @foreach($unsurs as $unsur)
                        <th class="text-center">{{ number_format($weightedPerUnsur[$unsur->id] ?? 0, 3) }}</th>
                        @endforeach
                        <th class="text-center">{{ number_format($totalBobot, 3) }}</th>
                    </tr>
                    <tr>
                        <th colspan="16" class="text-end"> IKM Unit Pelayanan </th>
                        <th class="text-center">{{ number_format($nilaiSKM, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row mt-3">
            <div class="tabler-responsive col-md-6">
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
                                            (D) Kurang
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection