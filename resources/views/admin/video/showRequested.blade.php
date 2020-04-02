@extends('admin.layouts.app')
@section('title', $video->title)
@section('content')
<div class="panel">
  <div class="panel-heading">
    <h3 class="panel-title text-capitalize">Video dari {{ $video->user->name() }}</h3>
    <div class="panel-actions">
      <div class="btn-group">
        <a href="{{ route('admin.videos.requested.accept', $video->id) }}" class="btn btn-outline btn-default btn-sm">Terima</a>
        <a href="{{ route('admin.videos.requested.reject', $video->id) }}" class="btn btn-outline btn-default btn-sm">Tolak</a>
      </div>
    </div>
  </div>
  <div class="card card-shadow rounded-0">
    <img class="card-img-top w-full" src="{{ $video->heroUrl() }}" alt="Card image cap">
    <div class="card-block">
      <h1 class="text-capitalize">{{ $video->title }}</h1>
      <p class="text-primary"><strong>{{ $video->category ? $video->category->name : '' }}</strong></p>
      <p>Oleh {{ $video->user->name() }} Di buat pada {{ $video->created_at->format('d-m-Y') }}</p>
      <div class="row">
        @foreach($video->videos as $vid)
        <div class="col-6">
          <div class="card">
            <div class="card-block bg-light">
              <video class="img-fluid" controls>
                <source src="{{ asset('storage/post/video/' . $vid->name) }}" />
              </video>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection