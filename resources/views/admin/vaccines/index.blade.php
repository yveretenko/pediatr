@extends('layouts.admin')

@section('title', 'Вакцини')
@section('title_icon', 'fa fa-vial')

@section('content')

<table class="table table-striped" id="vaccines_grid">
    <thead>
    <tr>
        <th>Вакцина</th>
        <th>Тип</th>
        <th>Країна</th>
        <th>Ціна</th>
        <th>
            <span class="d-none d-lg-inline">tabletki.ua</span>
            <span class="d-inline d-lg-none"><i class="fa fa-external-link-alt"></i></span>
        </th>
        <th>Аналог</th>
        <th>Коментар</th>
        <th></th>
    </tr>
    </thead>
</table>

<div class="modal fade" id="vaccine_edit_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form>
                    <input type="hidden" name="id">

                    <div class="form-row">
                        <div class="col-12 form-group">
                            <label class="mb-0">Закупочна ціна</label>
                            <input type="number" name="purchase_price" class="form-control" step="100">
                        </div>

                        <div class="col-12 form-group">
                            <div class="form-check-inline mr-2">
                                <input class="form-check-input" type="checkbox" value="1" name="available" id="available">
                                <label class="form-check-label" for="available">
                                    В наявності
                                </label>
                            </div>
                        </div>

                        <div class="col form-group text-right">
                            <div class="text-danger small" id="vaccine_save_errors" style="line-height:normal;"></div>
                        </div>

                        <div class="col-auto form-group">
                            <button class="btn btn-success" id="vaccine_save">Зберегти</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/vaccines.js') }}?nocache={{ filemtime(public_path('js/vaccines.js')) }}" defer></script>

@endsection
