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

        // Total Responden
        $totalResponses = Response::when($institutionId, function($query) use ($institutionId) {
            return $query->where('institution_id', $institutionId);
        })->count();

        // Total Layanan
        $totalServices = Service::when($institutionId, function($query) use ($institutionId) {
            return $query->where('institution_id', $institutionId);
        })->count();

        // Hitung Rata-rata SKM (menggunakan metode yang sama dengan ReportController)
        $responses = Response::with(['answers.question.unsur'])
            ->when($institutionId, function($query) use ($institutionId) {
                return $query->where('institution_id', $institutionId);
            })
            ->get();

        $unsurs = Unsur::orderBy('label_order')->get();
        
        // Hitung skor per responden per unsur (pakai rata-rata)
        $respondentScores = [];
        foreach ($responses as $response) {
            foreach ($unsurs as $unsur) {
                $answers = $response->answers
                    ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id);

                $respondentScores[$response->id][$unsur->id] = $answers->count() > 0
                    ? round($answers->avg('score'))
                    : 0;
            }
        }

        // Hitung total per unsur, rata-rata, dan bobot
        $totalBobot = 0;
        $jumlahUnsur = max(1, $unsurs->count());
        $bobotPerUnsur = 0.11; // 1 / $jumlahUnsur jika ingin dinamis

        foreach ($unsurs as $unsur) {
            $scoresPerRespondent = collect($responses)->map(fn($r) =>
                $respondentScores[$r->id][$unsur->id] ?? 0
            );

            $average = $scoresPerRespondent->avg();
            $weighted = $average * $bobotPerUnsur;
            $totalBobot += $weighted;
        }

        // Hitung nilai SKM
        $averageSKM = round($totalBobot * 25, 2);

        // Kategori Mutu SKM
        $kategoriMutu = $this->getKategoriMutu($averageSKM);

        // SKM Bulan Ini (menggunakan metode yang sama)
        $currentMonthResponses = Response::with(['answers.question.unsur'])
            ->when($institutionId, function($query) use ($institutionId) {
                return $query->where('institution_id', $institutionId);
            })
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->get();

        $currentMonthScores = [];
        foreach ($currentMonthResponses as $response) {
            foreach ($unsurs as $unsur) {
                $answers = $response->answers
                    ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id);

                $currentMonthScores[$response->id][$unsur->id] = $answers->count() > 0
                    ? round($answers->avg('score'))
                    : 0;
            }
        }

        $currentMonthBobot = 0;
        foreach ($unsurs as $unsur) {
            $scoresPerRespondent = collect($currentMonthResponses)->map(fn($r) =>
                $currentMonthScores[$r->id][$unsur->id] ?? 0
            );

            $average = $scoresPerRespondent->avg();
            $weighted = $average * $bobotPerUnsur;
            $currentMonthBobot += $weighted;
        }

        $currentMonthSKM = round($currentMonthBobot * 25, 2);

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

        // Data SKM per Bulan (6 bulan terakhir)
        $monthlySKMData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthResponses = Response::with(['answers.question.unsur'])
                ->when($institutionId, function($query) use ($institutionId) {
                    return $query->where('institution_id', $institutionId);
                })
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->get();

            $monthScores = [];
            foreach ($monthResponses as $response) {
                foreach ($unsurs as $unsur) {
                    $answers = $response->answers
                        ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id);

                    $monthScores[$response->id][$unsur->id] = $answers->count() > 0
                        ? round($answers->avg('score'))
                        : 0;
                }
            }

            $monthBobot = 0;
            foreach ($unsurs as $unsur) {
                $scoresPerRespondent = collect($monthResponses)->map(fn($r) =>
                    $monthScores[$r->id][$unsur->id] ?? 0
                );

                $average = $scoresPerRespondent->avg();
                $weighted = $average * $bobotPerUnsur;
                $monthBobot += $weighted;
            }

            $monthSKM = round($monthBobot * 25, 2);

            $monthlySKMData[] = [
                'month' => $date->month,
                'year' => $date->year,
                'skm' => $monthSKM,
                'total' => $monthResponses->count()
            ];
        }

        // Data untuk grafik SKM per Unsur
        $unsurData = [];
        
        foreach ($unsurs as $unsur) {
            $scoresPerRespondent = collect($responses)->map(fn($r) =>
                $respondentScores[$r->id][$unsur->id] ?? 0
            );

            $average = $scoresPerRespondent->avg();
            $avgScore = round($average * 25, 2);
            
            $unsurData[] = [
                'name' => $unsur->name,
                'score' => $avgScore
            ];
        }

        // Data SKM per Layanan (Top 5)
        $servicesData = Service::when($institutionId, function($query) use ($institutionId) {
                return $query->where('institution_id', $institutionId);
            })
            ->withCount('responses')
            ->with(['responses.answers.question.unsur'])
            ->get()
            ->map(function($service) use ($unsurs, $bobotPerUnsur) {
                $serviceResponses = $service->responses;
                
                // Hitung skor per responden per unsur untuk layanan ini
                $serviceScores = [];
                foreach ($serviceResponses as $response) {
                    foreach ($unsurs as $unsur) {
                        $answers = $response->answers
                            ->filter(fn($answer) => $answer->question && $answer->question->unsur_id === $unsur->id);

                        $serviceScores[$response->id][$unsur->id] = $answers->count() > 0
                            ? round($answers->avg('score'))
                            : 0;
                    }
                }

                // Hitung bobot total
                $serviceBobot = 0;
                foreach ($unsurs as $unsur) {
                    $scoresPerRespondent = collect($serviceResponses)->map(fn($r) =>
                        $serviceScores[$r->id][$unsur->id] ?? 0
                    );

                    $average = $scoresPerRespondent->avg();
                    $weighted = $average * $bobotPerUnsur;
                    $serviceBobot += $weighted;
                }

                $avgSKM = round($serviceBobot * 25, 2);
                
                return [
                    'name' => $service->name,
                    'skm' => $avgSKM,
                    'total_responses' => $serviceResponses->count()
                ];
            })
            ->sortByDesc('skm')
            ->take(5)
            ->values();

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
