@extends('admin::layouts.master')

@section('content')
  <div class="container-fluid">
    <div class="panel panel-bordered">
      <div class="panel-heading">
        <h3 class="panel-title">Data Permintaan Menjadi MiniTutor</h3>
        <div class="panel-actions">
          <a class="btn btn-sm btn-primary" href="{{ route('admin.minitutors.requests') }}">Back</a>
          <button class="btn btn-sm btn-success" onclick="$('#form-accept-request').submit()">Terima</button>
          <button class="btn btn-sm btn-danger" onclick="$('#form-reject-request').submit()">Tolak</button>

          <form action="{{ route('admin.minitutors.request.reject', $data['id']) }}" id="form-reject-request" method="post" class="d-none">
            @csrf
            @method('put')
          </form>
          <form action="{{ route('admin.minitutors.request.accept', $data['id']) }}" id="form-accept-request" method="post" class="d-none">
            @csrf
            @method('put')
          </form>
        </div>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-lg-2 col-md-4 mb-30 text-center">
            <a href="#show-activity" class="avatar avatar-100">
              <img alt="photo" src="{{ $data->user->avatarUrl() }}">
            </a>
            <a download href="{{ asset('storage/minitutor/cv/' . $data->cv) }}" class="btn btn-block btn-primary btn-sm mt-15">DOWNLOAD CV</a>
          </div>
          <div class="col-lg-10 col-md-6 mb-3">
            <h5 class="mb-0 mt-0">Nama</h5>
            <p class="mb-30"><a href="{{ route('admin.users.show', $data->user->id) }}">{{ $data->user->name() }}</a></p>
            <h5 class="mb-0 mt-30">Username</h5>
            <p class="mb-30">{{ $data->user->username }}</p>
            <h5 class="mb-0 mt-30">Email</h5>
            <p class="mb-30">{{ $data->user->email }}</p>
            <h5 class="mb-0 mt-30">Email di verifikasi</h5>
            <p class="mb-30">{{ $data->user->email_verified_at ?? 'Tidak' }}</p>

            <h5 class="mb-0 mt-30">Pedidikan terakhir</h5>
            <p class="mb-30"><strong>{{ $data->last_education }}</strong></p>
            <h5 class="mb-0 mt-30">Universitas</h5>
            <p class="mb-30"><strong>{{ $data->university }}</strong></p>
            <h5 class="mb-0 mt-30">Kota dan negara tempat study</h5>
            <p class="mb-30"><strong>{{ $data->city_and_country_of_study }}</strong></p>
            <h5 class="mb-0 mt-30">Jurusan</h5>
            <p class="mb-30"><strong>{{ $data->majors }}</strong></p>

            <h5 class="mb-0 mt-30">Spesialisasi/Minat bakat</h5>
            <p class="mb-30">{{ $data->interest_talent }}</p>
            <h5 class="mb-0 mt-30">Kontak (Whatsapp)</h5>
            <p class="mb-30">{{ $data->contact }} @if($data->join_group) (tambahkan ke group whatsapp minitutor jika diterima) @endif </p>
            <h5 class="mb-0 mt-30">Motivasi</h5>
            <p class="mb-30">{{ $data->reason }}</p>
            <h4 class="mt-30">Ekspektasi</h4>
            <p class="mb-30">{{ $data->expectation }}</p>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
