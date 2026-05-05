@extends('dashboard.layouts.tabler.main')

@section('container')


        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">Users</h2>
                
              </div>
            <p>
              @if(session('success'))
              <div class="alert alert-success">
                  {{ session('success') }}
              </div>
              @endif
              @if($users->count()==0)
              <div class="alert alert-success">
              <p class="text-center fs-3 entry">Tidak Ada Data Ditampilkan</p>
              </div>
              @endif
            </p>
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div >
                  <form method="GET" action="{{ route('users.index') }}" class="d-flex">
                  <input type="search" name="search" value="{{ request('search') }}" class="form-control d-inline-block w-100 me-3" placeholder="Search user…">
                  <button type="submit" class="btn btn-primary btn-3">Cari</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
        <!-- BEGIN PAGE BODY -->
        <div class="page-body">
          <div class="container-xl">
            
            <div class="card">
              <div class="table-responsive">
                <table class="table table-vcenter card-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Instansi</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users as $i => $user)
                    <tr>
                      <td>{{ $users->firstItem() + $i }}</td>
                      <td>{{ $user->name }}</td>
                      <td class="text-secondary">{{ $user->email }}</td>
                      <td class="text-secondary">{{ $user->institution->name }}</td>
                      <td>
                        {!! $user->is_approved ? '<span class="badge bg-success-lt">Disetujui</span>' : '<span class="badge bg-warning-lt">Menunggu Persetujuan</span>' !!}
                      </td>
                      <td>
                        <div class="d-flex gap-2">
                          <form method="POST" action="{{ route('users.approve', $user) }}">
                            @csrf
                            @method('put')
                            <button class="btn btn-sm btn-success" type="submit" {{ $user->is_approved ? 'disabled' : '' }}>
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-user-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /><path d="M15 19l2 2l4 -4" /></svg> Approve
                            </button>
                          </form>
                          <form method="POST" action="{{ route('users.reject', $user) }}">
                            @csrf
                            @method('put')
                            <button class="btn btn-sm btn-danger" type="submit" {{ !$user->is_approved ? 'disabled' : '' }}>
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-user-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4c.348 0 .686 .045 1.009 .128" /><path d="M16 19l2 -2m0 -2l-2 -2" /></svg> Reject
                            </button>
                          </form>
                          <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> Hapus
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            
          </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
              {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        <!-- END PAGE BODY -->

@endsection