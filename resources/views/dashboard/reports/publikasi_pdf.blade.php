@php
  if(request('quarter')) {
      $periodeText = "Triwulan " . request('quarter') . " Tahun " . request('year');
  } elseif(request('semester')) {
      $periodeText = "Semester " . request('semester') . " Tahun " . request('year');
  } elseif(request('month') && request('year')) {
      $periodeText = \Carbon\Carbon::createFromDate(request('year'), request('month'), 1)
          ->locale('id')->translatedFormat('F Y');
  } elseif(request('start_date') && request('end_date')) {
      $periodeText = \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') .
          " s/d " .
          \Carbon\Carbon::parse(request('end_date'))->locale('id')->translatedFormat('d F Y');
  } else {
      $periodeText = \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y');
  }
@endphp

<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; color: #1f2937; }
  .container { width: auto; margin: auto; border: 1px solid #e5e7eb; padding: 10mm; }

  .header { text-align: center; margin-bottom: 4mm; }
  .header .title { font-size: 14pt; font-weight: bold; color: #FF9B2F; text-transform: uppercase; }
  .header .subtitle { font-size: 16pt; font-weight: bold; margin: 0mm 0; }
  .header .period { font-size: 11pt; color: #6b7280; }

  .main-table { width: 100%; border-collapse: collapse; }
  .main-table td { vertical-align: top; padding: 0; }

  .score-ring {
    width: 65mm; height: 65mm; border-radius: 50%;
    border: 5mm solid #78C841; display: flex; align-items: center; justify-content: center;
    margin: auto;
  }
  .score-core { text-align: center; }
  .score-value { font-size: 70pt; font-weight: bold; margin-top: 30px; margin-bottom: 0px;  }
  .score-label { font-size: 13pt; color: #FB4141; text-transform: uppercase; margin-top:0px; }

  .stats-table { width: 100%; border-collapse: collapse; margin-top: 4mm; }
  .stats-table th, .stats-table td {
    border: 0.3mm solid #e5e7eb; padding: 0mm 4mm; font-size: 10.5pt;
  }
  .stats-table th { background: #B4E50D; text-align: left; font-weight: bold; }

.stats-table-edu { width: 100%; border-collapse: collapse; margin-top: 4mm; }
  .stats-table-edu th, .stats-table-edu td {
    border: 0.3mm solid #e5e7eb; padding: 0mm 4mm; font-size: 10pt;
  }
  .stats-table-edu th { background: #B4E50D; text-align: left; font-weight: bold; }

.stats-table-occu { width: 100%; border-collapse: collapse; margin-top: 4mm; }
  .stats-table-occu th, .stats-table-occu td {
    border: 0.3mm solid #e5e7eb; padding: 0mm 4mm; font-size: 10pt;
  }
  .stats-table-occu th { background: #B4E50D; text-align: left; font-weight: bold; }

  .footer-note { text-align: center; font-size: 10pt; color: #6b7280; margin-top: 8mm; }
  .thanks { border: 0.3mm dashed #e5e7eb; padding: 3mm; text-align: center; font-weight: bold; font-size: 10pt; margin-top: 2mm; }
</style>

<div class="container">
  <div class="header">
    <div class="title">Indeks Kepuasan Masyarakat (IKM)</div>
    <div class="subtitle">{{ $selectedInstitution ? Str::upper($selectedInstitution) : 'SEMUA INSTANSI' }}</div>
    <div class="period">Periode: {{ $periodeText }}</div>
  </div>

  <table class="main-table">
    <tr>
      <td style="width: 60mm; text-align: center;">
        <div class="score-ring">
          <div class="score-core">
            <div class="score-value">{{ number_format($nilaiSKM, 1) }}</div>
            <div class="score-label"><strong>{{ $kategoriMutu[0] }} ({{ $kategoriMutu[1] }}) </strong></div>
          </div>
        </div>
      </td>
      
    </tr>
  </table>
  
    <table class="stats-table">
          <tr><th colspan="2">Ringkasan</th></tr>
          <tr><td>Total Responden</td><td><strong>{{ $totalRespondents }} Orang</strong></td></tr>
          <tr><td>Periode</td><td>{{ $periodeText }}</td></tr>
        </table>

    <table class="stats-table">
          <tr>
            <th colspan="2">Jenis Kelamin Responden</th>
          </tr>
          <tr>
            <td>♂ Laki-laki</td>
            <td><strong>{{ $genderCounts['L'] ?? 0 }} Orang</strong></td>
        </tr>
          <tr>
            <td>♀ Perempuan</td>
            <td><strong>{{ $genderCounts['P'] ?? 0 }} Orang</strong>
            </td>
          </tr>
        </table>
  
  <table style="width: 100%;">
    <tr>
      <td style="vertical-align: top;">
        <table class="stats-table-edu">
    <thead>
      <tr>
        <th>Pendidikan Responden</th>
        <th style="width:18mm">Jumlah</th>
      </tr>
    </thead>
    <tbody>
      @forelse($educationCounts as $id => $count)
        <tr>
          <td>{{ $educationNames[$id] ?? 'Tidak Diketahui' }}</td>
          <td><strong>{{ $count }} </strong></td>
        </tr>
      @empty
        <tr><td colspan="2" style="color:#6b7280">Belum ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
      </td>
      <td style="vertical-align: top;">
        <table class="stats-table-occu">
    <thead>
      <tr>
        <th>Pekerjaan Responden</th>
        <th style="width:18mm">Jumlah</th>
      </tr>
    </thead>
    <tbody>
      @forelse($occupationCounts as $id => $count)
        <tr>
          <td>{{ $occupationNames[$id] ?? 'Tidak Diketahui' }}</td>
          <td><strong>{{ $count }} </strong></td>
        </tr>
      @empty
        <tr><td colspan="2" style="color:#6b7280">Belum ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
      </td>
    </tr>
  </table>
  

  <div class="footer-note">Terima kasih atas partisipasi Anda.</div>
  <div class="thanks">
    Penilaian Anda sangat berarti untuk mendorong perbaikan berkelanjutan serta peningkatan kualitas pelayanan bagi masyarakat.
  </div>
</div>
