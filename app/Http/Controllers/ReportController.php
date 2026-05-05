<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\Education;
use App\Models\Occupation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Response as Respondent;
use App\Models\Unsur;

class ReportController extends Controller
{
    // ── Apply date & institution filters to any Respondent query ─────────────
    private function applyFilters(Request $request, $query): ?string
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        } elseif ($request->filled('quarter') && $request->filled('year')) {
            $startMonth = ($request->quarter - 1) * 3 + 1;
            $query->whereYear('created_at', $request->year)
                  ->whereMonth('created_at', '>=', $startMonth)
                  ->whereMonth('created_at', '<=', $startMonth + 2);
        } elseif ($request->filled('semester') && $request->filled('year')) {
            [$startM, $endM] = $request->semester == 1 ? [1, 6] : [7, 12];
            $query->whereYear('created_at', $request->year)
                  ->whereBetween('created_at', [
                      Carbon::createFromDate($request->year, $startM, 1)->startOfDay(),
                      Carbon::createFromDate($request->year, $endM, 1)->endOfMonth()->endOfDay(),
                  ]);
        } elseif ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', $request->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        } else {
            $query->whereMonth('created_at', $currentMonth)
                  ->whereYear('created_at', $currentYear);
            $request->merge(['month' => $currentMonth, 'year' => $currentYear]);
        }

        $user = Auth::user();
        $selectedInstitution = null;

        if ($user->hasRole('super_admin')) {
            if ($request->filled('institution_id')) {
                if ($request->institution_id === 'mpp_ikm') {
                    $ids = Institution::whereHas('mpp', fn($q) => $q->where('slug', 'mpp-kota-magelang'))->pluck('id');
                    $query->whereIn('institution_id', $ids);
                    $selectedInstitution = 'MPP Kota Magelang';
                } elseif ($request->institution_id === 'kota_ikm') {
                    $ids = Institution::whereHas('group', fn($q) => $q->where('slug', 'kota-magelang'))->pluck('id');
                    $query->whereIn('institution_id', $ids);
                    $selectedInstitution = 'Kota Magelang';
                } else {
                    $query->where('institution_id', $request->institution_id);
                    $selectedInstitution = Institution::find($request->institution_id)?->name;
                }
            }
        } else {
            $query->where('institution_id', $user->institution_id);
            $selectedInstitution = $user->institution?->name;
        }

        return $selectedInstitution;
    }

    // ── Compute per-unsur stats via 2-level SQL aggregation (no PHP memory bloat) ──
    private function computeUnsurStats($baseQuery, $unsurs): array
    {
        // Inner: per-respondent per-unsur rounded avg score
        // Uses subquery so no massive IN clause is sent to DB
        $inner = DB::table('answers as a')
            ->join('questions as q', 'a.question_id', '=', 'q.id')
            ->whereIn('a.response_id', (clone $baseQuery)->select('id'))
            ->whereNotNull('q.unsur_id')
            ->selectRaw('a.response_id, q.unsur_id, ROUND(AVG(a.score)) as resp_score')
            ->groupBy('a.response_id', 'q.unsur_id');

        // Outer: per-unsur aggregation (tiny result: 1 row per unsur)
        $aggregated = DB::table(DB::raw("({$inner->toSql()}) as sub"))
            ->mergeBindings($inner)
            ->selectRaw('unsur_id, SUM(resp_score) as total, AVG(resp_score) as average')
            ->groupBy('unsur_id')
            ->get()
            ->keyBy('unsur_id');

        $totalPerUnsur    = [];
        $averagePerUnsur  = [];
        $weightedPerUnsur = [];
        $totalBobot       = 0;

        foreach ($unsurs as $unsur) {
            $row     = $aggregated->get($unsur->id);
            $average = $row ? (float) $row->average : 0.0;
            $total   = $row ? (float) $row->total   : 0.0;

            $totalPerUnsur[$unsur->id]    = $total;
            $averagePerUnsur[$unsur->id]  = round($average, 2);
            $weighted                     = $average * 0.11;
            $weightedPerUnsur[$unsur->id] = round($weighted, 4);
            $totalBobot                   += $weighted;
        }

        return compact('totalPerUnsur', 'averagePerUnsur', 'weightedPerUnsur', 'totalBobot');
    }

    // ── Shared dropdown data ──────────────────────────────────────────────────
    private function dropdownData(): array
    {
        $months = collect(range(1, 12))->mapWithKeys(
            fn($m) => [$m => Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F')]
        );
        $years = Respondent::selectRaw('YEAR(created_at) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year', 'year');
        $quarters  = [1 => 'Triwulan 1 (Jan-Mar)', 2 => 'Triwulan 2 (Apr-Jun)',
                      3 => 'Triwulan 3 (Jul-Sep)', 4 => 'Triwulan 4 (Okt-Des)'];
        $semesters = [1 => 'Semester 1 (Jan-Jun)', 2 => 'Semester 2 (Jul-Des)'];

        $institutions = Auth::user()->hasRole('super_admin')
            ? Institution::with(['mpp', 'group'])->orderBy('name')->get()
            : collect();

        return compact('months', 'years', 'quarters', 'semesters', 'institutions');
    }

    private function kategoriMutu(float $nilaiSKM): array
    {
        if ($nilaiSKM >= 88.31) return ['A', 'Sangat Baik'];
        if ($nilaiSKM >= 76.61) return ['B', 'Baik'];
        if ($nilaiSKM >= 65.00) return ['C', 'Kurang Baik'];
        return ['D', 'Tidak Baik'];
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $title    = 'Laporan SKM';
        $subtitle = 'Laporan SKM Berdasarkan Responden dan Nilai SKM';
        $unsurs   = Unsur::orderBy('label_order')->get();

        $baseQuery           = Respondent::query();
        $selectedInstitution = $this->applyFilters($request, $baseQuery);

        // Aggregate stats computed entirely in DB (no respondent rows loaded into PHP)
        $stats      = $this->computeUnsurStats($baseQuery, $unsurs);
        $nilaiSKM   = round($stats['totalBobot'] * 25, 2);
        $kategoriMutu = $this->kategoriMutu($nilaiSKM);

        // Paginated respondents (only current page rows loaded)
        $respondents = (clone $baseQuery)
            ->with(['service', 'institution.mpp', 'institution.group'])
            ->orderBy('created_at')
            ->paginate(25)
            ->withQueryString();

        // Per-respondent scores only for current page (~25 IDs — fast whereIn)
        $respondentScores = [];
        if ($respondents->isNotEmpty()) {
            $pageScores = DB::table('answers as a')
                ->join('questions as q', 'a.question_id', '=', 'q.id')
                ->whereIn('a.response_id', $respondents->pluck('id'))
                ->whereNotNull('q.unsur_id')
                ->selectRaw('a.response_id, q.unsur_id, ROUND(AVG(a.score)) as score')
                ->groupBy('a.response_id', 'q.unsur_id')
                ->get();
            foreach ($pageScores as $row) {
                $respondentScores[$row->response_id][$row->unsur_id] = (int) $row->score;
            }
        }

        return view('dashboard.reports.index', array_merge(
            compact('respondents', 'unsurs', 'title', 'subtitle',
                    'respondentScores', 'nilaiSKM', 'kategoriMutu', 'selectedInstitution'),
            $stats,
            $this->dropdownData()
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function cetakPdf(Request $request)
    {
        $data = $this->getCetakData($request, true);
        $pdf  = Pdf::loadView('dashboard.reports.cetak_pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('laporan_ikm.pdf');
    }

    public function cetakPublikasiPdf(Request $request)
    {
        $data = $this->getCetakData($request, false);
        $pdf  = Pdf::loadView('dashboard.reports.publikasi_pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->stream('laporan_ikm.pdf');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function getCetakData(Request $request, bool $includeDetailedRespondents = true): array
    {
        $title    = 'Laporan SKM';
        $subtitle = 'Laporan SKM Berdasarkan Responden dan Nilai SKM';
        $unsurs   = Unsur::orderBy('label_order')->get();

        $baseQuery           = Respondent::query();
        $selectedInstitution = $this->applyFilters($request, $baseQuery);

        // Unsur stats tetap dihitung dari DB aggregation supaya hemat memori.
        $stats = $this->computeUnsurStats($baseQuery, $unsurs);
        $totalPerUnsur    = $stats['totalPerUnsur'];
        $averagePerUnsur  = $stats['averagePerUnsur'];
        $weightedPerUnsur = $stats['weightedPerUnsur'];
        $totalBobot       = $stats['totalBobot'];

        // Data detail responden hanya dibawa untuk cetak tabel lengkap.
        $respondents = collect();
        $respondentScores = [];
        $pdfPage = 1;
        $pdfPerPage = 2000;
        $pdfTotalPages = 1;
        $pdfFrom = 0;
        $pdfTo = 0;
        if ($includeDetailedRespondents) {
            $pdfPage = max(1, (int) $request->input('pdf_page', 1));
            $pdfPerPage = (int) $request->input('pdf_per_page', 2000);
            $pdfPerPage = max(100, min($pdfPerPage, 5000));

            $totalDetailed = (clone $baseQuery)->count();
            $pdfTotalPages = max(1, (int) ceil($totalDetailed / $pdfPerPage));
            $pdfPage = min($pdfPage, $pdfTotalPages);

            $chunkResponseIds = (clone $baseQuery)
                ->orderBy('created_at')
                ->forPage($pdfPage, $pdfPerPage)
                ->pluck('id');

            $respondents = DB::table('responses as r')
                ->leftJoin('educations as e', 'e.id', '=', 'r.education_id')
                ->leftJoin('occupations as o', 'o.id', '=', 'r.occupation_id')
                ->leftJoin('institutions as i', 'i.id', '=', 'r.institution_id')
                ->leftJoin('services as s', 's.id', '=', 'r.service_id')
                ->whereIn('r.id', $chunkResponseIds)
                ->orderBy('r.created_at')
                ->selectRaw('r.id, r.created_at, r.age, e.level as education_level, o.type as occupation_type, i.name as institution_name, s.name as service_name')
                ->get();

            $scoreRows = DB::table('answers as a')
                ->join('questions as q', 'a.question_id', '=', 'q.id')
                ->whereIn('a.response_id', $chunkResponseIds)
                ->whereNotNull('q.unsur_id')
                ->selectRaw('a.response_id, q.unsur_id, ROUND(AVG(a.score)) as score')
                ->groupBy('a.response_id', 'q.unsur_id')
                ->get();

            foreach ($scoreRows as $row) {
                $respondentScores[$row->response_id][$row->unsur_id] = (int) $row->score;
            }

            if ($totalDetailed > 0) {
                $pdfFrom = (($pdfPage - 1) * $pdfPerPage) + 1;
                $pdfTo = min((($pdfPage - 1) * $pdfPerPage) + $respondents->count(), $totalDetailed);
            }
        }

        $nilaiSKM     = round($totalBobot * 25, 2);
        $kategoriMutu = $this->kategoriMutu($nilaiSKM);

        // Demographics dihitung di SQL supaya publikasi PDF tidak perlu load semua responden.
        $totalRespondents = (clone $baseQuery)->count();
        $genderCounts = (clone $baseQuery)
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');
        $educationCounts = (clone $baseQuery)
            ->whereNotNull('education_id')
            ->selectRaw('education_id, COUNT(*) as total')
            ->groupBy('education_id')
            ->pluck('total', 'education_id');
        $occupationCounts = (clone $baseQuery)
            ->whereNotNull('occupation_id')
            ->selectRaw('occupation_id, COUNT(*) as total')
            ->groupBy('occupation_id')
            ->pluck('total', 'occupation_id');
        $educationNames   = Education::whereIn('id', $educationCounts->keys())->pluck('level', 'id');
        $occupationNames  = Occupation::whereIn('id', $occupationCounts->keys())->pluck('type', 'id');

        return array_merge(
            compact('respondents', 'unsurs', 'title', 'subtitle',
                    'respondentScores', 'totalPerUnsur', 'averagePerUnsur',
                    'weightedPerUnsur', 'totalBobot', 'nilaiSKM', 'kategoriMutu',
                    'selectedInstitution', 'totalRespondents', 'genderCounts',
                    'educationCounts', 'educationNames', 'occupationCounts', 'occupationNames',
                    'pdfPage', 'pdfPerPage', 'pdfTotalPages', 'pdfFrom', 'pdfTo'),
            $this->dropdownData()
        );
    }
}

