let dates_disabled=close_dates;

$(function(){
    $("#contactForm input,#contactForm textarea").jqBootstrapValidation({
        preventSubmit: true,
        submitSuccess: function ($form, event) {
            event.preventDefault();

            let name=$("input#name").val();
            let phone=$("input#phone").val();
            let message=$("textarea#message").val();
            let date=$("#date").val();
            let age=$("#age").val();
            let firstName=name; // For Success/Failure Message

            // Check for white space in name for Success/Fail message
            if (firstName.indexOf(' ')>=0)
            {
                firstName=name.split(' ').slice(0, -1).join(' ');
            }

            $this=$("#sendMessageButton");
            $this.prop("disabled", true);

            let spinner=$form.find('.fa-spin');

            spinner.removeClass('d-none');

            $.ajax({
                url: "/appointments/request",
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    name: name,
                    phone: phone,
                    message: message,
                    date: date,
                    age: age
                },
                cache: false,
                success: function () {
                    $('#contactForm').trigger("reset");

                    $('#appointment_modal').modal('hide');
                    $('#contact_success_modal').modal('show');
                },
                error: function () {
                    alert('Помилка при відправленні повідомлення');
                },
                complete: function () {
                    spinner.addClass('d-none');

                    $this.prop("disabled", false);
                }
            });
        },
        filter: function () {
            return $(this).is(":visible");
        },
    });

    $("a[data-toggle=\"tab\"]").click(function (e) {
        e.preventDefault();
        $(this).tab("show");
    });

    $('#contactForm #date').datepicker({
        weekStart: 1,
        language: "uk",
        daysOfWeekDisabled: "0",
        daysOfWeekHighlighted: "1,2,3,4,5,6",
        autoclose: true,
        datesDisabled: dates_disabled,
        maxViewMode: 0,
        disableTouchKeyboard: true
    });

    if(window.location.hash==='#contact')
        $('#appointment_modal').modal('show');
});
