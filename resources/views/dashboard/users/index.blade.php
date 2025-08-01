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
                <div class="d-flex">
                  <input type="search" class="form-control d-inline-block w-100 me-3" placeholder="Search user…">
                  <a href="#" class="btn btn-primary btn-3">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2">
                      <path d="M12 5l0 14"></path>
                      <path d="M5 12l14 0"></path>
                    </svg>
                    New user
                  </a>
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
        <div class="d-flex mt-4">
              <ul class="pagination ms-auto">
                <li class="page-item disabled">
                  <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/chevron-left -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                      <path d="M15 6l-6 6l6 6"></path>
                    </svg>
                  </a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item active">
                  <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">4</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">5</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/chevron-right -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                      <path d="M9 6l6 6l-6 6"></path>
                    </svg>
                  </a>
                </li>
              </ul>
            </div>
        <!-- END PAGE BODY -->

@endsection