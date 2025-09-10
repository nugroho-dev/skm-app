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
            LAPORAN SARAN DAN MASUKAN HASIL SURVEI KEPUASAN MASYARAKAT <br>     
            {{ $selectedInstitution ? $selectedInstitution : 'Semua Instansi' }}
        </h3>
        <h4 style="margin: 0px;">
            
            
            @if(request('start_date') && request('end_date'))
            Periode:
            {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}
                s/d
                {{ \Carbon\Carbon::parse(request('end_date'))->locale('id')->translatedFormat('d F Y') }}
            
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
                <th>Saran </th>
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
                <td>{{ $res->suggestion }}</td>
             </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        Tidak ada data
                    </td>
                    <td></td>
                </tr>
            @endforelse
        </tbody>
        
    </table>
    
</body>
</html>