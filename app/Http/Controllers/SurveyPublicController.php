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
        

    // === Ambil responden per tahun terpilih ===
    $respondentsYear = (clone $baseQuery)
        ->with(['answers.question.unsur'])
        ->whereYear('created_at', $selectedYear)
        ->orderBy('created_at')
        ->get()
        ->groupBy(fn($r) => (int) $r->created_at->format('n'));

    // === Hitung IKM Bulanan (per unsur) ===
    $ikmBulanan = collect(range(1, 12))->map(function ($m) use ($respondentsYear, $unsurs, $selectedYear) {
        $respBulan = $respondentsYear->get($m, collect());
        $totalBobot = 0;

        foreach ($unsurs as $unsur) {
            $total = $respBulan
                ->flatMap->answers
                ->filter(fn($ans) => $ans->question && $ans->question->unsur_id === $unsur->id)
                ->sum('score');

            $avg = $respBulan->count() > 0 ? $total / $respBulan->count() : 0;
            $totalBobot += $avg * 0.11;
        }

        $nilaiIKM = $totalBobot * 25;

        return [
            'year'  => $selectedYear,
            'month' => $m,
            'label' => \Carbon\Carbon::createFromDate($selectedYear, $m, 1)->translatedFormat('F Y'),
            'ikm'   => round($nilaiIKM, 2),
        ];
    });

    // === Triwulan ===
    $ikmTriwulan = $ikmBulanan->groupBy(fn($r) => 'Q'.ceil($r['month']/3))->map(function ($grp, $q) use ($selectedYear) {
        return [
            'year'    => $selectedYear,
            'quarter' => (int) str_replace('Q','',$q),
            'label'   => "Triwulan ".str_replace('Q','',$q)." $selectedYear",
            'ikm'     => round($grp->avg('ikm'), 2),
        ];
    })->values();

    // === Semester ===
    $ikmSemester = $ikmBulanan->groupBy(fn($r) => 'S'.($r['month'] <= 6 ? 1 : 2))->map(function ($grp, $s) use ($selectedYear) {
        return [
            'year'     => $selectedYear,
            'semester' => (int) str_replace('S','',$s),
            'label'    => "Semester ".str_replace('S','',$s)." $selectedYear",
            'ikm'      => round($grp->avg('ikm'), 2),
        ];
    })->values();

    // === Tahunan (seluruh tahun, ikut filter instansi) ===
    $ikmTahunan = (clone $baseQuery)
        ->with(['answers.question.unsur'])
        ->orderBy('created_at')
        ->get()
        ->groupBy(fn($r) => (int) $r->created_at->format('Y'))
        ->map(function ($respTahun, $year) use ($unsurs) {
            $ikmBulananTahun = $respTahun->groupBy(fn($r) => (int) $r->created_at->format('n'))->map(function ($respBulan) use ($unsurs) {
                $totalBobot = 0;
                foreach ($unsurs as $unsur) {
                    $total = $respBulan
                        ->flatMap->answers
                        ->filter(fn($ans) => $ans->question && $ans->question->unsur_id === $unsur->id)
                        ->sum('score');

                    $avg = $respBulan->count() > 0 ? $total / $respBulan->count() : 0;
                    $totalBobot += $avg * 0.11;
                }
                return $totalBobot * 25;
            });

            return [
                'year' => (int) $year,
                'label'=> "Tahun $year",
                'ikm'  => round($ikmBulananTahun->avg() ?: 0, 2),
            ];
        })->values()->sortBy('year')->values();

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
        $institutions = Institution::with(['mpp', 'group'])
        ->orderBy('name')
        ->get();

    $institutionsall = Institution::with(['mpp', 'group'])
        ->orderBy('name')
        ->get();

        return view('survey.grafik', compact(
        'title','ikmBulanan','ikmTriwulan','ikmSemester','ikmTahunan', 'selectedYear','years','selectedInstitution','institutions','quarters','semesters','months','institutionsall'));
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
        $respondentScores = [];
        foreach ($respondents as $respondent) {
            foreach ($unsurs as $unsur) {
                $respondentScores[$respondent->id][$unsur->id] = $respondent->answers
                    ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id)
                    ->sum('score');
            }
        }
        // Hitung total score per unsur
        $totalPerUnsur = [];
        $averagePerUnsur = [];
        $weightedPerUnsur = [];
        $totalBobot = 0; // total semua unsur x 0.11
        foreach ($unsurs as $unsur) {
            $totalPerUnsur[$unsur->id] = $respondents
                ->flatMap->answers
                ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id)
                ->sum('score');
            $total=$totalPerUnsur[$unsur->id] ;
            $countRespondents = max(1, $respondents->count()); // supaya tidak bagi nol
            $average = $total / $countRespondents;
            $averagePerUnsur[$unsur->id] = $average;
            // Rata-rata × 0,11
            $weighted = $average * 0.11;
            $weightedPerUnsur[$unsur->id] = $weighted;

        // Tambahkan ke total bobot
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
