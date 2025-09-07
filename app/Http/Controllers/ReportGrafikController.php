<?php

namespace App\Http\Controllers;

use App\Models\Response as Respondent;
use App\Models\Institution;
use App\Models\Unsur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
     if (Auth::user()->hasRole('super_admin')) {
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
        } elseif (Auth::user()->hasRole('admin_instansi')) {
            $user = Auth::user();
            $institution = $user->institution;
            $baseQuery->where('institution_id', $institution->id);
            $selectedInstitution = Institution::find($institution->id)?->name;
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
    if (Auth::user()->hasRole('super_admin')) {
    $institutions = Institution::with(['mpp', 'group'])
        ->orderBy('name')
        ->get();
     } else {
            // Admin instansi: tidak ada pilihan instansi
            $institutions = collect(); // kosongkan supaya tidak error di blade
        }
        
        return view('dashboard.reportgrafik.index', compact(
        'title','ikmBulanan','ikmTriwulan','ikmSemester','ikmTahunan', 'selectedYear','years','selectedInstitution','institutions'
        ));
    }
}
