var datatable;
var grid=$('#vaccines_grid');

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
                url: '/admin/vaccines/filter/'
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
                    className: 'text-nowrap',
                    responsivePriority: 1
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
                    data: 'age',
                    className: 'text-center text-nowrap',
                    sortable: false,
                    responsivePriority: 5
                },
                {
                    data: 'purchase_price',
                    render: function(data, type, row){
                        return row.purchase_price ? Math.ceil(((row.purchase_price+500)*1.05)/100)*100 : '';
                    },
                    responsivePriority: 1,
                    className: 'font-weight-bold text-center'
                },
                {
                    data: 'link',
                    sortable: false,
                    render: function(data){
                        return data ? '<a href="'+data+'" target="_blank"><span class="d-none d-md-inline">tabletki.ua</span><span class="d-inline d-md-none"><i class="fa fa-external-link-alt"></i></span></A>' : '';
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
                    data: 'required',
                    render: function(data){
                        return data ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
                    },
                    className: 'text-center',
                    responsivePriority: 9
                },
                {
                    data: 'comment',
                    sortable: false,
                    responsivePriority: 7
                }
            ]
        }));
});