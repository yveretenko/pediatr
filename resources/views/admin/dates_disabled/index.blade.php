@extends('layouts.admin')

@section('title', 'Закриті дати')
@section('title_icon', 'fa fa-calendar-times')

@section('content')

<div type="text" id="dates_disabled"></div>

<div class="mt-4">
    <button class="btn btn-success" id="dates_disabled_save">Зберегти</button>
</div>

<script>
    let close_dates = @json($close_dates);
</script>

<script src="{{ asset('js/dates_disabled.js') }}?v={{ filemtime(public_path('js/dates_disabled.js')) }}" defer></script>

@endsection
