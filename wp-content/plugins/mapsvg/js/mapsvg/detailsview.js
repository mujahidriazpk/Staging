(function( $ ) {
/**
 * Details View controller. Large scrollable window with content and "X" close button
 * that can be placed in the map container, header/footer/sidebar or in a custom DIV container outside of the map.
 * @param {object} options
 * @extends MapSVG.Controller
 * @constructor
 */
MapSVG.DetailsController = function(options){
    MapSVG.Controller.call(this, options);
    this.modal = options.modal;
};
MapSVG.extend(MapSVG.DetailsController, MapSVG.Controller);

/**
 * Returns toolbar for the Details View
 * @returns {string}
 * @private
 */
MapSVG.DetailsController.prototype.getToolbarTemplate = function(){
    if(this.withToolbar)
        return '<div class="mapsvg-popover-close mapsvg-details-close"></div>';
    else
        return '';
};

/**
 * Final rendering actions
 * @private
 */
MapSVG.DetailsController.prototype.viewDidLoad = function(){
    var _this = this;
    this.events && this.events['shown'] && this.events['shown'].call(_this, _this.mapsvg);
    if(this.modal && MapSVG.isPhone && this.mapsvg.getData().options.detailsView.mobileFullscreen && !this.mobileCloseBtn){
        this.mobileCloseBtn = $('<button class="mapsvg-mobile-modal-close mapsvg-btn">'+_this.mapsvg.getData().options.mobileView.labelClose+'</button>');
        this.view.append(this.mobileCloseBtn);
    }
};

/**
 * Event handlers
 * @private
 */
MapSVG.DetailsController.prototype.setEventHandlers = function(){
    var _this = this;
    this.view.on('click','.mapsvg-popover-close, .mapsvg-mobile-modal-close',function(e){
        e.stopPropagation();
        _this.destroy();
        _this.events && _this.events['closed'] && _this.events['closed'].call(_this, _this.mapsvg);
    });
};

})( jQuery );
