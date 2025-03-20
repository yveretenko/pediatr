var datatable;
var grid=$('#vaccines_grid');
let edit_vaccine_modal=$('#vaccine_edit_modal');

var grid_default_params={
    pageLength: 50,
    processing: true,
    serverSide: true,
    bLengthChange: false,
    bFilter: false,
    responsive: true,
    autoWidth: false,
    pagingType: 'numbers',
    dom: '<"datatable_container position-relative"rt><"mt-2">'
};

$(document).ready(function(){
    datatable=grid.DataTable(jQuery.extend(grid_default_params, {
        order: [[0, 'asc']],
        responsive: true,
        ajax: {
            url: '/admin/vaccines/filter/',
            error: function(data){
                if (data.status===401)
                    window.location.href='/admin?redirect_to='+encodeURI(window.location.pathname);
                else
                    alert('Виникла помилка при завантаженні даних');
            }
        },
        language: {
            processing: "<div class='alert alert-warning border-warning col-10 col-sm-8 col-md-4 mx-auto' role='alert'>оновлюю <i class='fas fa-spinner fa-spin ml-2'></i></div>"
        },
        createdRow: function(row, data, dataIndex){
            if(!data.available){
                $(row).addClass('table-danger');
            }
        },
        columns: [
            {
                data: 'name',
                responsivePriority: 1,
                render: function(data, type, row){
                    return '<div class="text-nowrap">'+data+'</div>'+(row.type ? '<div class="text-muted small d-sm-none">'+row.type+'</span>' : '');
                },
            },
            {
                data: 'type',
                sortable: false,
                responsivePriority: 3
            },
            {
                data: 'country',
                sortable: false,
                responsivePriority: 10
            },
            {
                data: 'purchase_price',
                render: function(data, type, row){
                    return row.purchase_price ? Math.ceil(((row.purchase_price+700)*1.05)/100)*100 : '';
                },
                responsivePriority: 1,
                className: 'font-weight-bold text-center'
            },
            {
                data: 'link',
                sortable: false,
                render: function(data){
                    return data ? '<a href="'+data+'" target="_blank"><span class="d-none d-lg-inline">tabletki.ua</span><span class="d-inline d-lg-none"><i class="fa fa-external-link-alt"></i></span></A>' : '';
                },
                responsivePriority: 4,
                className: 'text-center'
            },
            {
                data: 'analogue_vaccine',
                sortable: false,
                responsivePriority: 8
            },
            {
                data: 'comment',
                sortable: false,
                responsivePriority: 7
            },
            {
                data: 'id',
                sortable: false,
                render: function(){
                    return '<button class="btn btn-success btn-sm vaccine_edit"><i class="fa fa-pencil-alt"></i><span class="d-none d-md-inline"> Редагувати</span></button>';
                },
                className: 'text-nowrap',
                responsivePriority: 2
            }
        ]
    }));
});

grid.on('click', '.vaccine_edit', function(){
    let row=datatable.row($(this).closest('tr')).data();

    $('#vaccine_save_errors').empty();

    edit_vaccine_modal.find('.modal-title').text(row.name);
    edit_vaccine_modal.find('input[name="id"]').val(row.id);
    edit_vaccine_modal.find('input[name="purchase_price"]').val(row.purchase_price);
    edit_vaccine_modal.find('input[name="available"]').prop('checked', row.available);

    edit_vaccine_modal.modal('show');
});

$('#vaccine_save').click(function(){
    $.ajax({
        url: '/admin/vaccines/save',
        data: edit_vaccine_modal.find('form').serializeArray(),
        dataType: 'json',
        method: 'POST'
    }).done(function(data){
        $('#vaccine_save_errors').empty();

        if (!data.errors.length)
        {
            edit_vaccine_modal.modal('hide');

            datatable.ajax.reload();
        }
        else
            $('#vaccine_save_errors').html(data.errors.join('<br>'));
    });

    return false;
});