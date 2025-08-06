@extends('dashboard.layouts.tabler.main')

@section('container')
<div class="page-body">
    <div class="container-xl">
         <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col justify-content-center">
                <h2 class="page-title">{{ $title }}</h2>
              </div>
              
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
        <div class="row row-cards justify-content-center">
            <div class="col-md-4 col-sm-12">
                <div class="row-cards">
                    <div class="col-md-12 col-lg-12 mb-3">
                        <a href="#" class="card card-link card-link-pop">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col bold">Pendidikan</div>
                                    <div class="col align-self-end text-end"> 
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                    </div>
                                </div>
                            </div> 
                        </a>
                    </div>
                    <div class="col-md-12 col-lg-12 mb-3">
                        <a href="#" class="card card-link card-link-pop">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col bold">Pekerjaan</div>
                                    <div class="col align-self-end text-end">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                    </div> 
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-12 col-lg-12 mb-3">
                        <a href="#" class="card card-link card-link-pop">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col bold">Unsur</div>
                                    <div class="col align-self-end text-end">
                                        
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg> 
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-12 col-lg-12 mb-3">
                        <a href="#" class="card card-link card-link-pop">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col bold">Pertanyaan</div>
                                    <div class="col align-self-end text-end">
                                        
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                    </div> 
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection