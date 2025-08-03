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
            
            <div class="row row-cards">
              @foreach($users as $user)
              <div class="col-md-6 col-lg-3">
                <div class="card">
                  <div class="card-body p-4 text-center">
                    <span class="avatar avatar-xl mb-3" style="background-image: url(./static/avatars/000m.jpg)"> </span>
                    <h3 class="m-0 mb-1"><a href="#">{{ $user->name }}</a></h3>
                    <div class="text-secondary">{{ $user->email }}</div>
                    <div class="text-secondary">{{ $user->institution->name }}</div>
                    <div class="mt-3">
                      {!! $user->is_approved==true ? '<span class="badge bg-success-lt">Disetujui</span>' : '<span class="badge bg-warning-lt">Menunggu Persetujuan</span>' !!}
                    </div>
                  </div>
                  <div class="d-flex">
                     @if(!$user->approved)
                        <form method="POST" action="{{ route('users.approve', $user) }}" class="card-btn">
                            @csrf
                            @method('put')
                            <button class="btn card-btn" type="submit">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /><path d="M15 19l2 2l4 -4" /></svg> Approve
                            </button>
                        </form>
                     @endif
                   
                    <a href="#" class="card-btn"><!-- Download SVG icon from http://tabler.io/icons/icon/phone -->
                      <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4c.348 0 .686 .045 1.009 .128" /><path d="M16 19h6" /></svg> Hapus</a>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
            
          </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
              {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
              
       </div>
        <!-- END PAGE BODY -->

@endsection