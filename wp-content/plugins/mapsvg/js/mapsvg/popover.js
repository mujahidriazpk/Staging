(function( $ ) {
/**
 * Creates a scrollable popover in a map container.
 * @extends MapSVG.Controller
 * @param options
 * @constructor
 */
MapSVG.PopoverController = function(options){
    options.autoresize = true;
    MapSVG.Controller.call(this, options);
    this.point = options.point;
    this.yShift = options.yShift;
    this.mapObject = options.mapObject;
    this.id = this.mapObject.id+'_'+Math.random();
    this.container.data('popover-id', this.id);
    var _this = this;
};
MapSVG.extend(MapSVG.PopoverController, MapSVG.Controller);

/**
 * Sets a point where the popover should be shown
 * @param {Array} point - [x,y]
 */
MapSVG.PopoverController.prototype.setPoint = function(point){
    this.point = point;
};

/**
 * Returns HTML template for the popover toolbar
 * @returns {string}
 */
MapSVG.PopoverController.prototype.getToolbarTemplate = function(){
    if(this.withToolbar)
        return '<div class="mapsvg-popover-close"></div>';
    else
        return '';
};

/**
 * Final rendering steps of the popover
 * @private
 */
MapSVG.PopoverController.prototype.viewDidLoad = function(){
    MapSVG.Controller.prototype.viewDidLoad.call(this);
    var _this = this;
    if(MapSVG.isPhone && _this.mapsvg.getData().options.popovers.mobileFullscreen && !this.mobileCloseBtn){
        this.mobileCloseBtn = $('<button class="mapsvg-mobile-modal-close mapsvg-btn">'+_this.mapsvg.getData().options.mobileView.labelClose+'</button>');
        this.view.append(this.mobileCloseBtn);
    }
    this.adjustPosition();
    this.container.toggleClass('mapsvg-popover-animate', true);
    this.container.toggleClass('mapsvg-popover-visible', true);
    _this.adjustHeight();
    _this.updateScroll();
    this.resizeSensor.setScroll();
    this.events && this.events['shown'] && this.events['shown'].call(_this, _this.mapsvg);
};
/**
 * Adjusts height of the popover
 */
MapSVG.PopoverController.prototype.adjustHeight = function() {
    var _this = this;
    _this.container.height(_this.container.find('.mapsvg-auto-height').outerHeight()+(_this.toolbarView?_this.toolbarView.outerHeight():0));
};
/**
 * Adjsuts position of the popver. Gets called on zoom and map container resize.
 */
MapSVG.PopoverController.prototype.adjustPosition = function() {
    var _this = this;
    var pos   = _this.mapsvg.convertSVGToPixel([_this.point.x, _this.point.y]);
    pos[1]   -= _this.yShift;
    pos[0] = Math.round(pos[0]);
    pos[1] = Math.round(pos[1]);
    _this.container[0].style.transform = 'translateX(-50%) translate(' + pos[0] + 'px,' + pos[1]+ 'px)';
};

/**
 * Sets event handlers for the popover
 */
MapSVG.PopoverController.prototype.setEventHandlers = function(){
    var _this = this;
    $('body').off('.popover.mapsvg');

    this.view.on('click touchend','.mapsvg-popover-close, .mapsvg-mobile-modal-close',function(e){
        e.stopImmediatePropagation();
        _this.close();
    });

    $('body').on('mouseup.popover.mapsvg touchend.popover.mapsvg', function(e){
        if(_this.mapsvg.getData().isScrolling || $(e.target).closest('.mapsvg-directory').length || $(e.target).closest('.mapsvg-popover').length || $(e.target).hasClass('mapsvg-btn-map'))
            return;
        _this.close();
    });
};
/**
 * Closes the popover
 */
MapSVG.PopoverController.prototype.close = function(){
    var _this = this;
    if((this.container.data('popover-id')!= this.id) || !_this.container.is(':visible'))
        return;
    _this.destroy();
    if(_this.mapObject instanceof MapSVG.Region){
        _this.mapsvg.deselectRegion(_this.mapObject);
    }
    if(_this.mapObject instanceof MapSVG.Marker){
        _this.mapsvg.deselectAllMarkers();
    }

    _this.events && _this.events['closed'] && _this.events['closed'].call(_this, _this.mapsvg);
};
/**
 * Destroys the popover
 */
MapSVG.PopoverController.prototype.destroy = function() {
    var _this = this;
    _this.container.toggleClass('mapsvg-popover-animate', false);
    _this.container.toggleClass('mapsvg-popover-visible', false);
    MapSVG.Controller.prototype.destroy.call(this);

};
/**
 * Shows the popover
 */
MapSVG.PopoverController.prototype.show = function(){
    var _this = this;
    _this.container.toggleClass('mapsvg-popover-animate', true);
    _this.container.toggleClass('mapsvg-popover-visible', true);
};

})( jQuery );