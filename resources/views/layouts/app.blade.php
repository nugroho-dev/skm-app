<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SKM') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --skm-bg: #f7f4ee;
            --skm-text: #10243c;
            --skm-muted: #5b6b7f;
            --skm-primary: #0f5f7a;
            --skm-primary-strong: #0a4b61;
            --skm-accent: #d4a74a;
            --skm-panel: #ffffff;
            --skm-border: #e4ddcf;
        }

        body {
            font-family: "Figtree", system-ui, -apple-system, sans-serif;
            color: var(--skm-text);
            min-height: 100vh;
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(212, 167, 74, 0.14), transparent 65%),
                radial-gradient(800px 600px at 95% 10%, rgba(15, 95, 122, 0.12), transparent 60%),
                var(--skm-bg);
        }

        main {
            position: relative;
        }

        main::before,
        main::after {
            content: "";
            position: fixed;
            z-index: -1;
            pointer-events: none;
            border-radius: 999px;
        }

        main::before {
            width: 260px;
            height: 260px;
            left: -70px;
            top: 140px;
            background: rgba(15, 95, 122, 0.08);
        }

        main::after {
            width: 220px;
            height: 220px;
            right: -70px;
            bottom: 100px;
            background: rgba(212, 167, 74, 0.13);
        }

        .skm-heading {
            font-family: "Playfair Display", Georgia, serif;
            letter-spacing: -0.02em;
        }

        .skm-navbar {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--skm-border);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .skm-brand {
            font-family: "Playfair Display", Georgia, serif;
            font-size: 1.35rem;
            color: var(--skm-text);
        }

        .skm-nav-link {
            color: var(--skm-text) !important;
            opacity: 0.9;
        }

        .skm-nav-link:hover {
            color: var(--skm-primary) !important;
            opacity: 1;
        }

        .skm-page {
            max-width: 1100px;
            margin-top: 0.75rem;
            margin-bottom: 2rem;
            padding: 1rem;
            border: 1px solid var(--skm-border);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(4px);
            box-shadow: 0 16px 40px -34px rgba(16, 36, 60, 0.7);
        }

        .skm-panel {
            background: var(--skm-panel);
            border: 1px solid var(--skm-border);
            border-radius: 14px;
            box-shadow: 0 12px 30px -24px rgba(16, 36, 60, 0.5);
        }

        .skm-panel-header {
            padding: 0.85rem 1rem;
            color: #fff;
            font-weight: 700;
            font-size: 0.92rem;
            border-top-left-radius: 13px;
            border-top-right-radius: 13px;
            letter-spacing: 0.02em;
        }

        .skm-panel-header.is-primary {
            background: #1f6feb;
        }

        .skm-panel-header.is-success {
            background: #0f9d74;
        }

        .skm-panel-header.is-info {
            background: #0ea5c6;
        }

        .skm-button-primary {
            background: var(--skm-primary);
            border-color: var(--skm-primary);
            color: #fff;
        }

        .skm-button-primary:hover {
            background: var(--skm-primary-strong);
            border-color: var(--skm-primary-strong);
            color: #fff;
        }

        .skm-button-soft {
            background: #fff;
            color: var(--skm-text);
            border: 1px solid var(--skm-border);
        }

        .skm-button-soft:hover {
            background: #f5f8fb;
            border-color: #cfd9e6;
            color: var(--skm-primary);
        }

        .wizard-step {
            border: 1px solid var(--skm-border);
            border-radius: 10px;
            padding: 0.6rem 0.7rem;
            text-align: center;
            background: #fff;
            font-size: 0.78rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }

        .wizard-step.is-active {
            border-color: var(--skm-primary);
            color: var(--skm-primary);
            font-weight: 700;
            box-shadow: 0 8px 20px -18px rgba(15, 95, 122, 0.8);
        }

        .wizard-step.is-done {
            border-color: rgba(15, 95, 122, 0.25);
            background: rgba(15, 95, 122, 0.06);
            color: var(--skm-primary);
        }

        .skm-filter-modal .modal-content {
            border: 1px solid rgba(228, 221, 207, 0.92);
            border-radius: 18px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(212, 167, 74, 0.08), transparent 38%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 246, 241, 0.98));
            box-shadow: 0 28px 52px -28px rgba(16, 36, 60, 0.52);
        }

        .skm-filter-modal {
            z-index: 1080;
        }

        .modal-backdrop.show {
            z-index: 1070;
        }

        .skm-filter-modal .modal-header {
            background: transparent;
            color: var(--skm-text);
            border-bottom: 1px solid rgba(228, 221, 207, 0.9);
            padding: 1.25rem 1.25rem 1rem;
        }

        .skm-filter-modal .modal-title {
            font-family: "Playfair Display", Georgia, serif;
            letter-spacing: -0.01em;
            font-size: clamp(1.2rem, 1.8vw, 1.55rem);
            margin-bottom: 0.25rem;
        }

        .skm-filter-modal .modal-subtitle {
            color: var(--skm-muted);
            font-size: 0.88rem;
            margin: 0;
            line-height: 1.6;
        }

        .skm-filter-modal .modal-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--skm-primary);
            background: rgba(15, 95, 122, 0.1);
            border: 1px solid rgba(15, 95, 122, 0.22);
            border-radius: 999px;
            padding: 0.33rem 0.65rem;
            margin-bottom: 0.55rem;
        }

        .skm-filter-modal .modal-body {
            padding: 1.1rem 1.25rem 1.25rem;
        }

        .skm-filter-modal .skm-filter-panel {
            border: 1px solid rgba(228, 221, 207, 0.92);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 12px 24px -26px rgba(16, 36, 60, 0.72);
            padding: 1rem;
        }

        .skm-filter-modal .skm-filter-panel-title {
            font-family: "Playfair Display", Georgia, serif;
            font-size: 1.08rem;
            color: var(--skm-text);
            margin: 0 0 0.2rem;
        }

        .skm-filter-modal .skm-filter-panel-note {
            margin: 0;
            font-size: 0.83rem;
            color: var(--skm-muted);
            line-height: 1.55;
        }

        .skm-filter-modal .form-label {
            font-weight: 600;
            color: var(--skm-text);
            font-size: 0.84rem;
            margin-bottom: 0.38rem;
        }

        .skm-filter-modal .form-control,
        .skm-filter-modal .form-select {
            border: 1px solid var(--skm-border);
            border-radius: 10px;
            padding: 0.6rem 0.78rem;
            font-size: 0.9rem;
            background-color: #fff;
            color: var(--skm-text);
        }

        .skm-filter-modal .form-control:focus,
        .skm-filter-modal .form-select:focus {
            border-color: rgba(15, 95, 122, 0.48);
            box-shadow: 0 0 0 0.2rem rgba(15, 95, 122, 0.14);
        }

        .skm-filter-modal .modal-footer {
            border-top: 1px solid rgba(228, 221, 207, 0.9);
            padding: 1rem 1.25rem 1.2rem;
            gap: 0.65rem;
        }

        .skm-filter-modal .modal-footer .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 0.58rem 1rem;
        }

        .skm-filter-modal .btn-reset {
            background: #fff;
            color: var(--skm-text);
            border: 1px solid var(--skm-border);
        }

        .skm-filter-modal .btn-reset:hover {
            background: #f6f8fb;
            border-color: #cfd9e6;
            color: var(--skm-primary);
        }

        .skm-filter-modal .btn-close {
            filter: none;
            opacity: 0.72;
        }

        .skm-filter-modal .btn-close:hover {
            opacity: 1;
        }

        @media (max-width: 767.98px) {
            .skm-filter-modal .modal-body,
            .skm-filter-modal .modal-header,
            .skm-filter-modal .modal-footer {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        @media (max-width: 767.98px) {
            .skm-page {
                border-radius: 14px;
                padding: 0.75rem;
                margin-top: 0.35rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg skm-navbar mb-4">
        <div class="container">
            <a class="navbar-brand skm-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <span class="d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:10px;background:var(--skm-primary);color:#fff;font-size:0.78rem;font-weight:700;">SKM</span>
                <span>SiSUKMA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if(request()->routeIs('survey.welcome'))
                        <li class="nav-item me-3">
                            <a class="nav-link skm-nav-link fw-semibold py-1 px-0" href="#tentang">Tentang</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link skm-nav-link fw-semibold py-1 px-0" href="#alur">Alur Survei</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link skm-nav-link fw-semibold py-1 px-0" href="#statistik">Statistik</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link skm-nav-link fw-semibold py-1 px-0" href="{{ route('survey.grafik') }}">Grafik</a>
                        </li>
                        <li class="nav-item me-3">
                            <button type="button" class="nav-link skm-nav-link fw-semibold py-1 px-0 border-0 bg-transparent" data-open-modal="#filterModal2">Publikasi</button>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link skm-nav-link fw-semibold py-1 px-0" href="#faq">FAQ</a>
                        </li>
                    @else
                        <li class="nav-item me-3">
                            <a class="nav-link skm-nav-link fw-semibold py-1 px-0 active" aria-current="page" href="{{ url('/') }}">Beranda</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link skm-nav-link fw-semibold py-1 px-0" href="{{ url('/survey/grafik') }}">Grafik</a>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link skm-nav-link fw-semibold py-1 px-0 border-0 bg-transparent" data-open-modal="#filterModal2">Publikasi</button>
                        </li>
                    @endif
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-sm skm-button-primary px-3" href="{{ route('survey.selectCity') }}">Mulai Survei</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container skm-page">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-open-modal]');
            if (!trigger) {
                return;
            }

            event.preventDefault();

            const selector = trigger.getAttribute('data-open-modal');
            const modalElement = document.querySelector(selector);
            if (!modalElement || !window.bootstrap || !window.bootstrap.Modal) {
                return;
            }

            const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        });
    </script>
    @stack('scripts')
    @php
        $filterQuarters = $quarters ?? [];
        $filterSemesters = $semesters ?? [];
        $filterMonths = $months ?? [];
        $filterYears = $years ?? [];
        $filterInstitutions = $institutionsall ?? ($institutions ?? collect());
    @endphp
    <div class="modal fade skm-filter-modal" id="filterModal2" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{  route('survey.publikasi') }}" target="_blank" method="GET">
                <div class="modal-header">
                    <div>
                        <span class="modal-badge">Publikasi Resmi</span>
                        <h5 class="modal-title" id="filterModalLabel">Atur Filter Laporan SKM</h5>
                        <p class="modal-subtitle">Pilih periode dan instansi untuk menghasilkan publikasi data survei yang relevan.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="skm-filter-panel mb-3">
                        <h6 class="skm-filter-panel-title">Periode Laporan</h6>
                        <p class="skm-filter-panel-note">Gunakan salah satu opsi filter waktu: rentang tanggal, triwulan, semester, bulan, atau tahun.</p>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Triwulan</label>
                                <select name="quarter" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @foreach($filterQuarters as $q => $label)
                                        <option value="{{ $q }}" {{ request('quarter') == $q ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Semester</label>
                                <select name="semester" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @foreach($filterSemesters as $s => $label)
                                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Bulan</label>
                                <select name="month" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @foreach($filterMonths as $num => $name)
                                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tahun</label>
                                <select name="year" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @foreach($filterYears as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="skm-filter-panel">
                        <h6 class="skm-filter-panel-title">Cakupan Instansi</h6>
                        <p class="skm-filter-panel-note">Pilih agregat kota, agregat MPP, atau instansi tertentu yang tersedia untuk publikasi.</p>
                        <div class="row g-3 mt-1">
                            <div class="col-md-12">
                            <label class="form-label">Instansi</label>
                            <select name="institution_id" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="kota_ikm" {{ request('institution_id') === 'kota_ikm' ? 'selected' : '' }}>Nilai IKM Kota Magelang</option>
                                <option value="mpp_ikm" {{ request('institution_id') === 'mpp_ikm' ? 'selected' : '' }}>Nilai IKM MPP</option>
                                @foreach($filterInstitutions as $inst)
                                    @if(!empty($inst->slug))
                                        @php $instFilterValue = 'inst:' . $inst->slug; @endphp
                                        <option value="{{ $instFilterValue }}" {{ request('institution_id') === $instFilterValue ? 'selected' : '' }}>
                                            {{ $inst->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('survey.welcome') }}" class="btn btn-reset">Reset Filter</a>
                    <button type="submit" class="btn skm-button-primary">Tampilkan Publikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>