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
                  <a href="{{  route('questioner.index') }}" class="btn btn-secondary m-1">Kembali</a>
                
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
                    <h3 class="card-title">Data Pertanyaan Survey</h3>
                  </div>
                  <div class="card-body border-bottom py-3">
                        <div class="accordion accordion-tabs" id="accordion-tabs">
                          @foreach($unsurs as $unsur)
                          <div class="accordion-item">
                            <div class="accordion-header">
                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#U{{ $unsur->label_order }}" aria-expanded="false">
                                U{{ $unsur->label_order ?? '-' }} {{ $unsur->name ?? '-' }}
                                <div class="accordion-button-toggle">
                                  <!-- Download SVG icon from http://tabler.io/icons/icon/chevron-down -->
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M6 9l6 6l6 -6"></path>
                                  </svg>
                                </div>
                              </button>
                            </div>
                            <div id="U{{ $unsur->label_order }}" class="accordion-collapse collapse" data-bs-parent="#accordion-tabs" style="">
                              <div class="accordion-body">
                                <a href="{{ route('question.create', ['unsur_id' => $unsur->id]) }}" class="btn btn-primary mb-3"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 11l0 6" /><path d="M9 14l6 0" /></svg> Tambah Pertanyaan</a>
                                @if($unsur->questions->isEmpty())
                                    <p class="text-muted">Belum ada pertanyaan untuk unsur ini.</p>
                                @else
                                <ul class="list-group">
                                  
                                    @foreach($unsur->questions as $question)
                                        <li class="list-group-item">
                                         
                                            <strong>{{ $question->text }}</strong> 
                                            
                                            <a href="{{ route('question.edit', [$unsur->id, $question->id]) }}" class="btn btn-sm btn-success"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg> Edit </a>
                                             <form action="{{ route('question.destroy', [$unsur->id, $question->id]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                              @csrf
                                              @method('DELETE') 
                                            <button type="submit" class="btn btn-sm btn-danger"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg> Hapus</button>
                                            </form>
                                            <ul class="mt-2">
                                                @forelse($question->choices as $choice)
                                                    <li> {{ $choice->score }}. {{ $choice->label }}</li>
                                                @empty
                                                    <li class="text-muted">Belum ada jawaban</li>
                                                @endforelse
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                                @endif
                              </div>
                            </div>
                          </div>
                          @endforeach
                        </div>
                  </div>
                  
                  <div class="card-footer">
                     
                  </div>
                </div>
              </div>
            </div>
          </div>
              @endsection