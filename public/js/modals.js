$(function(){
    let hash=window.location.hash;

    if($(hash+'_modal').length>0)
        $(hash+'_modal').modal('show');
});