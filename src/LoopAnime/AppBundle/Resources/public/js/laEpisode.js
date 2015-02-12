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
        // Send Comment -- Comment Creation
        $(document).on('click','#comment-send-button',function(e) {
            var comment = $('#comment_text').val();
            var _btn = $(this);

            if(!LACORE.isEmpty()) {
                LAEPISODE.comment(comment, _btn);
            }
        });
    },

    saveProgressSeen: function()
    {
        var seconds = this.player.plugin.playbackTime();
        LACORE.ajax.call('/episodes/ajax',{op: 'set_progress', id_episode: LAEPISODE.episode, id_link: LAEPISODE.link, watched_time: seconds});
    },

    like: function()
    {
        $.ajax({
            url: '{{ path("loopanime_shows_episodeAjax") }}',
            data: {op: 'rating', id_episode: id_episode, ratingUp: 1},
            dataType: 'JSON'
        }).done(function(data) {
            if(data.hasOwnProperty('data')) {
                data = data.data;
                updateDislikesAndLinkes(data);
                $('div.glyphicon-thumbs-down').css('color',"");
                $('div.glyphicon-thumbs-up').css('color',"green");
            }
        });
    },

    dislike: function()
    {
        $.ajax({
            url: '{{ path("loopanime_shows_episodeAjax") }}',
            data: {op: 'rating', id_episode: id_episode, ratingUp: 0},
            dataType: 'JSON'
        }).done(function(data) {
            if(data.hasOwnProperty('data')) {
                data = data.data;
                updateDislikesAndLinkes(data);
                $('div.glyphicon-thumbs-up').css('color', "");
                $('div.glyphicon-thumbs-down').css('color', "red");
            }
        });
    },

    markFavorite: function()
    {
        $.ajax({
            url: '{{ path("loopanime_shows_episodeAjax") }}',
            data: {op: 'mark_favorite', id_anime: id_anime},
            dataType: 'JSON',
            type: 'GET'
        }).done(function(data) {
            btn.toggleClass('btn-success').toggleClass('btn-warning');

            if(btn.hasClass('btn-warning'))
                btn.html('<div class="glyphicon glyphicon-star pull-left"></div>&nbsp;Favorite');
            else
                btn.html('<div class="glyphicon glyphicon-star pull-left"></div>&nbsp;Mark as Favorite');
        });
    },

    markSeen: function()
    {
        $('#btn_mark_seen').click(function(e) {
            e.preventDefault();
            var btn = $(this);

            $.ajax({
                url: '/episodes/ajax',
                data: {op: 'mark_as_seen', id_episode: LAEPISODE.episode, id_link: LAEPISODE.link},
                dataType: 'JSON',
                type: 'GET'
            }).done(function(data) {
                btn.toggleClass('btn-success').toggleClass('btn-info');

                if(btn.hasClass('btn-info'))
                    btn.html('<div class="glyphicon glyphicon-eye-open pull-left"></div>&nbsp;Seen');
                else
                    btn.html('<div class="glyphicon glyphicon-eye-open pull-left"></div>&nbsp;Mark as Seen');
            });
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
            if(data.hasOwnProperty('isError') && data.isError === false && data.hasOwnProperty('data') && !LACORE.isEmpty(data.data)) {
                data = data.data;

                var time = data.watchedTime + " sec(s)";
                if(data.watchedTime > 60) {
                    time = Math.round(data.watchedTime / 60) + " Min(s)";
                }

                if(confirm("You have seen "+ time +" of the episode on " + data.viewTime + ". Do you want to resume your progress?" )) {
                    LAEPISODE.player.seekTo(data.watchedTime);
                }
            }
        };

        // Make the request
        LACORE.ajax.call('/episodes/ajax',{op: 'get_last_progress', id_episode: LAEPISODE.episode},successFn);
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

        setPlayer: function (player) {
            clearInterval(this.saveProgress);
            console.log("player has been set");
            this.plugin = player;
            this.addEventListeners();
            LAEPISODE.getLastProgress();
        },

        addEventListeners: function()
        {
            this.plugin.on({
                play: function(player) {
                    LAEPISODE.player.saveProgress = setInterval(function(){LAEPISODE.saveProgressSeen()},5000);
                    console.log("player have been played");
                },
                pause: function(player) {
                    clearInterval(LAEPISODE.player.saveProgress);
                    console.log("player pause");
                },
                stop: function(player) {
                    clearInterval(LAEPISODE.player.saveProgress);
                    console.log("player stop"); }
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