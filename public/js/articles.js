$(document).ready(function(){
    $('.article_container').click(function(){
        let id=$(this).find('.article').data('article-id');

        let modal=$('#article_modal');

        modal.modal('show');

        $.ajax({
            url: '/articles/get',
            data: {id: id},
            dataType: 'json',
            method: 'POST'
        }).done(function(data){
            modal.find('h2').text(data.title);
            modal.find('.article_text').html(data.text);
            modal.find('#article_main_image').attr('src', '/img/articles/'+data.id+'.jpg');
        });
    });
});