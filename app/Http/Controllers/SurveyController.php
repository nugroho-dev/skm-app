<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\Education;
use App\Models\Occupation;
use App\Models\Institution;
use App\Models\Service;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Mpp;
use App\Models\InstitutionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class SurveyController extends Controller
{
    public function selectCity()
    {
    $mpp = Mpp::where('slug', 'mpp-kota-magelang')->first();;
    $institutionGroup = InstitutionGroup::where('slug', 'kota-magelang')->first();

    return view('survey.select-city', compact('institutionGroup', 'mpp'));
    }
    public function selectInstitution(Request $request, $slug)
    {
        $search = $request->input('search');
        $mpp = Mpp::where('slug', $slug)->first();
        $institutionGroup = InstitutionGroup::where('slug', $slug)->first();

        if ($mpp) {
            $query = Institution::where('mpp_id', $mpp->id)->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')->with(['mpp'])->get();
        });
            $title = "Instansi dalam " . $mpp->name;
        } elseif ($institutionGroup) {
            $query = Institution::where('institution_group_id', $institutionGroup->id)->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')->with(['group'])->get();});
            $title = "Instansi dari " . $institutionGroup->name;
        } else {
            abort(404);
        }

    // Filter pencarian jika ada input search
    if ($search) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    $institutions = $query->orderBy('name')->get();

    return view('survey.select-institution', compact('institutions', 'title', 'slug', 'search'));
    }
    public function form($slug)
    {
        $institution = Institution::where('slug', $slug)->with('services')->firstOrFail();
        $questions = Question::with('choices', 'unsur')->get();
        $occupations = Occupation::all();
        $educations = Education::all();
        return view('survey.form', compact('institution', 'questions', 'occupations', 'educations'));
    }

    public function submit(Request $request, $slug)
    {
        
        // Ambil institution sesuai slug
        $institution = Institution::where('slug', $slug)->firstOrFail();
    
        $rules = [
           
            'gender'         => 'required|in:L,P',
            'age'            => 'required|integer|min:18|max:100',
            'education_id'   => 'required|exists:educations,id',
            'occupation_id'  => 'required|exists:occupations,id',
            'service_id'     => 'required|exists:services,id',
            'suggestion'     => 'required|string|max:1000|min:4|',
            'answers'        => 'required|array',
            'answers.*'      => 'required|integer|min:1|max:4',
            //'g-recaptcha-response' => 'required|captcha'
        ];

        //$messages = [
            //'g-recaptcha-response.required' => 'Verifikasi captcha diperlukan.',
            //'g-recaptcha-response.captcha'  => 'Captcha tidak valid.',
       // ];

        
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $response = Response::create([
                
                'gender'         => $request->gender,
                'age'            => $request->age,
                'education_id'   => $request->education_id,
                'occupation_id'  => $request->occupation_id,
                'institution_id' => $institution->id,
                'service_id'     => $request->service_id,
                'suggestion'     => $request->suggestion,
            ]);

            foreach ($request->answers as $question_id => $score) {
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $question_id,
                    'score'       => $score,
                ]);
            }

            DB::commit();
            return redirect()->route('survey.selectCity')->with('success', 'Terima kasih atas partisipasi Anda!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }
    }
}
