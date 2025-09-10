<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Response as Respondent;
use App\Models\Institution;
use App\Models\Occupation;
use App\Models\Unsur;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class SurveyPublicController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Laporan SKM';
        $unsurs = Unsur::orderBy('label_order')->get();
        $unsurCount = max(1, $unsurs->count());

    // === Pilih tahun (default tahun ini) ===
        $selectedYear = (int) ($request->input('year') ?: now()->year);

    // === BASE QUERY (akan dipakai ulang untuk filter instansi) ===
        $baseQuery = Respondent::query();

    // === FILTER INSTANSI ===
     
        if ($request->filled('institution_id')) {
            if ($request->institution_id === 'mpp_ikm') {
                $mppIds = Institution::whereHas('mpp', function ($q) {
                    $q->where('slug', 'mpp-kota-magelang');
                })->pluck('id');
                $baseQuery->whereIn('institution_id', $mppIds);
                $selectedInstitution = 'MPP Kota Magelang';
            } elseif ($request->institution_id === 'kota_ikm') {
                $kotaIds = Institution::whereHas('group', function ($q) {
                    $q->where('slug', 'kota-magelang');
                })->pluck('id');
                $baseQuery->whereIn('institution_id', $kotaIds);
                $selectedInstitution = 'Kota Magelang';
            } else {
                $baseQuery->where('institution_id', $request->institution_id);
                $selectedInstitution = Institution::find($request->institution_id)?->name;
            }
        } else {
            $selectedInstitution = null;
        }
        

    
    // Ambil responden per tahun & bulan
$respondents = (clone $baseQuery)
    ->with(['answers.question.unsur'])
    ->orderBy('created_at')
    ->get()
    ->groupBy(fn($r) => (int) $r->created_at->format('Y'));

$ikmTahunan = collect();
$ikmBulanan = collect();
$ikmTriwulan = collect();
$ikmSemester = collect();

