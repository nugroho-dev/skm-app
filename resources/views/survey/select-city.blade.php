@extends('layouts.app')

@section('content')
<div class="p-5 mb-4 bg-body-tertiary rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Pilih Lokasi Survei</h1>
        <p>
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
        </p>
    </div>
</div>
<div class="row align-items-md-stretch">
    <div class="col-md-6">
        @if($institutionGroup)
        <div class="h-100 p-5 text-bg-dark rounded-3">
            <h2>{{ $institutionGroup->name }}</h2>
            <p>
               Survei ini mencakup seluruh layanan publik yang tersedia di lingkup Pemerintah Kota Magelang, termasuk dinas, badan, dan instansi lainnya yang tergabung maupun tidak tergabung dalam Mal Pelayanan Publik (MPP).
               <br> 📌 Contoh instansi: Dinas Pendidikan, Dinas Kesehatan, Dinas Lingkungan Hidup, dan lainnya.
            </p>
            <a href="{{ route('survey.selectInstitution',  $institutionGroup->slug) }}" class="btn btn-outline-light" type="button" >Pilih Instansi</a>
        </div>
        @endif
    </div>
    <div class="col-md-6">
         @if($mpp)
        <div class="h-100 p-5 bg-body-tertiary border rounded-3">
            <h2>{{ $mpp->name }}</h2>
            <p>
                Survei ini khusus untuk layanan yang diberikan melalui Mal Pelayanan Publik (MPP) Kota Magelang.MPP merupakan pusat layanan terpadu dari berbagai instansi yang mempermudah masyarakat dalam mengakses berbagai layanan dalam satu tempat.<br>📌 Contoh instansi di MPP: Disdukcapil, Imigrasi, Samsat, BPJS, dan lainnya.
            </p>
            <a href="{{ route('survey.selectInstitution', $mpp->slug) }}" class="btn btn-outline-secondary" type="button" >Pilih Instansi</a>
        </div>
         @endif
    </div>
</div>
<footer class="pt-3 mt-4 text-body-secondary border-top">
    © 2025
</footer>

@endsection