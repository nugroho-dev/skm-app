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
       $periodeText = "Tahun ". \Carbon\Carbon::now()->locale('id')->translatedFormat('Y');
  }
@endphp

<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #10243c; }
  .container { width: auto; margin: auto; border: 1px solid #d7e1ea; border-radius: 3mm; padding: 8mm 9mm; }

  .header { text-align: center; margin-bottom: 5mm; }
  .header .title { font-size: 13pt; font-weight: bold; color: #0f5f7a; text-transform: uppercase; letter-spacing: 0.4mm; }
  .header .subtitle { font-size: 17pt; font-weight: bold; margin: 1.2mm 0 0.7mm; }
  .header .period { font-size: 10.5pt; color: #5b6b7f; }

  .score-wrap {
    width: 68mm;
    height: 68mm;
    margin: 0 auto 2mm;
    border-radius: 50%;
    border: 3mm solid #0f9d74;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f6fbff;
  }
  .score-core { text-align: center; }
  .score-value { font-size: 42pt; font-weight: bold; line-height: 1; margin: 0; }
  .score-label { font-size: 10pt; color: #0f5f7a; text-transform: uppercase; margin-top: 1.2mm; font-weight: bold; }

  .section-table { width: 100%; border-collapse: collapse; margin-top: 4mm; }
  .section-table th, .section-table td { border: 0.25mm solid #d8e0e8; padding: 1.8mm 2.5mm; }
  .section-table th { background: #e9f3fb; text-align: left; font-size: 10pt; }
  .section-table td { font-size: 10pt; }

  .two-col { width: 100%; border-collapse: collapse; margin-top: 3mm; }
  .two-col td { width: 50%; vertical-align: top; }

  .footer-note { text-align: center; font-size: 9.8pt; color: #5b6b7f; margin-top: 7mm; }
  .thanks { border: 0.25mm dashed #b8c8d8; padding: 2.8mm; text-align: center; font-weight: bold; font-size: 9.8pt; margin-top: 2.2mm; color: #0f5f7a; }
</style>

<div class="container">
  <div class="header">
    <div class="title">Indeks Kepuasan Masyarakat (IKM)</div>
    <div class="subtitle">{{ $selectedInstitution ? Str::upper($selectedInstitution) : 'SEMUA INSTANSI' }}</div>
    <div class="period">Periode: {{ $periodeText }}</div>
  </div>

  <div class="score-wrap">
      <div class="score-core">
        <div class="score-value">{{ number_format($nilaiSKM, 1) }}</div>
        <div class="score-label">{{ $kategoriMutu[0] }} ({{ $kategoriMutu[1] }})</div>
      </div>
  </div>
  
    <table class="section-table">
          <tr><th colspan="2">Ringkasan</th></tr>
          <tr><td>Total Responden</td><td><strong>{{ $totalRespondents }} Orang</strong></td></tr>
          <tr><td>Periode</td><td>{{ $periodeText }}</td></tr>
        </table>

    <table class="section-table">
          <tr>
            <th colspan="2">Jenis Kelamin Responden</th>
          </tr>
          <tr>
            <td>Laki-laki</td>
            <td><strong>{{ $genderCounts['L'] ?? 0 }} Orang</strong></td>
        </tr>
          <tr>
            <td>Perempuan</td>
            <td><strong>{{ $genderCounts['P'] ?? 0 }} Orang</strong></td>
          </tr>
        </table>
  
  <table class="two-col">
    <tr>
      <td style="padding-right: 2mm;">
        <table class="section-table">
          <thead>
            <tr>
              <th>Pendidikan Responden</th>
              <th style="width:20mm">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            @forelse($educationCounts as $id => $count)
              <tr>
                <td>{{ $educationNames[$id] ?? 'Tidak Diketahui' }}</td>
                <td><strong>{{ $count }}</strong></td>
              </tr>
            @empty
              <tr><td colspan="2" style="color:#6b7280">Belum ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </td>
      <td style="padding-left: 2mm;">
        <table class="section-table">
          <thead>
            <tr>
              <th>Pekerjaan Responden</th>
              <th style="width:20mm">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            @forelse($occupationCounts as $id => $count)
              <tr>
                <td>{{ $occupationNames[$id] ?? 'Tidak Diketahui' }}</td>
                <td><strong>{{ $count }}</strong></td>
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
