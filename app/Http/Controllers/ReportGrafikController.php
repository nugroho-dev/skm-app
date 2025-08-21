<?php

namespace App\Http\Controllers;

use App\Models\Response as Respondent;
use App\Models\Institution;
use App\Models\Unsur;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportGrafikController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Laporan SKM';
        $subtitle = 'Laporan SKM Berdasarkan Responden dan Nilai SKM';
        $unsurs = Unsur::orderBy('label_order')->get();
        $unsurCount = $unsurs->count();
        $query = Respondent::with(['answers.question.unsur', 'institution', 'institution.mpp', 'institution.group']);
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // === LOGIKA PRIORITAS FILTER ===
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($request->filled('quarter') && $request->filled('year')) {
            $startMonth = ($request->quarter - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $query->whereYear('created_at', $request->year)
                ->whereMonth('created_at', '>=', $startMonth)
                ->whereMonth('created_at', '<=', $endMonth);
        } elseif ($request->filled('semester') && $request->filled('year')) {
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
            $query->whereMonth('created_at', $request->month)
                ->whereYear('created_at', $request->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        } else {
            $query->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear);
            $request->merge([
                'month' => $currentMonth,
                'year' => $currentYear
            ]);
        }

        // === FILTER INSTANSI ===
        if ($request->filled('institution_id')) {
            if ($request->institution_id === 'mpp_ikm') {
                $mppIds = Institution::whereHas('mpp', function ($q) {
                    $q->where('slug', 'mpp-kota-magelang');
                })->pluck('id');
                $query->whereIn('institution_id', $mppIds);
                $selectedInstitution = 'MPP Kota Magelang';
            } elseif ($request->institution_id === 'kota_ikm') {
                $kotaIds = Institution::whereHas('group', function ($q) {
                    $q->where('slug', 'kota-magelang');
                })->pluck('id');
                $query->whereIn('institution_id', $kotaIds);
                $selectedInstitution = 'Kota Magelang';
            } else {
                $query->where('institution_id', $request->institution_id);
                $selectedInstitution = Institution::find($request->institution_id)?->name;
            }
        } else {
            $selectedInstitution = null;
        }

        $respondents = $query->orderBy('created_at')->get();

        // === HITUNG NILAI IKM ===
        $totalBobot = 0;
        foreach ($unsurs as $unsur) {
            $total = $respondents
                ->flatMap->answers
                ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id)
                ->sum('score');
            $average = $respondents->count() > 0 ? $total / $respondents->count() : 0;
            $totalBobot += $average * 0.11;
        }
        $nilaiSKM = $totalBobot * 25;

        // === GRAFIK IKM ===
        $ikmBulanan = DB::table('responses')
    ->join('answers', 'responses.id', '=', 'answers.response_id')
    ->selectRaw('YEAR(responses.created_at) as year')
    ->selectRaw('MONTH(responses.created_at) as month')
    ->selectRaw('SUM(answers.score) as total_score')
    ->selectRaw('COUNT(DISTINCT responses.id) as total_responden')
    ->whereNull('responses.deleted_at')
    ->groupBy('year', 'month')
    ->orderBy('year')
    ->orderBy('month')
    ->get()
    ->map(function ($row) use ($unsurCount) {
        $avg = $row->total_responden > 0 ? $row->total_score / $row->total_responden : 0;
        $nilai = ($avg * 0.11 * $unsurCount) * 25;

        return [
            'year'  => $row->year,
            'month' => $row->month,
            'label' => Carbon::createFromDate($row->year, $row->month, 1)->translatedFormat('F Y'),
            'ikm'   => round($nilai, 2),
        ];
    });

        $ikmTahunan = $ikmBulanan->groupBy('year')->map(function ($group) {
            return [
                'year' => $group->first()['year'],
                'ikm' => round($group->avg('ikm'), 2)
            ];
        })->values();

        $ikmTriwulan = $ikmBulanan->groupBy(function ($item) {
            return $item['year'] . '-Q' . ceil($item['month'] / 3);
        })->map(function ($group, $key) {
            [$year, $q] = explode('-Q', $key);
            return [
                'year' => $year,
                'quarter' => $q,
                'label' => "Triwulan $q $year",
                'ikm' => round($group->avg('ikm'), 2)
            ];
        })->values();

        $ikmSemester = $ikmBulanan->groupBy(function ($item) {
            return $item['year'] . '-S' . ($item['month'] <= 6 ? 1 : 2);
        })->map(function ($group, $key) {
            [$year, $s] = explode('-S', $key);
            return [
                'year' => $year,
                'semester' => $s,
                'label' => "Semester $s $year",
                'ikm' => round($group->avg('ikm'), 2)
            ];
        })->values();

        // === DATA DROPDOWN ===
        $institutions = Institution::with(['mpp', 'group'])->orderBy('name')->get();
        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F')]);
        $years = Respondent::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year', 'year');
        $quarters = [1 => 'Triwulan 1 (Jan-Mar)', 2 => 'Triwulan 2 (Apr-Jun)', 3 => 'Triwulan 3 (Jul-Sep)', 4 => 'Triwulan 4 (Okt-Des)'];
        $semesters = [1 => 'Semester 1 (Jan-Jun)', 2 => 'Semester 2 (Jul-Des)'];

        return view('dashboard.reportgrafik.index', compact(
            'respondents',
            'unsurs',
            'institutions',
            'quarters',
            'semesters',
            'months',
            'years',
            'title',
            'subtitle',
            'nilaiSKM',
            'selectedInstitution',
            'ikmBulanan',
            'ikmTahunan',
            'ikmTriwulan',
            'ikmSemester'
        ));
    }
}
