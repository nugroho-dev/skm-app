<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\Education;
use App\Models\Occupation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Response as Respondent;
use App\Models\Unsur;


class ReportRespondentController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Laporan Jumlah Responden';
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

        return view('dashboard.reportrespondent.index', compact( 'institutions','quarters','semesters', 'months', 'years', 'title', 'subtitle','totalRespondents', 'genderCounts', 'educationCounts', 'educationNames','occupationCounts', 'occupationNames', 'respondents', 'unsurs', 'selectedInstitution'));
    }

    public function getCetakData(Request $request)
    {
        $title = 'Laporan Jumlah Responden';
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

        return compact( 'institutions','quarters','semesters', 'months', 'years', 'title', 'subtitle','totalRespondents', 'genderCounts', 'educationCounts', 'educationNames','occupationCounts', 'occupationNames', 'respondents', 'unsurs', 'selectedInstitution');
    }
     public function cetakPdf(Request $request)
    {
    // ambil data yang sama seperti cetak()
        $data = $this->getCetakData($request);

        $pdf = Pdf::loadView('dashboard.reportrespondent.cetak_pdf', $data)
                ->setPaper('a4', 'portrait');

        return $pdf->stream('laporan_responden.pdf');
    }

}
