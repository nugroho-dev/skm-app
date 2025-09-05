<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SKM') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">SiSUKMA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-3">
                        <a class="nav-link fw-bold py-1 px-0 active" aria-current="page" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link fw-bold py-1 px-0" href="{{ url('/survey/grafik') }}">Grafik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold py-1 px-0" href="#" data-bs-toggle="modal" data-bs-target="#filterModal2"> Publikasi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <div class="modal fade" id="filterModal2" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{  route('survey.publikasi') }}" target="_blank" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Laporan SKM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Filter Rentang Tanggal -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>

                        <!-- Filter Triwulan -->
                        <div class="col-md-6">
                            <label class="form-label">Triwulan</label>
                            <select name="quarter" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($quarters as $q => $label)
                                    <option value="{{ $q }}" {{ request('quarter') == $q ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Semester -->
                        <div class="col-md-6">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($semesters as $s => $label)
                                    <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Bulan -->
                        <div class="col-md-6">
                            <label class="form-label">Bulan</label>
                            <select name="month" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Tahun -->
                        <div class="col-md-6">
                            <label class="form-label">Tahun</label>
                            <select name="year" class="form-select">
                                <option value="">-- Semua --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         @if($institutionsall->isNotEmpty())
                        <!-- Filter Instansi -->
                       
                        <div class="col-md-12">
                            <label class="form-label">Instansi</label>
                            <select name="institution_id" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="kota_ikm">Nilai IKM Kota Magelang</option>
                                <option value="mpp_ikm">Nilai IKM MPP</option>
                                @foreach($institutionsall as $inst)
                                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>