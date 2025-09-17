$(document).ready(function(){
    var login_form=$("#login_form");

    login_form.submit(function(){
        login_form.find('button').prop('disabled', true);
        login_form.find('i.fa-spinner').show();
        login_form.find('i.fa-check, i.fa-times').hide();

        $.ajax({
            type: "POST",
            url: '/admin/index/login',
            data: $("#login_form").serializeArray(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data){
                if (data && data.success)
                {
                    login_form.find('i.fa-spinner').fadeOut(function(){ login_form.find('i.fa-check').fadeIn(function(){ setTimeout("location.href='"+data.redirect_to+"';", 1000); }); });
                }
                else
                {
                    login_form.find('button').prop('disabled', false);
                    login_form.find('i.fa-spinner').fadeOut(function(){ login_form.find('i.fa-times').fadeIn(); });
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
