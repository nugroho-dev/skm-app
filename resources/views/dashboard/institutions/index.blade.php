@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body">
    <div class="container-xl">
    <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none mb-3" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">Instansi</h2>
                
              </div>
              <div class="col">
                <p>
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                </p>
            </div>
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                  <a href="#" class="btn btn-primary btn-3">Tambah Data</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
    
            <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Data Instansi</h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                    <div class="d-flex">
                     
                      <div class="ms-auto text-secondary">
                        Search:
                        <div class="ms-2 d-inline-block">
                           <form method="GET" action="{{ route('institutions.index') }}" class="d-flex">
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control d-inline-block w-100 me-3" placeholder="Search institutions…">
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
                          <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Select all invoices"></th>
                          <th class="w-1">
                            No.
                            <!-- Download SVG icon from http://tabler.io/icons/icon/chevron-up -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-sm icon-thick icon-2">
                              <path d="M6 15l6 -6l6 6"></path>
                            </svg>
                          </th>
                          <th>Instansi</th>
                          <th>Instansi Induk</th>
                          <th>Tenan MPP</th>
                          <th>Jumlah User</th>
                          <th>Jumlah Layanan</th>
                          
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($institutions as $index => $institution)
                        <tr>
                          <td><input class="form-check-input m-0 align-middle table-selectable-check" type="checkbox" aria-label="Select invoice"></td>
                          <td><span class="text-secondary">{{ $institutions->firstItem() + $index }}</span></td>
                          <td><a href="invoice.html" class="text-reset" tabindex="-1">{{ $institution->name }}</a></td>
                          <td>
                            
                            {{ $institution->group->name }}
                          </td>
                          <td>
                            
                            {{ $institution->mpp->name }}
                          </td>
                          <td>{{ $institution->users()->count() }} Penguna</td>
                          <td>{{ $institution->services()->count() }} Layanan</td>
                          
                          
                          <td class="text-end">
                            <span class="dropdown">
                              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Actions</button>
                              <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#"> Edit </a>
                                <a class="dropdown-item" href="#"> Hapus </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"> Layanan </a>
                              </div>
                            </span>
                          </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border px-4 py-2 text-center text-gray-500">Tidak ada data instansi.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer">
                     {{ $institutions->links('pagination::bootstrap-5') }}
                  </div>
                </div>
              </div>
            </div>
</div>
              @endsection