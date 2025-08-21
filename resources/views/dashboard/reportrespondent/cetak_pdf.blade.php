<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Responden</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }
        h2 {
            margin: 1px 0;
            text-align: center;
            color: #1a237e;
        }
        .box-info {
            border: 1px solid #90caf9;
            background: #e3f2fd;
            padding: 6px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        th, td {
           
            padding: 4px;
        }
        th {
            background: #1976d2;
            color: #fff;
            font-size: 10px;
        }
        td {
            font-size: 10px;
            text-align: center;
        }
        tr:nth-child(even) td { background: #f9f9f9; }
    </style>
</head>
<body>
    <h2>Laporan Jumlah Responden</h2>
<table>
        <tr>
            <td valign="top" width="33%">
                 <div class="box-info">
                    <p><b>Total Responden:</b><br> {{ $totalRespondents }}</p>
                </div>
            </td>
            <td valign="top" width="33%">
                 <div class="box-info">
                    <p><b>Instansi:</b><br> {{ $selectedInstitution ?? 'Semua Instansi' }}</p>
                 </div>
            </td>
            <td valign="top" width="33%">
                <div class="box-info">
                    <p><b>Periode:</b> <br>
                        @if(request('start_date') && request('end_date'))
                            {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') }}
                        @elseif(request('month') && request('year'))
                            {{ $months[request('month')] }} {{ request('year') }}
                        @elseif(request('quarter') && request('year'))
                            {{ $quarters[request('quarter')] }} {{ request('year') }}
                        @elseif(request('semester') && request('year'))
                            {{ $semesters[request('semester')] }} {{ request('year') }}
                        @elseif(request('year'))
                            Tahun {{ request('year') }}
                        @else
                            {{ now()->translatedFormat('F Y') }}
                        @endif
                    </p>
                 </div>
            </td>
        </tr>
</table>
   

    <!-- Tabel induk dengan 3 kolom -->
    <table>
        <tr>
            <td valign="top" width="33%">
                <h3 style="text-align:center;">Per Jenis Kelamin</h3>
                <table >
                    <thead>
                        <tr style="border: 1px solid #000;">
                            <th style="border: 1px solid #000;">Jenis Kelamin</th>
                            <th style="border: 1px solid #000;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($genderCounts as $gender => $count)
                            <tr style="border: 1px solid #000;">
                                <td style="border: 1px solid #000;">{{ $gender === 'L' ? 'Laki-Laki' : ($gender === 'P' ? 'Perempuan' : '-') }}</td>
                                <td style="border: 1px solid #000;">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>

            <td valign="top" width="33%">
                <h3 style="text-align:center;">Per Pendidikan</h3>
                <table>
                    <thead>
                        <tr style="border: 1px solid #000;">
                            <th style="border: 1px solid #000;">Pendidikan</th>
                            <th style="border: 1px solid #000;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($educationCounts as $eduId => $count)
                            <tr style="border: 1px solid #000;">
                                <td style="border: 1px solid #000;">{{ $educationNames[$eduId] ?? '-' }}</td>
                                <td style="border: 1px solid #000;">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>

            <td valign="top" width="33%">
                <h3 style="text-align:center;">Per Pekerjaan</h3>
                <table>
                    <thead>
                        <tr style="border: 1px solid #000;">
                            <th style="border: 1px solid #000;">Pekerjaan</th>
                            <th style="border: 1px solid #000;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($occupationCounts as $occId => $count)
                            <tr style="border: 1px solid #000;">
                                <td style="border: 1px solid #000;">{{ $occupationNames[$occId] ?? '-' }}</td>
                                <td style="border: 1px solid #000;">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>