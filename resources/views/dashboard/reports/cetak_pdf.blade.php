<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        table, th, td { border: 1px solid black; padding: 4px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    
    <div style="text-align: center; text-transform: uppercase;">
        <h3 style="margin: 0px;">
            LAPORAN HASIL SURVEI KEPUASAN MASYARAKAT <br>     
            {{ $selectedInstitution ? $selectedInstitution : 'Semua Instansi' }}
        </h3>
        <h4 style="margin: 0px;">
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
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}
            @endif
        </h4>         
    </div>
    <br>
    <table>
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
                     <td style="text-align: center;">
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
                <th style="white-space:nowrap">{{ $kategoriMutu[0] }} <br> ({{ $kategoriMutu[1] }})</th>
            </tr>
        </tfoot>
    </table>
    <table style="border-style: hidden; vertical-align: top; ">
        <tr>
            <td style="border-style: hidden; vertical-align: top;  padding:0px; margin:0px;">
                <table style="margin:0px;">
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
                            <td style="text-align: center;">{{ number_format($averagePerUnsur[$unsur->id] ?? 0, 2) }}</td>
                            <td style="text-align: center;">
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
            </td>
            <td style="border-style: hidden; vertical-align: top; padding:0px; margin:0px; ">
                <table style="margin:0px;">
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
                <table style="margin:0px;">
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
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>