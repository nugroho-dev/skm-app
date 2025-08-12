<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unsur;
use App\Models\Question;
use App\Models\Choice;

class QuestionController extends Controller
{
    public function index()
    {
        $unsurs = Unsur::with(['questions.choices'=> function($q) {
            $q->orderBy('score', 'asc');
        }])->orderBy('label_order', 'asc')->get();
        $title = 'Daftar Pertanyaan';
        return view('dashboard.questioner.question.index', compact('unsurs', 'title'));
    }
    public function create(Request $request)
    {
        // Ambil unsur_id dari query string
        $unsurId = $request->query('unsur_id');
        $title = 'Tambah Pertanyaan';
        // Ambil data unsur dari DB (optional untuk ditampilkan namanya)
        $unsur = Unsur::findOrFail($unsurId);

        return view('dashboard.questioner.question.create', compact('unsur', 'title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unsur_id' => 'required|exists:unsurs,id',
            'text' => 'required|string|max:255',
            'choices' => 'required|array|min:2', // Minimal 2 pilihan
            'choices.*.label' => 'required|string|max:255',
            'choices.*.score' => 'required|integer|min:0|max:100',
        ]);

        // Simpan pertanyaan
        $question = Question::create([
            'unsur_id' => $validated['unsur_id'],
            'text' => $validated['text'],
        ]);

        // Simpan pilihan jawaban
        foreach ($validated['choices'] as $choice) {
            Choice::create([
                'question_id' => $question->id,
                'label' => $choice['label'],
                'score' => $choice['score'],
            ]);
        }

        return redirect()
            ->route('question.index')
            ->with('success', 'Pertanyaan dan jawaban berhasil ditambahkan');
    }
    public function edit(Unsur $unsur, Question $question)
    {
        $question->load('choices');
        $title = 'Tambah Pertanyaan';
        return view('dashboard.questioner.question.edit', compact('unsur', 'question', 'title'));
    }

    public function update(Request $request, Unsur $unsur, Question $question)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'choices.*.label' => 'required|string|max:255',
            'choices.*.score' => 'required|numeric|min:0',
        ]);

        // Update pertanyaan
        $question->update([
            'text' => $validated['text'],
        ]);

        // Update jawaban
        foreach ($validated['choices'] as $choiceId => $choiceData) {
            $choice = $question->choices()->find($choiceId);
            if ($choice) {
                $choice->update($choiceData);
            }
        }

        return redirect()->route('question.index', $unsur->id)
            ->with('success', 'Pertanyaan dan jawaban berhasil diperbarui.');
    }
    public function destroy(Unsur $unsur, Question $question)
    {
        $question->choices()->delete();
        $question->delete();

        return redirect()->route('question.index', $unsur->id)
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
