$(function() {
    jQuery.expr[':'].Contains = function(a, i, m) {
      return jQuery(a).text().toUpperCase()
          .indexOf(m[3].toUpperCase()) >= 0;
    };

    $('input[name=quick_search]').on('keyup cut paste', function(){
        var query=$(this).val();

        if (query.length<2)
        {
            $('.articles').find('>div').show();
            return;
        }

        $('.articles').find('div.article_container').hide();

        $('.articles').find('div.article_header:Contains('+query+')').closest('div.article_container').show();

        $('#articles_not_found').toggle($('.articles').find('div.article_container:visible').length===0);
    });
});