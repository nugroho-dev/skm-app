@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body">
    <div class="container-xl">
    <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none mb-3" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">{{ $title }}</h2>
                
              </div>
              
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                  <a href="{{ auth()->user()->hasRole('super_admin') ? '/services/create/'.$institution_slug : route('instansi.services.create') }}" class="btn btn-primary btn-3">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 11l0 6" /><path d="M9 14l6 0" /></svg>
                    Tambah Data
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
            <div class="col-12">
                <p>
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                </p>
            </div>
            <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Layanan</h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                     
                      <div class="ms-auto text-secondary">
                        Search:
                        <div class="ms-2 d-inline-block">
                           <form method="GET" action="{{ auth()->user()->hasRole('super_admin') ? route('services.index') : route('instansi.services.index') }}" class="d-flex">
                            <input type="hidden" name="institution" value="{{ auth()->user()->hasRole('super_admin') ? request('institution', $institution_slug) : '' }}">
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control d-inline-block w-100 me-3" placeholder="Cari layanan…">
                            <button type="submit" class="btn btn-primary btn-3">Cari</button>
                            </form>
                          
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                      <thead>
                        <tr>
                          
                          <th class="w-1">No.</th>
                          <th>Nama Layanan</th>
                          
                          
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($services as $index => $service)
                        <tr>
                          
                          <td><span class="text-secondary">{{ $services->firstItem() + $index }}</span></td>
                          <td><a href="invoice.html" class="text-reset" tabindex="-1">{{ $service->name }}</a></td>
                          <td class="text-end">
                            <span class="dropdown">
                              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                              <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('institutions.edit', $service->slug ) }}"> Edit </a>
                                <form action="{{ route('institutions.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus instansi ini?')">
                                  @csrf
                                  @method('DELETE')
                                <button type="submit" class="dropdown-item" > Hapus </button>
                                </form>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"> Layanan </a>
                              </div>
                            </span>
                          </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border px-4 py-2 text-center text-gray-500">Tidak ada data layanan di instansi ini.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer">
                     {{ $services->links('pagination::bootstrap-5') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
              @endsection