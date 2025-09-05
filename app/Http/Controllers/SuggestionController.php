<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Response as Respondent;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function index(Request $request)
{
     $title = 'Laporan SKM';
    $query = Respondent::with(['answers.question.unsur', 'institution', 'institution.mpp', 'institution.group'])
        ->orderBy('created_at');
    // === FILTER TANGGAL ===
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end   = Carbon::parse($request->end_date)->endOfDay();
        $query->whereBetween('created_at', [$start, $end]);
    } elseif ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', Carbon::parse($request->start_date));
    } elseif ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', Carbon::parse($request->end_date));
    }
    // === FILTER INSTANSI ===
    $selectedInstitution = null;
    if (Auth::user()->hasRole('super_admin')) {
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
        }
        $institutions = Institution::with(['mpp', 'group'])->orderBy('name')->get();

    } elseif (Auth::user()->hasRole('admin_instansi')) {
        $query->where('institution_id', Auth::user()->institution_id);
        $selectedInstitution = Auth::user()->institution->name ?? '-';
        $institutions = collect();
    }

    $respondents = $query->whereNotNull('suggestion')->paginate(50);

    // === Saran Responden ===
    

    return view('dashboard.suggestion.index', compact(
        'respondents',
        'institutions',
        'selectedInstitution',
        'title'
    ));
 }
 public function cetakPdf(Request $request)
    {
    // ambil data yang sama seperti cetak()
        $data = $this->getCetakData($request);

        $pdf = Pdf::loadView('dashboard.suggestion.cetak_pdf', $data)
                ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan_ikm.pdf');
    }
    private function getCetakData(Request $request)
    {
        
        $title = 'Laporan Saran SKM';
        $query = Respondent::with(['answers.question.unsur', 'institution', 'institution.mpp', 'institution.group'])
            ->orderBy('created_at');

        // === FILTER TANGGAL ===
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end   = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->start_date));
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->end_date));
        }

        $selectedInstitution = null;
    if (Auth::user()->hasRole('super_admin')) {
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
        }
        $institutions = Institution::with(['mpp', 'group'])->orderBy('name')->get();

    } elseif (Auth::user()->hasRole('admin_instansi')) {
        $query->where('institution_id', Auth::user()->institution_id);
        $selectedInstitution = Auth::user()->institution->name ?? '-';
        $institutions = collect();
    }


        $respondents = $query->whereNotNull('suggestion')->get();

        return compact('respondents', 'title', 'institutions', 'selectedInstitution');
    }
}
