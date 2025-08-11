
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
      <div class="card">
    <div class="card-header">
        <h4>Edit Pertanyaan untuk Unsur: {{ $unsur->name }}</h4>
</div>
    <div class="card-body">
        <form action="{{ route('question.update', [$unsur->id, $question->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="question_text" class="form-label">Pertanyaan</label>
                <input type="text" name="text" id="text" 
                      class="form-control @error('text') is-invalid @enderror"
                      value="{{ old('text', $question->text) }}" required>
                @error('text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h5>Jawaban</h5>
            @foreach($question->choices as $choice)
                <div class="mb-3">
                    <label>Jawaban</label>
                    <input type="text" name="choices[{{ $choice->id }}][label]" 
                          value="{{ old('choices.' . $choice->id . '.label', $choice->label) }}"
                          class="form-control @error('choices.' . $choice->id . '.label') is-invalid @enderror">
                    @error('choices.' . $choice->id . '.label')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label>Skor</label>
                    <input type="number" name="choices[{{ $choice->id }}][score]" step="1" min="0"
                          value="{{ old('choices.' . $choice->id . '.score', $choice->score) }}"
                          class="form-control @error('choices.' . $choice->id . '.score') is-invalid @enderror">
                    @error('choices.' . $choice->id . '.score')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('question.index', $unsur->id) }}" class="btn btn-secondary">Batal</a>
        </form>
      </div>
</div>
    </div>
</div>
@endsection