$(document).ready(function(e) {

    $('.trigger-favorites').click(function(e) {
        e.preventDefault();
        var animeId = $(this).attr('data-anime');
        var button = $(this);
        var oldHtml = button.html();
        button.html('<i class="fa fa-spin fa-circle-o-notch"></i> Loading..');
        $.ajax({
            url: '/animes/ajax',
            data: {op: 'mark_favorite', id_anime: animeId},
            type: 'POST',
            dataType: 'JSON'
        }).done(function(result) {
            if(button.hasClass('btn-blue') || button.hasClass('btn-pink')) {
                if(button.hasClass('btn-blue')) {
                    button.html('<i class="fa fa-star"></i> Remove from Favorites');
                } else {
                    button.html('<i class="fa fa-star-o"></i> Add to Favorites');
                }
                button.blur();
                button.toggleClass('btn-blue');button.toggleClass('btn-pink');
            }
        }).fail(function(result) {
            button.html(oldHtml);
        });
    });



});

// image lazy loading
$(function() {
    $("img.lazy").lazyload();
});
$("img.lazy").lazyload({
    effect : "fadeIn"
});