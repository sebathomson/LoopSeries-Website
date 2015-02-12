LAReleasePlugin = {

    options: {
        loadContent: '<i class="fa fa-spin fa-circle-o-notch"></i> Loading..',
        parameter: 'rd'
    },

    init: function(wrapper, navigationUrl, parameter)
    {
        if(!wrapper.jQuery) {
            wrapper = $(wrapper);
        }
        if(!wrapper.length){
            console.error('Wrapper ' + wrapper + 'was not found on the page!');
        }
        this.wrapper = wrapper;
        if(!LACORE.isEmpty(parameter)) {
            this.options.parameter = parameter;
        }
        this.navigationUrl = navigationUrl;
        this.addEventListeners();
    },

    addEventListeners: function()
    {
        var _this = this;
        var _wrapper = _this.wrapper;
        var _options = _this.options;
        _wrapper.on('click','.js-nav-control',function(e){
            console.log(_this);
            e.preventDefault();
            var _el = $(this);
            var parameter = _el.data(_options.parameter);
            console.log('p:' + parameter);
            _this.navigateTo(parameter);
        });
    },

    navigateTo: function(parameter)
    {
        var _wrapper = this.wrapper;
        var _options = this.options;
        _wrapper.html(_options.loadContent);
        if(!LACORE.isEmpty(parameter)) {
            postData = {};
            postData[_options.parameter] = parameter;
            LACORE.ajax.call(this.navigationUrl, postData, function(data) { _wrapper.html(data);}, function(data) { console.error(data); }, 'html');
        }
    }
};