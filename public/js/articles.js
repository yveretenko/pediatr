let article_modal=$('#article_modal');

function open_article(article_id)
{
    article_modal.modal('show');

    $.ajax({
        url: '/articles/get',
        data: {id: article_id},
        dataType: 'json',
        method: 'POST'
    }).done(function(data){
        article_modal.find('h2').text(data.title);
        article_modal.find('.article_text').html(data.text);
        article_modal.find('#article_main_image').attr('src', '/img/articles/'+data.id+'.jpg');

        window.location.hash='article'+data.id;
    });
}

$(document).ready(function(){
    $('.article_container').click(function(){
        let article_id=$(this).find('.article').data('article-id');

        open_article(article_id);
    });

    if (window.location.hash.startsWith('#article'))
    {
        let article_id=window.location.hash.replace('#article', '').replace(/\D/g, '');

        open_article(article_id);
    }

    article_modal.on('hidden.bs.modal', function(){
        history.pushState("", document.title, window.location.pathname+window.location.search);
    });
});