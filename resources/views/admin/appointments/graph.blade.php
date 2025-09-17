@extends('layouts.admin')

@section('content')

<div class="form-row pl-2 pl-md-0 mb-3">
    <div class="col-sm-auto form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="group_by" id="group_by_week" value="week">

            <label class="form-check-label" for="group_by_week">
                тиждень (всі)
            </label>
        </div>
    </div>

    <div class="col-sm-auto form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="group_by" id="group_by_week_last_year" value="week" data-limit="last_year" checked>

            <label class="form-check-label" for="group_by_week_last_year">
                тиждень (останній рік)
            </label>
        </div>
    </div>

    <div class="col-sm-auto form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="group_by" id="group_by_month" value="month">

            <label class="form-check-label" for="group_by_month">
                місяць
            </label>
        </div>
    </div>

    <div class="col-sm-auto form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="group_by" id="group_by_year" value="year">

            <label class="form-check-label" for="group_by_year">
                рік
            </label>
        </div>
    </div>
</div>

<div id="graph"></div>

@push('scripts')
    <script src="https://code.highcharts.com/stock/highstock.src.js" defer></script>
    <script src="{{ asset('js/graph.js') }}" defer></script>
@endpush

@endsection
