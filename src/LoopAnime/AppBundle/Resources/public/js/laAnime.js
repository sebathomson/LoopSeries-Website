LAANIME = {

    init: function()
    {
        this.addEventListeners();
    },

    addEventListeners: function()
    {
        var _wrapper = $(document);
        var me = this;

        _wrapper.on('click', '.js-favorite', function(e) {
            e.preventDefault();
            var idAnime = $(this).data('anime');
            $(this).removeClass('fa-heart').addClass('fa-circle-o-notch fa-spin');

            var doneFn = me.generateDoneFn($(this));
            me.favorite(idAnime, doneFn);
        });

        _wrapper.on('click', '.js-unfavorite', function(e) {
            e.preventDefault();
            var idAnime = $(this).data('anime');
            $(this).removeClass('fa-heart-o').addClass('fa-circle-o-notch fa-spin');

            var doneFn = me.generateDoneFn($(this));
            me.unfavorite(idAnime, doneFn);
        });
    },

    generateDoneFn: function(el)
    {
        return function(data) {
            el.removeClass('fa-circle-o-notch fa-spin');
            if (el.hasClass('js-favorite')) {
                el.addClass('js-unfavorite').removeClass('js-favorite').addClass('fa-heart-o');
            } else if(el.hasClass('js-unfavorite')) {
                el.addClass('js-favorite').removeClass('js-unfavorite').addClass('fa-heart');
            }
        };
    },

    favorite: function(idAnime, doneFn)
    {
        if (LACORE.isEmpty(idAnime)) {
            return;
        }
        $.ajax({
            url: '/animes/ajax',
            data: {op: 'mark_favorite', id_anime: idAnime},
            type: 'POST',
            dataType: 'JSON'
        }).done(function(result) {
            if (typeof doneFn == "function") {
                doneFn(result);
            }
        });
    },

    unfavorite: function(idAnime, doneFn)
    {
        if (LACORE.isEmpty(idAnime)) {
            return;
        }

        $.ajax({
            url: '/animes/ajax',
            data: {op: 'mark_favorite', id_anime: idAnime},
            type: 'POST',
            dataType: 'JSON'
        }).done(function(result) {
            if (typeof doneFn == "function") {
                doneFn(result);
            }
        });

    }

};