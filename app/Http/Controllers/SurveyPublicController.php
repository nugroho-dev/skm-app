<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Institution;
use App\Models\Occupation;
use App\Models\Unsur;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class SurveyPublicController extends Controller
{
    private const PUBLIC_ALLOWED_INSTITUTION_FILTERS = ['kota_ikm', 'mpp_ikm'];
    private const PUBLIC_INSTITUTION_FILTER_PREFIX = 'inst:';
    private const IKM_WEIGHT_PER_UNSUR = 0.11;

    private function resolvePublicInstitutionFilter(Request $request): ?array
    {
        $institutionFilter = $request->query('institution_id');

        if ($institutionFilter === null || $institutionFilter === '') {
            return null;
        }

        if (! in_array($institutionFilter, self::PUBLIC_ALLOWED_INSTITUTION_FILTERS, true)) {
            if (! str_starts_with($institutionFilter, self::PUBLIC_INSTITUTION_FILTER_PREFIX)) {
                abort(403, 'Filter instansi tidak diizinkan.');
            }

            $institutionSlug = substr($institutionFilter, strlen(self::PUBLIC_INSTITUTION_FILTER_PREFIX));

            if ($institutionSlug === '' || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $institutionSlug)) {
                abort(403, 'Format filter instansi tidak valid.');
            }

            $institution = Institution::query()
                ->where('slug', $institutionSlug)
                ->first();

            if (! $institution) {
                abort(404, 'Instansi tidak ditemukan.');
            }

            return [
                'type' => 'institution',
                'institution' => $institution,
            ];
        }

        return [
            'type' => 'preset',
            'value' => $institutionFilter,
        ];
    }

    private function buildCacheKey(string $prefix, array $params = []): string
    {
        ksort($params);

        return 'survey-public:' . $prefix . ':' . md5(json_encode($params));
    }

    private function getPresetInstitutionIds(string $preset): array
    {
        return Cache::remember(
            $this->buildCacheKey('institution-preset', ['preset' => $preset]),
            now()->addMinutes(30),
            function () use ($preset) {
                if ($preset === 'mpp_ikm') {
                    return Institution::whereHas('mpp', function ($query) {
                        $query->where('slug', 'mpp-kota-magelang');
                    })->pluck('id')->all();
                }

                if ($preset === 'kota_ikm') {
                    return Institution::whereHas('group', function ($query) {
                        $query->where('slug', 'kota-magelang');
                    })->pluck('id')->all();
                }

                return [];
            }
        );
    }

    private function applyPublicInstitutionFilterToQuery(QueryBuilder $query, ?array $institutionFilter, string $column = 'institution_id'): ?string
    {
        if ($institutionFilter === null) {
            return null;
        }

        if ($institutionFilter['type'] === 'preset') {
            $institutionIds = $this->getPresetInstitutionIds($institutionFilter['value']);

            if (empty($institutionIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn($column, $institutionIds);
            }

            return $institutionFilter['value'] === 'mpp_ikm'
                ? 'MPP Kota Magelang'
                : 'Kota Magelang';
        }

        $query->where($column, $institutionFilter['institution']->id);

        return $institutionFilter['institution']->name;
    }

    private function baseResponsesQuery(): QueryBuilder
    {
        return DB::table('responses')->whereNull('responses.deleted_at');
    }

    private function buildRespondentUnsurScoresQuery(?array $institutionFilter, ?callable $dateFilter = null): QueryBuilder
    {
        $query = DB::table('responses as responses')
            ->join('answers as answers', 'answers.response_id', '=', 'responses.id')
            ->join('questions as questions', 'questions.id', '=', 'answers.question_id')
            ->whereNull('responses.deleted_at')
            ->whereNull('answers.deleted_at')
            ->whereNull('questions.deleted_at')
            ->selectRaw('responses.id as response_id, responses.created_at, responses.institution_id, questions.unsur_id, ROUND(AVG(answers.score), 0) as respondent_unsur_score')
            ->groupBy('responses.id', 'responses.created_at', 'responses.institution_id', 'questions.unsur_id');

        $this->applyPublicInstitutionFilterToQuery($query, $institutionFilter, 'responses.institution_id');

        if ($dateFilter) {
            $dateFilter($query, 'responses.created_at');
        }

        return $query;
    }

    private function applyDateFilterToQuery(QueryBuilder $query, Request $request, string $createdAtColumn = 'created_at'): void
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween($createdAtColumn, [$start, $end]);

            return;
        }

        if ($request->filled('quarter') && $request->filled('year')) {
            $startMonth = ((int) $request->quarter - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $query->whereYear($createdAtColumn, $request->year)
                ->whereMonth($createdAtColumn, '>=', $startMonth)
                ->whereMonth($createdAtColumn, '<=', $endMonth);

            return;
        }

        if ($request->filled('semester') && $request->filled('year')) {
            $startMonth = (int) $request->semester === 1 ? 1 : 7;
            $endMonth = (int) $request->semester === 1 ? 6 : 12;

            $query->whereYear($createdAtColumn, $request->year)
                ->whereMonth($createdAtColumn, '>=', $startMonth)
                ->whereMonth($createdAtColumn, '<=', $endMonth);

            return;
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth($createdAtColumn, $request->month)
                ->whereYear($createdAtColumn, $request->year);

            return;
        }

        if ($request->filled('year')) {
            $query->whereYear($createdAtColumn, $request->year);

            return;
        }

        $query->whereMonth($createdAtColumn, $currentMonth)
            ->whereYear($createdAtColumn, $currentYear);

        $request->merge([
            'month' => $currentMonth,
            'year' => $currentYear,
        ]);
    }

    private function getIkmSeries(QueryBuilder $respondentUnsurScoresQuery, string $period, ?int $year = null): array
    {
        $periodAverages = DB::query()->fromSub(clone $respondentUnsurScoresQuery, 'respondent_unsur_scores');

        if ($year !== null) {
            $periodAverages->whereYear('created_at', $year);
        }

        if ($period === 'month') {
            $periodAverages
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as period_value, unsur_id, AVG(respondent_unsur_score) as avg_unsur_score')
                ->groupByRaw('YEAR(created_at), MONTH(created_at), unsur_id');
        } elseif ($period === 'quarter') {
            $periodAverages
                ->selectRaw('YEAR(created_at) as year, QUARTER(created_at) as period_value, unsur_id, AVG(respondent_unsur_score) as avg_unsur_score')
                ->groupByRaw('YEAR(created_at), QUARTER(created_at), unsur_id');
        } elseif ($period === 'semester') {
            $periodAverages
                ->selectRaw('YEAR(created_at) as year, CASE WHEN MONTH(created_at) <= 6 THEN 1 ELSE 2 END as period_value, unsur_id, AVG(respondent_unsur_score) as avg_unsur_score')
                ->groupByRaw('YEAR(created_at), CASE WHEN MONTH(created_at) <= 6 THEN 1 ELSE 2 END, unsur_id');
        } else {
            $periodAverages
                ->selectRaw('YEAR(created_at) as year, unsur_id, AVG(respondent_unsur_score) as avg_unsur_score')
                ->groupByRaw('YEAR(created_at), unsur_id');
        }

        $ikmQuery = DB::query()->fromSub($periodAverages, 'period_averages');

        if ($period === 'year') {
            $rows = $ikmQuery
                ->selectRaw('year, ROUND(SUM(avg_unsur_score * ?) * 25, 2) as ikm', [self::IKM_WEIGHT_PER_UNSUR])
                ->groupBy('year')
                ->orderBy('year')
                ->get();
        } else {
            $rows = $ikmQuery
                ->selectRaw('year, period_value, ROUND(SUM(avg_unsur_score * ?) * 25, 2) as ikm', [self::IKM_WEIGHT_PER_UNSUR])
                ->groupBy('year', 'period_value')
                ->orderBy('year')
                ->orderBy('period_value')
                ->get();
        }

        return $rows->map(function ($row) use ($period) {
            if ($period === 'month') {
                return [
                    'year' => (int) $row->year,
                    'month' => (int) $row->period_value,
                    'label' => 'Bulan ' . Carbon::create()->month((int) $row->period_value)->translatedFormat('F') . ' ' . $row->year,
                    'ikm' => (float) $row->ikm,
                ];
            }

            if ($period === 'quarter') {
                return [
                    'year' => (int) $row->year,
                    'quarter' => (int) $row->period_value,
                    'label' => 'Triwulan ' . $row->period_value . ' ' . $row->year,
                    'ikm' => (float) $row->ikm,
                ];
            }

            if ($period === 'semester') {
                return [
                    'year' => (int) $row->year,
                    'semester' => (int) $row->period_value,
                    'label' => 'Semester ' . $row->period_value . ' ' . $row->year,
                    'ikm' => (float) $row->ikm,
                ];
            }

            return [
                'year' => (int) $row->year,
                'label' => 'Tahun ' . $row->year,
                'ikm' => (float) $row->ikm,
            ];
        })->values()->all();
    }

    public function index(Request $request)
    {
        $title = 'Laporan SKM';
        $selectedYear = (int) ($request->input('year') ?: now()->year);
        $institutionFilter = $this->resolvePublicInstitutionFilter($request);
        $yearsQuery = $this->baseResponsesQuery();
        $selectedInstitution = $this->applyPublicInstitutionFilterToQuery($yearsQuery, $institutionFilter, 'responses.institution_id');

        $cacheKey = $this->buildCacheKey('grafik', [
            'institution_id' => $request->query('institution_id'),
            'selected_year' => $selectedYear,
        ]);

        $chartData = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($institutionFilter, $selectedYear) {
            $respondentUnsurScoresQuery = $this->buildRespondentUnsurScoresQuery($institutionFilter);

            return [
                'ikmBulanan' => $this->getIkmSeries($respondentUnsurScoresQuery, 'month', $selectedYear),
                'ikmTriwulan' => $this->getIkmSeries($respondentUnsurScoresQuery, 'quarter', $selectedYear),
                'ikmSemester' => $this->getIkmSeries($respondentUnsurScoresQuery, 'semester', $selectedYear),
                'ikmTahunan' => $this->getIkmSeries($respondentUnsurScoresQuery, 'year'),
            ];
        });

        $years = $yearsQuery
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderBy('y')
            ->pluck('y');

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
            'title',
            'selectedYear',
            'years',
            'selectedInstitution',
            'quarters',
            'semesters',
            'months',
            'institutionsall'
        ) + $chartData);
    }

    public function welcome(Request $request)
    {
        $title = 'Sistem Informasi Survei Kepuasan Masyarakat (SiSUKMA)';

        $baseQuery = $this->baseResponsesQuery();

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

        $years = (clone $baseQuery)
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderBy('y')
            ->pluck('y');

        $institutions = Institution::with(['mpp', 'group'])
            ->orderBy('name')
            ->get();

        $totalRespondents = (clone $baseQuery)->count();
        $avgIkm = DB::table('answers')->whereNull('deleted_at')->avg('score') ?? 0;
        $institutionCount = Institution::count();

        return view('welcome', compact(
            'title', 'years', 'institutions', 'quarters', 'semesters', 'months',
            'totalRespondents', 'avgIkm', 'institutionCount'
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
        $institutionFilter = $this->resolvePublicInstitutionFilter($request);

        $cacheKey = $this->buildCacheKey('publikasi', [
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
            'quarter' => $request->query('quarter'),
            'semester' => $request->query('semester'),
            'month' => $request->query('month'),
            'year' => $request->query('year'),
            'institution_id' => $request->query('institution_id'),
        ]);

        $metrics = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request, $institutionFilter, $unsurs) {
            $responsesQuery = $this->baseResponsesQuery();
            $this->applyDateFilterToQuery($responsesQuery, $request, 'responses.created_at');
            $selectedInstitution = $this->applyPublicInstitutionFilterToQuery($responsesQuery, $institutionFilter, 'responses.institution_id');

            $respondentUnsurScoresQuery = $this->buildRespondentUnsurScoresQuery(
                $institutionFilter,
                function (QueryBuilder $query, string $createdAtColumn) use ($request) {
                    $this->applyDateFilterToQuery($query, $request, $createdAtColumn);
                }
            );

            $perUnsurStats = DB::query()
                ->fromSub($respondentUnsurScoresQuery, 'respondent_unsur_scores')
                ->selectRaw('unsur_id, COUNT(*) as respondent_count, SUM(respondent_unsur_score) as total_score, AVG(respondent_unsur_score) as average_score')
                ->groupBy('unsur_id')
                ->get()
                ->keyBy('unsur_id');

            $totalPerUnsur = [];
            $averagePerUnsur = [];
            $weightedPerUnsur = [];
            $totalBobot = 0.0;

            foreach ($unsurs as $unsur) {
                $stat = $perUnsurStats->get($unsur->id);
                $total = $stat ? (float) $stat->total_score : 0.0;
                $average = $stat ? (float) $stat->average_score : 0.0;
                $weighted = $average * self::IKM_WEIGHT_PER_UNSUR;

                $totalPerUnsur[$unsur->id] = round($total, 2);
                $averagePerUnsur[$unsur->id] = round($average, 2);
                $weightedPerUnsur[$unsur->id] = round($weighted, 4);
                $totalBobot += $weighted;
            }

            $totalRespondents = (clone $responsesQuery)->count();
            $genderCounts = (clone $responsesQuery)
                ->selectRaw('gender, COUNT(*) as total')
                ->groupBy('gender')
                ->pluck('total', 'gender');
            $educationCounts = (clone $responsesQuery)
                ->selectRaw('education_id, COUNT(*) as total')
                ->groupBy('education_id')
                ->pluck('total', 'education_id');
            $occupationCounts = (clone $responsesQuery)
                ->selectRaw('occupation_id, COUNT(*) as total')
                ->groupBy('occupation_id')
                ->pluck('total', 'occupation_id');

            return [
                'selectedInstitution' => $selectedInstitution,
                'totalPerUnsur' => $totalPerUnsur,
                'averagePerUnsur' => $averagePerUnsur,
                'weightedPerUnsur' => $weightedPerUnsur,
                'totalBobot' => $totalBobot,
                'totalRespondents' => $totalRespondents,
                'genderCounts' => $genderCounts,
                'educationCounts' => $educationCounts,
                'occupationCounts' => $occupationCounts,
            ];
        });

        $nilaiSKM = $metrics['totalBobot'] * 25;

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

        $institutions = Institution::with(['mpp', 'group'])
            ->orderBy('name')
            ->get();

        $months = collect(range(1, 12))->mapWithKeys(function ($m) {
            return [$m => Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F')];
        });

        $years = $this->baseResponsesQuery()
                    ->selectRaw('YEAR(created_at) as year')
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

        $educationNames = $metrics['educationCounts']->isNotEmpty()
            ? Education::whereIn('id', $metrics['educationCounts']->keys())->pluck('level', 'id')
            : collect();
        $occupationNames = $metrics['occupationCounts']->isNotEmpty()
            ? Occupation::whereIn('id', $metrics['occupationCounts']->keys())->pluck('type', 'id')
            : collect();

        return [
            'respondents' => collect(),
            'unsurs' => $unsurs,
            'institutions' => $institutions,
            'quarters' => $quarters,
            'semesters' => $semesters,
            'months' => $months,
            'years' => $years,
            'title' => $title,
            'subtitle' => $subtitle,
            'totalPerUnsur' => $metrics['totalPerUnsur'],
            'respondentScores' => [],
            'averagePerUnsur' => $metrics['averagePerUnsur'],
            'weightedPerUnsur' => $metrics['weightedPerUnsur'],
            'totalBobot' => $metrics['totalBobot'],
            'nilaiSKM' => $nilaiSKM,
            'kategoriMutu' => $kategoriMutu,
            'selectedInstitution' => $metrics['selectedInstitution'],
            'totalRespondents' => $metrics['totalRespondents'],
            'genderCounts' => $metrics['genderCounts'],
            'educationCounts' => $metrics['educationCounts'],
            'educationNames' => $educationNames,
            'occupationCounts' => $metrics['occupationCounts'],
            'occupationNames' => $occupationNames,
        ];
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