foreach ($respondents as $year => $respTahun) {
    // === Bulanan ===
    $respBulanan = $respTahun->groupBy(fn($r) => (int) $r->created_at->format('n'));
    $ikmBulanan[$year] = $respBulanan->map(function ($respBulan, $month) use ($unsurs, $year) {
        $totalBobot = 0;
        $unsurCount = max(1, $unsurs->count());

        foreach ($unsurs as $unsur) {
            $scoresPerRespondent = $respBulan->map(function ($r) use ($unsur) {
                $answers = $r->answers->filter(fn($a) => $a->question && $a->question->unsur_id === $unsur->id);
                return $answers->count() > 0 ? round($answers->avg('score')) : 0;
            });
            $avg = $scoresPerRespondent->avg() ?: 0;
            $totalBobot += $avg * 0.11;// (1 / $unsurCount);
        }

        return [
            'year'  => $year,
            'month' => $month,
            'label' => "Bulan " . \Carbon\Carbon::create()->month($month)->translatedFormat('F') . " $year",
            'ikm'   => round($totalBobot * 25, 2),
        ];
    })->values()->toArray();

    // === Triwulan ===
    $ikmTriwulan[$year] = collect(range(1, 4))->map(function ($q) use ($respBulanan, $unsurs, $year) {
        $startMonth = ($q - 1) * 3 + 1;
        $endMonth   = $startMonth + 2;

        $respTriwulan = collect();
        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $respTriwulan = $respTriwulan->merge($respBulanan->get($m, collect()));
        }

        $totalBobot = 0;
        $unsurCount = max(1, $unsurs->count());

        foreach ($unsurs as $unsur) {
            $scoresPerRespondent = $respTriwulan->map(function ($r) use ($unsur) {
                $answers = $r->answers->filter(fn($a) => $a->question && $a->question->unsur_id === $unsur->id);
                return $answers->count() > 0 ? round($answers->avg('score')) : 0;
            });
            $avg = $scoresPerRespondent->avg() ?: 0;
            $totalBobot += $avg * 0.11;// (1 / $unsurCount);
        }

        return [
            'year'    => $year,
            'quarter' => $q,
            'label'   => "Triwulan $q $year",
            'ikm'     => round($totalBobot * 25, 2),
        ];
    })->values()->toArray();

    // === Semester ===
    $ikmSemester[$year] = collect([1, 2])->map(function ($s) use ($respBulanan, $unsurs, $year) {
        $startMonth = $s === 1 ? 1 : 7;
        $endMonth   = $s === 1 ? 6 : 12;

        $respSemester = collect();
        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $respSemester = $respSemester->merge($respBulanan->get($m, collect()));
        }

        $totalBobot = 0;
        $unsurCount = max(1, $unsurs->count());

        foreach ($unsurs as $unsur) {
            $scoresPerRespondent = $respSemester->map(function ($r) use ($unsur) {
                $answers = $r->answers->filter(fn($a) => $a->question && $a->question->unsur_id === $unsur->id);
                return $answers->count() > 0 ? round($answers->avg('score')) : 0;
            });
            $avg = $scoresPerRespondent->avg() ?: 0;
            $totalBobot += $avg * 0.11;//(1 / $unsurCount);
        }

        return [
            'year'     => $year,
            'semester' => $s,
            'label'    => "Semester $s $year",
            'ikm'      => round($totalBobot * 25, 2),
        ];
    })->values()->toArray();

    // === Tahunan ===
    $totalBobot = 0;
    $unsurCount = max(1, $unsurs->count());

    foreach ($unsurs as $unsur) {
        $scoresPerRespondent = $respTahun->map(function ($r) use ($unsur) {
            $answers = $r->answers->filter(fn($a) => $a->question && $a->question->unsur_id === $unsur->id);
            return $answers->count() > 0 ? round($answers->avg('score')) : 0;
        });
        $avg = $scoresPerRespondent->avg() ?: 0;
        $totalBobot += $avg * 0.11;//(1 / $unsurCount);
    }

    $ikmTahunan->push([
        'year'  => $year,
        'label' => "Tahun $year",
        'ikm'   => round($totalBobot * 25, 2),
    ]);
}
// === Flatten biar bisa langsung map() di JS ===
    $ikmBulanan  = collect($ikmBulanan)->flatten(1)->values()->toArray();
    $ikmTriwulan = collect($ikmTriwulan)->flatten(1)->values()->toArray();
    $ikmSemester = collect($ikmSemester)->flatten(1)->values()->toArray();
    $ikmTahunan  = $ikmTahunan->values()->toArray();

    // === List tahun utk dropdown ===
    $years = (clone $baseQuery)
        ->selectRaw('YEAR(created_at) as y')
        ->distinct()
        ->orderBy('y')
        ->pluck('y');
    // Data untuk dropdown filter
    $quarters = [
            1 => 'Triwulan 1 (Jan-Mar)',
            2 => 'Triwulan 2 (Apr-Jun)',
            3 => 'Triwulan 3 (Jul-Sep)',
            4 => 'Triwulan 4 (Okt-Des)'
        ];
        $semesters = [
            1 => 'Semester 1 (Jan-Jun)',
            2 => 'Semester 2 (Jul-Des)'
        ];
     
      $months = collect(range(1, 12))->mapWithKeys(function ($m) {
            return [$m => Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F')];
        });
        

    $institutionsall = Institution::with(['mpp', 'group'])
        ->orderBy('name')
        ->get();

        return view('survey.grafik', compact(
        'title','ikmBulanan','ikmTriwulan','ikmSemester','ikmTahunan', 'selectedYear','years','selectedInstitution','quarters','semesters','months','institutionsall'));
    }
    public function welcome(Request $request)
    {
        $title = 'Sistem Informasi Survei Kepuasan Masyarakat (SiSUKMA)';
        

    // === BASE QUERY (akan dipakai ulang untuk filter instansi) ===
        $baseQuery = Respondent::query();

    $quarters = [
            1 => 'Triwulan 1 (Jan-Mar)',
            2 => 'Triwulan 2 (Apr-Jun)',
            3 => 'Triwulan 3 (Jul-Sep)',
            4 => 'Triwulan 4 (Okt-Des)'
        ];
        $semesters = [
            1 => 'Semester 1 (Jan-Jun)',
            2 => 'Semester 2 (Jul-Des)'
        ];
     
      $months = collect(range(1, 12))->mapWithKeys(function ($m) {
            return [$m => Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F')];
        });

    // === List tahun utk dropdown ===
    $years = (clone $baseQuery)
        ->selectRaw('YEAR(created_at) as y')
        ->distinct()
        ->orderBy('y')
        ->pluck('y');
    // Data untuk dropdown filter
    
    $institutions = Institution::with(['mpp', 'group'])
        ->orderBy('name')
        ->get();

        return view('welcome', compact(
        'title','years','institutions','quarters','semesters','months'
        ));
    }
    /**
     * Supaya tidak duplikasi logika, kita ambil data dari satu fungsi saja
     */
    private function getCetakData(Request $request)
    {
        $title = 'Laporan SKM';
        $subtitle = 'Laporan SKM Berdasarkan Responden dan Nilai SKM';

        $unsurs = Unsur::orderBy('label_order')->get();

        $query = Respondent::with([
            'service',
            'answers.question.unsur',
            'institution',
            'institution.mpp', 'institution.group'
        ]);
        $currentMonth = now()->month;
        $currentYear = now()->year;
        // === LOGIKA PRIORITAS FILTER ===
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // 1. Rentang tanggal
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);

        } elseif ($request->filled('quarter') && $request->filled('year')) {
            // 2. Triwulan
            $startMonth = ($request->quarter - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $query->whereYear('created_at', $request->year)
                ->whereMonth('created_at', '>=', $startMonth)
                ->whereMonth('created_at', '<=', $endMonth);

        } elseif ($request->filled('semester') && $request->filled('year')) {
            // 3. Semester
            if ($request->semester == 1) {
                $query->whereYear('created_at', $request->year)
                    ->whereBetween('created_at', [
                        Carbon::createFromDate($request->year, 1, 1),
                        Carbon::createFromDate($request->year, 6, 30)
                    ]);
            } elseif ($request->semester == 2) {
                $query->whereYear('created_at', $request->year)
                    ->whereBetween('created_at', [
                        Carbon::createFromDate($request->year, 7, 1),
                        Carbon::createFromDate($request->year, 12, 31)
                    ]);
            }

        } elseif ($request->filled('month') && $request->filled('year')) {
            // 4. Bulan & Tahun
            $query->whereMonth('created_at', $request->month)
                ->whereYear('created_at', $request->year);

        } elseif ($request->filled('year')) {
            // 5. Hanya Tahun
            $query->whereYear('created_at', $request->year);

        } else {
            // === DEFAULT (Bulan & Tahun sekarang) ===
            $query->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear);
            $request->merge([
                'month' => $currentMonth,
                'year' => $currentYear
            ]);
        }
        // === AKHIR LOGIKA PRIORITAS FILTER ===
        // Filter instansi
        
        if ($request->filled('institution_id')) {
            if ($request->institution_id === 'mpp_ikm') {
                // Semua instansi yang tergabung dalam MPP
                $mppIds = Institution::whereHas('mpp', function ($q) {
                    $q->where('slug', 'mpp-kota-magelang');
                })->pluck('id');

                $query->whereIn('institution_id', $mppIds);
                $selectedInstitution = 'MPP Kota Magelang';

            } elseif ($request->institution_id === 'kota_ikm') {
                // Semua instansi yang menginduk pada Kota Magelang
                $kotaIds = Institution::whereHas('group', function ($q) {
                    $q->where('slug', 'kota-magelang');
                })->pluck('id');

                $query->whereIn('institution_id', $kotaIds);
                $selectedInstitution = 'Kota Magelang';

            } else {
                // Satu instansi spesifik
                $query->where('institution_id', $request->institution_id);
                $selectedInstitution = Institution::find($request->institution_id)?->name;
            }
        } else {
            $selectedInstitution = null;
        }
        
        $respondents = $query->orderBy('created_at')->get();
        // === Hitung skor per responden per unsur (pakai rata-rata) ===
        $respondentScores = [];
        foreach ($respondents as $respondent) {
            foreach ($unsurs as $unsur) {
                $answers = $respondent->answers
                    ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id);

                $respondentScores[$respondent->id][$unsur->id] = $answers->count() > 0
                    ? round($answers->avg('score'))
                    : 0;
            }
        }
         // === Hitung total per unsur, rata-rata, dan bobot ===
        $totalPerUnsur = [];
        $averagePerUnsur = [];
        $weightedPerUnsur = [];
        $totalBobot = 0;

        $jumlahUnsur = max(1, $unsurs->count());
        $bobotPerUnsur = 0.11;//1 / $jumlahUnsur;

        foreach ($unsurs as $unsur) {
            // ambil semua nilai responden dari $respondentScores yang sudah dibulatkan
        $scoresPerRespondent = collect($respondents)->map(fn($r) =>
            $respondentScores[$r->id][$unsur->id] ?? 0
        );

        $total = $scoresPerRespondent->sum();
        $average = $scoresPerRespondent->avg();

        $totalPerUnsur[$unsur->id] = $total;
        $averagePerUnsur[$unsur->id] = round($average, 2);

        $weighted = $average * $bobotPerUnsur;
        $weightedPerUnsur[$unsur->id] = round($weighted, 4);

        $totalBobot += $weighted;
        }

        $nilaiSKM = $totalBobot * 25;
        // Tentukan kategori mutu layanan
        if ($nilaiSKM >= 88.31) {
            $kategoriMutu = ['A', 'Sangat Baik'];
        } elseif ($nilaiSKM >= 76.61) {
            $kategoriMutu = ['B', 'Baik'];
        } elseif ($nilaiSKM >= 65.00) {
            $kategoriMutu = ['C', 'Kurang Baik'];
        } else {
            $kategoriMutu = ['D', 'Tidak Baik'];
        }
        // Data untuk dropdown filter
        $institutions = Institution::with(['mpp', 'group'])
            ->orderBy('name')
            ->get();
        $months = collect(range(1, 12))->mapWithKeys(function ($m) {
            return [$m => Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F')];
        });
        $years = Respondent::selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year', 'year');
        $quarters = [
            1 => 'Triwulan 1 (Jan-Mar)',
            2 => 'Triwulan 2 (Apr-Jun)',
            3 => 'Triwulan 3 (Jul-Sep)',
            4 => 'Triwulan 4 (Okt-Des)'
        ];
        $semesters = [
            1 => 'Semester 1 (Jan-Jun)',
            2 => 'Semester 2 (Jul-Des)'
        ];
        // Hitung jumlah responden
        $totalRespondents = $respondents->count();
        // Hitung jumlah per jenis kelamin
        $genderCounts = $respondents->groupBy('gender')->map->count();

        // Hitung jumlah per tingkat pendidikan
        $educationCounts = $respondents->groupBy('education_id')->map->count();

        // Hitung jumlah perkerjaan
        $occupationCounts = $respondents->groupBy('occupation_id')->map->count();

        // Ambil nama pendidikan (jika relasi ada)
        $educationNames = Education::whereIn('id', $educationCounts->keys())->pluck('level', 'id');
        // Ambil nama pekerjaan (jika relasi ada)
        $occupationNames = Occupation::whereIn('id', $occupationCounts->keys())->pluck('type', 'id');

        return compact('respondents', 'unsurs', 'institutions','quarters','semesters', 'months', 'years', 'title', 'subtitle','totalPerUnsur','respondentScores','averagePerUnsur','weightedPerUnsur', 'totalBobot', 'nilaiSKM', 'kategoriMutu','selectedInstitution','totalRespondents', 'genderCounts', 'educationCounts', 'educationNames','occupationCounts', 'occupationNames');
    }
    public function cetakPublikasiPdf(Request $request)
    {
    // ambil data yang sama seperti cetak()
        $data = $this->getCetakData($request);

        $pdf = Pdf::loadView('dashboard.reports.publikasi_pdf', $data)
                ->setPaper('a4', 'portrait');

        return $pdf->stream('laporan_ikm.pdf');
    }


}
