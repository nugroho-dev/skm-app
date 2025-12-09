<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\Service;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Unsur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $title = 'Dashboard';

        // Tentukan institution_id berdasarkan role
        if ($user->hasRole('super_admin')) {
            $institutionId = null; // Super admin melihat semua
        } else {
            $institutionId = $user->institution_id;
        }

        // Cache key berdasarkan institution
        $cacheKey = 'dashboard_stats_' . ($institutionId ?? 'all');

        // Cache selama 5 menit untuk mengurangi load
        $stats = Cache::remember($cacheKey, 300, function() use ($institutionId) {
            // Total Responden
            $totalResponses = Response::when($institutionId, function($query) use ($institutionId) {
                return $query->where('institution_id', $institutionId);
            })->count();

            // Total Layanan
            $totalServices = Service::when($institutionId, function($query) use ($institutionId) {
                return $query->where('institution_id', $institutionId);
            })->count();

            return compact('totalResponses', 'totalServices');
        });

        $totalResponses = $stats['totalResponses'];
        $totalServices = $stats['totalServices'];

        $unsurs = Unsur::orderBy('label_order')->get();
        $unsurs = Unsur::orderBy('label_order')->get();
        
        // OPTIMASI: Hitung SKM menggunakan query aggregation untuk performa
        $averageSKM = $this->calculateSKMOptimized($institutionId, $unsurs);
        
        // Kategori Mutu SKM
        $kategoriMutu = $this->getKategoriMutu($averageSKM);

        // SKM Bulan Ini - optimized
        $currentMonthSKM = $this->calculateSKMOptimized($institutionId, $unsurs, now()->year, now()->month);

        // Data untuk grafik Responden per Bulan (6 bulan terakhir)
        $monthlyData = Response::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->when($institutionId, function($query) use ($institutionId) {
                return $query->where('institution_id', $institutionId);
            })
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Data SKM per Bulan (6 bulan terakhir) - optimized
        $monthlySKMData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthSKM = $this->calculateSKMOptimized($institutionId, $unsurs, $date->year, $date->month);
            
            $monthCount = Response::when($institutionId, function($query) use ($institutionId) {
                    return $query->where('institution_id', $institutionId);
                })
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlySKMData[] = [
                'month' => $date->month,
                'year' => $date->year,
                'skm' => $monthSKM,
                'total' => $monthCount
            ];
        }

        // Data untuk grafik SKM per Unsur - optimized
        $unsurData = $this->calculateSKMPerUnsur($institutionId, $unsurs);

        // Data SKM per Layanan (Top 5) - optimized
        $servicesData = $this->calculateTopServices($institutionId, $unsurs);

        return view('dashboard.index', compact(
            'user', 
            'title', 
            'totalResponses', 
            'totalServices', 
            'averageSKM', 
            'kategoriMutu',
            'currentMonthSKM',
            'monthlyData',
            'monthlySKMData',
            'unsurData',
            'servicesData'
        ));
    }

    /**
     * Optimasi perhitungan SKM menggunakan query aggregation
     * Menghindari loading semua data ke memory
     */
    private function calculateSKMOptimized($institutionId, $unsurs, $year = null, $month = null)
    {
        $bobotPerUnsur = 0.11;
        $totalBobot = 0;

        foreach ($unsurs as $unsur) {
            // Query agregasi langsung di database dengan proper subquery
            $subquery = DB::table('responses')
                ->join('answers', 'responses.id', '=', 'answers.response_id')
                ->join('questions', 'answers.question_id', '=', 'questions.id')
                ->when($institutionId, function($query) use ($institutionId) {
                    return $query->where('responses.institution_id', $institutionId);
                })
                ->when($year, function($query) use ($year) {
                    return $query->whereYear('responses.created_at', $year);
                })
                ->when($month, function($query) use ($month) {
                    return $query->whereMonth('responses.created_at', $month);
                })
                ->where('questions.unsur_id', $unsur->id)
                ->groupBy('responses.id')
                ->selectRaw('ROUND(AVG(answers.score)) as avg_score_per_response');

            $avgScore = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
                ->mergeBindings($subquery)
                ->selectRaw('AVG(avg_score_per_response) as overall_avg')
                ->value('overall_avg');

            $avgScore = $avgScore ?? 0;
            $weighted = $avgScore * $bobotPerUnsur;
            $totalBobot += $weighted;
        }

        return round($totalBobot * 25, 2);
    }

    /**
     * Hitung SKM per unsur dengan optimasi
     */
    private function calculateSKMPerUnsur($institutionId, $unsurs)
    {
        $unsurData = [];

        foreach ($unsurs as $unsur) {
            // Query agregasi dengan proper subquery
            $subquery = DB::table('responses')
                ->join('answers', 'responses.id', '=', 'answers.response_id')
                ->join('questions', 'answers.question_id', '=', 'questions.id')
                ->when($institutionId, function($query) use ($institutionId) {
                    return $query->where('responses.institution_id', $institutionId);
                })
                ->where('questions.unsur_id', $unsur->id)
                ->groupBy('responses.id')
                ->selectRaw('ROUND(AVG(answers.score)) as avg_score_per_response');

            $avgScore = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
                ->mergeBindings($subquery)
                ->selectRaw('AVG(avg_score_per_response) as overall_avg')
                ->value('overall_avg');

            $avgScore = ($avgScore ?? 0) * 25;

            $unsurData[] = [
                'name' => $unsur->name,
                'score' => round($avgScore, 2)
            ];
        }

        return $unsurData;
    }

    /**
     * Hitung top 5 layanan dengan optimasi
     */
    private function calculateTopServices($institutionId, $unsurs)
    {
        $bobotPerUnsur = 0.11;
        
        // Ambil service dengan limit untuk performa
        $services = Service::when($institutionId, function($query) use ($institutionId) {
                return $query->where('institution_id', $institutionId);
            })
            ->withCount('responses')
            ->having('responses_count', '>', 0)
            ->limit(50) // Batasi untuk performa
            ->get();

        $servicesData = $services->map(function($service) use ($unsurs, $bobotPerUnsur) {
            $totalBobot = 0;

            foreach ($unsurs as $unsur) {
                // Query agregasi per service dengan proper subquery
                $subquery = DB::table('responses')
                    ->join('answers', 'responses.id', '=', 'answers.response_id')
                    ->join('questions', 'answers.question_id', '=', 'questions.id')
                    ->where('responses.service_id', $service->id)
                    ->where('questions.unsur_id', $unsur->id)
                    ->groupBy('responses.id')
                    ->selectRaw('ROUND(AVG(answers.score)) as avg_score_per_response');

                $avgScore = DB::table(DB::raw("({$subquery->toSql()}) as subquery"))
                    ->mergeBindings($subquery)
                    ->selectRaw('AVG(avg_score_per_response) as overall_avg')
                    ->value('overall_avg');

                $avgScore = $avgScore ?? 0;
                $weighted = $avgScore * $bobotPerUnsur;
                $totalBobot += $weighted;
            }

            $avgSKM = round($totalBobot * 25, 2);

            return [
                'name' => $service->name,
                'skm' => $avgSKM,
                'total_responses' => $service->responses_count
            ];
        })
        ->sortByDesc('skm')
        ->take(5)
        ->values();

        return $servicesData;
    }

    private function getKategoriMutu($skm)
    {
        if ($skm >= 88.31 && $skm <= 100) {
            return ['kategori' => 'A', 'mutu' => 'Sangat Baik', 'color' => 'success'];
        } elseif ($skm >= 76.61 && $skm <= 88.30) {
            return ['kategori' => 'B', 'mutu' => 'Baik', 'color' => 'info'];
        } elseif ($skm >= 65.00 && $skm <= 76.60) {
            return ['kategori' => 'C', 'mutu' => 'Kurang Baik', 'color' => 'warning'];
        } else {
            return ['kategori' => 'D', 'mutu' => 'Tidak Baik', 'color' => 'danger'];
        }
    }
}
