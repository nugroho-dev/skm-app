
@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body ">
    <div class="container-xl ">
        <div class="row justify-content-center">

  <div class="card">
    <div class="card-header">
        <h3>Tambah Pertanyaan untuk Unsur: {{ $unsur->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('question.store') }}" method="POST">
            @csrf

            <input type="hidden" name="unsur_id" value="{{ $unsur->id }}">

            <div class="mb-3">
                <label class="form-label">Teks Pertanyaan</label>
                <input type="text" name="text" class="form-control @error('text') is-invalid @enderror" placeholder="Masukkan pertanyaan" value="{{ old('text') }}">
                @error('text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <h5>Pilihan Jawaban</h5>
            <div id="choices-wrapper">
                <div class="choice-item mb-3">
                    <input type="text" name="choices[0][label]" class="form-control mb-2" placeholder="Teks Jawaban">
                    <input type="number" name="choices[0][score]" class="form-control" placeholder="Score (0-100)">
                </div>
            </div>

            <button type="button" id="add-choice" class="btn btn-sm btn-secondary mt-2">+ Tambah Pilihan</button>

            <div class="text-end mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let choiceIndex = 1;
    document.getElementById('add-choice').addEventListener('click', function () {
        let wrapper = document.getElementById('choices-wrapper');
        let html = `
            <div class="choice-item mb-3">
                <input type="text" name="choices[${choiceIndex}][label]" class="form-control mb-2" placeholder="Teks Jawaban">
                <input type="number" name="choices[${choiceIndex}][score]" class="form-control" placeholder="Score (0-100)">
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', html);
        choiceIndex++;
    });
</script>
        
        </div>
    </div>
</div>
@endsection