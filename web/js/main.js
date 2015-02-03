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

    $(document).on('click','.js-ajax',function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var _this = $(this);
        if(isEmpty(url)) {
            console.error("Please add data-url to the element.",$(this));
            return false;
        }
        var type = $(this).data('type') || 'json';
        var data = $(this).data('data') || {};
        $.ajax({
            url: url,
            type: "GET",
            dataType: type,
            data: data
        }).success(function(msg){
            console.log(_this,msg);
            _this.trigger('ajax-success',[e,msg]);
            showBox(msg);
        }).fail(function(msg) {
            _this.trigger('ajax-failure',e);
            console.error(msg);
        })
    });

});

function showBox(msg) {
    alert(msg);
}

function isEmpty(element) {
    if(typeof element === "undefined" || typeof element === "null" || (typeof element === "object" && element.length === 0)) {
        return true;
    }
    return false;
}

// image lazy loading
$(function() {
    $("img.lazy").lazyload();
});
$("img.lazy").lazyload({
    effect : "fadeIn"
});