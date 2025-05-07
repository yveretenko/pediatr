$(function(){
    let hash=window.location.hash;

    modal_name = modal_name ? modal_name : hash.substring(1);

    let modal_id='#'+modal_name+'_modal';

    if($(modal_id).length>0)
        $(modal_id).modal('show');

    $('#try_again').click(function(){
        $('#pay_fail_modal').modal('hide');
        $('#pay_modal').modal('show');
    });

    // fix for scrolling in case of scenario when one modal is closed and another is opened after
    $(document).on('hidden.bs.modal', '.modal', function () {
        $('.modal:visible').length && $(document.body).addClass('modal-open');
    });
});