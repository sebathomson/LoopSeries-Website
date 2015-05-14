LAEPISODE = {

    episode: 0,
    anime: 0,
    link: 0,

    options: {

    },

    init: function(episode, anime, link)
    {
        this.episode = episode;
        this.anime = anime;
        this.link = link;

        this.addEventListeners();
    },

    addEventListeners: function()
    {
        var me = this;

        // Send Comment -- Comment Creation
        $(document).on('click','#comment-send-button',function(e) {
            var comment = $('#comment_text').val();
            var _btn = $(this);

            if(!LACORE.isEmpty(comment)) {
                LAEPISODE.comment(comment, _btn);
            } else {
                console.error('comment cannot be empty');
            }
        });

        // Change Mirror
        $(document).on('change','#mirror_combo', function(e) {
            window.location = "/episodes/" + me.episode + "/" + $(this).val();
        });

        // Mark as Seen
        $(document).on('click','.mark-as-seen', function(e) {
            var _el = $(this);
            var idEpisode = _el.data('episode');
            var link = _el.data('link');

            var doneFn = me.generateDoneFunction(_el);
            me.markSeen(idEpisode, link, doneFn);
        });

        // Mark as Unseen
        $(document).on('click','.mark-as-unseen', function(e) {
            var _el = $(this);
            var idEpisode = _el.data('episode');
            var link = _el.data('link');

            var doneFn = me.generateDoneFunction(_el);
            me.markUnseen(idEpisode, link, doneFn);
        });
    },

    generateDoneFunction: function(_el)
    {
        return function(data) {
            switch(_el.data('action')) {
                case "hide":
                    $(_el.data('target')).fadeOut();
                    break;
                case "update":
                    var target = $(_el.data('target'));
                    target.find('.episode-title').html(data.nextEpisode.title);
                    target.find('.episode-poster').attr('src', data.nextEpisode.poster);
                    break;
                case "swap":
                case "switch":
                    var target = $(_el.data('target'));
                    console.log(target);
                    if (target.hasClass('hidden')) {
                        target.removeClass('hidden');
                        _el.addClass('hidden');
                    }
                    break;
                case "refresh":
                    window.location = window.location;
                    break;
            }
        };
    },

    saveProgressSeen: function()
    {
        var seconds = this.player.plugin.playbackTime();
        var me = this;
        var doneFunction = function() { LAEPISODE.player.savingOnProgress = false};

        if (!me.player.savingOnProgress) {
            me.player.savingOnProgress = true;
            LACORE.ajax.call('/episodes/ajax',{op: 'set_progress', id_episode: LAEPISODE.episode, id_link: LAEPISODE.link, watched_time: seconds}, doneFunction, doneFunction);
        }
    },

    dislike: function(idEpisode, doneFn)
    {
        if (LACORE.isEmpty(idEpisode)) {
            console.error('IdEpisode Cannot be empty');
            return;
        }

        $.ajax({
            url: '/episodes/ajax',
            data: {op: 'rating', id_episode: idEpisode, ratingUp: 0},
            dataType: 'JSON'
        }).done(function(data) {
            if(data.hasOwnProperty('data')) {
                data = data.data;
                if (typeof doneFn === 'function') {
                    doneFn(data);
                }
            }
        });
    },

    markFavorite: function(idAnime, doneFn)
    {
        if (LACORE.isEmpty(idAnime)) {
            console.error('idAnime cannot be empty');
            return;
        }

        $.ajax({
            url: '/animes/ajax',
            data: {op: 'mark_favorite', id_anime: idAnime},
            dataType: 'JSON',
            type: 'POST'
        }).done(function(data) {
            if (typeof doneFn === 'function') {
                doneFn(data);
            }
        });
    },

    markSeen: function(idEpisode, link, doneFn)
    {
        if (LACORE.isEmpty(idEpisode)) {
            console.error('idEpisode cannot be empty');
            return;
        }

        $.ajax({
            url: '/episodes/ajax',
            data: {op: 'mark_as_seen', id_episode: idEpisode, id_link: link},
            dataType: 'JSON',
            type: 'GET'
        }).done(function(data) {
            if (typeof doneFn == 'function') {
                doneFn(data);
            }
        });
    },

    markUnseen: function(idEpisode, link, doneFn)
    {
        if (LACORE.isEmpty(idEpisode)) {
            console.error('idEpisode cannot be empty');
            return;
        }

        $.ajax({
            url: '/episodes/ajax',
            data: {op: 'mark_as_unseen', id_episode: idEpisode, id_link: link},
            dataType: 'JSON',
            type: 'GET'
        }).done(function(data) {
            console.log(doneFn);
            if (typeof doneFn == 'function') {
                doneFn(data);
            }
        });
    },

    comment: function(comment, btn)
    {
        $.ajax({
            url: '/comments/create-comment/'+this.episode+'/',
            data: {comment: comment},
            type: 'POST',
            dataType: 'JSON'
        }).done(function(data) {
            btn.btn('disable');
        });
    },

    updateDislikesAndLinkes: function(data) {
        if(data.hasOwnProperty('likes')) {
            $('#thumbs-up-counter').html(data.likes);
            $('#thumbs-down-counter').html(data.dislikes);
        }
    },

    getLastProgress: function() {

        var successFn = function(data) {
            data = jQuery.parseJSON(data);
            if(data.hasOwnProperty('isError') && data.isError === false && data.hasOwnProperty('data') && !LACORE.isEmpty(data.data)) {
                data = data.data;

                var time = data.watchedTime + " sec(s)";
                if(data.watchedTime > 60) {
                    time = Math.round(data.watchedTime / 60) + " Min(s)";
                }

                if (data.watchedTime > 10) {
                    if (confirm("You have seen "+ time +" of the episode on " + data.viewTime + ". Do you want to resume your progress?" )) {
                        LAEPISODE.player.seekTo(data.watchedTime);
                    }
                }
            }
        };

        // Make the request
        LACORE.ajax.call('/episodes/ajax',{op: 'get_last_progress', id_episode: LAEPISODE.episode}, successFn);
    },

    releasePlugin: {

        plugin: {},

        init: function(wrapper)
        {
            this.plugin = LACORE.releasePanel(wrapper,'/episodes/navigate-season','season');
        },

        navigateTo: function(season)
        {
            this.plugin.navigateTo(season);
        }
    },

    player: {

        plugin: {},
        saveProgress: undefined,
        savingOnProgress: false,

        setPlayer: function (player) {
            clearInterval(this.saveProgress);
            this.plugin = player;
            this.addEventListeners();
            LAEPISODE.getLastProgress();
        },

        addEventListeners: function()
        {
            this.plugin.on({
                play: function(player) {
                    LAEPISODE.player.saveProgress = setInterval(function(){LAEPISODE.saveProgressSeen()},5000);
                    //console.log("player have been played");
                },
                pause: function(player) {
                    clearInterval(LAEPISODE.player.saveProgress);
                    //console.log("player pause");
                },
                stop: function(player) {
                    clearInterval(LAEPISODE.player.saveProgress);
                    //console.log("player stop");
                }
            });
        },

        seekTo: function(seconds)
        {
            var _plugin = this.plugin;
            _plugin.on('metadata',function(player) {
                _plugin.seekTo(seconds);
            });
            _plugin.play();
        }

    },

    setUserPreference: function(preferencesMarkSeen) {
        switch (preferencesMarkSeen) {
            case "askme_before_leave":
                jQuery(window).bind('beforeunload', function () {
                    if (confirm('Do you want to mark this episode as seen?'))
                        LAEPISODE.markSeen(true);
                });
                break;
            case "on_video_finish":
                this.plugin.on('end', function () {
                    LAEPISODE.markSeen(true);
                });
                break;
            case "on_player_start":
                this.plugin.on('start', function () {
                    LAEPISODE.markSeen(true);
                });
                break;
            case "after_10min":
                this.plugin.on('start', function () {
                    setTimeout(LAEPISODE.markSeen(true), 600000);
                });
                break;
            case "after_20min":
                this.plugin.on('start', function () {
                    setTimeout(LAEPISODE.markSeen(true), 1200000);
                });
                break;
        }
    }

};