var datatable;
var grid=$('#grid');
let date_comment_modal=$('#date_comment_modal');
let edit_appointment_modal=$('#edit_appointment_modal');
let buffer={};

var grid_default_params={
    pageLength: 50,
    processing: true,
    serverSide: true,
    bLengthChange: false,
    bFilter: false,
    responsive: true,
    autoWidth: false,
    pagingType: 'numbers',
    dom: '<"datatable_container position-relative"rti><"mt-2"p>'
};

$(document).ready(function(){
    if (grid.length)
    {
        datatable=grid.DataTable(jQuery.extend(grid_default_params, {
            order: [[0, 'desc']],
            responsive: true,
            drawCallback: function(d){
                $.ajax({
                    url: '/admin/appointments/appointments_count_by_date',
                    dataType: 'json',
                    method: 'POST'
                }).done(function(data){
                    $('#appointments_calendar').find('div.badge-danger').empty();

                    $.each(data, function(date, counter){
                        $('#appointments_calendar').find('A[data-date="'+date+'"]').find('div.badge').text(counter);
                    });
                });

                $.ajax({
                    url: '/admin/appointment_vaccines/vaccines_by_week',
                    dataType: 'json',
                    method: 'POST'
                }).done(function(data){
                    $.each(data, function(week, vaccines){
                        let html='';

                        $.each(vaccines, function(name, count){
                            html+='<div class="badge badge-info mr-1">'+count+' '+name+'</div>';
                        });

                        $('#appointments_calendar').find('div.week_vaccines[data-week="'+week+'"]').html(html);
                    });
                });

                if ($('#appointments_calendar').find('A.active').length)
                    $.ajax({
                        url: '/admin/date_comments/get_by_date',
                        dataType: 'json',
                        data: {
                            date: $('#appointments_calendar').find('A.active').data('date')
                        },
                        method: 'POST'
                    }).done(function(data){
                        $('#date_comment').text(data.comment);
                    });
                else
                    $('#date_comment').empty();

                $('.appointment_comment').mark($('#appointments_filter_form').find('input[name="comment"]').val());
            },
            ajax: {
                url: '/admin/appointments/filter/',
                data: function(d){
                    var filter_form=$('#appointments_filter_form');

                    d.filters={
                        tel:     filter_form.find('input[name="tel"]').val(),
                        name:    filter_form.find('input[name="name"]').val(),
                        comment: filter_form.find('input[name="comment"]').val(),
                        date:    $('#appointments_calendar').find('A.active').data('date')
                    };
                }
            },
            createdRow: function(row, data, dataIndex){
                if (data.is_future)
                {
                    if (data.is_today)
                        $(row).addClass('table-warning');
                    else if (data.is_tomorrow)
                        $(row).addClass('table-info');
                    else
                        $(row).addClass('table-success');
                }

                $(row).find('.hidden_file_upload').find('input[type="file"]').fileupload({
                    url: '/admin/index/upload/?id='+data.id,
                    dataType: 'json',
                    done: function(e, data){
                        if (data.result.error)
                            alert('Виникла помилка, файл не було завантажено');

                        datatable.ajax.reload();
                    }
                });

                if ($('#appointments_filter_form').find('input[name="tel"]').val() && data.tel && !buffer[data.tel])
                    buffer[data.tel]=data.name;
            },
            language: {
                emptyTable: 'записів не знайдено',
                zeroRecords: 'записів не знайдено',
                info: 'Записів: _TOTAL_',
                infoEmpty: 'Записів не знайдено',
                infoFiltered: '(всього _MAX_)',
                processing: "<div class='alert alert-warning border-warning col-10 col-sm-8 col-md-4 mx-auto' role='alert'>оновлюю записи <i class='fas fa-spinner fa-spin ml-2'></i></div>"
            },
            columnDefs: [
                {
                    "targets": [0],
                    "className": 'pr-1 pr-md-2'
                },
                {
                    "targets": [3],
                    "className": 'small'
                },
                {
                    "targets": [4],
                    "className": 'text-nowrap text-white text-right pl-0 pr-1 pl-md-2 pr-md-2'
                }
            ],
            columns: [
                {
                    data: 'readable_date',
                    orderable: false,
                    render: function (data, type, row){
                        return '<span class="text-nowrap">'+data+'</span>'+'<br>'+row.time;
                    },
                    responsivePriority: 1
                },
                {
                    data: 'name',
                    orderable: false,
                    responsivePriority: 4,
                    render: function (data, type, row){
                        return data+(row.visits_to_date>=5 ? '&nbsp;<i class="fas fa-star text-warning client_icon"></i>' : '')+(row.blacklisted ? '&nbsp;<i class="fas fa-ban text-danger client_icon" title="'+row.blacklisted_reason+'"></i>' : '');
                    },
                },
                {
                    data: 'tel',
                    orderable: false,
                    render: function (data, type, row){
                        let tel = data>0 ? "<A class='text-reset' href='tel:"+data+"'>"+data.substr(0, 3)+'<span class="d-none d-md-inline"> </span>'+data.substr(3, 3)+'<span class="d-none d-md-inline"> </span>'+data.substr(6, 2)+'<span class="d-none d-md-inline"> </span>'+data.substr(8, 2)+"</A>" : '';

                        let visits_history = row.visits_to_date ? " <A class='ml-1 ml-sm-2 appointment_history' href='#'><i class='fa fa-history'></i></A> <sub class='text-danger'>"+row.visits_to_date+"</sub>" : '';

                        let vaccines=[];
                        $.each(row.vaccines, function(index, vaccine){
                            vaccines.push('<span class="badge badge-info" title="'+vaccine.name+'">'+vaccine.short_name+'</span>');
                        });

                        return '<div class="text-nowrap">'+tel+visits_history+'</div>'+'<div class="d-block d-sm-none">'+row.name+(row.visits_to_date>=5 ? '&nbsp;<i class="fas fa-star text-warning client_icon"></i>' : '')+(row.blacklisted ? '&nbsp;<i class="fas fa-ban text-danger client_icon" title="'+row.blacklisted_reason+'"></i>' : '')+'</div><div class="d-sm-none">'+(row.call_back ? '<span class="badge badge-warning"><i class="fa fa-phone"></i></span> ' : '')+(row.neurology ? '<span class="badge badge-danger">Невр</span> ' : '')+(row.earlier ? '<span class="badge badge-primary">Раніше</span> ' : '')+vaccines.join(' ')+'</div>';
                    },
                    responsivePriority: 2,
                },
                {
                    data: 'comment',
                    orderable: false,
                    render: function (data, type, row){
                        let file = row.file ? '<span class="text-nowrap"><A title="'+(row.file.length>20 ? row.file : '')+'" href="/admin/appointments/file/?id='+row.id+'"><i class="fa fa-paperclip mr-1"></i>'+(row.file.length>20 ? row.file.substr(0, 20)+'&hellip;' : row.file)+'</A></span>' : '';

                        let vaccines=[];
                        $.each(row.vaccines, function(index, vaccine){
                            vaccines.push('<span class="badge badge-info" style="font-size:90%;" title="'+vaccine.name+'">'+vaccine.short_name+'</span>');
                        });

                        return '<div style="max-width:33vw; line-height:normal;" class="appointment_comment">'+data.replace(/([^>])\n/g, '$1<br/>')+'</div><div>'+(row.call_back ? '<span class="badge badge-warning" style="font-size:90%;">Передзвонити</span> ' : '')+(row.neurology ? '<span class="badge badge-danger" style="font-size:90%;">Невр</span> ' : '')+(row.earlier ? '<span class="badge badge-primary" style="font-size:90%;">Раніше</span> ' : '')+vaccines.join(' ')+'</div>'+file;
                    },
                    responsivePriority: 6
                },
                {
                    data: 'id',
                    render: function(){
                        let edit_button='<A class="btn btn-sm btn-success edit_appointment"><i class="fa fa-pencil-alt mr-0 mr-md-1"></i><span class="d-none d-md-inline"> Редагувати</span></A>';

                        let attach_file_button='<span class="hidden_file_upload"><label><A class="btn btn-sm btn-info ml-1 ml-md-2 d-none d-sm-inline-block"><i class="fa fa-paperclip mr-0 mr-md-1"></i><span class="d-none d-md-inline"> Файл</span></A><input type="file" name="file_uploader"></label></span>';

                        let delete_button='<A class="btn btn-sm btn-danger ml-1 ml-md-2 delete_appointment"><i class="fa fa-trash mr-0 mr-md-1"></i><span class="d-none d-md-inline"> Видалити</span></A>';

                        return edit_button+attach_file_button+delete_button;
                    },
                    orderable: false,
                    responsivePriority: 3
                }
            ]
        }));
    }

    $('#appointments_calendar').find('A[data-date]').click(function(){
        $('#appointments_calendar').find('A[data-date]').not($(this)).removeClass('active');

        $(this).toggleClass('active');

        if (!$(this).hasClass('active'))
            $(this).blur();

        $('#date_comment').closest('.alert').find('button').prop('disabled', !$(this).hasClass('active'));

        datatable.ajax.reload();

        return false;
    });

    $('#appointment_add').click(function(){
        // can't use reset() method as form may have pre-populated values for $_GET
        edit_appointment_modal.find('input[type="time"], input[type="date"], input[type="tel"], input[type="text"], input[type="hidden"], textarea').val('');
        edit_appointment_modal.find('input[type="checkbox"]').prop('checked', false);
        edit_appointment_modal.find('select[name="vaccines\\[\\]"]').find("option:selected").prop('selected', false)
        edit_appointment_modal.find('select[name="vaccines\\[\\]"]').multiselect("refresh");

        if ($('#appointments_calendar').find('A.active').length)
            edit_appointment_modal.find('input[name="date"]').val($('#appointments_calendar').find('A.active').data('date'));

        edit_appointment_modal.find('.modal-header').find('h5').text('Новий запис');
        $('#appoinment_save_errors').empty();

        $('#created_at').closest('div').hide();
        $('#updated_at').closest('div').hide();

        edit_appointment_modal.modal('show');

        $('#suggestions').empty();
        $.each(buffer, function(tel, name){
            $('#suggestions').append('<div class="badge badge-success d-inline-block mr-1" data-tel="'+tel+'">'+tel+(name ? ' '+name : '')+'</div>');
        });

        return false;
    });

    $('#suggestions').on('click', 'div.badge', function(){
        edit_appointment_modal.find('input[name="tel"]').val($(this).data('tel')).trigger('change');
    });

    grid
        .on({
            click: function(){
                let row=datatable.row($(this).closest('tr')).data();

                let vaccines=[];
                $.each(row.vaccines, function(index, vaccine){
                    vaccines.push(vaccine.id);
                });

                edit_appointment_modal.find('.modal-header').find('h5').text('Редагувати запис');
                $('#appoinment_save_errors').empty();

                edit_appointment_modal.modal('show');

                edit_appointment_modal.find('input[name="id"]').val(row.id);
                edit_appointment_modal.find('input[name="name"]').val(row.name);
                edit_appointment_modal.find('input[name="tel"]').val(row.tel);
                edit_appointment_modal.find('input[name="date"]').val(row.date);
                edit_appointment_modal.find('input[name="time"]').val(row.time);
                edit_appointment_modal.find('textarea[name="comment"]').val(row.comment);
                edit_appointment_modal.find('select[name="vaccines\\[\\]"]').val(vaccines).multiselect("refresh");
                edit_appointment_modal.find('input[name="neurology"]').prop('checked', row.neurology);
                edit_appointment_modal.find('input[name="earlier"]').prop('checked', row.earlier);

                $('#created_at').text(row.created_at).closest('div').toggle(!!row.created_at);
                $('#updated_at').text(row.updated_at).closest('div').toggle(!!row.updated_at);

                $('#suggestions').empty();
            }
        }, ".edit_appointment")
        .on({
            click: function(e){
                if (confirm('Ви впевнені?'))
                {
                    var row=datatable.row($(this).closest('tr')).data();

                    $.ajax({
                        url: '/admin/appointments/delete',
                        data: {id: row.id},
                        dataType: 'json',
                        method: 'POST'
                    }).done(function(data){
                        if (data && data.success===true)
                            datatable.ajax.reload();
                    });
                }
            }
        }, ".delete_appointment")
        .on({
            click: function(e){
                var modal=$('#appointment_history_modal');

                var row=datatable.row($(this).closest('tr')).data();

                modal.modal('show');
                modal.find('#history_loading').show();
                modal.find('table').hide();
                modal.find('div.alert').hide();

                $.ajax({
                    url: '/admin/appointments/history/',
                    data: {tel: row.tel},
                    dataType: 'json',
                    method: 'POST'
                }).done(function(data){
                    modal.find('#history_loading').hide();

                    modal.find('table').toggle(data.length>0);
                    modal.find('div.alert').toggle(data.length===0);

                    if (data.length)
                    {
                        let html='';
                        $.each(data, function(index, row){
                            let vaccines=[];
                            $.each(row.vaccines, function(index, vaccine){
                                vaccines.push('<span class="badge badge-info" title="'+vaccine.name+'">'+vaccine.short_name+'</span>');
                            });

                            html+='<tr><td>'+row.date+'<div class="small text-muted">'+row.days_ago+'</div>'+'</td><td>'+row.name+'</td><td>'+row.comment+'<div>'+(row.neurology ? '<span class="badge badge-danger">Невр</span> ' : '')+vaccines.join(' ')+'</div>'+(row.file ? '<div><A href="/admin/appointments/file/?id='+row.id+'"><i class="fa fa-paperclip mr-1"></i>'+row.file+'</A></div>' : '')+'</td></tr>';
                        });

                        modal.find('table').find('tbody').html(html);
                    }
                });

                return false;
            }
        }, ".appointment_history")
    ;

    $('#appointment_save').click(function(){
        $.ajax({
            url: '/admin/appointments/save',
            data: edit_appointment_modal.find('form').serializeArray(),
            dataType: 'json',
            method: 'POST'
        }).done(function(data){
            if (!data.errors.length)
            {
                edit_appointment_modal.modal('hide');

                datatable.ajax.reload();
            }
            else
                $('#appoinment_save_errors').html(data.errors.join('<br>'));
        });

        return false;
    });

    $('#appointments_filter_form').submit(function(){
        datatable.ajax.reload();

        return false;
    });

    $('div.modal-content').find('input[name="tel"]').change(function(){
        let tel=$(this).val();
        let form=$(this).closest('form');

        if (tel && !form.find('input[name="id"]').val())
        {
            $.ajax({
                url: '/admin/appointments/get_by_telephone/',
                data: {
                    tel: tel
                },
                dataType: 'json',
                method: 'POST'
            }).done(function(data){
                if (data.name)
                    form.find('input[name="name"]').val(data.name);

                if (data.blacklisted)
                    alert('УВАГА! Цей телефон у чорному списку!'+(data.blacklisted_reason ? "\n\nПричина: "+data.blacklisted_reason : ''));
            });
        }
    });

    $('#appointments_filter_form').find('input[name="tel"]').change(function(){
        let tel=$(this).val();

        if (!tel)
            return;

        $.ajax({
            url: '/admin/blacklist/get_by_telephone/',
            data: {
                tel: tel
            },
            dataType: 'json',
            method: 'POST'
        }).done(function(data){
            if (Object.keys(data).length)
            {
                let text="Знайдено номери в чорному списку:\n\n";

                $.each(data, function(tel, reason){
                    text+=tel;

                    if (reason)
                        text+='   '+reason;

                    text+="\n";
                });

                alert(text);
            }
        });
    });

    $('select[name="vaccines\\[\\]"]').multiselect({
        nonSelectedText: '',
        nSelectedText: 'вакцин',
        numberDisplayed: 4,
        buttonWidth: '100%'
    });

    $('input[id="file_search"]').on('input', function(){
        let search=$(this).val().toLowerCase();

        $('.file_container').each(function(){
            let link=$(this).find('a');
            let patient_name=$(this).find('span.patient_name');

            $(this).toggle(link.text().toLowerCase().indexOf(search)>=0 || patient_name.text().toLowerCase().indexOf(search)>=0);
        });
    });

    $('#edit_date_comment').click(function(){
        let active_date=$('#appointments_calendar').find('A.active');

        let date=active_date.data('date');
        let weekday=$.trim(active_date.text()).substr(0, 2);

        date_comment_modal.find('.modal-title').text(date.substr(-2)+'/'+date.substr(5, 2)+'/'+date.substr(0, 4)+', '+weekday);
        date_comment_modal.find('textarea').val($('#date_comment').text());

        date_comment_modal.modal('show');
    });

    date_comment_modal.on('shown.bs.modal', function () {
        $('textarea').focus();
    })

    $('#date_comment_save').click(function(){
        let comment=date_comment_modal.find('textarea').val();

    	$.post('/admin/date_comments/save', {
            date: $('#appointments_calendar').find('A.active').data('date'),
            comment: comment
        })
        .done(function(){
            date_comment_modal.modal('hide');
            $('#date_comment').text(comment);
        })
        .fail(function(){
            alert('Помилка при збереженні коментаря');
        });
    });

    if (edit_appointment_modal.find('input[name="tel"]').val())
    {
        edit_appointment_modal.find('.modal-header').find('h5').text('Новий запис');
        edit_appointment_modal.modal('show');

        edit_appointment_modal.find('input[name="tel"]').trigger('change');
    }

    $(document).on('keyup', function(e) {
        if (e.key==='Escape' && edit_appointment_modal.length && !edit_appointment_modal.is(':visible'))
            $('#reset_appointments_form').click();
    });

    $('#reset_appointments_form').click(function(){
        $('#appointments_filter_form').find('input').val(''); // don't use reset here because if $_GET['tel'] is set, tel input will not be emptied

        $('#appointments_calendar').find('.active').removeClass('active').blur();

        datatable.ajax.reload();
    });

    $('#appointments_filter_form').find('input[name="tel"]').trigger('change');
});