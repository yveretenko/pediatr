@php use Carbon\Carbon; @endphp

@section('title', 'Файли')
@section('title_icon', 'far fa-file-alt')

@extends('layouts.admin')

@section('content')

    <div class="col-md-4 pl-0 mb-4">
        <input type="search" class="form-control" placeholder="пошук за назвою файла або іменем пацієнта"
               id="file_search">
    </div>

    @foreach ($appointments as $appointment)
        <div class="mb-2 file_container" style="line-height:normal;">
            <a href="{{ route('admin.appointments.file', $appointment->id) }}">
                {{ basename($appointment->file) }}
            </a>

            <div class="small">
                <span class="patient_name">{{ $appointment->name }}</span>
                {{ Carbon::parse($appointment->date)->format('d/m/Y') }}
            </div>
        </div>
    @endforeach

    <script src="{{ asset('js/files.js') }}?nocache={{ filemtime(public_path('js/files.js')) }}" defer></script>

@endsection
