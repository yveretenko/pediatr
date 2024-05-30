let article_modal=$('#article_modal');

function open_article(article_id)
{
    $.ajax({
        url: '/articles/get',
        data: {id: article_id},
        dataType: 'json',
        method: 'POST'
    }).done(function(data){
        article_modal.modal('show');

        article_modal.find('h2').text(data.title);
        article_modal.find('.article_text').html(data.text);
        article_modal.find('#article_main_image').attr('src', '/img/articles/'+data.id+'.jpg');

        article_modal.find('#article_main_image').closest('div').toggle(!data.is_video);

        $(document).prop('title', $(document).prop('title').split('-')[0]+' - '+data.title);

        window.location.hash='article'+data.id;
    });
}

window.onhashchange = function(){
    if (!window.location.hash.startsWith('#article'))
        article_modal.modal('hide');
    else
    {
        let article_id=window.location.hash.replace('#article', '').replace(/\D/g, '');

        open_article(article_id);
    }
}

$(document).ready(function(){
    $('.article_container').click(function(){
        let article_id=$(this).find('.article').data('article-id');

        open_article(article_id);
    });

    if (window.location.hash.startsWith('#article'))
        window.onhashchange();

    article_modal.on('hidden.bs.modal', function(){
        history.pushState("", document.title, window.location.pathname+window.location.search);

        $(document).prop('title', $(document).prop('title').split('-')[0]);
    });

    // on closing modal stop video playback
    $('.modal').on('hide.bs.modal', function(){
         var memory = $(this).html();
         $(this).html(memory);
    });
});