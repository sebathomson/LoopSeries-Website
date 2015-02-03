LAReleasePlugin = {

    options: {
        loadContent: '<i class="fa fa-spin fa-circle-o-notch"></i> Loading..'
    },

    init: function(wrapper, navigationUrl)
    {
        if(!wrapper.jQuery) {
            wrapper = $(wrapper);
        }
        if(!wrapper.length){
            console.error('Wrapper ' + wrapper + 'was not found on the page!');
        }
        this.wrapper = wrapper;
        this.navigationUrl = navigationUrl;
        this.addEventListeners();
    },

    addEventListeners: function()
    {
        var _wrapper = this.wrapper;
        _wrapper.on('click','.js-nav-control',function(e){
            e.preventDefault();
            var _el = $(this);
            var date = _el.data('date');
            LAReleasePlugin.navigateTo(date);
        });
    },

    navigateTo: function(date)
    {
        var _wrapper = this.wrapper;
        var _options = this.options;
        _wrapper.html(_options.loadContent);
        if(!LACORE.isEmpty(date)) {
            LACORE.ajax.call(this.navigationUrl, {rd: date}, function(data) { _wrapper.html(data);}, function(data) { console.error(data); }, 'html');
        }
    }
};