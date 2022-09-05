(function( $ ) {
/**
 * Abstract class. Creates a scrollable controller. Extended by {@link #mapsvgpopovercontroller|MapSVG.PopoverController} / {@link #mapsvgdetailscontroller|MapSVG.DetailsController} / {@link #mapsvgdirectorycontroller|MapSVG.DirectoryController}
 * @abstract
 * @constructor
 * @param {object} options - List of options
 */
MapSVG.Controller = function(options){
    this.container            = options.container;
    this.mapsvg               = options.mapsvg;
    this.template             = options.template;
    this.scrollable           = options.scrollable === undefined ? true : options.scrollable;
    this.withToolbar          = options.withToolbar === undefined ? true : options.withToolbar;
    this.autoresize           = MapSVG.parseBoolean(options.autoresize);
    this.templates            = {
        toolbar: Handlebars.compile(this.getToolbarTemplate()),
        main: this.getMainTemplate()
    };
    this.data                 = options.data;
    this.width = options.width;
    this.color = options.color;
    this.events = {};
    if(options.events){
        for(var i in options.events) {
            if(typeof options.events[i] == 'function'){
                this.events[i] = options.events[i];
            }
        }
    }
    this._init();
};

/**
 * This method fires when the view is fully loaded. Can be used to do any final actions.
 * @method
 */
MapSVG.Controller.prototype.viewDidLoad = function(){
    var _this = this;
    _this.updateScroll();
    if(this.autoresize){
        _this.adjustHeight();
        this.resizeSensor.setScroll();
    }
};

/**
 * Fires when the view appears after being hidden.
 * Should be overriden by a child class.
 * @abstract
 */
MapSVG.Controller.prototype.viewDidAppear = function(){};

/**
 * This method cannot be overriden and it fires always for all child classes.
 * @private
 */
MapSVG.Controller.prototype._viewDidLoad     = function(){
    this.updateScroll();
};

/**
 * This method fires when the view disappears.
 * Should be overriden by a child class.
 * @abstract
 */
MapSVG.Controller.prototype.viewDidDisappear = function(){};

/**
 * Updates the size of the scrollable container. Automatically fires when window size or content size changes.
 */
MapSVG.Controller.prototype.updateScroll = function(){
    if(!this.scrollable)
        return;
    var _this = this;
    this.contentWrap.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
    setTimeout(function(){
        _this.contentWrap.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
    },300);
};

/**
 * Adjusts height of the container to fit content.
 */
MapSVG.Controller.prototype.adjustHeight = function() {
    var _this = this;
    _this.container.height(_this.container.find('.mapsvg-auto-height').outerHeight()+(_this.toolbarView?_this.toolbarView.outerHeight():0));
};

/**
 * Initialization actions for all child classes. Should not be overriden.
 * @private
 */
MapSVG.Controller.prototype._init = function(){
    var _this = this;
    _this.render();
    _this.init();
};
/**
 * Initialization actions. Empty method. Can be overriden by child classes.
 */
MapSVG.Controller.prototype.init = function(){};

/**
 * This method must be overriden by a child class and return an HTML code for the toolbar.
 */
MapSVG.Controller.prototype.getToolbarTemplate = function(){
    return '';
};

/**
 * This method must be overriden by a child class and  to return an HTML code for the main content
 */
MapSVG.Controller.prototype.getMainTemplate = function(){
    return this.template;
};

/**
 * Renders the content.
 */
MapSVG.Controller.prototype.render = function(){

    var _this = this;
    this.view    = $('<div />').attr('id','mapsvg-controller-'+this.name).addClass('mapsvg-controller-view');

    // Wrap cointainer, includes scrollable container
    this.contentWrap    = $('<div />').addClass('mapsvg-controller-view-wrap');
    this.contentWrap2    = $('<div />');

    // Scrollable container
    this.contentSizer    = $('<div />').addClass('mapsvg-auto-height');
    this.contentView    = $('<div />').addClass('mapsvg-controller-view-content');
    this.contentSizer.append(this.contentView);

    if(this.scrollable){
        this.contentWrap.addClass('nano');
        this.contentWrap2.addClass('nano-content');
    }
    this.contentWrap.append(this.contentWrap2);
    this.contentWrap2.append(this.contentSizer);

    // Add toolbar if it exists in template file
    if(this.withToolbar && this.templates.toolbar){
        this.toolbarView = $('<div />').addClass('mapsvg-controller-view-toolbar');
        this.view.append(this.toolbarView);
    }

    this.view.append(this.contentWrap);

    // Add view into container
    this.container.append(this.view);
    this.container.data('controller', this);

    if(this.width)
        this.view.css({width: this.width});
    if(this.color)
        this.view.css({'background-color': this.color});

    _this.viewReadyToFill();
    this.redraw();

    setTimeout(function(){
        _this._viewDidLoad();
        _this.viewDidLoad();
        _this.setEventHandlersCommon();
        _this.setEventHandlers();
    },1);
};

/**
 * Fires right before rendering starts.
 */
MapSVG.Controller.prototype.viewReadyToFill = function(){
    var _this = this;
    if(_this.autoresize){
        _this.resizeSensor = new MapSVG.ResizeSensor(this.contentSizer[0], function(){
            _this.adjustHeight();
            _this.updateScroll();
            _this.events['resize'] && _this.events['resize'].call(_this, _this.mapsvg);
        });
    }
};

/**
 * Redraws the container.
 */
MapSVG.Controller.prototype.redraw = function(data){

    if(data !== undefined){
        this.data = data;
    }

    try{
        this.contentView.html( this.templates.main(this.data) );
    }catch(err){
        console.error(err);
        this.contentView.html("");
    }

    if(this.withToolbar && this.templates.toolbar)
        this.toolbarView.html( this.templates.toolbar(this.data) );

    this.updateTopShift();

    if(this.noPadding)
        this.contentView.css({padding: 0});

    this.updateScroll();
};

/**
 * Updates top shift of the main container depending on toolbar height
 */
MapSVG.Controller.prototype.updateTopShift = function(){
    var _this = this;
    if(!this.withToolbar)
        return;
    // bad, i know.
    _this.contentWrap.css({'top': _this.toolbarView.outerHeight(true)+'px'});
    setTimeout(function(){
        _this.contentWrap.css({'top': _this.toolbarView.outerHeight(true)+'px'});
    },100);
    setTimeout(function(){
        _this.contentWrap.css({'top': _this.toolbarView.outerHeight(true)+'px'});
    },200);
    setTimeout(function(){
        _this.contentWrap.css({'top': _this.toolbarView.outerHeight(true)+'px'});
        _this.updateScroll();
    },500);

};

/**
 * Set common event handlers for all child classes
 */
MapSVG.Controller.prototype.setEventHandlersCommon = function(){

};

/**
 * Set event handlers. Can be overriden by a child class.
 */
MapSVG.Controller.prototype.setEventHandlers = function(){
};

/**
 * Destroys the controller.
 */
MapSVG.Controller.prototype.destroy = function(){
    delete this.resizeSensor;
    this.view.empty().remove();
};

})( jQuery );