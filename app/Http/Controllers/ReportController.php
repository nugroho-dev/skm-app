<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\Answer;
use App\Models\Institution;
use Carbon\Carbon;
use App\Models\Response as Respondent;
use App\Models\Unsur;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Laporan SKM';
        $subtitle = 'Laporan SKM Berdasarkan Responden dan Nilai SKM';

        $unsurs = Unsur::orderBy('label_order')->get();

    $query = Respondent::with([
        'service',
        'answers.question.unsur',
        'institution'
    ]);

    // Filter periode (bulan/tahun)
    if ($request->filled('month') && $request->filled('year')) {
        $query->whereMonth('created_at', $request->month)
              ->whereYear('created_at', $request->year);
    }

    // Filter instansi
    if ($request->filled('institution_id')) {
        $query->where('institution_id', $request->institution_id);
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
    // Data untuk dropdown filter
    $institutions = Institution::orderBy('name')->get();
    $months = collect(range(1, 12))->mapWithKeys(function ($m) {
        return [$m => Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F')];
    });
    $years = Respondent::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year', 'year');

    return view('dashboard.reports.index', compact('respondents', 'unsurs', 'institutions', 'months', 'years', 'title', 'subtitle','totalPerUnsur','respondentScores','averagePerUnsur','weightedPerUnsur', 'totalBobot', 'nilaiSKM'));
    }
    // Ambil daftar unsur (supaya kolom tabel dinamis sesuai unsur yang ada)
       

}
