var datatable;
var grid=$('#appointments_grid');
let date_comment_modal=$('#date_comment_modal');
let edit_appointment_modal=$('#edit_appointment_modal');
let filter_form=$('#appointments_filter_form');
let suggestion={
    tel: null,
    name: null
};
let popup_message_timeout;

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
    datatable=grid.DataTable(jQuery.extend(grid_default_params, {
        order: [[0, 'desc']],
        responsive: true,
        drawCallback: function(d){
            $.each(d.json.dates, function(index, date){
                let date_button=$('#appointments_calendar').find('A[data-date="'+index+'"]');

                date_button.find('.badge-danger').text(date.appointments_count ? date.appointments_count : '');
                date_button.find('.badge-info').text((date.vaccines_count && date.is_future) ? date.vaccines_count : '');
                date_button.find('i.fa-lock').toggle(date.is_future && date.is_disabled);
            });

            let selected_date=$('#appointments_calendar').find('A.active').data('date');

            $('#date_comment').text(selected_date ? d.json.dates[selected_date].comment : '');

            $('.appointment_comment').mark(filter_form.find('input[name="comment"]').val());

            if (filter_form.find('input[name="tel"]').val())
            {
                let tels={};
                $.each(d.json.data, function(index, row){
                    if (!tels[row.tel] || tels[row.tel].length<row.name.length)
                        tels[row.tel]=row.name;
                });

                if (Object.keys(tels).length===1)
                {
                    suggestion.tel=Object.keys(tels)[0];
                    suggestion.name=tels[suggestion.tel];
                }
                else
                {
                    suggestion.tel=null;
                    suggestion.name=null;
                }
            }

            if (Object.keys(d.json.blacklist).length>0)
            {
                let alert_rows=[];
                $.each(d.json.blacklist, function(tel, reason){
                    alert_rows.push(tel+(reason ? ' '+reason : ''));
                });

                alert('Знайдено номери у чорному списку:\n\n'+alert_rows.join('\n'));
            }
        },
        ajax: {
            url: '/admin/appointments/filter/',
            data: function(d){
                d.filters={
                    tel:     filter_form.find('input[name="tel"]').val(),
                    name:    filter_form.find('input[name="name"]').val(),
                    comment: filter_form.find('input[name="comment"]').val(),
                    date:    $('#appointments_calendar').find('A.active').data('date'),
                    vaccine: filter_form.find('select[name="vaccine"]').val(),
                };
            },
            error: function(data){
                if (data.status===401)
                    window.location.href='/admin?redirect_to='+encodeURI(window.location.pathname);
                else
                    alert('Виникла помилка при завантаженні даних');
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
                url: '/admin/appointments/'+data.id+'/file-upload',
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                done: function(e, data){
                    if (data.result.error)
                        alert('Виникла помилка, файл не було завантажено');

                    datatable.ajax.reload();
                }
            });
        },
        language: {
            emptyTable: 'записів не знайдено',
            zeroRecords: 'записів не знайдено',
            info: 'Записів: _TOTAL_',
            infoEmpty: 'Записів не знайдено',
            infoFiltered: '(всього _MAX_)',
            processing: "<div class='alert alert-warning border-warning col-10 col-sm-8 col-md-4 mx-auto' role='alert'>оновлюю записи <i class='fas fa-spinner fa-spin ml-2'></i></div>"
        },
        columns: [
            {
                data: 'readable_date',
                orderable: false,
                className: 'pr-1 pr-md-2 text-nowrap',
                render: function (data, type, row){
                    let is_too_long_class = (row.duration && row.duration>60) ? 'text-success' : 'text-muted';

                    return '<span class="text-nowrap">'+data+'</span>'+'<br>'+row.time+(row.duration ? ' <span class="'+is_too_long_class+' small">'+row.duration+'хв</span>' : '')
                },
                responsivePriority: 1
            },
            {
                data: 'name',
                orderable: false,
                responsivePriority: 4,
                render: function (data, type, row){
                    return data+(row.visits_to_date>=5 ? '&nbsp;<i class="fas fa-star text-warning client_icon"></i>' : '')+(row.blacklisted ? '&nbsp;<i class="fas fa-ban text-danger client_icon" data-toggle="tooltip" data-html="true" title="'+row.blacklisted_reason+'"></i>' : '');
                },
            },
            {
                data: 'tel',
                className: 'px-1 px-md-2',
                orderable: false,
                render: function (data, type, row){
                    let tel = data>0 ? "<A class='text-reset' href='tel:"+data+"'>"+data.substr(0, 3)+'<span class="d-none d-md-inline"> </span>'+data.substr(3, 3)+'<span class="d-none d-md-inline"> </span>'+data.substr(6, 2)+'<span class="d-none d-md-inline"> </span>'+data.substr(8, 2)+"</A>" : '';

                    let telegram = data>0 ? "<A class='ml-1 ml-sm-2' href='https://t.me/+38"+data+"' target='_blank'><i class='fab fa-telegram-plane'></i></A>" : '';

                    let visits_history = row.visits_to_date ? " <A class='ml-1 ml-sm-2 appointment_history' href='#'><i class='fa fa-history'></i></A> <sub class='text-danger'>"+row.visits_to_date+"</sub>" : '';

                    let vaccines=[];
                    $.each(row.vaccines, function(index, vaccine){
                        vaccines.push('<span class="badge badge-info" title="'+vaccine.name+'">'+vaccine.short_name+((row.is_future && !vaccine.available) ? ' <i class="fa fa-sm fa-circle text-warning"></i>' : '')+'</span>');
                    });

                    return '<div class="text-nowrap">'+tel+telegram+visits_history+'</div>'+'<div class="d-block d-sm-none">'+row.name+(row.visits_to_date>=5 ? '&nbsp;<i class="fas fa-star text-warning client_icon"></i>' : '')+(row.blacklisted ? '&nbsp;<i class="fas fa-ban text-danger client_icon" data-toggle="tooltip" data-html="true" title="'+row.blacklisted_reason+'"></i>' : '')+'</div><div class="d-sm-none">'+(row.online ? '<span class="badge badge-info"><i class="fa fa-laptop"></i></span> ' : '')+(row.call_back ? '<span class="badge badge-warning"><i class="fa fa-phone"></i></span> ' : '')+(row.neurology ? '<span class="badge badge-danger">Невр</span> ' : '')+(row.earlier ? '<span class="badge badge-primary">Раніше</span> ' : '')+vaccines.join(' ')+'</div>';
                },
                responsivePriority: 2,
            },
            {
                data: 'comment_formatted',
                orderable: false,
                className: 'small',
                render: function (data, type, row){
                    let file = row.file ? '<span class="text-nowrap"><A title="'+(row.file.length>20 ? row.file : '')+'" href="/admin/appointments/'+row.id+'/file"><i class="fa fa-paperclip mr-1 mt-2"></i>'+(row.file.length>20 ? row.file.substr(0, 20)+'&hellip;' : row.file)+'</A></span>' : '';

                    let vaccines=[];
                    $.each(row.vaccines, function(index, vaccine){
                        vaccines.push('<span class="badge badge-info" style="font-size:90%;" title="'+vaccine.name+'">'+vaccine.short_name+((row.is_future && !vaccine.available) ? ' <i class="fa fa-sm fa-circle text-warning"></i>' : '')+'</span>');
                    });

                    return '<div style="max-width:33vw; line-height:normal;" class="appointment_comment">'+data+'</div><div>'+(row.online ? '<span class="badge badge-info" style="font-size:90%;">Онлайн</span> ' : '')+(row.call_back ? '<span class="badge badge-warning" style="font-size:90%;">Передзвонити</span> ' : '')+(row.neurology ? '<span class="badge badge-danger" style="font-size:90%;">Невр</span> ' : '')+(row.earlier ? '<span class="badge badge-primary" style="font-size:90%;">Раніше</span> ' : '')+vaccines.join(' ')+'</div>'+file;
                },
                responsivePriority: 6
            },
            {
                data: 'id',
                className: 'text-nowrap text-right pl-0 pr-1 pl-md-2 pr-md-2',
                render: function(){
                    let edit_button='<button class="btn btn-sm btn-success edit_appointment"><i class="fa fa-pencil-alt mr-0 mr-md-1"></i><span class="d-none d-md-inline"> Редагувати</span></button>';

                    let attach_file_button='<span class="hidden_file_upload"><label><A class="btn btn-sm text-white btn-info ml-1 ml-md-2 d-none d-sm-inline-block"><i class="fa fa-paperclip mr-0 mr-md-1"></i><span class="d-none d-md-inline"> Файл</span></A><input type="file" name="file_uploader" accept=".doc,.docx,.pdf"></label></span>';

                    let delete_button='<button class="btn btn-sm btn-danger ml-1 ml-md-2 delete_appointment"><i class="fa fa-trash mr-0 mr-md-1"></i><span class="d-none d-md-inline"> Видалити</span></button>';

                    let copy_appointment_text_button='<button class="btn btn-sm btn-info ml-1 ml-md-2 copy_appointment_text"><i class="fa fa-copy"></i><span class="d-none d-md-inline"></span></button>';

                    return edit_button+attach_file_button+copy_appointment_text_button+delete_button;
                },
                orderable: false,
                responsivePriority: 3
            }
        ]
    }));

    $('#appointments_calendar').find('A[data-date]').click(function(){
        $('#appointments_calendar').find('A[data-date]').not($(this)).removeClass('active');

        $(this).toggleClass('active');

        if (!$(this).hasClass('active'))
            $(this).blur();

        $('#edit_date_comment').toggleClass('disabled', !$(this).hasClass('active'));

        filter_form.find('input[type="text"], input[type="tel"], input[type="search"], select').val('');

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
        $('#appointment_save_errors').empty();

        $('#created_at').closest('div').hide();
        $('#updated_at').closest('div').hide();

        edit_appointment_modal.modal('show');

        if (suggestion.tel)
            $('#suggestion').show().find('.badge').text(suggestion.tel+' '+suggestion.name);
        else
            $('#suggestion').hide();

        return false;
    });

    $('#suggestion').on('click', 'div.badge', function(){
        edit_appointment_modal.find('input[name="tel"]').val(suggestion.tel);
        edit_appointment_modal.find('input[name="name"]').val(suggestion.name);
    });

    $('#appointments_filter_form_show_more').click(function(){
        filter_form.find('input[name="name"]').closest('div').toggleClass('col-6 col-md');
        filter_form.find('input[name="tel"]').closest('div').toggleClass('col-6 col-md');
        filter_form.find('select[name="vaccine"]').closest('div').toggleClass('d-none col-6 col-md');
        filter_form.find('input[name="comment"]').closest('div').toggleClass('d-none col-6 col-md');

        $(this).hide();

        return false;
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
                $('#appointment_save_errors').empty();

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
                edit_appointment_modal.find('input[name="online"]').prop('checked', row.online);
                edit_appointment_modal.find('input[name="call_back"]').prop('checked', row.call_back);

                $('#created_at').text(row.created_at).closest('div').toggle(!!row.created_at);
                $('#updated_at').text(row.updated_at).closest('div').toggle(!!row.updated_at);

                $('#suggestion').hide();
            }
        }, ".edit_appointment")
        .on({
            click: function(e){
                if (confirm('Ви впевнені?'))
                {
                    var row=datatable.row($(this).closest('tr')).data();

                    $.ajax({
                        url: '/admin/appointments/'+row.id,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        method: 'DELETE'
                    }).done(function(data){
                        if (data && data.success===true)
                            datatable.ajax.reload();
                    });
                }
            }
        }, ".delete_appointment")
        .on({
            click: function(){
                let row=datatable.row($(this).closest('tr')).data();

                let textarea=$('<textarea style="width:0; height:0;">'+row.appointment_text+'</textarea>');
                textarea.appendTo('body');
                textarea[0].select();
                document.execCommand('copy');
                textarea.remove();

                clearTimeout(popup_message_timeout);

                $('#popup_message').html('<b>Текст скопійовано:</b><br><br>'+row.appointment_text.replace(/\n\n/g, "<br />")).show();

                popup_message_timeout=setTimeout(function(){
                    $('#popup_message').fadeOut(750);
                }, 2000);
            }
        }, ".copy_appointment_text")
        .on({
            click: function(e){
                var modal=$('#appointment_history_modal');

                var row=datatable.row($(this).closest('tr')).data();

                modal.modal('show');
                modal.find('#history_loading').show();
                modal.find('table').hide();
                modal.find('div.alert').hide();

                $.ajax({
                    url: '/admin/appointments/history',
                    data: {tel: row.tel},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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

                            let labels_html=(row.online ? '<span class="badge badge-info">Онлайн</span> ' : '')+(row.neurology ? '<span class="badge badge-danger">Невр</span> ' : '')+vaccines.join(' ');

                            let file_html = row.file ? '<A href="/admin/appointments/'+row.id+'/file"><i class="fa fa-paperclip mr-1"></i>'+row.file+'</A>' : '';

                            let comment_html='<div>'+[row.comment, labels_html, file_html].filter(function(e){return e}).join('</div><div class="mt-1">')+'</div>';

                            html+='<tr><td>'+row.date+'<div>'+row.days_ago+(row.address_label ? '<span class="badge badge-success ml-1">'+row.address_label+'</span>' : '')+'</div></td><td>'+row.name+'</td><td>'+comment_html+'</td></tr>';
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
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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
                $('#appointment_save_errors').html(data.errors.join('<br>'));
        });

        return false;
    });

    filter_form.submit(function(){
        $('#appointments_calendar').find('A[data-date]').removeClass('active');

        datatable.ajax.reload();

        return false;
    });

    $('div.modal-content').find('input[name="tel"]').change(function(){
        let tel=$(this).val();
        let form=$(this).closest('form');

        if (tel && !form.find('input[name="name"]').val())
        {
            $.ajax({
                url: '/admin/appointments/get-by-telephone',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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

    $('select[name="vaccines\\[\\]"]').multiselect({
        nonSelectedText: '',
        nSelectedText: 'вакцин',
        numberDisplayed: 4,
        buttonWidth: '100%'
    });

    $('#edit_date_comment').click(function(){
        let active_date=$('#appointments_calendar').find('A.active');

        let date=active_date.data('date');
        let weekday=$.trim(active_date.text()).substr(0, 2);

        date_comment_modal.find('.modal-title').text(date.substr(-2)+'/'+date.substr(5, 2)+'/'+date.substr(0, 4)+', '+weekday);
        date_comment_modal.find('textarea').val($('#date_comment').text());

        date_comment_modal.modal('show');

        return false;
    });

    date_comment_modal.on('shown.bs.modal', function () {
        $('textarea').focus();
    })

    $('#date_comment_save').click(function(){
        let comment=date_comment_modal.find('textarea').val();

        $.ajax({
            url: '/admin/date-comments/save',
            method: 'POST',
            data: {
                date: $('#appointments_calendar').find('A.active').data('date'),
                comment: comment
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                date_comment_modal.modal('hide');
                $('#date_comment').text(comment);
            },
            error: function() {
                alert('Помилка при збереженні коментаря');
            }
        });
    });

    // if tel is set in $_GET, open edit modal
    if (edit_appointment_modal.find('input[name="tel"]').val())
    {
        edit_appointment_modal.find('.modal-header').find('h5').text('Новий запис');
        edit_appointment_modal.modal('show');

        edit_appointment_modal.find('input[name="tel"]').trigger('change');
    }

    $(document).on('keyup', function(e) {
        if (e.key==='Escape' && !$('.modal:visible').length)
            $('#reset_appointments_form').click();
    });

    $('#reset_appointments_form').click(function(){
        filter_form.find('select, input').val(''); // don't use reset here because if $_GET['tel'] is set, tel input will not be emptied

        $('#appointments_calendar').find('.active').removeClass('active').blur();
        $('#edit_date_comment').addClass('disabled');

        datatable.ajax.reload();
    });

    filter_form.find('input[name="tel"]').trigger('change');

    $(document).tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    $('#appointment_grid_reload').click(function(){
    	let reload_icon=$(this).find('i.fa');

        reload_icon.addClass('fa-spin');

    	datatable.ajax.reload(function(){
            reload_icon.removeClass('fa-spin');
        });
    });
});
