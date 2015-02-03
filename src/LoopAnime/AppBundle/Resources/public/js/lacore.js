LACORE = {

    init: function()
    {
        this.addEventListeners();
    },

    addEventListeners: function()
    {

    },

    ajax: {
        basicOptions: {
            type: 'POST',
            dataType: 'JSON'
        },

        call: function(url, data, successFn, failureFn, dataType) {
            var ajaxOptions = this.basicOptions;
            ajaxOptions.url = url;
            ajaxOptions.data = data;
            if(!LACORE.isEmpty(dataType)){
                ajaxOptions.dataType = dataType;
            }

            $.ajax(ajaxOptions)
                .success(successFn)
                .fail(failureFn)
        }
    },

    releasePanel: function(wrapper, navigationUrl)
    {
        var constructor = $.extend(LAReleasePlugin,{});
        if(!this.isEmpty(wrapper) && !this.isEmpty(navigationUrl)) {
            constructor.init(wrapper,navigationUrl);
        }
        return constructor;
    },

    isEmpty: function(val)
    {
        return !!(val === "" || val === undefined || val === false || val === null || (typeof val === "object" && val.length === 0));
    }

};