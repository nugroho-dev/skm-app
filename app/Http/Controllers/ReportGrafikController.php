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
    $institutions = Institution::with(['mpp', 'group'])
        ->orderBy('name')
        ->get();

        return view('dashboard.reportgrafik.index', compact(
        'title','ikmBulanan','ikmTriwulan','ikmSemester','ikmTahunan', 'selectedYear','years','selectedInstitution','institutions'
        ));
    }
}
