$(function(){
    let hash=window.location.hash;

    if($(hash+'_modal').length>0)
        $(hash+'_modal').modal('show');

    $('#try_again').click(function(){
        $('#pay_fail_modal').modal('hide');
        $('#pay_modal').modal('show');
    });
});