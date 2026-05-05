<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportGrafikController extends Controller
{
    public function index(Request $request)
    {
        $title        = 'Laporan SKM';
        $user         = Auth::user();
        $selectedYear = (int) ($request->input('year') ?: now()->year);

        // ── Step 1: Subquery – per respondent, per month, per unsur avg score ──────
        // Aggregation done entirely in DB; PHP never touches raw response rows.
        $baseQuery = DB::table('responses as r')
            ->join('answers as a', 'a.response_id', '=', 'r.id')
            ->join('questions as q', 'a.question_id', '=', 'q.id')
            ->whereNotNull('q.unsur_id')
            ->selectRaw(
                'r.id, YEAR(r.created_at) as year, MONTH(r.created_at) as month,
                 q.unsur_id, ROUND(AVG(a.score)) as avg_score'
            )
            ->groupByRaw('r.id, YEAR(r.created_at), MONTH(r.created_at), q.unsur_id');

        // ── Step 2: Institution filter ────────────────────────────────────────────
        $selectedInstitution = null;

        if ($user->hasRole('super_admin')) {
            if ($request->filled('institution_id')) {
                if ($request->institution_id === 'mpp_ikm') {
                    $ids = Institution::whereHas('mpp', fn($q) => $q->where('slug', 'mpp-kota-magelang'))->pluck('id');
                    $baseQuery->whereIn('r.institution_id', $ids);
                    $selectedInstitution = 'MPP Kota Magelang';
                } elseif ($request->institution_id === 'kota_ikm') {
                    $ids = Institution::whereHas('group', fn($q) => $q->where('slug', 'kota-magelang'))->pluck('id');
                    $baseQuery->whereIn('r.institution_id', $ids);
                    $selectedInstitution = 'Kota Magelang';
                } else {
                    $baseQuery->where('r.institution_id', $request->institution_id);
                    $selectedInstitution = Institution::find($request->institution_id)?->name;
                }
            }
        } else {
            $institution = $user->institution;
            $baseQuery->where('r.institution_id', $institution->id);
            $selectedInstitution = $institution->name;
        }

        // ── Step 3: Aggregate to monthly-unsur level (tiny result set) ───────────
        // Result: max ≈ years × 12 × unsur_count rows, e.g. 5 × 12 × 9 = 540 rows
        $monthlyData = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
            ->mergeBindings($baseQuery)
            ->selectRaw('year, month, unsur_id, SUM(avg_score) as score_sum, COUNT(*) as resp_count')
            ->groupByRaw('year, month, unsur_id')
            ->get();

        // ── Step 4: IKM computation helper ───────────────────────────────────────
        // Accepts a Collection of {unsur_id, score_sum, resp_count}.
        // Uses respondent-count weighted average so multi-month periods are correct.
        $computeIkm = function ($rows) {
            $totalBobot = $rows->groupBy('unsur_id')->sum(function ($unsurRows) {
                $totalResp = $unsurRows->sum('resp_count');
                return $totalResp > 0
                    ? ($unsurRows->sum('score_sum') / $totalResp) * 0.11
                    : 0;
            });
            return round($totalBobot * 25, 2);
        };

        // ── Step 5: Build period arrays in PHP (tiny loop) ───────────────────────
        $ikmBulanan  = [];
        $ikmTriwulan = [];
        $ikmSemester = [];
        $ikmTahunan  = [];

        $years = $monthlyData->pluck('year')->unique()->sort()->values();

        foreach ($years as $year) {
            $yearData = $monthlyData->where('year', $year);

            // Monthly
            foreach ($yearData->pluck('month')->unique()->sort()->values() as $month) {
                $ikmBulanan[] = [
                    'year'  => $year,
                    'month' => $month,
                    'label' => 'Bulan ' . Carbon::create()->month($month)->translatedFormat('F') . " $year",
                    'ikm'   => $computeIkm($yearData->where('month', $month)),
                ];
            }

            // Quarterly (triwulan)
            for ($q = 1; $q <= 4; $q++) {
                $start = ($q - 1) * 3 + 1;
                $qData = $yearData->whereBetween('month', [$start, $start + 2]);
                if ($qData->isEmpty()) continue;
                $ikmTriwulan[] = [
                    'year'    => $year,
                    'quarter' => $q,
                    'label'   => "Triwulan $q $year",
                    'ikm'     => $computeIkm($qData),
                ];
            }

            // Semester
            foreach ([1 => [1, 6], 2 => [7, 12]] as $s => [$startM, $endM]) {
                $sData = $yearData->whereBetween('month', [$startM, $endM]);
                if ($sData->isEmpty()) continue;
                $ikmSemester[] = [
                    'year'     => $year,
                    'semester' => $s,
                    'label'    => "Semester $s $year",
                    'ikm'      => $computeIkm($sData),
                ];
            }

            // Annual
            $ikmTahunan[] = [
                'year'  => $year,
                'label' => "Tahun $year",
                'ikm'   => $computeIkm($yearData),
            ];
        }

        $institutions = $user->hasRole('super_admin')
            ? Institution::with(['mpp', 'group'])->orderBy('name')->get()
            : collect();

        return view('dashboard.reportgrafik.index', compact(
            'title', 'ikmBulanan', 'ikmTriwulan', 'ikmSemester', 'ikmTahunan',
            'selectedYear', 'years', 'selectedInstitution', 'institutions'
        ));
    }
}
