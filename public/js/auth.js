$(document).ready(function(){
    var login_form=$("#login_form");

    login_form.submit(function(){
        login_form.find('button').prop('disabled', true);
        login_form.find('i.fa-spinner').show();
        login_form.find('i.fa-check, i.fa-times').hide();

        $.ajax({
            type: "POST",
            url: '/admin/index/login/',
            data: $("#login_form").serializeArray(),
            success: function(data){
                if (data)
                {
                    login_form.find('button').prop('disabled', false);
                    login_form.find('i.fa-spinner').fadeOut(function(){ login_form.find('i.fa-times').fadeIn(); });
                }
                else
                {
                    var redirect_to=document.URL.replace(/.*redirect_to=([^&]*).*|(.*)/, '$1');

                    if (!redirect_to)
                        redirect_to='/admin/';

                    login_form.find('i.fa-spinner').fadeOut(function(){ login_form.find('i.fa-check').fadeIn(function(){ setTimeout("location.href='"+decodeURIComponent(redirect_to)+"';", 1000); }); });
                }
            },
            error: function(){
                login_form.find('button').prop('disabled', false);
                login_form.find('i.fa-spinner').fadeOut(function(){ login_form.find('i.fa-times').fadeIn(); });
            }
        });

		return false;
	});
});