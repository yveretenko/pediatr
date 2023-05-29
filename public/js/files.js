$(document).ready(function(){
    $('input[id="file_search"]').on('input', function(){
        let search=$(this).val().toLowerCase();

        $('.file_container').each(function(){
            let link=$(this).find('a');
            let patient_name=$(this).find('span.patient_name');

            $(this).toggle(link.text().toLowerCase().indexOf(search)>=0 || patient_name.text().toLowerCase().indexOf(search)>=0);
        });
    });
});