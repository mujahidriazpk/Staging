/**
 * MapSVG 8.0.0 - Interactive Map Plugin
 *
 * Author: Roman S. Stepanov
 * http://codecanyon.net/user/RomanCode/portfolio?ref=RomanCode
 *
 * MapSVG @CodeCanyon: http://codecanyon.net/item/jquery-interactive-svg-map-plugin/1694201?ref=RomanCode
 * Licenses: http://codecanyon.net/licenses/regular_extended?ref=RomanCode
 */
var MapSVG = {};
// if(window) {
//     window.MapSVG = MapSVG;
// }

MapSVG.templatesLoaded = {};
MapSVG.urls = mapsvg_paths || {};

// Create Element.remove() function if not exists
if (!('remove' in Element.prototype)) {
    Element.prototype.remove = function() {
        if (this.parentNode) {
            this.parentNode.removeChild(this);
        }
    };
}

Math.hypot = Math.hypot || function() {
        var y = 0;
        var length = arguments.length;

        for (var i = 0; i < length; i++) {
            if (arguments[i] === Infinity || arguments[i] === -Infinity) {
                return Infinity;
            }
            y += arguments[i] * arguments[i];
        }
        return Math.sqrt(y);
    };
SVGElement.prototype.getTransformToElement = SVGElement.prototype.getTransformToElement || function(toElement) {
        return toElement.getScreenCTM().inverse().multiply(this.getScreenCTM());
};


(function($, window, MapSVG, Math){


    MapSVG.ResizeSensor = function(element, callback) {

        var _this = this;

        _this.element       = element;
        _this.callback      = callback;

        var  zIndex = parseInt(getComputedStyle(element));
        if(isNaN(zIndex)) { zIndex = 0; };
        zIndex--;

        _this.expand = document.createElement('div');
        _this.expand.style.position = "absolute";
        _this.expand.style.left = "0px";
        _this.expand.style.top = "0px";
        _this.expand.style.right = "0px";
        _this.expand.style.bottom = "0px";
        _this.expand.style.overflow = "hidden";
        _this.expand.style.zIndex = zIndex;
        _this.expand.style.visibility = "hidden";

        var  expandChild = document.createElement('div');
        expandChild.style.position = "absolute";
        expandChild.style.left = "0px";
        expandChild.style.top = "0px";
        expandChild.style.width = "10000000px";
        expandChild.style.height = "10000000px";
        _this.expand.appendChild(expandChild);

        _this.shrink = document.createElement('div');
        _this.shrink.style.position = "absolute";
        _this.shrink.style.left = "0px";
        _this.shrink.style.top = "0px";
        _this.shrink.style.right = "0px";
        _this.shrink.style.bottom = "0px";
        _this.shrink.style.overflow = "hidden";
        _this.shrink.style.zIndex = zIndex;
        _this.shrink.style.visibility = "hidden";

        var  shrinkChild           = document.createElement('div');
        shrinkChild.style.position = "absolute";
        shrinkChild.style.left     = "0px";
        shrinkChild.style.top      = "0px";
        shrinkChild.style.width    = "200%";
        shrinkChild.style.height   = "200%";
        _this.shrink.appendChild(shrinkChild);

        _this.element.appendChild(_this.expand);
        _this.element.appendChild(_this.shrink);

        var  size = element.getBoundingClientRect();

        _this.currentWidth  = size.width;
        _this.currentHeight = size.height;

        _this.setScroll();

        _this.expand.addEventListener('scroll', function(){_this.onScroll()});
        _this.shrink.addEventListener('scroll', function(){_this.onScroll()});
    };
    MapSVG.ResizeSensor.prototype.onScroll = function(){
        var _this = this;
        var  size = _this.element.getBoundingClientRect();

        var  newWidth = size.width;
        var  newHeight = size.height;

        if(newWidth != _this.currentWidth || newHeight != _this.currentHeight) {
            _this.currentWidth = newWidth;
            _this.currentHeight = newHeight;
            _this.callback();
        }

        this.setScroll();
    };
    MapSVG.ResizeSensor.prototype.setScroll = function(){
        this.expand.scrollLeft = 10000000;
        this.expand.scrollTop  = 10000000;
        this.shrink.scrollLeft = 10000000;
        this.shrink.scrollTop  = 10000000;
    };
    MapSVG.ResizeSensor.prototype.destroy = function(){
        this.expand.remove();
        this.shrink.remove();
    };

    MapSVG.userAgent = navigator.userAgent.toLowerCase();

    // Check for iPad/Iphone/Android
    MapSVG.touchDevice =
        (MapSVG.userAgent.indexOf("ipad") > -1) ||
        (MapSVG.userAgent.indexOf("iphone") > -1) ||
        (MapSVG.userAgent.indexOf("ipod") > -1) ||
        (MapSVG.userAgent.indexOf("android") > -1);

    MapSVG.ios =
        (MapSVG.userAgent.indexOf("ipad") > -1) ||
        (MapSVG.userAgent.indexOf("iphone") > -1) ||
        (MapSVG.userAgent.indexOf("ipod") > -1);

    MapSVG.android = MapSVG.userAgent.indexOf("android");

    // MapSVG.isPhone = window.matchMedia("only screen and (min-device-width: 320px) and (max-device-width: 812px)").matches;
    MapSVG.isPhone = window.matchMedia("only screen and (max-width: 812px)").matches;

    MapSVG.browser = {};
    MapSVG.browser.ie = MapSVG.userAgent.indexOf("msie") > -1 || MapSVG.userAgent.indexOf("trident") > -1 || MapSVG.userAgent.indexOf("edge") > -1 ? {} : false;
    MapSVG.browser.firefox = MapSVG.userAgent.indexOf("firefox") > -1;

    if (!String.prototype.trim) {
        String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};
    }

    // Create function for retrieving mouse coordinates
    MapSVG.mouseCoords = function(e){
        if(e.clientX){
            return {'x':e.clientX + $(document).scrollLeft(), 'y':e.clientY + $(document).scrollTop()};
        }if(e.pageX){
            return {'x':e.pageX, 'y':e.pageY};
        }else if(MapSVG.touchDevice){
            e = e.originalEvent || e;
            return e.touches && e.touches[0] ?
            {'x':e.touches[0].pageX, 'y':e.touches[0].pageY} :
            {'x':e.changedTouches[0].pageX, 'y':e.changedTouches[0].pageY};
        }
    };


    MapSVG.get = function(index){
        return jQuery('.mapsvg').eq(index).mapSvg();
    };

    MapSVG.getById = function(id){
        var len = jQuery('.mapsvg').length;
        for(var i = 0; i<len; i++){
            var m = MapSVG.get(i);
            if(m.id==id){
                return m;
            }
        }
    };


    MapSVG.extend = function(sub, base) {
        sub.prototype = Object.create(base.prototype);
        sub.prototype.constructor = sub;
    };

    MapSVG.ucfirst = function(string){
        return string.charAt(0).toUpperCase()+string.slice(1);
    };
    MapSVG.parseBoolean = function (string) {
        switch (String(string).toLowerCase()) {
            case "on":
            case "true":
            case "1":
            case "yes":
            case "y":
                return true;
            case "off":
            case "false":
            case "0":
            case "no":
            case "n":
                return false;
            default:
                return undefined;
        }
    };
    MapSVG.isNumber = function(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    };

    MapSVG.safeURL = function(url){
        if(url.indexOf('http://') == 0 || url.indexOf('https://') == 0)
            url = "//"+url.split("://").pop();
        return url.replace(/^.*\/\/[^\/]+/, '');
    };

    MapSVG.convertToText = function(obj) {
        //create an array that will later be joined into a string.
        var string = [];

        //is object
        //    Both arrays and objects seem to return "object"
        //    when typeof(obj) is applied to them. So instead
        //    I am checking to see if they have the property
        //    join, which normal objects don't have but
        //    arrays do.
        if (obj === null) {
            return null;
        } if (obj === undefined) {
            return '""';
        } else if (typeof(obj) == "object" && (obj.join == undefined)) {
            var prop;
            for (prop in obj) {
                if (obj.hasOwnProperty(prop)){
                    var key = '"'+prop.replace(/\"/g,'\\"')+'"'; //prop.search(/[^a-zA-Z]+/) === -1 ?  prop : ...
                    string.push( key + ': ' + MapSVG.convertToText(obj[prop]));
                }
            }
            return "{" + string.join(",") + "}";

            //is array
        } else if (typeof(obj) == "object" && !(obj.join == undefined)) {
            var prop;
            for(prop in obj) {
                string.push(MapSVG.convertToText(obj[prop]));
            }
            return "[" + string.join(",") + "]";

            //is function
        } else if (typeof(obj) == "function") {
            return obj.toString().replace('function anonymous','function');
            // string.push(obj.toString().replace('function anonymous','function'));

            //all other values can be done with JSON.stringify
        } else {
            return JSON.stringify(obj);
            // var s = JSON.stringify(obj);
            // string.push(s);
        }

        return string.join(",");
    };


    /*
     * CONTROLLER
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
    MapSVG.Controller.prototype.viewDidLoad = function(){
        var _this = this;
        _this.updateScroll();
        if(this.autoresize){
            _this.adjustHeight();
            this.resizeSensor.setScroll();
        }
    };
    MapSVG.Controller.prototype.viewDidAppear    = function(){};
    MapSVG.Controller.prototype.viewDidDisappear = function(){};
    MapSVG.Controller.prototype._viewDidLoad     = function(){
        this.updateScroll();
    };
    MapSVG.Controller.prototype.updateScroll = function(){
        if(!this.scrollable)
            return;
        var _this = this;
        this.contentWrap.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
        setTimeout(function(){
            _this.contentWrap.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
        },300);
    };

    MapSVG.Controller.prototype.adjustHeight = function() {
        var _this = this;
        _this.container.height(_this.container.find('.mapsvg-auto-height').outerHeight()+(_this.toolbarView?_this.toolbarView.outerHeight():0));
    };

    MapSVG.Controller.prototype.init = function(){};

    MapSVG.Controller.prototype._init = function(){
        var _this = this;
        _this.render();
        _this.init();
    };
    MapSVG.Controller.prototype.getToolbarTemplate = function(){
        return '';
    };
    MapSVG.Controller.prototype.getMainTemplate = function(){
        return this.template;
    };

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

    MapSVG.Controller.prototype.redraw = function(){

        this.contentView.html( this.templates.main(this.data) );

        if(this.withToolbar && this.templates.toolbar)
            this.toolbarView.html( this.templates.toolbar(this.data) );

        this.updateTopShift();

        if(this.noPadding)
            this.contentView.css({padding: 0});

        this.updateScroll();
    };
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
        },500);
    };

    MapSVG.Controller.prototype.setEventHandlersCommon = function(){
    };
    MapSVG.Controller.prototype.setEventHandlers = function(){
    };
    MapSVG.Controller.prototype.destroy = function(){
        delete this.resizeSensor;
        this.view.empty().remove();
    };


    /*
     * Directory Controller
     */
    MapSVG.DirectoryController = function(options){
        this.database = options.database;
        this.noPadding = true;
        this.position = options.position;
        this.search = options.search;
        this.filters = options.filters;
        MapSVG.Controller.call(this, options);
    };
    MapSVG.extend(MapSVG.DirectoryController, MapSVG.Controller);

    MapSVG.DirectoryController.prototype.getToolbarTemplate = function(){
        var _this = this;

        var t = '<div class="mapsvg-directory-search-wrap">';
        // t    += '<input class="mapsvg-directory-search" placeholder="{{menu.searchPlaceholder}}" />';

        // if(this.search){loadFiltersController
        //     t    += '<div class="mapsvg-directory-search-wrap-margin" >';
        //     t    += '<input class="mapsvg-directory-search" placeholder="{{options.menu.searchPlaceholder}}" />';
        //     t    += '</div>';
        // }

        t    += '<div class="mapsvg-directory-filter-wrap filter-wrap"></div>';
        t    += '</div>';
        t    += '</div>';

        return t;
    };

    MapSVG.DirectoryController.prototype.viewDidLoad = function() {

        var _this = this;
        this.menuBtn = $('<div class="mapsvg-button-menu"><i class="mapsvg-icon-menu"></i> ' + this.mapsvg.getData().options.mobileView.labelList + '</div>');
        this.mapBtn  = $('<div class="mapsvg-button-map"><i class="mapsvg-icon-map"></i> '   + this.mapsvg.getData().options.mobileView.labelMap  + '</div>');

        // Make directory hidden by default on mobiles
        if(MapSVG.isPhone){
            if(this.mapsvg.getData().options.menu.showFirst == 'map'){
                this.toggle(false);
            }else{
                this.toggle(true);
            }
        }

        this.mobileButtons = $('<div class="mapsvg-mobile-buttons"></div>');
        this.mobileButtons.append(this.menuBtn, this.mapBtn);

        if(this.mapsvg.getData().options.menu.on !== false)
            this.mobileButtons.appendTo(this.mapsvg.getData().$wrapAll);

        this.events && this.events['shown'] && this.events['shown'].call(this.view);
    };

    MapSVG.DirectoryController.prototype.setEventHandlers = function(){

        var _this = this;
        var _data = _this.mapsvg.getData();

        $(window).on('resize',function(){
            _this.updateTopShift();
        });

        this.menuBtn.on('click', function(){
            _this.toggle(true);
            // if(!$(this).hasClass('active')){
            //     _this.toggle();
            //     $(this).parent().find('div').removeClass('active');
            //     $(this).addClass('active');
            // }
        });
        this.mapBtn.on('click', function(){
            _this.toggle(false);
            _this.mapsvg.redraw();
            // if(!$(this).hasClass('active')){
            //     _this.toggle();
            //     $(this).parent().find('div').removeClass('active');
            //     $(this).addClass('active');
            // }
        });

        this.view.on('click.menu.mapsvg', '.mapsvg-directory-item', function (e) {

            e.preventDefault();
            var objID  = $(this).data('object-id');

            var regions;
            var marker;
            var detailsViewObject;
            var eventObject;

            _this.deselectItems();
            _this.selectItems(objID);

            if(MapSVG.isPhone && _this.mapsvg.getData().options.menu.showMapOnClick){
                _this.toggle(false);
            }


            if(_this.mapsvg.getData().options.menu.source == 'regions'){
                regions = [_this.mapsvg.getRegion(objID)];
                eventObject = regions[0];
                detailsViewObject = regions[0];
            } else {
                detailsViewObject = _this.database.getLoadedObject(objID);
                eventObject = detailsViewObject;
                if(detailsViewObject.regions){
                    regions = detailsViewObject.regions.map(function(region){
                        return _this.mapsvg.getRegion(region.id);
                    });
                }
            }

            if(detailsViewObject.location && detailsViewObject.location.marker)
                marker = detailsViewObject.location.marker;

            if(_this.mapsvg.getData().options.actions.directoryItem.click.showDetails){
                _this.mapsvg.loadDetailsView(detailsViewObject);
            }
            var skipPopover;

            if(regions && regions.length > 0) {

                if(_this.mapsvg.getData().options.actions.directoryItem.click.zoom){
                    _this.mapsvg.zoomTo(regions, _this.mapsvg.getData().options.actions.directoryItem.click.zoomToLevel);
                }

                if(regions.length > 1){
                    _this.mapsvg.setMultiSelect(true);
                }
                regions.forEach(function(region){

                    var center = region.getCenter();
                    e.clientX = center[0];
                    e.clientY = center[1];

                    if(_this.mapsvg.getData().options.actions.directoryItem.click.selectRegion){
                        //region.setSelected(true);
                        _this.mapsvg.selectRegion(region, true);
                    }
                    if(_this.mapsvg.getData().options.actions.directoryItem.click.showRegionPopover){
                        _this.mapsvg.showPopover(region);
                    }
                    if(_this.mapsvg.getData().options.actions.directoryItem.click.fireRegionOnClick){
                        var events = _this.mapsvg.getData().events;
                        if(events && events['click.region'])
                            events && events['click.region'].call(region, e, _this.mapsvg);
                    }
                });
                if(regions.length > 1){
                    _this.mapsvg.setMultiSelect(false, false);
                }

            }
            if(marker){
                if(_this.mapsvg.getData().options.actions.directoryItem.click.zoomToMarker){
                    _this.mapsvg.zoomTo(marker, _this.mapsvg.getData().options.actions.directoryItem.click.zoomToMarkerLevel);
                }
                if(_this.mapsvg.getData().options.actions.directoryItem.click.showMarkerPopover){
                    _this.mapsvg.showPopover(detailsViewObject);
                }
                if(_this.mapsvg.getData().options.actions.directoryItem.click.fireMarkerOnClick){
                    var events = _this.mapsvg.getData().events;
                    if(events && events['click.marker'])
                        events && events['click.marker'].call(marker, e, _this.mapsvg);
                }
            }

            _this.events['click'] && _this.events['click'].call($(this), e, eventObject, _this.mapsvg);

            var actions = _this.mapsvg.getData().options.actions;


            if(actions.directoryItem.click.goToLink){
                var linkParts = actions.directoryItem.click.linkField.split('.');
                var url;
                if(linkParts.length > 1){
                    var obj = linkParts.shift();
                    var attr = '.'+linkParts.join('.');
                    if(obj == 'Region'){
                        if(regions[0] && regions[0].data)
                            url = eval('regions[0].data'+attr);
                    }else{
                        if(detailsViewObject)
                            url = eval('detailsViewObject'+attr);
                    }

                    if(url){
                        if(actions.directoryItem.click.newTab){
                            var win = window.open(url, '_blank');
                            win.focus();
                        }else{
                            window.location.href = url;
                        }
                    }
                }
            }
            if(actions.directoryItem.click.showAnotherMap){
                if(_data.editMode){
                    alert('"Show another map" action is disabled in the preview');
                    return true;
                }
                var linkParts = actions.directoryItem.click.showAnotherMapField.split('.');
                var url;
                if(linkParts.length > 1){
                    var obj = linkParts.shift();
                    var attr = '.'+linkParts.join('.');
                    var map_id;
                    if(obj == 'Region'){
                        if(regions[0] && regions[0].data)
                            map_id = eval('regions[0].data'+attr);
                    }else{
                        if(detailsViewObject)
                            map_id = eval('detailsViewObject'+attr);
                    }

                    if(map_id){
                        var container = actions.directoryItem.click.showAnotherMapContainerId ? $('#'+actions.directoryItem.click.showAnotherMapContainerId) : _data.$map;
                        jQuery.get(ajaxurl, {action:"mapsvg_get",id: map_id},function(data){
                            if(container.find('svg').length)
                                container.mapSvg().destroy();
                            eval('var options = '+data);
                            container.mapSvg(options);
                        });
                    }
                }
            }
        }).on('mouseover.menu.mapsvg',  '.mapsvg-directory-item', function (e) {

            var objID = $(this).data('object-id');
            var regions;
            var detailsViewObject;
            var eventObject;

            if(_this.mapsvg.getData().options.menu.source == 'regions'){
                regions = [_this.mapsvg.getRegion(objID)];
                eventObject = regions[0];
                detailsViewObject = regions[0];
            } else {
                detailsViewObject = _this.database.getLoadedObject(objID);
                eventObject = detailsViewObject;
                if(detailsViewObject.regions){
                    regions = detailsViewObject.regions.map(function(region){
                        return _this.mapsvg.getRegion(region.id);
                    });
                }
            }

            if(regions && regions.length){
                _this.mapsvg.highlightRegions(regions);

                regions.forEach(function(region){
                    if(region && !region.disabled){
                        _this.mapsvg.getData().options.mouseOver && _this.mapsvg.getData().options.mouseOver.call(region, e, _this);
                    }
                });
            }
            _this.events['mouseover'] && _this.events['mouseover'].call($(this), e, eventObject, _this.mapsvg);
        }).on('mouseout.menu.mapsvg',  '.mapsvg-directory-item', function (e) {

            var objID = $(this).data('object-id');
            var regions;
            var detailsViewObject;
            var eventObject;

            if(_this.mapsvg.getData().options.menu.source == 'regions'){
                regions = [_this.mapsvg.getRegion(objID)];
                eventObject = regions[0];
                detailsViewObject = regions[0];
            } else {
                detailsViewObject = _this.database.getLoadedObject(objID);
                eventObject = detailsViewObject;
                if(detailsViewObject.regions){
                    regions = detailsViewObject.regions.map(function(region){
                        return _this.mapsvg.getRegion(region.id);
                    });
                }
            }

            if(regions && regions.length){
                _this.mapsvg.unhighlightRegions(regions);
                regions.forEach(function(region){
                    if(region && !region.disabled){
                        _this.mapsvg.getData().options.mouseOut && _this.mapsvg.getData().options.mouseOut.call(region, e, _this);
                    }
                });
            }

            _this.events['mouseout'] && _this.events['mouseout'].call($(this), e, eventObject, _this.mapsvg);

        }).on('click.menu.mapsvg','.mapsvg-filter-delete',function(e){
            var filterField = $(this).data('filter');
            _this.database.query.filters[filterField] = null;
            delete _this.database.query.filters[filterField];
            _this.mapsvg.deselectAllRegions();
            _this.mapsvg.loadDataObjects();
        });

    };


    MapSVG.DirectoryController.prototype.highlightItems = function(ids){
        var _this = this;
        if(typeof ids != 'object')
            ids = [ids];
        ids.forEach(function(id){
            _this.view.find('#mapsvg-directory-item-'+id).addClass('hover');
        });
    };
    MapSVG.DirectoryController.prototype.unhighlightItems = function(){
        this.view.find('.mapsvg-directory-item').removeClass('hover');
    };
    MapSVG.DirectoryController.prototype.selectItems = function(ids){
        var _this = this;
        if(typeof ids != 'object')
            ids = [ids];
        ids.forEach(function(id){
            _this.view.find('#mapsvg-directory-item-'+id).addClass('selected');
        });
    };
    MapSVG.DirectoryController.prototype.deselectItems = function(){
        this.view.find('.mapsvg-directory-item').removeClass('selected');
    };

    MapSVG.DirectoryController.prototype.addFilter = function(field){
      var schema = this.database.getSchema();
    };
    MapSVG.DirectoryController.prototype.loadItemsToDirectory = function(){
        var items;
        var _this = this;

        // if(this.mapsvg.getData().options.menu.source == 'regions'){
        //     var _items = [];
        //     items = this.mapsvg.getData().regions;
        //     items.forEach(function(item){
        //         if(!item.disabled)
        //             _items.push(item.forTemplate());
        //     });
        //     items = _items;
        // }else{
            items = this.database.getLoaded();

            if(this.database.table == 'regions'){

                var f = {};
                if(_this.mapsvg.getData().options.menu.filterout.field){
                    f.field = _this.mapsvg.getData().options.menu.filterout.field;
                    f.val   = _this.mapsvg.getData().options.menu.filterout.val;
                }

                items = items.filter(function(item){
                    var ok = true;
                    var status = _this.mapsvg.getData().options.regionStatuses;
                   if(status[item.status]){
                       ok = !status[item.status].disabled;
                   }

                   if(ok && f.field){
                       ok = (item[f.field] != f.val);
                   }
                   return ok;
                });
            }
        // }
        try{
            this.contentView.html( this.templates.main({'items': items}) );
        }catch (err) {
            console.error('MapSVG: Error in the "Directory item" template');
        }
        if(items.length == 0){
            this.contentView.html('<div class="mapsvg-no-results">'+this.mapsvg.getData().options.menu.noResultsText+'</div>');
        }
        this.setFilters();
        this.updateScroll();
    };
    MapSVG.DirectoryController.prototype.getRegion = function(id){
        var _this = this;
        var region;
        if(_this.mapsvg.getData().options.menu.source == 'regions'){
            region = _this.mapsvg.getRegion(id);
        }else{
            var obj = _this.database.getLoadedObject(id);
            if(obj.region_id)
                region = _this.mapsvg.getRegion(obj.region_id);
        }
        return region;
    };
    MapSVG.DirectoryController.prototype.setFilters = function(){
        var _this = this;

        /*
        var filters = this.toolbarView.find('.mapsvg-directory-filter-wrap');
        this.toolbarView.find('.mapsvg-filter-tag').remove();


        if(this.filterButton){
        }

        if(!_this.formBuilder && _this.mapsvg.getData().options.filters && _this.mapsvg.getData().options.filters.on){


            if(this.mapsvg.getData().options.filters.hide){

                if(!this.filterButton){
                    this.filterButton = $('<div class="mapsvg-show-filters"><button class="btn">'
                        +this.mapsvg.getData().options.filters.buttonText
                        +'</button></div>');
                    this.toolbarView.find('.mapsvg-directory-filter-wrap').empty().append(this.filterButton);
                    this.filterButton.on('click',function(){
                        _this.mapsvg.loadFiltersView();
                    });
                }

                var filtersCounter = Object.keys(_this.database.query.filters).length;
                filtersCounter = filtersCounter > 0 ? filtersCounter : '';
                this.filterButton.find('button').html(this.mapsvg.getData().options.filters.buttonText+' <b>'+filtersCounter+'</b>');

                return;
            }

            _this.formBuilder = new MapSVG.FormBuilder({
                container: _this.view.find('.mapsvg-directory-filter-wrap'),
                filtersMode: true,
                schema: _this.mapsvg.filtersSchema.getSchema(),
                editMode: false,
                mapsvg: _this.mapsvg,
                // mediaUploader: mediaUploader,
                // data: _dataRecord,
                admin: _this.admin,
                events: {
                    // save: function(data){_this.saveDataObject(data); },
                    // update:  function(data){ _this.updateDataObject(data); },
                    // close: function(){ _this.closeFormHandler(); },
                    // load: function(){_this.updateScroll(); }
                }
            });

            // _this.formBuilder.view.find('.select2').select2().on('select2:select',function(){
            //
            // });
            _this.formBuilder.view.on('change','select,input[type="radio"],input',function(){
                var filter = {};
                var field = $(this).data('parameter-name');

                if($(this).attr('name') === 'distanceAddress'){
                    return;
                }
                if($(this).attr('name') === 'distanceLatLng' || $(this).attr('name') === 'distanceLength'){
                    var distanceData = {
                        units: _this.formBuilder.view.find('[name="distanceUnits"]').val(),
                        latlng: _this.formBuilder.view.find('[name="distanceLatLng"]').val(),
                        length: _this.formBuilder.view.find('[name="distanceLength"]').val()
                        // ,
                        // address: _this.formBuilder.view.find('[name="distanceAddress"]').val()
                    };
                    if(distanceData.units && distanceData.length && distanceData.latlng){
                        filter.distance = distanceData;
                    } else {
                      return;
                    }
                } else {
                    filter[field] = $(this).val();
                }


                _this.database.query.setFilters(filter);

                // _this.formBuilder.view.find('select,input[type="radio"]').each(function(index){
                //     var field = $(this).data('parameter-name');
                //     var val = $(this).val();
                //     filters[field] = val;
                // });
                _this.database.getAll(filter);
            });
            setTimeout(
                function(){
                    _this.updateTopShift();
                    _this.updateScroll();
                }, 200);
        }

        if(_this.mapsvg.getData().options.filters && _this.mapsvg.getData().options.filters.on || ( _this.database.query.filters && Object.keys(_this.database.query.filters).length > 0)){

            for(var field_name in _this.database.query.filters){
                var field_value = _this.database.query.filters[field_name];
                var _field_name = field_name;
                var filterField = _this.mapsvg.filtersSchema.getField(_field_name);

                if(_this.mapsvg.getData().options.filters.on && filterField){
                    filters.find('[data-parameter-name="'+_field_name+'"]').val(field_value);
                }else{
                    if(field_name == 'regions'){
                        // check if there is such filter. If there is then change its value
                        // if there isn't then add a tag with close button
                        _field_name = '';
                        field_value = _this.mapsvg.getRegion(field_value).title || field_value;
                    }

                    if(field_name !== 'distance'){
                        filters.append('<div class="mapsvg-filter-tag">'+(_field_name?_field_name+': ':'')+field_value+' <span class="mapsvg-filter-delete" data-filter="'+field_name+'">×</span></div>');
                    }
                }
            }
            this.view.addClass('mapsvg-with-filter');

        }else{
            this.view.removeClass('mapsvg-with-filter');
        }
        */
        this.updateTopShift();
    };

    MapSVG.DirectoryController.prototype.toggle = function(on){
        var _this = this;
        if(on){
            this.container.parent().show();
            _this.mapsvg.getData().$mapContainer.hide();
            // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-map', true);
            // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-directory', ƒalse);
            this.menuBtn.addClass('active');
            this.mapBtn.removeClass('active');
            // redraw?
        }else{
            this.container.parent().hide();
            _this.mapsvg.getData().$mapContainer.show();
            // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-map', false);
            // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-directory', true);
            this.menuBtn.removeClass('active');
            this.mapBtn.addClass('active');
        }

        if(!this.container.parent().is(':visible')){
            if(MapSVG.isPhone){
                _this.mapsvg.getData().$wrap.css('height','auto');
                _this.updateScroll();
            }
        }else{
            if(MapSVG.isPhone && this.container.height() < parseInt(this.mapsvg.getData().options.menu.minHeight)){
                _this.mapsvg.getData().$wrap.css('height',parseInt(this.mapsvg.getData().options.menu.minHeight)+'px');
                _this.updateScroll();
            }
        }

        this.updateTopShift();
    };

    MapSVG.DirectoryController.prototype.addPagination = function(pager){
        this.contentView.append('<div class="mapsvg-pagination-container"></div>');
        this.contentView.find('.mapsvg-pagination-container').html(pager);
    };

    /*
     * Details View Controller
     */
    MapSVG.DetailsController = function(options){
        MapSVG.Controller.call(this, options);
        this.modal = options.modal;
    };
    MapSVG.extend(MapSVG.DetailsController, MapSVG.Controller);

    MapSVG.DetailsController.prototype.getToolbarTemplate = function(){
        if(this.withToolbar)
            return '<div class="mapsvg-popover-close mapsvg-details-close"></div>';
        else
            return '';
    };

    MapSVG.DetailsController.prototype.init = function(){
    };

    MapSVG.DetailsController.prototype.viewDidLoad = function(){
        var _this = this;
        this.events && this.events['shown'] && this.events['shown'].call(_this, _this.mapsvg);
        if(this.modal && MapSVG.isPhone && this.mapsvg.getData().options.detailsView.mobileFullscreen && !this.mobileCloseBtn){
            this.mobileCloseBtn = $('<button class="mapsvg-mobile-modal-close mapsvg-btn">'+_this.mapsvg.getData().options.mobileView.labelClose+'</button>');
            this.view.append(this.mobileCloseBtn);
        }
    };

    MapSVG.DetailsController.prototype.setEventHandlers = function(){
        var _this = this;
        this.view.on('click','.mapsvg-popover-close, .mapsvg-mobile-modal-close',function(e){
            e.stopPropagation();
            _this.destroy();
            _this.events && _this.events['closed'] && _this.events['closed'].call(_this, _this.mapsvg);
        });
    };

    MapSVG.FiltersController = function(options){
        MapSVG.DetailsController.call(this, options);
    };
    MapSVG.extend(MapSVG.FiltersController, MapSVG.DetailsController);


    /*
     * Popover View Controller
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


    MapSVG.PopoverController.prototype.setPoint = function(point){
        this.point = point;
    };

    MapSVG.PopoverController.prototype.getToolbarTemplate = function(){
        if(this.withToolbar)
            return '<div class="mapsvg-popover-close"></div>';
        else
            return '';
    };

    MapSVG.PopoverController.prototype.init = function(){
    };

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
    MapSVG.PopoverController.prototype.adjustHeight = function() {
        var _this = this;
        _this.container.height(_this.container.find('.mapsvg-auto-height').outerHeight()+(_this.toolbarView?_this.toolbarView.outerHeight():0));
    };
    MapSVG.PopoverController.prototype.adjustPosition = function() {
        var _this = this;
        var pos   = _this.mapsvg.convertSVGToPixel([_this.point.x, _this.point.y]);
        pos[1]   -= _this.yShift;
        _this.container[0].style.transform = 'translateX(-50%) translate(' + pos[0] + 'px,' + pos[1]+ 'px)';
    };


    MapSVG.PopoverController.prototype.setEventHandlers = function(){
        var _this = this;
        $('body').off('.popover.mapsvg');

        this.view.on('click touchend','.mapsvg-popover-close, .mapsvg-mobile-modal-close',function(e){
            e.stopImmediatePropagation();
            _this.close();
        });

        $('body').one('mouseup.popover.mapsvg touchend.popover.mapsvg ', function(e){
            setTimeout(function(){
                if(_this.mapsvg.getData().isScrolling || $(e.target).closest('.mapsvg-popover').length || $(e.target).hasClass('mapsvg-btn-map'))
                    return;
                _this.close();
            },50);
        });
    };
    MapSVG.PopoverController.prototype.close = function(){
        var _this = this;
        if((this.container.data('popover-id')!= this.id) || !_this.container.is(':visible'))
            return;
        _this.destroy();
        if(_this.mapObject instanceof MapSVG.Region){
            _this.mapsvg.deselectRegion(_this.mapObject);
        }

        _this.events && _this.events['closed'] && _this.events['closed'].call(_this, _this.mapsvg);
    };
    MapSVG.PopoverController.prototype.destroy = function() {
        var _this = this;
        _this.container.toggleClass('mapsvg-popover-animate', false);
        _this.container.toggleClass('mapsvg-popover-visible', false);
        MapSVG.Controller.prototype.destroy.call(this);

    };
    MapSVG.PopoverController.prototype.show = function(){
        var _this = this;
        _this.container.toggleClass('mapsvg-popover-animate', true);
        _this.container.toggleClass('mapsvg-popover-visible', true);
    };


})(jQuery, window, MapSVG, Math);



(function( $ ) {

    var mapSVG = {};



    //
    // REGION
    //
    function MapObject(jQueryObject, mapsvg){
        this.id = "";
        this.objects = [];
        this.events = {};
        this.data = {};
        this.node = jQueryObject;
        this.mapsvg = mapsvg;
        this.nodeType = jQueryObject[0].tagName;
    }

    MapObject.prototype.isMarker = function(){
        return this instanceof MapSVG.Marker;
    };
    MapObject.prototype.isRegion = function(){
        return this instanceof MapSVG.Region;
    };
    MapObject.prototype.setData = function(data){
        var _this = this;
        for(var name in data){
            _this.data[name] = data[name];
        }
    };
    MapObject.prototype.getBBox = function(){
        var _data = this.mapsvg.getData();
        var bbox = this instanceof Marker ? {x: this.x, y: this.y, width: this.default.width/_data.scale, height: this.default.height/_data.scale} : this.node[0].getBBox();
        bbox = $.extend(true, {}, bbox);

        if(!(this instanceof Marker)){
            var matrix = this.node[0].getTransformToElement(this.mapsvg.getData().$svg[0]);
            var x2 = bbox.x+bbox.width;
            var y2 = bbox.y+bbox.height;


            // transform a point using the transformed matrix
            var position = this.mapsvg.getData().$svg[0].createSVGPoint();
            position.x = bbox.x;
            position.y = bbox.y;
            position = position.matrixTransform(matrix);
            bbox.x = position.x;
            bbox.y = position.y;
            // var position = this.mapsvg.getData().$svg[0].createSVGPoint();
            position.x = x2;
            position.y = y2;
            position = position.matrixTransform(matrix);
            bbox.width = position.x - bbox.x;
            bbox.height = position.y - bbox.y;

        }

        return [bbox.x,bbox.y,bbox.width,bbox.height];
    };
    MapObject.prototype.getGeoBounds = function(){
        // var _data = this.mapsvg.getData();
        // var xoffset = _data.$map.offset().left;
        // var yoffset = _data.$map.offset().top;
        //
        //
        // var x = (this.node[0].getBoundingClientRect().left - xoffset + jQuery('body').scrollLeft())/_data.scale  + _data.viewBox[0];
        // var y = (this.node[0].getBoundingClientRect().top - yoffset + jQuery('body').scrollTop())/_data.scale   + _data.viewBox[1];
        // var w = this.node[0].getBoundingClientRect().width/_data.scale;
        // var h = this.node[0].getBoundingClientRect().height/_data.scale;
        // var bbox = this.node[0].getBBox();
        var bbox = this.getBBox();
        var sw = this.mapsvg.convertSVGToGeo(bbox[0], (bbox[1] + bbox[3]));
        var ne = this.mapsvg.convertSVGToGeo((bbox[0] + bbox[2]), bbox[1]);

        return {sw: sw,ne: ne};
    };
    MapObject.prototype.getComputedStyle = function(prop, node){
        node = node || this.node[0];
        var _p1,_p2;
        if(_p1 = node.getAttribute(prop)){
            return _p1;
        }else if(_p2 = node.getAttribute('style')){
            var s = _p2.split(';');
            var z = s.filter(function(e){
                var e = e.trim();
                var attr = e.split(':');
                if (attr[0]==prop)
                    return true;
            });
            if(z.length){
                return z[0].split(':').pop().trim();
            }
        }

        var parent = $(node).parent();
        var nodeType = parent.length ? parent[0].tagName : null;

        if (nodeType && nodeType!='svg')
            return this.getComputedStyle(prop,parent[0]);
        else
            return undefined;
    };
    MapObject.prototype.getStyle = function(prop){
        var _p1, _p2;
        if(_p1 = this.attr(prop)){
            return _p1;

        }else if(_p2 = this.attr('style')){
            var s = _p2.split(';');
            var z = s.filter(function(e){
                var e = e.trim();
                if (e.indexOf(prop)===0)
                    return e;
            });

            return z.length ? z[0].split(':').pop().trim() : undefined;
        }
        return "";
    };
    MapObject.prototype.getCenter = function(){

        // var c = this.getBBox();

        var x = this.node[0].getBoundingClientRect().left;
        var y = this.node[0].getBoundingClientRect().top;
        var w = this.node[0].getBoundingClientRect().width;
        var h = this.node[0].getBoundingClientRect().height;
        return [x+w/2,y+h/2];
    };
    MapObject.prototype.getCenterSVG = function(){
        var _this = this;
        var c = _this.getBBox();
        return {x: c[0]+c[2]/2, y: c[1]+c[3]/2};
    };
    MapObject.prototype.getCenterLatLng = function(yShift){
        yShift = yShift ? yShift : 0;
        var bbox = this.getBBox();
        var x = bbox[0] + bbox[2]/2;
        var y = bbox[1] + bbox[3]/2 - yShift;
        var latlng = this.mapsvg.convertSVGToGeo(x,y);
        return {lat: latlng[0], lng: latlng[1]};
    };
    MapObject.prototype.setTooltip = function(text){
        this.tooltip = text ? text :  undefined;
    };
    MapObject.prototype.setPopover = function(text){
        this.popover = text ? text :  undefined;
    };
    MapObject.prototype.attr = function(v1,v2){
        var svgDom = this.node[0];

        if(typeof v1 == "object"){
            $.each(v1,function(key,item){
                if (typeof item == "string" || typeof item == "number"){
                    svgDom.setAttribute(key,item);
                }
            });
        }
        else if(typeof v1 == "string" && (typeof v2 == "string" || typeof v2 == "number")){
            svgDom.setAttribute(v1,v2);
        }
        else if(v2 == undefined) {
            return svgDom.getAttribute(v1);
        }
    };
    MapObject.prototype.setId = function(id){
        if(!id) return false;
        this.id = id;
        this.node[0].setAttribute('id',id);
    };
    // EVENTS
    MapObject.prototype.on = function(event, callback) {
        if (!this.events[event]){
            this.events[event] = [];
        }
        this.events[event].push(callback);
    };

    MapObject.prototype.off = function(event, callback) {
        for(var eventName in this.events){
            if(this.events[eventName] && this.events[eventName].length > 0){
                if(eventName.indexOf(event) === 0 && event.length <= eventName){
                    this.events[eventName] = [];
                }
            }
        }
    };
    MapObject.prototype.trigger = function(event){
        var _this = this;
        for(var eventName in this.events){
            if(this.events[eventName] && this.events[eventName].length > 0){
                var eventNameReal = eventName.explode('.')[0];
                if(eventNameReal.indexOf(event)===0){
                    this.events[eventName].forEach(function(callback){
                        callback && callback.call(_this);
                    });
                }
            }
        }
    };



    //
    // REGION
    //
    function Region(jQueryObject, globalOptions, regionID, mapsvg){
        MapObject.call(this, jQueryObject);

        this.globalOptions = globalOptions;
        this.mapsvg = mapsvg;


        this.id = this.node.attr('id');

        if(this.id && globalOptions.regionPrefix){
            this.setId(this.id.replace(globalOptions.regionPrefix, ''));
        }

        // else{
        //     (!this.id)
        // }
        //     this.setId(this.nodeType+'_'+regionID.id++);
        //     this.autoID = true;
        // }


        this.id_no_spaces = this.id.replace(' ','_');

        this.title = this.node.attr('title');

        this.node[0].setAttribute('class',(this.node.attr('class')||'')+' mapsvg-region');

        this.setStyleInitial();

        var regionOptions  = globalOptions.regions && globalOptions.regions[this.id] ? globalOptions.regions[this.id] : null;

        this.disabled      = this.getDisabledState();
        this.disabled &&   this.attr('class',this.attr('class')+' mapsvg-disabled');

        this.default_attr  = {};
        this.selected_attr = {};
        this.hover_attr    = {};
        var selected = false;
        if(regionOptions && regionOptions.selected){
            selected = true;
            delete regionOptions.selected;
        }
        regionOptions && this.update(regionOptions);
        this.setFill();
        if(selected) {
            this.setSelected(true);
        }
        this.saveState();
    }
    MapSVG.extend(Region, MapObject);

    Region.prototype.setStyleInitial = function(){
        this.style = {fill: this.getComputedStyle('fill')};
        this.style.stroke = this.getComputedStyle('stroke') || '';
        var w;
        if(this.node.data('stroke-width')){
            w = this.node.data('stroke-width');
        }else{
            w = this.getComputedStyle('stroke-width');
            w = w ? w.replace('px','') : '1';
            w = w == "1" ? 1.2 : parseFloat(w);
        }
        this.style['stroke-width'] = w;
        this.node.data('stroke-width', w);
    };

    Region.prototype.saveState = function(){
        this.initialState = JSON.stringify(this.getOptions());
    };

    Region.prototype.getBBox = function(){
        var _data = this.mapsvg.getData();
        var bbox = this.node[0].getBBox();
        bbox = $.extend(true, {}, bbox);

        var matrix = this.node[0].getTransformToElement(this.mapsvg.getData().$svg[0]);
        var x2 = bbox.x+bbox.width;
        var y2 = bbox.y+bbox.height;


        // transform a point using the transformed matrix
        var position = this.mapsvg.getData().$svg[0].createSVGPoint();
        position.x = bbox.x;
        position.y = bbox.y;
        position = position.matrixTransform(matrix);
        bbox.x = position.x;
        bbox.y = position.y;
        // var position = this.mapsvg.getData().$svg[0].createSVGPoint();
        position.x = x2;
        position.y = y2;
        position = position.matrixTransform(matrix);
        bbox.width = position.x - bbox.x;
        bbox.height = position.y - bbox.y;

        return [bbox.x,bbox.y,bbox.width,bbox.height];
    };
    Region.prototype.changed = function(){
        return JSON.stringify(this.getOptions()) != this.initialState;
    };
    Region.prototype.edit = function(id){
        this.nodeOriginal = this.node.clone();
    };
    Region.prototype.editCommit = function(){
        this.nodeOriginal = null;
    };
    Region.prototype.editCancel = function(){
        // this.node[0].setAttribute('d', )
        this.nodeOriginal.appendTo(_this.mapsvg.getData().$svg);
        this.node = this.nodeOriginal;
        this.nodeOriginal = null;
    };

    Region.prototype.getOptions = function(forTemplate){
        var globals = this.globalOptions.regions[this.id];
        var o = {
            id: this.id,
            id_no_spaces: this.id_no_spaces,
            title: this.title,
            // disabled: this.disabled === this.getDisabledState(true) ? undefined : this.disabled,
            // status: this.status === undefined ? null : this.status,
            fill: this.globalOptions.regions[this.id] && this.globalOptions.regions[this.id].fill,
            tooltip: this.tooltip,
            popover: this.popover,
            href: this.href,
            target: this.target,
            data: this.data,
            gaugeValue: this.gaugeValue
        };
        if(forTemplate){
            o.disabled  = this.disabled;
            o.dataCounter = (this.data && this.data.length) || 0;
        }
        $.each(o,function(key,val){
            if(val == undefined){
                delete o[key];
            }
        });
        if(this.customAttrs){
            var that = this;
            this.customAttrs.forEach(function(attr){
                o[attr] = that[attr];
            });
        }
        return o;
    };
    Region.prototype.forTemplate = function(){
        var data = {
            id: this.id,
            title: this.title,
            objects: this.objects,
            data: this.data
        };
        for(var key in this.data){
            if(key!='title' && key!='id')
                data[key] = this.data[key];
        }

        return data;
    };

    Region.prototype.update = function(options){
        for(var key in options){
            // check if there's a setter for a property
            var setter = 'set'+MapSVG.ucfirst(key);
            if (setter in this)
                this[setter](options[key]);
            else{
                this[key] = options[key];
                this.customAttrs = this.customAttrs || [];
                this.customAttrs.push(key);
            }
        }
    };
    Region.prototype.setId = function(id){
        this.id = id;
        this.node.prop('id', id);
    };
    Region.prototype.setTitle = function(title){
        this.title = title;
    };
    Region.prototype.setStyle = function(style){
        $.extend(true, this.style, style);
        this.setFill();
    };
    Region.prototype.getChoroplethColor = function(){
        var o = this.globalOptions.gauge;
        var w = (parseFloat(this.data[this.globalOptions.regionChoroplethField]) - o.min) / o.maxAdjusted;

        return {
            r: Math.round(o.colors.diffRGB.r * w + o.colors.lowRGB.r),
            g: Math.round(o.colors.diffRGB.g * w + o.colors.lowRGB.g),
            b: Math.round(o.colors.diffRGB.b * w + o.colors.lowRGB.b),
            a: Math.round(o.colors.diffRGB.a * w + o.colors.lowRGB.a)
        };
    };

    Region.prototype.setFill = function(fill){

        var _this = this;


        if(this.globalOptions.colorsIgnore){
            this.node.css(this.style);
            return;
        }

        if(fill){
            var regions = {};
            regions[this.id] = {fill: fill};
            $.extend(true, this.globalOptions, {regions: regions});
        }else if(!fill && fill!==undefined && this.globalOptions.regions && this.globalOptions.regions[this.id] && this.globalOptions.regions[this.id].fill){
            delete this.globalOptions.regions[this.id].fill;
        }

        // Priority: gauge > status > options.fill > disabled > base > svg
        if(this.globalOptions.gauge.on && this.data && this.data[this.globalOptions.regionChoroplethField]){
            var rgb = this.getChoroplethColor();
            // var o = this.globalOptions.gauge;
            // var w = (parseFloat(this.data[this.globalOptions.regionChoroplethField]) - o.min) / o.maxAdjusted;
            //
            // var rgb = {
            //     r: Math.round(o.colors.diffRGB.r * w + o.colors.lowRGB.r),
            //     g: Math.round(o.colors.diffRGB.g * w + o.colors.lowRGB.g),
            //     b: Math.round(o.colors.diffRGB.b * w + o.colors.lowRGB.b),
            //     a: Math.round(o.colors.diffRGB.a * w + o.colors.lowRGB.a)
            // };
            this.default_attr['fill'] = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + rgb.a+')';

        }else if(this.status!==undefined && this.mapsvg.regionsDatabase && this.mapsvg.regionsDatabase.getSchemaFieldByType('status') && this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict && this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict[this.status] && this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict[this.status].color){
            this.default_attr['fill'] = this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict[this.status].color;

        }else if(this.globalOptions.regions[this.id] && this.globalOptions.regions[this.id].fill) {
            this.default_attr['fill'] = this.globalOptions.regions[this.id].fill;

        // }else if(this.disabled && this.globalOptions.colors.disabled){
        //     this.default_attr['fill'] = this.globalOptions.colors.disabled;

        }else if(this.globalOptions.colors.base){
            this.default_attr['fill'] = this.globalOptions.colors.base;

        }else if(this.style.fill!='none'){
            this.default_attr['fill'] = this.style.fill ? this.style.fill : this.globalOptions.colors.baseDefault;

        }else{
            this.default_attr['fill'] = 'none';
        }


        if(MapSVG.isNumber(this.globalOptions.colors.selected))
            this.selected_attr['fill'] = MapSVG.tinycolor(this.default_attr.fill).lighten(parseFloat(this.globalOptions.colors.selected)).toRgbString();
        else
            this.selected_attr['fill'] = this.globalOptions.colors.selected;

        if(MapSVG.isNumber(this.globalOptions.colors.hover))
            this.hover_attr['fill'] = MapSVG.tinycolor(this.default_attr.fill).lighten(parseFloat(this.globalOptions.colors.hover)).toRgbString();
        else
            this.hover_attr['fill'] = this.globalOptions.colors.hover;


        this.node.css('fill',this.default_attr['fill']);
        this.fill = this.default_attr['fill'];

        if(this.style.stroke!='none' && this.globalOptions.colors.stroke != undefined){
            this.node.css('stroke',this.globalOptions.colors.stroke);
        }else{
            var s = this.style.stroke == undefined ? '' : this.style.stroke;
            this.node.css('stroke', s);
        }

        if(this.selected)
            this.setSelected();

    };
    Region.prototype.setDisabled = function(on, skipSetFill){
        on = on !== undefined ? MapSVG.parseBoolean(on) : this.getDisabledState(); // get default disabled state if undefined
        var prevDisabled = this.disabled;
        this.disabled = on;
        on ? this.attr('class',this.attr('class')+' mapsvg-disabled') : this.attr('class',this.attr('class').replace(' mapsvg-disabled',''));
        if(this.disabled != prevDisabled)
            this.mapsvg.deselectRegion(this);
        !skipSetFill && this.setFill();
    };
    Region.prototype.setStatus = function(status){
        var statusOptions;
        if(statusOptions = this.globalOptions.regionStatuses && this.globalOptions.regionStatuses[status]){
            this.status = status;
            this.data.status = status;
            this.setDisabled(statusOptions.disabled, true);
        }else{
            this.status = undefined;
            this.data.status = undefined;
            this.setDisabled(false, true);
        }
        this.setFill();
    };
    Region.prototype.setSelected = function(on){
        //this.selected = MapSVG.parseBoolean(on);
        this.mapsvg.selectRegion(this);
    };
    Region.prototype.setGaugeValue = function(val){
        this.gaugeValue = $.isNumeric(val) ? parseFloat(val) : undefined;
    };
    Region.prototype.getDisabledState = function(asDefault){
        var opts = this.globalOptions.regions[this.id];
        if(!asDefault && opts && opts.disabled !== undefined){
            return opts.disabled;
        }else if(
            this.globalOptions.disableAll || this.style.fill == 'none' || this.id == 'labels' || this.id == 'Labels'
        ){
            return true;
        }else{
            return false;
        }
    };
    Region.prototype.highlight = function(){
        this.node.css({'fill' : this.hover_attr.fill});
    };
    Region.prototype.unhighlight = function(){
        this.node.css({'fill' : this.default_attr.fill});
    };
    Region.prototype.select = function(){
        this.node.css({'fill' : this.selected_attr.fill});
        this.selected = true;
    };
    Region.prototype.deselect = function(){
        this.node.css({'fill' : this.default_attr.fill});
        this.selected = false;
    };


    /*
    / MARKER
    */
    function Marker(params){

        this.imagePath = params.location.markerImageUrl;
        var img = $('<img src="'+this.imagePath+'" />').addClass('mapsvg-marker');
        MapObject.call(this, img, this.mapsvg);

        this.location = params.location;
        this.location.marker = this;
        this.mapsvg   = params.mapsvg;
        params.object && this.setObject(params.object);
        this.positioned = false;

        this.setId(this.mapsvg.markerId());

        if(MapSVG.isNumber(this.location.lat) && MapSVG.isNumber(this.location.lng)){
            var xy = this.mapsvg.convertGeoToSVG([this.location.lat, this.location.lng]);
            this.x = xy[0];
            this.y = xy[1];
        } else if (MapSVG.isNumber(this.location.x) && MapSVG.isNumber(this.location.y)){
            this.x = parseFloat(this.location.x);
            this.y = parseFloat(this.location.y);
        }else{
            return false;
        }

        this.setImage(this.imagePath);

        // this.adjustPosition();
        // this.mapsvg.markerAdd(this);

        this.positioned = true;
    }
    MapSVG.extend(Marker, MapObject);

    Marker.prototype.setId = function(id){
        MapObject.prototype.setId.call(this, id);
        this.mapsvg.updateMarkersDict();
    };
    Marker.prototype.getBBox = function(){
        var _data = this.mapsvg.getData();
        // TODo this place needs marker.width/height!
        var bbox = {x: this.x, y: this.y, width: this.width/_data.scale, height: this.  height/_data.scale};
        bbox = $.extend(true, {}, bbox);

        return [bbox.x,bbox.y,bbox.width,bbox.height];
    };

    Marker.prototype.getOptions = function(){
        var o = {
            // attached: this.attached,
            // isLink: this.isLink,
            // urlField: this.urlField,
            // dataId: this.dataId,
            // tooltip: this.tooltip,
            // popover: this.popover,
            // href: this.href,
            // target: this.target,
            // data: this.data,
            id: this.id,
            src: this.src,
            // width: this.default.width,
            // height: this.default.height,
            x: this.x,
            y: this.y,
            geoCoords: this.geoCoords
        };
        $.each(o,function(key,val){
            if(val == undefined){
                delete o[key];
            }
        });
        return o;
    };

    Marker.prototype.update = function(data, mapScale){
        for(var key in data){
            // check if there's a setter for a property
            var setter = 'set'+MapSVG.ucfirst(key);
            if (setter in this)
                this[setter](data[key],mapScale);
        }
    };
    Marker.prototype.setImage = function(src, mapScale){
        if(!src)
            return false;
        var _this = this;
        src = MapSVG.safeURL(src);
        mapScale = mapScale || this.mapScale;
        var img  = new Image();
        var marker = this;
        this.src = src;
        img.onload = function(){
            // marker.default.width = this.width;
            // marker.default.height = this.height;
            // marker.attr({x: marker.x, y: marker.y, width: this.width, height: this.height});
            marker.width = this.width;
            marker.height = this.height;
            if(marker.node[0].getAttribute('src')!=='src'){
                marker.node[0].setAttribute('src', src);
            }
            _this.adjustPosition();
        };
        img.src  = src;
        if(this.location){
            this.location.setImage(src);
        }

    };

    Marker.prototype.setXy = function(xy){
        this.x = xy[0];
        this.y = xy[1];
        this.xy = [this.x, this.y];

        if(this.location){
            this.location.x = this.x;
            this.location.y = this.y;
        }
        // this.node[0].setAttribute('x',  this.x);
        // this.node[0].setAttribute('y',  this.y);
        // this.adjustPosition(this.mapScale);
        // this.node.css({
        //     left:xy[0],
        //     bottom: xy[1]
        // });
        if(this.mapsvg.getData().mapIsGeo){
            this.geoCoords = this.mapsvg.convertSVGToGeo(xy[0], xy[1]);
            this.location && this.location.setLatLng({lat: this.geoCoords[0], lng: this.geoCoords[1] });
        }

        this.adjustPosition();
        if(this.onChange)
            this.onChange.call(this);
    };
    Marker.prototype.moveToClick = function(xy){

        var _data = this.mapsvg.getData();
        var markerOptions = {};

        xy[0] = xy[0] + _data.viewBox[0];
        xy[1] = xy[1] + _data.viewBox[1];


        if(_data.mapIsGeo)
            this.geoCoords = this.mapsvg.convertSVGToGeo(xy[0], xy[1]);

        markerOptions.xy = xy;
        this.update(markerOptions);
    };

    Marker.prototype.adjustPosition = function(mapScale){
        // var w = this.default.cx !== undefined ? this.default.cx : this.default.width/2;
        // var h = this.default.cy !== undefined ? this.default.cy : this.default.height;
        // var dx = w - w/mapScale;
        // var dy = h - h/mapScale;
        // this.attr('width',this.default.width/(mapScale));
        // this.attr('height',this.default.height/(mapScale));
        // this.attr('transform','translate('+dx+','+dy+')');
        // this.mapScale = mapScale;
        var _this = this;
        var pos = _this.mapsvg.convertSVGToPixel([this.x, this.y]);

        // pos[0] = pos[0] - (_data.layers.popovers.offset().left - _data.$map.offset().left);
        // pos[1] = pos[1] - (_data.layers.popovers.offset().top - _data.$map.offset().top);

        if(pos[0] > 30000000){
            this.node[0].style.left = pos[0]-30000000;
            pos[0] = 30000000;
            if(this.textLabel) {
                this.textLabel[0].style.left = pos[0]-30000000;
            }
        }else{
            this.node[0].style.left = 0;
        }
        if(pos[1] > 30000000){
            this.node[0].style.top = pos[1]-30000000;
            pos[1] = 30000000;
            if(this.textLabel) {
                this.textLabel[0].style.top = pos[1]-30000000;
            }
        }else{
            this.node[0].style.top = 0;
        }

        pos[0] -= this.width/2;
        pos[1] -= this.height;

        // this.node[0].style.transform = 'translate(-50%,-50%) translate('+pos[0]+'px,'+pos[1]+'px)';
        this.node[0].style.transform = 'translate('+pos[0]+'px,'+pos[1]+'px)';


        // this.node[0].style.transform = 'translate(-50%,-100%) translate('+pos[0]+'px,'+pos[1]+'px)';

        if(this.textLabel){
            this.textLabel[0].style.transform = 'translate(-50%,-100%) translate(' + pos[0] + 'px,' + (pos[1] - (this.height||23)) + 'px)';
        }
    };

    Marker.prototype.setGeoCoords = function(coords){
        if(typeof coords == "string"){
            coords = coords.trim().split(',');
            coords = [parseFloat(coords[0]),parseFloat(coords[1])];
        }
        if(typeof coords == 'object' && coords.length==2){
            if($.isNumeric(coords[0]) && $.isNumeric(coords[1])){
                var xy = this.mapsvg.convertGeoToSVG(coords);
                this.setXy(xy);
            }
        }
    };
    // GET MARKER COORDINATES TRANSLATED TO 1:1 SCALE (used when saving new added markers)
    Marker.getDefaultCoords = function(markerX, markerY, markerWidth, markerHeight, mapScale){
        markerX       = parseFloat(markerX);
        markerY       = parseFloat(markerY);
        markerWidth   = parseFloat(markerWidth);
        markerHeight  = parseFloat(markerHeight);
        // markerX       = markerX + markerWidth/(2*mapScale) - markerWidth/2;
        // markerY       = markerY + markerHeight/mapScale - markerHeight;


        return [markerX, markerY];
    };
    Marker.prototype.drag = function(startCoords, scale, endCallback, clickCallback){
        var _this = this;
        this.ox = this.x;
        this.oy = this.y;

        $('body').on('mousemove.drag.mapsvg',function(e){
            e.preventDefault();
            _this.mapsvg.getData().$map.addClass('no-transitions');
            //$('body').css('cursor','move');
            var mouseNew = MapSVG.mouseCoords(e);
            var dx = mouseNew.x - startCoords.x;
            var dy = mouseNew.y - startCoords.y;
            _this.setXy([_this.ox + dx/scale, _this.oy + dy/scale])
            // _this.x = ;
            // _this.y = ;

            // _this.attr({x:_this.x, y:_this.y});
            //_this.attr('transform','translate('+dx/scale+','+dy/scale+')');
        });
        $('body').on('mouseup.drag.mapsvg',function(e){
            e.preventDefault();
            _this.undrag();
            var mouseNew = MapSVG.mouseCoords(e);
            var dx = mouseNew.x - startCoords.x;
            var dy = mouseNew.y - startCoords.y;
            _this.setXy([_this.ox + dx/scale, _this.oy + dy/scale])

            // _this.x = _this.ox + dx/scale;
            // _this.y = _this.oy + dy/scale;
            // _this.attr({x:_this.x, y:_this.y});
            endCallback.call(_this);
            if(_this.ox == _this.x && _this.oy == _this.y)
                clickCallback.call(_this);
        });
    };
    Marker.prototype.undrag = function(){
        //this.node.closest('svg').css('pointer-events','auto');
        //$('body').css('cursor','default');
        $('body').off('.drag.mapsvg');
        this.mapsvg.getData().$map.removeClass('no-transitions');
    };
    Marker.prototype.delete = function(){
        if(this.textLabel){
            this.textLabel.remove();
            this.textLabel = null;
        }
        this.node.empty().remove();
        this.mapsvg.markerDelete(this);
    };
    Marker.prototype.setObject = function(obj){
        this.object = obj;
        this.objects = [obj];
        this.node.attr('data-object-id', this.object.id);
    };
    Marker.prototype.hide = function(){
        this.node.addClass('mapsvg-marker-hidden');
        if(this.textLabel){
            this.textLabel.hide();
        }
    };
    Marker.prototype.show = function(){
        this.node.removeClass('mapsvg-marker-hidden');
        if(this.textLabel){
            this.textLabel.show();
        }
    };
    Marker.prototype.clusterize = function(){
        // this.clusterized = true;
        // this.node.addClass('mapsvg-object-clusterized');
        // if(this.textLabel){
        //     this.textLabel.addClass('mapsvg-object-clusterized');
        // }
    };
    Marker.prototype.unclusterize = function(){
        // this.clusterized = false;
        // this.node.removeClass('mapsvg-object-clusterized');
        // if(this.textLabel){
        //     this.textLabel.removeClass('mapsvg-object-clusterized');
        // }
    };

    function MarkersCluster(options){

        this.mapsvg = options.mapsvg;
        this.x = options.x; // SVG-x (not pixel-x)
        this.y = options.y; // SVG-y (not pixel-y)
        this.cellX = options.cellX; // SVG-x (not pixel-x)
        this.cellY = options.cellY; // SVG-y (not pixel-y)
        this.markers = options.markers || [];

        this.cellSize = 50;
        this.width = 30;

        var _this = this;

        var node = jQuery('<div class="mapsvg-marker-cluster">'+this.markers.length+'</div>');

        node.data("cluster", this);

        MapObject.call(this, node, this.mapsvg);

        if(this.markers.length < 2){
            this.node.hide(); // don't show cluster at the start
            // if(this.markers[0]){
            //     _this.mapsvg.markerAdd(this.markers[0]);
            // }
        }



        // var xy = _this.mapsvg.convertSVGToPixel([this.x, this.y]);

        // this.cellX = Math.ceil(xy[0] / this.cellSize );
        // this.cellY = Math.ceil(xy[1] / this.cellSize );
        //
        // var xysvg = _this.mapsvg.convertPixelToSVG([(this.cellX-1)*this.cellSize, (this.cellY-1)*this.cellSize]);
        //
        // this.x = xysvg[0];
        // this.y = xysvg[1];

        // if(options.markers && typeof options.markers == 'object' && options.markers.length){
        //     options.markers.forEach(function(marker){
        //         _this.addMarker(marker);
        //     });
        // }
        //

        // this.mapsvg.markersClusterAdd(this);
        this.adjustPosition();
        this.setEventHandlers();
    }
    MapSVG.extend(MarkersCluster, MapObject);



    MarkersCluster.prototype.addMarker = function(marker){
        this.markers.push(marker);
        if(this.markers.length > 1){
            if(this.markers.length === 2){
                // this.markers[0].clusterize();
                this.node.show();
            }
            if(this.markers.length === 2){

                var x = this.markers.map(function(m){ return m.x });
                this.min_x = Math.min.apply(null, x);
                this.max_x = Math.max.apply(null, x);

                var y = this.markers.map(function(m){ return m.y });
                this.min_y = Math.min.apply(null, y);
                this.max_y = Math.max.apply(null, y);

                this.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.y = this.min_y + ((this.max_y - this.min_y) / 2);
            }
            if(this.markers.length > 2){
                if(marker.x < this.min_x){
                    this.min_x = marker.x;
                } else if(marker.x > this.max_x){
                    this.max_x = marker.x;
                }
                if(marker.y < this.min_y){
                    this.min_y = marker.y;
                } else if(marker.x > this.max_x){
                    this.max_y = marker.y;
                }
                this.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.y = this.min_y + ((this.max_y - this.min_y) / 2);
            }
            // marker.clusterize();
        } else {
            this.x = marker.x;
            this.y = marker.y;
        }

        this.node.text(this.markers.length);
        this.adjustPosition();
    };
    MarkersCluster.prototype.canTakeMarker = function(marker){

        var _this = this;

        var xy = _this.mapsvg.convertSVGToPixel([marker.x, marker.y]);

        return (this.cellX === Math.ceil(xy[0] / this.cellSize )
            &&
           this.cellY === Math.ceil(xy[1] / this.cellSize ))
        // return (this.cellX === Math.ceil(marker.x * _this.mapsvg.getScale() / 30 )
        //     &&
        //    this.cellY === Math.ceil(marker.y * _this.mapsvg.getScale() / 30 ))
    };
    MarkersCluster.prototype.destroy = function(){
        this.markers.forEach(function(marker){
            marker.unclusterize();
        });
        this.markers = null;
        this.node.remove();
    };

    MarkersCluster.prototype.adjustPosition = function(mapScale){

        var _this = this;


        var pos = _this.mapsvg.convertSVGToPixel([this.x, this.y]);

        if(pos[0] > 30000000){
            this.node[0].style.left = pos[0]-30000000;
            pos[0] = 30000000;
        }else{
            this.node[0].style.left = 0;
            // this.node[0].style.left = pos[0]+'px';
        }
        if(pos[1] > 30000000){
            this.node[0].style.top = pos[1]-30000000;
            pos[1] = 30000000;
        }else{
            this.node[0].style.top = 0;
            // this.node[0].style.top = pos[1]+'px';
        }


        // pos[0] += (this.cellSize/2)*(this.mapsvg.getScale()/this.initialScale) - this.width/2; // todo
        // pos[1] += (this.cellSize/2)*(this.mapsvg.getScale()/this.initialScale) - this.width/2;
        pos[0] -= this.width/2;
        pos[1] -= this.width/2;

        // this.node[0].style.transform = 'translate(-50%,-50%) translate('+pos[0]+'px,'+pos[1]+'px)';
        this.node[0].style.transform = 'translate('+pos[0]+'px,'+pos[1]+'px)';
    };

    MarkersCluster.prototype.setEventHandlers = function(){
        var _this = this;
        // this.node.on("click", function(){
        //     _this.mapsvg.zoomTo(_this.markers);
        // });
    };


    MapSVG.MapObject = MapObject;
    MapSVG.Region = Region;
    MapSVG.Marker = Marker;
    MapSVG.MarkersCluster = MarkersCluster;

    //
    // Location
    //
    function LocationAddress(fields){
        for(var i in fields){
            this[i] = fields[i];
        }
    }

    LocationAddress.prototype.__defineGetter__('state', function(){
        return this.country_short === 'US' ? this.administrative_area_level_1 : null;
    });
    LocationAddress.prototype.__defineGetter__('state_short', function(){
        return this.country_short === 'US' ? this.administrative_area_level_1_short : null;
    });

    LocationAddress.prototype.__defineGetter__('county', function(){
        return this.country_short === 'US' ? this.administrative_area_level_2 : null;
    });

    LocationAddress.prototype.__defineGetter__('zip', function(){
        return this.postal_code;
    });

    function Location(options, mapsvg){
        this.img = options.img;
        this.setImage(this.img);
        this.lat = options.lat!==undefined ? parseFloat(options.lat) : undefined;
        this.lng = options.lng!==undefined ? parseFloat(options.lng) : undefined;
        this.x   = options.x!==undefined ? parseFloat(options.x) : undefined;
        this.y   = options.y!==undefined ? parseFloat(options.y) : undefined;
        this.address = new LocationAddress(options.address);
    }
    Location.prototype.setImage = function(img){
        var src = img.split('/').pop();
        if(img.indexOf('uploads')!==-1){
            src = 'uploads/'+src;
        }
        this.img = src;
    };
    Location.prototype.setLatLng = function(latlng){
        this.lat = latlng.lat;
        this.lng = latlng.lng;
    };
    Location.prototype.__defineGetter__('markerImageUrl', function(){
        if ((this.img && this.img.indexOf('uploads/') === 0)){
            return MapSVG.urls.uploads+'markers/'+(this.img.replace('uploads/',''));
        } else {
            return MapSVG.urls.root+'markers/'+(this.img || '_pin_default.png');
        }
    });
    Location.prototype.toJSON = function(){
        return {
            img: this.img,
            lat: this.lat,
            lng: this.lng,
            x: this.x,
            y: this.y,
            address: this.address
        };
    }
    MapSVG.Location = Location;



    // START MAPSVG

    var instances = {};
    var globalID  = 0;



    var defRegionTemplate =
        '<!-- Region fields are available in this template -->\n' +
        '<h5>{{#if title}} {{title}} {{else}} {{id}} {{/if}}</h5>\n' +
        '<p>Status: {{status_text}}</p>\n\n' +
        '<!-- Show all linked Database Objects: -->\n' +
        '{{#each objects}}\n\n' +
        '  <!-- DB Object are available inside of this block -->\n\n' +
        '  <h5>{{title}}</h5>\n' +
        '  <!-- When you need to render a field as HTML, use 3 curly braces instead of 2:-->\n' +
        '  <p>{{{description}}}</p>\n' +
        '  <p><em>{{location.address.formatted}}</em></p>\n\n' +
        '  <!-- Show all images: -->\n' +
        '  {{#each images}}\n' +
        '    <!-- Image fields "thumbnail", "medium", "full" -->\n' +
        '    <!-- are available in this block                -->\n' +
        '    <img src="{{thumbnail}}" />\n' +
        '  {{/each}}\n\n' +
        '{{/each}}';

    var defDBTemplate =
        '<!-- DB Object fields are available in this template. -->\n' +
        '<h5>{{title}}</h5>\n' +
        '<!-- When you need to render a fields as HTML, use 3 curly braces instead of 2:-->\n' +
        '<p>{{{description}}}</p>\n' +
        '<p><em>{{location.address.formatted}}</em></p>\n\n' +
        '<!-- Show all images: -->\n' +
        '{{#each images}}\n' +
        '  <!-- Image fields "thumbnail", "medium", "full" -->\n' +
        '  <!-- are available in this block                -->\n' +
        '  <img src="{{thumbnail}}" />\n' +
        '{{/each}}\n\n' +
        '<!-- Show all linked Regions, comma-separated: -->\n' +
        '<p> Regions: \n' +
        '  {{#each regions}}\n' +
        '    <!-- Region fields are available in this block -->\n' +
        '    {{#if title}}\n'+
        '      {{title}}\n'+
        '    {{else}}\n'+
        '      {{id}}\n'+
        '    {{/if}}{{#unless @last}}, {{/unless}}\n'+
        '  {{/each}}\n' +
        '</p>';

    var dirItemItemTemplate =
        '<!-- If Directory Source = Database: DB Object fields are available in this template -->\n' +
        '<!-- If Directory Source = Regions: Region fields are available in this template -->\n' +
        '{{title}}';


    // Default options
    var defaults = {
        markerLastID        : 0,
        regionLastID        : 0,
        dataLastID          : 1,
        disableAll          : false,
        width               : null,
        height              : null,
        lockAspectRatio     : false,
        padding             : {top: 0, left: 0, right: 0, bottom: 0},
        maxWidth            : null,
        maxHeight           : null,
        minWidth            : null,
        minHeight           : null,
        loadingText         : 'Loading map...',
        //colors              : {base: "#E1F1F1", background: "#eeeeee", hover: "#548eac", selected: "#065A85", stroke: "#7eadc0"},
        colorsIgnore              : false,
        colors              : {baseDefault: "#000000",
                               background: "#eeeeee",
                               selected: 40,
                               hover: 20,
                               directory: '#fafafa',
                               detailsView: '',
                               status: {},
                               clusters: "",
                               clustersBorders: "",
                               clustersText: "",
                               clustersHover: "",
                               clustersHoverBorders: "",
                               clustersHoverText: ""
                               },
        regions             : {},
        clustering          : {on: false},
        viewBox             : [],
        cursor              : 'default',
        manualRegions       : false,
        onClick             : null,
        mouseOver           : null,
        mouseOut            : null,
        menuOnClick         : null,
        beforeLoad          : null,
        afterLoad           : null,
        zoom                : {on: false, limit: [0,10], delta: 2, buttons: {on: true, location: 'right'}, mousewheel: true, fingers: true},
        scroll              : {on: false, limit: false, background: false, spacebar: false},
        responsive          : true,
        tooltips            : {on: false, position: 'bottom-right', template: '', maxWidth: '', minWidth: 100},
        popovers            : {on: false, position: 'top', template: '', centerOn: true, width: 300, maxWidth: 50, maxHeight: 50},
        multiSelect         : false,
        regionStatuses      : {
            '1': {"label": "Enabled", "value": '1', "color": "", "disabled": false},
            '0': {"label": "Disabled", "value": '0', "color": "", "disabled": true}
        },
        events              : {
            'afterLoad' : 'function(){\n' +
            '  // var mapsvg = this;\n'+
            '  // var regions = mapsvg.getData().regions();\n'+
            '  // var dbObjects = mapsvg.database.getLoaded();\n'+
            '}',
            'beforeLoad' : 'function(){\n' +
            '  // var mapsvg = this;\n'+
            '  // var settings = mapsvg.getData().options;\n' +
            '  // console.log(settings);\n' +
            '}',
            'databaseLoaded' : 'function (){\n' +
            '  // var mapsvg = this;\n'+
            '  // var dbObjects = mapsvg.database.getLoaded();\n'+
            '}',
            'click.region' : 'function (e, mapsvg){\n' +
            '  // var region = this;\n'+
            '  // console.log(region);\n'+
            '}',
            'mouseover.region' : 'function (e, mapsvg){\n' +
            '  // var region = this;\n'+
            '  // console.log(region);\n'+
            '}',
            'mouseout.region' : 'function (e, mapsvg){\n' +
            '  // var region = this;\n'+
            '  // console.log(region);\n'+
            '}',
            'click.marker' : 'function (e, mapsvg){\n' +
            '  // var marker = this;\n'+
            '  // console.log(marker);\n'+
            '}',
            'mouseover.marker' : 'function (e, mapsvg){\n' +
            '  // var marker = this;\n'+
            '  // console.log(marker);\n'+
            '}',
            'mouseout.marker' : 'function (e, mapsvg){\n' +
            '  // var marker = this;\n'+
            '  // console.log(marker);\n'+
            '}',
            'click.directoryItem' : 'function (e, regionOrObject, mapsvg){\n' +
            '  // var itemjQueryObject = this;\n'+
            '}',
            'mouseover.directoryItem' : 'function (e, regionOrObject, mapsvg){\n' +
            '  // var itemjQueryObject = this;\n'+
            '}',
            'mouseout.directoryItem' : 'function (e, regionOrObject, mapsvg){\n' +
            '  // var itemjQueryObject = this;\n'+
            '}',
            'shown.popover' : 'function (mapsvg){\n' +
            '  // var popoverjQueryObject = this;\n'+
            '}',
            'closed.popover' : 'function (mapsvg){\n' +
            '  // var popoverjQueryObject = this;\n'+
            '}',
            'closed.detailsView' : 'function (mapsvg){\n' +
            '  // var detailsjQueryObject = this;\n'+
            '}',
            'shown.detailsView' : 'function (mapsvg){\n' +
            '  // var detailsjQueryObject = this;\n'+
            '}'
        },
        css: ".mapsvg-tooltip {\n\n}\n" +
            ".mapsvg-popover {\n\n}\n" +
            ".mapsvg-details-container {\n\n}\n" +
            ".mapsvg-directory-item {\n\n}\n" +
            ".mapsvg-region-label {\n" +
            "  /* background-color: rgba(255,255,255,.6); */\n" +
            "  font-size: 11px;\n" +
            "  padding: 3px 5px;\n" +
            "  border-radius: 4px;\n" +
            "}\n" +
            ".mapsvg-marker-label {\n" +
            "  padding: 3px 5px;\n" +
            "  /*\n" +
            "  border-radius: 4px;\n" +
            "  background-color: white;\n" +
            "  margin-top: -4px;\n" +
            "  */\n}\n" +
            ".mapsvg-filters-wrap {\n\n}\n" +
            "\n\n\n\n\n\n"
        ,
        templates           : {
            popoverRegion: defRegionTemplate,
            popoverMarker: defDBTemplate,
            tooltipRegion: '<!-- Region fields are available in this template -->\n{{id}} - {{title}}',
            tooltipMarker: '<!-- DB Object fields are available in this template -->\n{{title}}',
            directoryItem: dirItemItemTemplate,
            detailsView: defDBTemplate,
            detailsViewRegion: defRegionTemplate,
            labelMarker: '<!-- DB Object fields are available in this template -->\n{{title}}',
            labelRegion: '<!-- Region fields are available in this template -->\n{{title}}',
            labelLocation: 'You are here!',

        },
        gauge               : {on: false, labels: {low: "low", high: "high"}, colors: {lowRGB: null, highRGB: null, low: "#550000", high: "#ee0000"}, min: 0, max: 0},
        filters: {
            on: false,
            source: 'database',
            location: 'leftSidebar',
            modalLocation: 'mapContainer',
            width: '100%',
            hide: false,
            buttonText: 'Filters',
            padding: ''
        },
        menu                : {
            on: false,
            location: 'leftSidebar',
            search: false,
            containerId: '',
            searchPlaceholder: "Search...",
            searchFallback: false,
            source: 'database',
            showFirst: 'map',
            showMapOnClick: true,
            minHeight: '400px',
            sortBy: 'id',
            sortDirection: 'desc',
            clickActions: {
                region: 'default',
                marker: 'default',
                directoryItem: {
                    triggerClick: true,
                    showPopover: false,
                    showDetails: true
                }
            },
            detailsViewLocation: 'overDirectory',
            noResultsText: 'No results found',
            filterout: {field:'',cond:'=',val:''}
        },
        database: {
            pagination: {
                on: true,
                perpage: 30,
                next: "Next",
                prev: "Prev.",
                showIn: 'directory'
            },
            loadOnStart: true,
            table: ''
        },
        actions: {
            map: {
                afterLoad: {
                    selectRegion: true
                }
            },
            region: {
                click: {
                    showDetails: true,
                    showDetailsFor: 'region',
                    filterDirectory: false,
                    loadObjects: false,
                    showPopover: false,
                    showPopoverFor: 'region',
                    goToLink: false,
                    linkField: 'Region.link'
                },
                touch: {
                    showPopover: false
                }
            },
            marker: {
                click: {
                    showDetails: true,
                    showPopover: false,
                    goToLink: false,
                    linkField: 'Object.link'
                },
                touch: {
                    showPopover: false
                }
            },
            directoryItem: {
                click: {
                    showDetails: true,
                    showPopover: false,
                    goToLink: false,
                    selectRegion: true,
                    fireRegionOnClick: true,
                    linkField: 'Object.link'
                }
            }
        },
        detailsView : {
            location: 'mapContainer', // top || slide || custom
            containerId: '',
            width: '100%',
            mobileFullscreen: true
        },
        mobileView: {
            labelMap: 'Map',
            labelList: 'List',
            labelClose: 'Close'
        },
        googleMaps: {
            on: false,
            apiKey: '',
            loaded: false,
            center: 'auto', // or {lat: 12, lon: 13}
            type: 'roadmap',
            minZoom: 1,
            style: 'default',
            styleJSON: []
        },
        groups: [],
        floors: [],
        layersControl: {
            on: false,
            position: 'top-left',
            label: 'Show on map',
            expanded: true,
            maxHeight: '100%'
        },
        floorsControl: {
            on: false,
            position: 'top-left',
            label: 'Floors',
            expanded: false,
            maxHeight: '100%'
        },
        containers: {
            leftSidebar: {on: false, width: '250px'},
            rightSidebar: {on: false, width: '250px'},
            header: {on: false, height: 'auto'},
            footer: {on: false, height: 'auto'},
        },
        labelsMarkers: {on:false},
        labelsRegions: {on:false},
        svgFileVersion: 1,
    };

    // Default marker style
    var markerOptions = {'src': MapSVG.urls.root+'markers/pin1_red.png'};


    /** Main Class **/
    mapSVG = function(elem, options){

        var _data;

        this.methods = {
            prototypes : {'MapObject': MapObject, 'Region': Region, 'Marker': Marker},
            setMarkersClickAsLink: function(){
                this.database.loadSchema().done(function(schema){
                    if(schema){
                        schema.forEach(function(field){
                            if(field.type == 'marker'){
                                _data.markerIsLink = MapSVG.parseBoolean(field.isLink);
                                _data.markerUrlField = field.urlField;
                            }
                        });
                    }
                });
            },
            setGroups: function(){
                _data.groups = _data.options.groups;
                _data.groups.forEach(function(g){
                    g.objects && g.objects.length && g.objects.forEach(function(obj){
                        _data.$svg.find('#'+obj.value).toggle(g.visible);
                    });
                });
            },
            setLayersControl : function(options){
                if(options)
                    $.extend(true, _data.options.layersControl, options);
                if(_data.options.layersControl.on){
                    if(!_data.$layersControl){
                        _data.$layersControl = $('<div class="mapsvg-layers-control"></div>');
                        _data.$layersControlLabel = $('<div class="mapsvg-layers-label"></div>').appendTo(_data.$layersControl);
                        _data.$layersControlListWrap = $('<div class="mapsvg-layers-list-wrap"></div>').appendTo(_data.$layersControl);
                        _data.$layersControlListNano = $('<div class="nano"></div>').appendTo(_data.$layersControlListWrap);
                        _data.$layersControlList = $('<div class="mapsvg-layers-list nano-content"></div>').appendTo(_data.$layersControlListNano);
                        _data.$layersControl.appendTo(_data.$mapContainer);
                    }
                    _data.$layersControl.show();
                    _data.$layersControlLabel.html(_data.options.layersControl.label);
                    _data.$layersControlList.empty();
                    _data.$layersControl.removeClass('mapsvg-top-left mapsvg-top-right mapsvg-bottom-left mapsvg-bottom-right');
                    _data.$layersControl.addClass('mapsvg-'+_data.options.layersControl.position);
                    if(_data.options.menu.on && !_data.options.menu.customContainer && _data.options.layersControl.position.indexOf('left')!==-1){
                        _data.$layersControl.css('left', _data.options.menu.width);
                    }
                    // if(!_data.options.layersControl.expanded && !_data.$layersControl.hasClass('closed')){
                    //     _data.$layersControl.addClass('closed')
                    // }
                    _data.$layersControl.css({'max-height': _data.options.layersControl.maxHeight});

                    _data.options.groups.forEach(function(g){
                        var item = $('<div class="mapsvg-layers-item" data-group-id="'+g.id+'">' +
                            '<input type="checkbox" class="ios8-switch ios8-switch-sm" '+(g.visible?'checked':'')+' />' +
                            '<label>'+g.title+'</label> ' +
                            '</div>').appendTo(_data.$layersControlList);
                    });
                    _data.$layersControlListNano.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
                    _data.$layersControl.off();
                    _data.$layersControl.on('click','.mapsvg-layers-item', function() {
                        var id = $(this).data('group-id');
                        var input = $(this).find('input');
                        input.prop('checked', !input.prop('checked'));
                        _data.options.groups.forEach(function(g){
                           if(g.id === id) g.visible = !g.visible;
                        });
                        _this.setGroups();
                    });
                    _data.$layersControlLabel.on('click',function(){
                        _data.$layersControl.toggleClass('closed');
                    });

                    _data.$layersControl.toggleClass('closed',!_data.options.layersControl.expanded);

                }else{
                    _data.$layersControl && _data.$layersControl.hide();
                }

            },
            setFloorsControl : function(options){
                if(options)
                    $.extend(true, _data.options.floorsControl, options);
                if(_data.options.floorsControl.on){
                    if(!_data.$floorsControl){
                        _data.$floorsControl = $('<div class="mapsvg-floors-control"></div>');
                        _data.$floorsControlLabel = $('<div class="mapsvg-floors-label"></div>').appendTo(_data.$floorsControl);
                        _data.$floorsControlListWrap = $('<div class="mapsvg-floors-list-wrap"></div>').appendTo(_data.$floorsControl);
                        _data.$floorsControlListNano = $('<div class="nano"></div>').appendTo(_data.$floorsControlListWrap);
                        _data.$floorsControlList = $('<div class="mapsvg-floors-list nano-content"></div>').appendTo(_data.$floorsControlListNano);
                        _data.$floorsControl.appendTo(_data.$map);
                    }
                    _data.$floorsControlLabel.html(_data.options.floorsControl.label);
                    _data.$floorsControlList.empty();
                    _data.$floorsControl.removeClass('mapsvg-top-left mapsvg-top-right mapsvg-bottom-left mapsvg-bottom-right')
                    _data.$floorsControl.addClass('mapsvg-'+_data.options.floorsControl.position);
                    // if(!_data.options.floorsControl.expanded && !_data.$floorsControl.hasClass('closed')){
                    //     _data.$floorsControl.addClass('closed')
                    // }
                    _data.$floorsControl.css({'max-height': _data.options.floorsControl.maxHeight});

                    _data.options.floors.forEach(function(f){
                        var item = $('<div class="mapsvg-floors-item" data-floor-id="'+f.object_id+'">' +
                            '<label>'+f.title+'</label> ' +
                            '</div>').appendTo(_data.$floorsControlList);
                    });
                    _data.$floorsControlListNano.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
                    _data.$floorsControl.off();
                    _data.$floorsControl.on('click','.mapsvg-floors-item', function() {
                        var id = $(this).data('floor-id');
                        _this.setFloor(id);
                    });
                    _data.$floorsControlLabel.on('click',function(){
                        _data.$floorsControl.toggleClass('closed');
                    });

                    _data.$floorsControl.toggleClass('closed',!_data.options.floorsControl.expanded);

                }else{
                    _data.$floorsControl && _data.$floorsControl.hide();
                }

            },
            setFloor: function(id){
                _data.$floorsControl.find('.mapsvg-floors-item').toggleClass('active',false);
                _data.$floorsControl.find('[data-floor-id="'+id+'"]').toggleClass('active',true);
                _data.options.floors.forEach(function(floor){
                   _data.$svg.find('#'+floor.object_id).hide();
                });
                var floor = _data.$svg.find('#'+id);
                floor.show();
                floor = new MapObject(floor, _this);
                var bbox = floor.getBBox();
                _data._viewBox = bbox;
                _this.setViewBox(_data._viewBox);
                _data.zoomLevels = null;
                _data.zoomLevel = 1;
                _this.setZoom();
                floor = null;
            },
            getGroupSelectOptions: function(){
                var id;
                var optionGroups = [];
                var options = [];
                var options2 = [];

                _data.$svg.find('g').each(function(index){
                    if(id = $(this)[0].getAttribute('id')){
                        // _data.groups.push(id);
                        options.push({label: id, value: id});
                    }
                });
                optionGroups.push({title: "SVG Layers / Groups", options: options});

                _data.$svg.find('path,ellipse,circle,polyline,polygon,rectangle,img,text').each(function(index){
                    if(id = $(this)[0].getAttribute('id')){
                        // _data.groups.push(id);
                        options2.push({label: id, value: id});
                    }
                });
                optionGroups.push({title: "Other SVG objects", options: options2});


                return optionGroups;
            },
            loadDataObjects: function(params){
                var _this = this;
                return _this.database.getAll(params);
            },
            loadDirectory: function(){
                var _this = this;
                // There is an option which allows to don't load DB object at map start up
                // So don't load directory to prevent 'no records found' message from appearing
                if(_data.options.menu.source === 'database' && !_this.database.loaded){
                    return false;
                }
                if(_data.options.menu.on){
                    _data.controllers.directory.loadItemsToDirectory();
                    // _data.controllers.directory.toggle(true);
                }
                _this.setPagination();
            },
            setPagination : function(){

                _data.$pagerMap && _data.$pagerMap.empty().remove();
                _data.$pagerDir && _data.$pagerDir.empty().remove();

                if(_data.options.database.pagination.on && _data.options.database.pagination.perpage !== 0){

                    _data.$directory.toggleClass('mapsvg-with-pagination', (['directory','both'].indexOf(_data.options.database.pagination.showIn)!==-1));
                    _data.$map.toggleClass('mapsvg-with-pagination', (['map','both'].indexOf(_data.options.database.pagination.showIn)!==-1));

                    if(_data.options.menu.on){
                        _data.$pagerDir = _this.getPagination();
                        _data.controllers.directory.addPagination(_data.$pagerDir);
                    }
                    _data.$pagerMap = _this.getPagination();
                    _data.$map.append(_data.$pagerMap);
                }
            },
            getPagination : function(callback){

                // pager && (pager.empty().remove());
                var pager = $('<nav class="mapsvg-pagination"><ul class="pager"><!--<li class="mapsvg-first"><a href="#">First</a></li>--><li class="mapsvg-prev"><a href="#">&larr; '+_data.options.database.pagination.prev+' '+_data.options.database.pagination.perpage+'</a></li><li class="mapsvg-next"><a href="#">'+_data.options.database.pagination.next+' '+_data.options.database.pagination.perpage+' &rarr;</a></li><!--<li class="mapsvg-last"><a href="#">Last</a></li>--></ul></nav>');

                if(_this.database.onFirstPage() && _this.database.onLastPage()){
                    pager.hide();
                }else{
                    pager.find('.mapsvg-prev').removeClass('disabled');
                    pager.find('.mapsvg-first').removeClass('disabled');
                    pager.find('.mapsvg-last').removeClass('disabled');
                    pager.find('.mapsvg-next').removeClass('disabled');

                    _this.database.onLastPage() &&
                    (pager.find('.mapsvg-next').addClass('disabled') && pager.find('.mapsvg-last').addClass('disabled'));

                    _this.database.onFirstPage() &&
                    (pager.find('.mapsvg-prev').addClass('disabled') && pager.find('.mapsvg-first').addClass('disabled'));
                }

                pager.on('click','.mapsvg-next:not(.disabled)',function(e){
                    e.preventDefault();
                    if(_this.database.onLastPage())
                        return;
                    _this.database.getAll({page: _this.database.page+1}).done(function(){
                        callback && callback();
                        // var parts = window.location.hash.split('/');
                        // var pagePart = parts.filter(function(p){
                        //     return p.indexOf('page-') !== -1
                        // })[0];
                        // var newPagePart = 'page-'+_this.database.page+1;
                        //
                        // if(pagePart){
                        //     window.location.hash = window.location.hash.replace(pagePart, newPagePart)
                        // }else{
                        //     window.location.hash = '!'+parts.push(newPagePart).join('/');
                        // }

                        // {page:1,object:23,region:"US-TX"}
                        // #!US-TX/page-1/object-23
                        // window.location.hash = _this.database.page+1;
                    });
                }).on('click','.mapsvg-prev:not(.disabled)',function(e){
                    e.preventDefault();
                    if(_this.database.onFirstPage())
                        return;
                    _this.database.getAll({page: _this.database.page-1}).done(function(){
                        callback && callback();
                    });
                }).on('click','.mapsvg-first:not(.disabled)',function(e){
                    e.preventDefault();
                    if(_this.database.onFirstPage())
                        return;
                    _this.database.getAll({page: 1}).done(function(){
                        callback && callback();
                    });
                }).on('click','.mapsvg-last:not(.disabled)',function(e){
                    e.preventDefault();
                    if(_this.database.onLastPage())
                        return;
                    _this.database.getAll({lastpage: true}).done(function(){
                        callback && callback();
                    });
                });

                return pager;
            },
            deleteMarkers: function(){
                while(_data.markers.length){
                    _data.markers[0].delete && _data.markers[0].delete();
                }
            },
            addLocations: function(){

                var _this = this;

                var dbObjects  = this.database.getLoaded();
                var locationField = this.database.getSchemaFieldByType('location');
                if(!locationField){
                    return false;
                }
                locationField = locationField.name;
                
                if(locationField) {

                    _this.deleteMarkers();

                    _data.clusters = {};
                    _data.clustersByZoom = [];
                    _data.markersClusters = [];
                    _data.markersClustersDict = {};



                    if(dbObjects && dbObjects.length > 0){
                        dbObjects.forEach(function(object){
                            if(object[locationField] && !(object[locationField] instanceof Location) && ((object[locationField].lat && object[locationField].lng) || (object[locationField].x && object[locationField].y))){
                                object[locationField] = new Location(object[locationField]);
                                if((object[locationField].lat && object[locationField].lng) || (object[locationField].x && object[locationField].y)){
                                    var marker = new Marker({
                                        location: object[locationField],
                                        object: object,
                                        mapsvg: _this
                                    });
                                }
                            }
                        });
                        if(_data.options.clustering.on){
                            _this.startClusterizer();
                        } else {
                            dbObjects.forEach(function(object){
                                if(object.location && object.location.marker){
                                    _this.markerAdd(object.location.marker);
                                }
                            });
                        }
                    }
                }
            },
            addClustersFromWorker : function(zoomLevel, clusters){
                var _this = this;

                _data.clustersByZoom[zoomLevel] = [];
                for(var cell in clusters){
                    var markers = clusters[cell].markers.map(function(marker){
                        // todo check if location & marker exists
                        return _this.database.getLoadedObject(marker.id).location.marker;
                    });
                    _data.clustersByZoom[zoomLevel].push( new MapSVG.MarkersCluster({
                        mapsvg: _this,
                        markers: markers,
                        x: clusters[cell].x,
                        y: clusters[cell].y,
                        cellX: clusters[cell].cellX,
                        cellY: clusters[cell].cellY
                    }));

                }
                if(_data.zoomLevel === zoomLevel){
                    _this.clusterizeMarkers();
                }
            },
            startClusterizer : function(){
                var _this = this;

                if(!_this.database || _this.database.getLoaded().length === 0){
                    return;
                }
                var locationField = _this.database.getSchemaFieldByType('location');
                if(!locationField){
                    return false;
                }


                if(!_data.clusterizerWorker){

                    _data.clusterizerWorker = new Worker(MapSVG.urls.root+"js/clustering.js");

                    // Receive messages from postMessage() calls in the Worker
                    _data.clusterizerWorker.onmessage = function(evt) {
                        if(evt.data.clusters){
                            _this.addClustersFromWorker(evt.data.zoomLevel, evt.data.clusters);
                        }
                    };
                }

                // Pass data to the WebWorker
                _data.clusterizerWorker.postMessage({
                    objects: _this.database.getLoaded().map(function(o){
                        return {id: o.id, x: o.location ? o.location.marker.x : null, y: o.location ? o.location.marker.y : null}
                    }),
                    cellSize: 50,
                    mapWidth: _data.$map.width(),
                    zoomLevels: _data.zoomLevels,
                    zoomLevel: _data.zoomLevel,
                    zoomDelta: _data.zoomDelta,
                    svgViewBox: _data.svgDefault.viewBox
                });

                _this.on("zoom", function(){
                    _data.clusterizerWorker.postMessage({
                        message: "zoom",
                        zoomLevel: _data.zoomLevel
                    });
                });


                return;
            },
            clusterizeMarkers : function(){

                _data.layers.markers.children().each(function(i,obj){
                    $(obj).detach();
                });
                _data.markers = [];
                _data.markersDict = {};
                _data.markersClusters = [];
                _data.markersClustersDict = {};

                _data.$map.addClass('no-transitions-markers');

                _data.clustersByZoom && _data.clustersByZoom[_data.zoomLevel] && _data.clustersByZoom[_data.zoomLevel].forEach(function(cluster){
                    if(cluster.markers.length > 1) {
                        _this.markersClusterAdd(cluster);
                    } else {
                        _this.markerAdd(cluster.markers[0]);
                    }
                });
                if(_data.editingMarker){
                    _this.markerAdd(_data.editingMarker);
                }

                // setTimeout(function(){
                    _data.$map.removeClass('no-transitions-markers');
                // },200);

                    // var last = dbObjects.length - 1;

                    // if(MapSVG.thisShitCalled === undefined){
                    //     MapSVG.thisShitCalled = 0;
                    // }
                    // MapSVG.thisShitCalled += 1;
                    //
                    //
                    // dbObjects && dbObjects.forEach(function(object, index, array) {
                    //     // setTimeout(function(){
                    //     setTimeout(function(){
                    //         if(object.location && object.location.marker && object.location.marker instanceof Marker){
                    //             // var cluster = _data.markersClusters.find(function(cluster){
                    //             //     return cluster.canTakeMarker(object.location.marker);
                    //             // });
                    //             var xy = _this.convertSVGToPixel([object.location.marker.x, object.location.marker.y]);
                    //
                    //             var cellX = Math.ceil(xy[0] / 50 );
                    //             var cellY = Math.ceil(xy[1] / 50 );
                    //
                    //             var cluster = _data.markersClustersDict[cellX+'|'+cellY];
                    //
                    //             if(cluster){
                    //                 cluster.addMarker(object.location.marker);
                    //             } else {
                    //                 new MapSVG.MarkersCluster({
                    //                     mapsvg: _this,
                    //                     markers: [object.location.marker],
                    //                     x: object.location.marker.x,
                    //                     y: object.location.marker.y
                    //                 });
                    //             }
                    //         }
                    //         if(index === last){
                    //             _this.addRemainingMarkers();
                    //
                    //         }
                    //     },0);
                    // });

            },
            addRemainingMarkers: function(){
                _data.markersClusters.forEach(function(cluster){
                    if(cluster.markers.length === 1){
                        _this.markerAdd(cluster.markers[0]);
                    }
                });
            },


        /*
        addDataObjectsAsMarkers: function(){

            var data  = this.database.getLoaded();
            var _this = this;

            _this.deleteMarkers();

            data && data.forEach(function(obj){
                if(obj.marker && !(obj instanceof Marker)){
                    obj.marker.id = 'marker_'+obj.id;
                    obj.marker.attached = true;
                    var marker = _this.markerAdd(obj.marker);
                    marker && marker.setObject(obj);
                }
            });

        },
        */
            getCssUrl: function(){
                return MapSVG.urls.root+'css/mapsvg.css';
            },
            isGeo: function(){
                return _data.mapIsGeo;
            },
            functionFromString: function(string){
                var func;
                var error = false;
                var fn = string.trim();
                if(fn.indexOf("{")==-1 || fn.indexOf("function")!==0 || fn.indexOf("(")==-1){
                    return {error: "MapSVG user function error: no function body."};
                }
                var fnBody = fn.substring(fn.indexOf("{") + 1, fn.lastIndexOf("}"));
                var params = fn.substring(fn.indexOf("(") + 1, fn.indexOf(")"));
                try{
                    func = new Function(params,fnBody);
                }catch(err){
                    error = err;
                }

                if (!error)
                    return func;
                else
                    return error;//{error: {line: error.line, text: "MapSVG user function error: (line "+error.line+"): "+error.message}};
            },
            getOptions: function(forTemplate, forWeb, optionsDelta) {
                var options = $.extend(true, {}, _data.options);
                // for(var key in optionsDelta){
                //     options[key] = optionsDelta[key];
                // }
                $.extend(true, options, optionsDelta);

                options.viewBox = _data._viewBox;
                options.filtersSchema = _this.filtersSchema.getSchema();
                if (options.filtersSchema) {
                    options.filtersSchema.forEach(function (field) {
                        if (field.type == 'distance') {
                            field.value = '';
                        }
                    });
                }

                delete options.markers;
                //var region = {id: "", title: "", disabled: false, selected: false,
                if (forTemplate){
                    // options.regions = [];
                    // _data.regions.forEach(function(r){
                    //     options.regions.push(r.getOptions(forTemplate));
                    // });
                    // options.markers = _data.options.markers;
                    options.svgFilename = options.source.split('/').pop();
                    options.svgFiles = MapSVG.svgFiles;
                }else{
                    // _data.regions.forEach(function(r){
                        // r.changed() && (options.regions[r.id] = r.getOptions());
                        // if(options.regions[r.id] && options.regions[r.id].fill){
                                // options.regions[r.id] = {fill: r.fill};
                        // }
                        // if(r.fill != r.style.fill && r.fill != options.colors.baseDefault){
                        //     options.regions[r.id] = {fill: r.fill};
                        // }
                    // });
                    // if(_data.markers.length > 0)
                    //     options.markers = [];
                    // _data.markers.forEach(function(marker){
                    //     if(!marker.attached)
                    //         options.markers.push(marker.getOptions());
                    // });
                }


                if(forWeb)
                    $.each(options,function(key,val){
                        if(JSON.stringify(val)==JSON.stringify(defaults[key]))
                            delete options[key];
                    });
                delete options.backend;

                return options;
            },
            // EVENTS
            on: function(event, callback) {
                this.lastChangeTime = Date.now();
                if (!_data.events[event])
                    _data.events[event] = [];
                _data.events[event].push(callback);
            },
            off: function(event, callback) {
                for(var eventName in _data.events){
                    if(_data.events[eventName] && _data.events[eventName].length > 0){
                        if(eventName.indexOf(event) === 0 && event.length <= eventName){
                            _data.events[eventName] = [];
                        }
                    }
                }
            },
            trigger: function(event){
                var _this = this;
                for(var eventName in _data.events){
                    if(_data.events[eventName] && _data.events[eventName].length > 0){
                        var eventNameReal = eventName.split('.')[0];
                        if(eventNameReal.indexOf(event)===0){
                            _data.events[eventName].forEach(function(callback){
                                try{
                                    callback && callback.call(_this);
                                }catch (err){
                                    console.log(err);
                                }
                            });
                        }
                    }
                }
                $(window).trigger(event, _this);
            },
            // SETTERS
            setEvents: function(functions){
                _data.events = _data.events || {};

                for (var eventName in functions) {
                    if (typeof functions[eventName] === 'string') {
                        var func = functions[eventName] != "" ? this.functionFromString(functions[eventName]) : null;

                        if (func && !func.error && !(func instanceof TypeError || func instanceof SyntaxError) )
                            _data.events[eventName] = func;
                        else
                            _data.events[eventName] = null;
                    } else if(typeof functions[eventName] === 'function') {
                        _data.events[eventName] = functions[eventName];
                    }
                    if(eventName.indexOf('directory')!==-1){
                        var event = eventName.split('.')[0];
                        if(_data.controllers && _data.controllers.directory){
                            _data.controllers.directory.events[event] = _data.events[eventName];
                        }
                    }
                }

                $.extend(true, _data.options.events, functions);
            },
            setActions : function(options){
                $.extend(true, _data.options.actions, options);
            },
            setDetailsView: function(options){

                options = options || _data.options.detailsView;
                $.extend(true, _data.options.detailsView, options);


                // Since 5.0.0: no top/near locations.
                if(_data.options.detailsView.location === 'top' && _data.options.menu.position === 'left'){
                    _data.options.detailsView.location = 'leftSidebar';
                } else if(_data.options.detailsView.location === 'top' && _data.options.menu.position === 'right'){
                    _data.options.detailsView.location = 'rightSidebar';
                }
                if(_data.options.detailsView.location === 'near'){
                    _data.options.detailsView.location = 'map';
                }

                if(!_data.$details){
                    _data.$details   = $('<div class="mapsvg-details-container"></div>');
                }

                _data.$details.toggleClass('mapsvg-details-container-relative', !(MapSVG.isPhone && _data.options.detailsView.mobileFullscreen) && !_this.shouldBeScrollable(_data.options.detailsView.location));


                if(_data.options.detailsView.location === 'custom'){
                    $('#'+_data.options.detailsView.containerId).append(_data.$details);
                } else {
                    if(MapSVG.isPhone && _data.options.detailsView.mobileFullscreen){
                        $('body').append(_data.$details);
                        _data.$details.addClass('mapsvg-container-fullscreen')
                    }else{
                        var $cont = '$'+_data.options.detailsView.location;
                        _data[$cont].append(_data.$details);
                    }
                    if(_data.options.detailsView.margin){
                        _data.$details.css('margin',_data.options.detailsView.margin);
                    }
                    _data.$details.css('width',_data.options.detailsView.width);
                }


                /*
                if(_data.options.detailsView.location != 'custom'){
                    _data.$details   = $('<div class="mapsvg-details-container"></div>');
                    if(MapSVG.isPhone){
                        $('body').append(_data.$details);
                    }else{
                        _data.$wrap.append(_data.$details);
                        if(!_data.options.menu.customContainer){
                            if(_data.options.menu.on && _data.options.detailsView.location == 'near'){
                                _data.$details.css({left: _data.options.menu.width});
                                _data.$details.addClass('near');
                            }else if(!_data.options.menu.on || _data.options.detailsView.location == 'top'){
                                _data.$details.addClass('top');
                            }
                        }
                    }
                }else{
                    _data.$details = $('#'+_data.options.detailsView.containerId);
                }
                */

            },
            setMobileView: function(options){
                $.extend(true, _data.options.mobileView, options);
            },
            deleteDataField: function(name){
                _data.options.data.forEach(function(obj){
                    delete obj[name];
                });
            },
            addZeroDataField: function(name){
                _data.options.data.forEach(function(obj){
                    obj[name] = '';
                });
            },
            attachDataToRegions: function(object){
                if(object) {
                    if(object.regions && object.regions.length){
                        if(typeof object.regions == 'object'){
                            object.regions.forEach(function(region){
                                var r = _this.getRegion(region.id);
                                if(r)
                                    r.objects.push(object);
                            });
                        }
                    }
                } else {
                    _data.regions.forEach(function(region){
                        region.objects = [];
                    });
                    _this.database.getLoaded().forEach(function(obj, index){
                        if(obj.regions && obj.regions.length){
                            if(typeof obj.regions == 'object'){
                                obj.regions.forEach(function(region){
                                    var r = _this.getRegion(region.id);
                                    if(r)
                                        r.objects.push(obj);
                                });
                            }
                        }
                    });
                }
            },
            setTemplates: function(templates){
                var _this = this;
                _data.templates = _data.templates || {};
                for (var name in templates){
                    if(name != undefined){
                        _data.options.templates[name] = templates[name];
                        var t = _data.options.templates[name];
                        if(name == 'directoryItem'){
                            t = '{{#each items}}<div id="mapsvg-directory-item-{{id}}" class="mapsvg-directory-item" data-object-id="{{id}}">'+t+'</div>{{/each}}';
                            name = 'directory';
                        }

                        _data.templates[name] = Handlebars.compile(t, {strict: false});
                        if(_data.editMode && (name == 'directory' && _data.controllers && _data.controllers.directory)){
                            _data.controllers.directory.templates.main = _data.templates[name];
                            _this.loadDirectory();
                        }
                    }
                }
            },
            setRegionStatus : function(region, status){
                var status = _this.regionsDatabase.getSchemaField('status').optionsDict[status];
                if(status.disabled)
                    region.setDisabled(true);
                else
                    region.setDisabled(false);

                if(status.color)
                    region.setFill(status.color);
                else
                    region.setFill();

            },
            update : function(options){
                for (var key in options){
                    if (key == "regions"){
                        $.each(options.regions,function(id,regionOptions){
                            var region = _this.getRegion(id);
                            region && region.update(regionOptions);
                            if(regionOptions.gaugeValue!=undefined){
                                _this.updateGaugeMinMax();
                                _this.regionsRedrawColors();
                            }
                            if(regionOptions.disabled!=undefined){
                                _this.deselectRegion(region);
                                _data.options.regions[id] = _data.options.regions[id] || {};
                                _data.options.regions[id].disabled = region.disabled;
                            }
                        });
                    }else if (key == "markers"){
                        $.each(options.markers,function(id,markerOptions){
                            var marker = _this.getMarker(id);
                            marker && marker.update(markerOptions);
                        });
                    }else{
                        var setter = 'set'+MapSVG.ucfirst(key);
                        if (_this.hasOwnProperty(setter))
                            this[setter](options[key]);
                        else{
                            _data.options[key] = options[key];
                        }
                    }
                }
            },
            setTitle: function(title){
                title && (_data.options.title = title);
            },
            setExtension: function(extension){
                if(extension){
                    _data.options.extension = extension;
                }else{
                    delete _data.options.extension;
                }
            },
            setDisableLinks: function(on){
                on = MapSVG.parseBoolean(on);
                if(on){
                    _data.$map.on('click.a.mapsvg','a',function(e){
                        e.preventDefault();
                    });
                }else{
                    _data.$map.off('click.a.mapsvg');
                }
                _data.disableLinks = on;
            },
            setLoadingText: function(val){_data.options.loadingText = val},
            setLockAspectRatio: function(val){ _data.options.lockAspectRatio =  MapSVG.parseBoolean(val);},
            setOnClick: function(h){_data.options.onClick = h || undefined;},
            setMouseOver: function(h){_data.options.mouseOver = h || undefined;},
            setMouseOut: function(h){_data.options.mouseOut = h || undefined;},
            setBeforeLoad: function(h){_data.options.beforeLoad = h || undefined;},
            setAfterLoad: function(h){_data.options.afterLoad = h || undefined;},
            setPopoverShown: function(h){_data.options.popoverShown = h || undefined;},
            // on: function(event, handler){
            //     _data.eventHandlers = _data.eventHandlers || {};
            //     _data.eventHandlers[event] = handler;
            // },
            setMarkerEditHandler : function(handler){
                _data.markerEditHandler = handler;
            },
            setRegionChoroplethField : function(field){
                _data.options.regionChoroplethField = field;
                _this.redrawGauge();
            },
            setRegionEditHandler : function(handler){
                _data.regionEditHandler = handler;
            },
            setDisableAll: function(on){
                on = MapSVG.parseBoolean(on);
                $.extend(true, _data.options, {disableAll:on});
                _data.$map.toggleClass('mapsvg-disabled-regions', on);
            },
            setRegionStatuses : function(_statuses){
                _data.options.regionStatuses = _statuses;
                var colors = {};
                for(var status in _data.options.regionStatuses){
                    colors[status] = _data.options.regionStatuses[status].color.length ? _data.options.regionStatuses[status].color : undefined;
                }
                _this.setColors({status: colors});
            },
            setColorsIgnore : function(val){
                _data.options.colorsIgnore = MapSVG.parseBoolean(val);
                _this.regionsRedrawColors();
            },
            setColors : function(colors){
                $.extend(true, _data.options, {colors:colors});

                if(colors && colors.status)
                    _data.options.colors.status = colors.status;

                //_data.$map.css({'background': _data.options.colors.background});
                //if(colors.stroke)
                //    _data.regions.forEach(function(r){
                //        //if (r.default_attr['stroke'] == _data.options.colors.stroke)
                //        //    r.default_attr['stroke'] = color;
                //        r.node.css('stroke',colors.stroke);
                //    });
                if(_data.options.colors.background)
                    _data.$map.css({'background': _data.options.colors.background});
                if(_data.options.colors.hover)
                    _data.options.colors.hover = (_data.options.colors.hover == ""+parseInt(_data.options.colors.hover)) ? parseInt(_data.options.colors.hover) : _data.options.colors.hover;
                if(_data.options.colors.selected)
                    _data.options.colors.selected = (_data.options.colors.selected == ""+parseInt(_data.options.colors.selected)) ? parseInt(_data.options.colors.selected) : _data.options.colors.selected;

                _data.$leftSidebar.css({'background-color': _data.options.colors.leftSidebar});
                _data.$rightSidebar.css({'background-color': _data.options.colors.rightSidebar});
                _data.$header.css({'background-color': _data.options.colors.header});
                _data.$footer.css({'background-color': _data.options.colors.footer});


                if(_data.$details && _data.options.colors.detailsView !== undefined){
                    _data.$details.css({'background-color': _data.options.colors.detailsView});
                }
                if(_data.$directory && _data.options.colors.directory !== undefined){
                    _data.$directory.css({'background-color': _data.options.colors.directory});
                }
                if(_data.$filtersModal && _data.options.colors.modalFilters !== undefined){
                    _data.$filtersModal.css({'background-color': _data.options.colors.modalFilters});
                }

                if(_data.$filters && _data.options.colors.directorySearch){
                    _data.$filters.css({
                        'background-color': _data.options.colors.directorySearch
                    })
                }else if(_data.$filters) {
                    _data.$filters.css({
                        'background-color': ''
                    })
                }

                _this.clusterCSS = _this.clusterCSS || $('<style></style>').appendTo('head');
                var css = '';
                if(_data.options.colors.clusters){
                    css += "background-color: "+_data.options.colors.clusters+";";
                }
                if(_data.options.colors.clustersBorders){
                    css += "border-color: "+_data.options.colors.clustersBorders+";";
                }
                if(_data.options.colors.clustersText){
                    css += "color: "+_data.options.colors.clustersText+";";
                }
                _this.clusterCSS.html(".mapsvg-marker-cluster {"+css+"}");

                _this.clusterHoverCSS = _this.clusterHoverCSS || $('<style></style>').appendTo('head');
                var cssHover = "";
                if(_data.options.colors.clustersHover){
                    cssHover += "background-color: "+_data.options.colors.clustersHover+";";
                }
                if(_data.options.colors.clustersHoverBorders){
                    cssHover += "border-color: "+_data.options.colors.clustersHoverBorders+";";
                }
                if(_data.options.colors.clustersHoverText){
                    cssHover += "color: "+_data.options.colors.clustersHoverText+";";
                }
                _this.clusterHoverCSS.html(".mapsvg-marker-cluster:hover {"+cssHover+"}");



                $.each(_data.options.colors,function(key, color){
                    if(color === null || color == "")
                        delete _data.options.colors[key];
                });

                _this.regionsRedrawColors();
            },
            setTooltips : function (options) {

                if(options.on !== undefined)
                    options.on = MapSVG.parseBoolean(options.on);

                $.extend(true, _data.options, {tooltips: options});

                _data.tooltip = _data.tooltip || {posOriginal: {}, posShifted: {}, posShiftedPrev: {}, mirror: {}};
                _data.tooltip.posOriginal    = {};
                _data.tooltip.posShifted     = {};
                _data.tooltip.posShiftedPrev = {};
                _data.tooltip.mirror         = {};


                if(_data.tooltip.container){
                    _data.tooltip.container[0].className = _data.tooltip.container[0].className.replace(/(^|\s)mapsvg-tt-\S+/g, '');
                }else{
                    _data.tooltip.container = $('<div />').addClass('mapsvg-tooltip');
                    _data.$map.append(_data.tooltip.container);
                }


                var ex = _data.options.tooltips.position.split('-');
                if(ex[0].indexOf('top')!=-1 || ex[0].indexOf('bottom')!=-1){
                    _data.tooltip.posOriginal.topbottom = ex[0];
                }
                if(ex[0].indexOf('left')!=-1 || ex[0].indexOf('right')!=-1){
                    _data.tooltip.posOriginal.leftright = ex[0];
                }
                if(ex[1]){
                    _data.tooltip.posOriginal.leftright = ex[1];
                }

                var event = 'mousemove.tooltip.mapsvg-'+_data.$map.attr('id');
                _data.tooltip.container.addClass('mapsvg-tt-'+_data.options.tooltips.position);

                _data.tooltip.container.css({'min-width': _data.options.tooltips.minWidth+'px', 'max-width': _data.options.tooltips.maxWidth+'px'});

                $('body').off(event).on(event, function(e) {

                    MapSVG.mouse = MapSVG.mouseCoords(e);

                    _data.tooltip.container[0].style.left = (e.clientX + $(window).scrollLeft() - _data.$map.offset().left) +'px';
                    _data.tooltip.container[0].style.top  = (e.clientY + $(window).scrollTop()  - _data.$map.offset().top)  +'px';

                    var m = {x: e.clientX + $(window).scrollLeft(), y: e.clientY + $(window).scrollTop()};

                    var tbbox = _data.tooltip.container[0].getBoundingClientRect();
                    var mbbox = _data.$wrap[0].getBoundingClientRect();
                    tbbox = {
                        top: tbbox.top + $(window).scrollTop(),
                        bottom: tbbox.bottom + $(window).scrollTop(),
                        left: tbbox.left + $(window).scrollLeft(),
                        right: tbbox.right + $(window).scrollLeft(),
                        width: tbbox.width,
                        height: tbbox.height
                    };
                    mbbox = {
                        top: mbbox.top + $(window).scrollTop(),
                        bottom: mbbox.bottom + $(window).scrollTop(),
                        left: mbbox.left + $(window).scrollLeft(),
                        right: mbbox.right + $(window).scrollLeft(),
                        width: mbbox.width,
                        height: mbbox.height
                    };

                    if(m.x > mbbox.right || m.y > mbbox.bottom || m.x < mbbox.left || m.y < mbbox.top){
                        return;
                    }

                    if(_data.tooltip.mirror.top || _data.tooltip.mirror.bottom){
                    // may be cancel mirroring
                        if(_data.tooltip.mirror.top && m.y > _data.tooltip.mirror.top){
                            _data.tooltip.mirror.top    = false;
                            delete _data.tooltip.posShifted.topbottom;
                        }else if(_data.tooltip.mirror.bottom && m.y < _data.tooltip.mirror.bottom){
                            _data.tooltip.mirror.bottom = false;
                            delete _data.tooltip.posShifted.topbottom;
                        }
                    }else{
                    // may be need mirroring

                        if(tbbox.bottom < mbbox.top + tbbox.height){
                            _data.tooltip.posShifted.topbottom = 'bottom';
                            _data.tooltip.mirror.top    = m.y;
                        }else if(tbbox.top > mbbox.bottom - tbbox.height){
                            _data.tooltip.posShifted.topbottom = 'top';
                            _data.tooltip.mirror.bottom = m.y;
                        }
                    }

                    if(_data.tooltip.mirror.right || _data.tooltip.mirror.left){
                    // may be cancel mirroring

                        if(_data.tooltip.mirror.left && m.x > _data.tooltip.mirror.left){
                            _data.tooltip.mirror.left  = false;
                            delete _data.tooltip.posShifted.leftright;
                        }else if(_data.tooltip.mirror.right && m.x < _data.tooltip.mirror.right){
                            _data.tooltip.mirror.right = false;
                            delete _data.tooltip.posShifted.leftright;
                        }
                    }else{
                    // may be need mirroring
                        if(tbbox.right < mbbox.left + tbbox.width){
                            _data.tooltip.posShifted.leftright = 'right';
                            _data.tooltip.mirror.left = m.x;
                        }else if(tbbox.left > mbbox.right - tbbox.width){
                            _data.tooltip.posShifted.leftright = 'left';
                            _data.tooltip.mirror.right = m.x;
                        }
                    }

                    var pos  = $.extend({}, _data.tooltip.posOriginal, _data.tooltip.posShifted);
                    var _pos = [];
                    pos.topbottom && _pos.push(pos.topbottom);
                    pos.leftright && _pos.push(pos.leftright);
                    pos = _pos.join('-');

                    if(_data.tooltip.posShifted.topbottom!=_data.tooltip.posOriginal.topbottom  || _data.tooltip.posShifted.leftright!=_data.tooltip.posOriginal.leftright){
                        _data.tooltip.container[0].className = _data.tooltip.container[0].className.replace(/(^|\s)mapsvg-tt-\S+/g, '');
                        _data.tooltip.container.addClass('mapsvg-tt-'+pos);
                        _data.tooltip.posShiftedPrev = pos;
                    }
                });
            },
            setPopovers : function (options){
                if(options.on !== undefined)
                    options.on = MapSVG.parseBoolean(options.on);

                $.extend(_data.options.popovers, options);

                if(!_data.$popover) {
                    _data.$popover = $('<div />').addClass('mapsvg-popover');
                    // _data.$popover.closeButton = $('<div class="mapsvg-popover-close"></div>');
                    // _data.$popover.contentDiv = $('<div class="mapsvg-popover-content"></div>');
                    // _data.$popover.append(_data.$popover.contentDiv);
                    // _data.$popover.append(_data.$popover.closeButton);
                    _data.layers.popovers.append(_data.$popover);
                }
                _data.$popover.css({
                    width: _data.options.popovers.width + (_data.options.popovers.width == 'auto' ? '' : 'px'),
                    'max-width': _data.options.popovers.maxWidth + '%',
                    'max-height': _data.options.popovers.maxHeight*_data.$wrap.outerHeight()/100+'px'
                });


                // if(_data.options.popovers.centerOn && !_data.popoverResizeSensor){
                //     _data.popoverResizeSensor = new MapSVG.ResizeSensor(_data.$popover[0], function(){
                //         if(_data.options.popovers.centerOn){
                //             _this.centerOn();
                //         }
                //     });
                // }

                if(_data.options.popovers.mobileFullscreen && MapSVG.isPhone){
                    $('body').toggleClass('mapsvg-fullscreen-popovers', true);
                    _data.$popover.appendTo('body');
                }
                // _data.$popover.closeButton.off();
                // _data.$popover.closeButton.on('click touchend', function(e){
                //     _this.hidePopover();
                //     _this.deselectRegion();
                //     if(_data.events['closed.popover']){
                //         _data.events['closed.popover'].call(_data.$popover, _this);
                //     }
                // });
            },
            setRegionPrefix : function(prefix){
                _data.options.regionPrefix = prefix;
            },
            setInitialViewBox : function(v){
                if(typeof v == 'string')
                    v = v.trim().split(' ');
                _data._viewBox = [parseFloat(v[0]), parseFloat(v[1]), parseFloat(v[2]), parseFloat(v[3])];
                if(_data.options.googleMaps.on){
                    _data.options.googleMaps.center = _data.googleMaps.map.getCenter().toJSON();
                    _data.options.googleMaps.zoom = _data.googleMaps.map.getZoom();
                }
                _data.zoomLevel = 0;
            },
            setViewBoxOnStart : function(){
                _data.viewBoxFull = _data.svgDefault.viewBox;
                _data.viewBoxFake = _data.viewBox;
                _data.whRatioFull = _data.viewBoxFull[2] / _data.viewBox[2];
                _data.$svg[0].setAttribute('viewBox',_data.viewBoxFull.join(' '));
                _data.vbstart = 1;
            },
            setViewBox : function(v,skipAdjustments){


                if(typeof v == 'string'){
                    v = v.trim().split(' ');
                }
                var d = (v && v.length==4) ? v : _data.svgDefault.viewBox;
                var isZooming = parseFloat(d[2]) != _data.viewBox[2] || parseFloat(d[3]) != _data.viewBox[3];
                _data.viewBox = [parseFloat(d[0]), parseFloat(d[1]), parseFloat(d[2]), parseFloat(d[3])];
                _data.whRatio = _data.viewBox[2] / _data.viewBox[3];

                !_data.vbstart && _this.setViewBoxOnStart();

                if(!v){
                    _data._viewBox = _data.viewBox;
                    _data._scale = 1;
                }

                var p = _data.options.padding;

                if(p.top){
                    _data.viewBox[1] -= p.top;
                    _data.viewBox[3] += p.top;
                }
                if(p.right){
                    _data.viewBox[2] += p.right;
                }
                if(p.bottom){
                    _data.viewBox[3] += p.bottom;
                }
                if(p.left){
                    _data.viewBox[0] -= p.left;
                    _data.viewBox[2] += p.left;
                }

                _data.scale = _this.getScale();
                _data.superScale = _data.whRatioFull*_data.svgDefault.viewBox[2]/_data.viewBox[2];

                _data.scroll = _data.scroll || {};
                _data.scroll.tx = (_data.svgDefault.viewBox[0]-_data.viewBox[0])*_data.scale;
                _data.scroll.ty = (_data.svgDefault.viewBox[1]-_data.viewBox[1])*_data.scale;


                _data.$scrollpane.css({
                    'transform': 'translate('+_data.scroll.tx+'px,'+_data.scroll.ty+'px)'
                });
                _data.$svg.css({
                    'transform': 'scale('+_data.superScale+')'
                });
                if(isZooming && !skipAdjustments){
                    _this.updateSize();
                }

                if(isZooming){
                    _data.options.clustering.on && _this.throttle(_this.clusterizeOnZoom, 400, _this);
                }

                return true;
            },
            clusterizeOnZoom: function(){
                if(this.getData().options.googleMaps.on && this.getData().googleMaps.map && this.getData().zoomDelta) {
                    this.getData().zoomLevel = this.getData().googleMaps.map.getZoom() - this.getData().zoomDelta;
                }
                this.trigger('zoom');
                this.clusterizeMarkers();
            },
            throttle: function(method, delay, scope, params) {
                clearTimeout(method._tId);
                method._tId = setTimeout(function(){
                    method.call(scope, params);
                }, delay);
            },
            setViewBoxReal : function(bbox){
                _data.viewBoxFull = bbox;
                _data.viewBoxFake = bbox;
                _data.whRatioFull = _data.viewBoxFull[2] / _data.viewBox[2];

                _data.viewBox = bbox;
                _data.svgDefault.viewBox = _data.viewBox;
                _data.viewBoxFull = bbox;
                _data.viewBoxFake = _data.viewBox;
                _data.whRatioFull = _data.viewBoxFull[2] / _data.viewBox[2];
                _data.$svg[0].setAttribute('viewBox',_data.viewBoxFull.join(' '));

                _data.scale   = _this.getScale();

                var tx = (-bbox[0])*_data.scale;
                var ty = (-bbox[1])*_data.scale;
                _data.$layers.css({
                    'transform': 'translate('+tx+'px,'+ty+'px)'
                });
                _data.zoomLevel = 0;
                _this.setViewBox(bbox);
            },
            setViewBoxByGoogleMapBounds : function(){

                var googleMapBounds = _data.googleMaps.map.getBounds();
                if(!googleMapBounds) return;
                var googleMapBoundsJSON = googleMapBounds.toJSON();

                if(googleMapBoundsJSON.west==-180 && googleMapBoundsJSON.east==180){
                    var center = _data.googleMaps.map.getCenter().toJSON();
                }
                    var ne = [googleMapBounds.getNorthEast().lat(), googleMapBounds.getNorthEast().lng()];
                    var sw = [googleMapBounds.getSouthWest().lat(), googleMapBounds.getSouthWest().lng()];

                    var xyNE = _this.convertGeoToSVG(ne);
                    var xySW = _this.convertGeoToSVG(sw);

                    // check if map on border between 180/-180 longitude
                    if(xyNE[0] < xySW[0]){
                        var mapPointsWidth = (_data.svgDefault.viewBox[2] / _data.mapLonDelta) * 360;
                        xySW[0] = -(mapPointsWidth - xySW[0]);
                    }

                    var width  = xyNE[0] - xySW[0];
                    var height = xySW[1] - xyNE[1];
                    _this.setViewBox([xySW[0], xyNE[1], width, height]);

            },
            redraw: function(){


                if(MapSVG.browser.ie){
                    _data.whRatio2 = _data.svgDefault.viewBox[2] / _data.svgDefault.viewBox[3];
                    // MapSVG.get(0).getData().svgDefault.viewBox[2] / MapSVG.get(0).getData().svgDefault.viewBox[3];
                    _data.$svg.height(_data.$map.outerWidth() / _data.whRatio2);
                }

                if(_data.options.googleMaps.on && _data.googleMaps.map){
                    // var center = _data.googleMaps.map.getCenter();
                    google.maps.event.trigger(_data.googleMaps.map, 'resize');
                    // _data.googleMaps.map.setCenter(center);
                    // _this.setViewBoxByGoogleMapBounds();
                }else{
                    _this.setViewBox(_data.viewBox);
                }
                _data.$popover && _data.$popover.css({
                    'max-height': _data.options.popovers.maxHeight*_data.$wrap.outerHeight()/100+'px'
                });
                _this.updateSize();

                // _data.$wrap.css({
                //     width: _data.$wrap.width(),
                //     height: _data.$wrap.width() / _data.whRatio
                // });
                // if(!MapSVG.browser.ie) {
                //     _data.$wrap.css({
                //         width: 'auto',
                //         height: 'auto'
                //     });
                // }else{
                //     _data.$wrap.css({
                //         width: 'auto'
                //     });
                // }
                // _this.updateSize();
            },
            setPadding: function(options){
                options = options || _data.options.padding;
                for(var i in options){
                    options[i] = options[i] ? parseInt(options[i]) : 0;
                }
                $.extend(_data.options.padding, options);


                // var p = _data.options.padding;
                //
                // var v = $.extend([],_data._viewBox);
                //
                // if(p.top){
                //     v[1] -= p.top;
                //     v[3] += p.top;
                // }
                // if(p.right){
                //     v[2] += p.right;
                // }
                // if(p.bottom){
                //     v[3] += p.bottom;
                // }
                // if(p.left){
                //     v[0] -= p.left;
                //     v[2] += p.left;
                // }
                _this.setViewBox();
                _this.trigger('sizeChange');
            },
            // trigger: function(event){
            //     _data.eventHandlers && _data.eventHandlers[event] && _data.eventHandlers[event]();
            // },
            setSize : function( width, height, responsive ){

                // Convert strings to numbers
                _data.options.width      = parseFloat(width);
                _data.options.height     = parseFloat(height);
                _data.options.responsive = responsive!=null && responsive!=undefined  ? MapSVG.parseBoolean(responsive) : _data.options.responsive;

                // Calculate width and height
                if ((!_data.options.width && !_data.options.height)){
                    _data.options.width	 = _data.svgDefault.width;
                    _data.options.height = _data.svgDefault.height;
                }else if (!_data.options.width && _data.options.height){
                    _data.options.width	 = parseInt(_data.options.height * _data.svgDefault.width / _data.svgDefault.height);
                }else if (_data.options.width && !_data.options.height){
                    _data.options.height = parseInt(_data.options.width * _data.svgDefault.height/_data.svgDefault.width);
                }

                //if(_data.options.responsive){
                //    var maxWidth  = _data.options.width;
                //    var maxHeight = _data.options.height;
                //    _data.options.width	 = _data.svgDefault.width;
                //    _data.options.height = _data.svgDefault.height;
                //}

                _data.whRatio      = _data.options.width / _data.options.height;
                _data.scale        = _this.getScale();

                _this.setResponsive(responsive);

                if(_data.markers)
                    _this.markersAdjustPosition();
                if(_data.options.labelsRegions.on){
                    _this.labelsRegionsAdjustPosition();
                }


                return [_data.options.width, _data.options.height];
            },
            setResponsive : function(on,force){

                on = on != undefined ? MapSVG.parseBoolean(on) : _data.options.responsive;

                _data.$map.css({
                    'width': '100%',
                    'height': '0',
                    'padding-bottom': (_data.viewBox[3]*100/_data.viewBox[2])+'%'
                });
                if(on){
                    _data.$wrap.css({
                        'width': '100%',
                        'height': 'auto'
                    });
                }else{
                    _data.$wrap.css({
                        'width': _data.options.width+'px',
                        'height': _data.options.height+'px'
                    });
                }
                $.extend(true, _data.options, {responsive: on});

                if(!_data.resizeSensor){
                    _data.resizeSensor = new MapSVG.ResizeSensor(_data.$map[0], function(){
                        _this.redraw();
                    });
                }

                _this.redraw();
            },
            setScroll : function(options, skipEvents){
                options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
                options.limit != undefined && (options.limit = MapSVG.parseBoolean(options.limit));
                $.extend(true, _data.options, {scroll: options});
                !skipEvents && _this.setEventHandlers();
            },
            setZoom : function (options){
                options = options || {};
                options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
                options.mousewheel != undefined && (options.mousewheel = MapSVG.parseBoolean(options.mousewheel));

                // delta = 1.2 changed to delta = 2 since introducing Google Maps + smooth zoom
                options.delta = 2;

                // options.delta && (options.delta = parseFloat(options.delta));

                if(options.limit){
                    if(typeof options.limit == 'string')
                        options.limit = options.limit.split(';');
                    options.limit = [parseInt(options.limit[0]),parseInt(options.limit[1])];
                }
                if(!_data.zoomLevels){
                    _this.setZoomLevels();
                }

                $.extend(true, _data.options, {zoom: options});
                //(options.buttons && options.buttons.on) && (options.buttons.on = MapSVG.parseBoolean(options.buttons.on));
                _data.$map.off('mousewheel.mapsvg');


                if(_data.options.zoom.on && _data.options.zoom.mousewheel){
                    // var lastZoomTime = 0;
                    // var zoomTimeDelta = 0;

                    if(MapSVG.browser.firefox){
                        _data.firefoxScroll = { insideIframe: false };

                        _data.$map.on('mouseenter', function() {
                            _data.firefoxScroll.insideIframe = true;
                            _data.firefoxScroll.scrollX = window.scrollX;
                            _data.firefoxScroll.scrollY = window.scrollY;
                        }).on('mouseleave', function() {
                            _data.firefoxScroll.insideIframe = false;
                        });

                        $(document).scroll(function() {
                            if (_data.firefoxScroll.insideIframe)
                                window.scrollTo(_data.firefoxScroll.scrollX, _data.firefoxScroll.scrollY);
                        });
                    }

                    _data.$map.on('mousewheel.mapsvg',function(event, delta, deltaX, deltaY) {
                        if($(event.target).hasClass('mapsvg-popover') || $(event.target).closest('.mapsvg-popover').length)
                            return;
                        // zoomTimeDelta = Date.now() - lastZoomTime;
                        // lastZoomTime = Date.now();
                        event.preventDefault();
                        var d = delta > 0 ? 1 : -1;
                        var m = MapSVG.mouseCoords(event);
                        m.x = m.x - _data.$svg.offset().left;
                        m.y = m.y - _data.$svg.offset().top;

                        var center = _this.convertPixelToSVG([m.x, m.y]);
                        d > 0 ? _this.zoomIn(center) : _this.zoomOut(center);
                        // _this.zoom(d);
                        return false;
                    });
                }
                _this.setZoomButtons();
                _data.canZoom = true;
            },
            setZoomLevels : function(){

                _data.zoomLevels = {};

                var _scale = 1;
                for(var i = 0; i <= 20; i++){
                    _data.zoomLevels[i+''] = {
                        _scale: _scale,
                        viewBox: [0,0,_data._viewBox[2] /_scale, _data._viewBox[3] /_scale]
                    };
                    _scale = _scale * _data.options.zoom.delta;

                }
                _scale = 1;
                for(var i = 0; i >= -20; i--){
                    _data.zoomLevels[i+''] = {
                        _scale: _scale,
                        viewBox: [0,0,_data._viewBox[2] /_scale, _data._viewBox[3] /_scale]
                    };
                    _scale = _scale / _data.options.zoom.delta;

                }
            },
            setZoomButtons : function(){
                var loc = _data.options.zoom.buttons.location || 'hide';
                if(! _data.zoomButtons){

                    var buttons = $('<div />').addClass('mapsvg-buttons');
                    var group = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);

                    buttons.zoomIn = $('<div />').addClass('mapsvg-btn-map mapsvg-in');
                    var event = MapSVG.touchDevice? 'touchend' : 'click';
                    buttons.zoomIn.on(event,function(e){
                        e.stopPropagation();
                        _this.zoomIn();
                    });

                    buttons.zoomOut = $('<div />').addClass('mapsvg-btn-map mapsvg-out');
                    buttons.zoomOut.on(event,function(e){
                        e.stopPropagation();
                        _this.zoomOut();
                    });
                    group.append(buttons.zoomIn).append(buttons.zoomOut);

                    buttons.location = $('<div />').addClass('mapsvg-btn-map mapsvg-btn-location');
                    buttons.location.on(event,function(e){
                        e.stopPropagation();
                        _this.zoomOut();
                    });
                    buttons.append(buttons.location);

                    _data.zoomButtons = buttons;
                    _data.$map.append(_data.zoomButtons);
                }
                _data.zoomButtons.removeClass('left');
                _data.zoomButtons.removeClass('right');
                loc == 'right' && _data.zoomButtons.addClass('right')
                ||
                loc == 'left' && _data.zoomButtons.addClass('left');

                (_data.options.zoom.on &&  loc!='hide') ? _data.zoomButtons.show() : _data.zoomButtons.hide();
            },
            setManualRegions : function(on){
                _data.options.manualRegions = MapSVG.parseBoolean(on);
            },
            setCursor : function(type){
                type = type == 'pointer' ? 'pointer' : 'default';
                _data.options.cursor = type;
                if(type == 'pointer')
                    _data.$map.addClass('mapsvg-cursor-pointer');
                else
                    _data.$map.removeClass('mapsvg-cursor-pointer');
            },
            setPreloaderText : function(text){
                _data.options.loadingText = text;
            },
            setMultiSelect : function (on, deselect){
                _data.options.multiSelect = MapSVG.parseBoolean(on);
                if(deselect !== false)
                    _this.deselectAllRegions();
            },
            setGauge : function (options){

                options = options || _data.options.gauge;
                options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
                $.extend(true, _data.options, {gauge: options});

                var needsRedraw = false;

                if(!_data.$gauge){
                    _data.$gauge = {};
                    _data.$gauge.gradient = $('<td>&nbsp;</td>').addClass('mapsvg-gauge-gradient');
                    _this.setGaugeGradientCSS();
                    _data.$gauge.container = $('<div />').addClass('mapsvg-gauge').hide();
                    _data.$gauge.table = $('<table />');
                    var tr = $('<tr />');
                    _data.$gauge.labelLow = $('<td>'+_data.options.gauge.labels.low+'</td>');
                    _data.$gauge.labelHigh = $('<td>'+_data.options.gauge.labels.high+'</td>');
                    tr.append(_data.$gauge.labelLow);
                    tr.append(_data.$gauge.gradient);
                    tr.append(_data.$gauge.labelHigh);
                    _data.$gauge.table.append(tr);
                    _data.$gauge.container.append(_data.$gauge.table);
                    _data.$map.append(_data.$gauge.container);
                }

                if (!_data.options.gauge.on && _data.$gauge.container.is(":visible")){
                    _data.$gauge.container.hide();
                    needsRedraw = true;
                }else if(_data.options.gauge.on && !_data.$gauge.container.is(":visible")){
                    _data.$gauge.container.show();
                    needsRedraw = true;
                    _this.regionsDatabase.on('change',function(){
                        _this.redrawGauge();
                    });
                }

                if(options.colors){
                    _data.options.gauge.colors.lowRGB = MapSVG.tinycolor(_data.options.gauge.colors.low).toRgb();
                    _data.options.gauge.colors.highRGB = MapSVG.tinycolor(_data.options.gauge.colors.high).toRgb();
                    _data.options.gauge.colors.diffRGB = {
                        r: _data.options.gauge.colors.highRGB.r - _data.options.gauge.colors.lowRGB.r,
                        g: _data.options.gauge.colors.highRGB.g - _data.options.gauge.colors.lowRGB.g,
                        b: _data.options.gauge.colors.highRGB.b - _data.options.gauge.colors.lowRGB.b,
                        a: _data.options.gauge.colors.highRGB.a - _data.options.gauge.colors.lowRGB.a
                    };
                    needsRedraw = true;
                    _data.$gauge && _this.setGaugeGradientCSS();
                }

                if(options.labels){
                    _data.$gauge.labelLow.html(_data.options.gauge.labels.low);
                    _data.$gauge.labelHigh.html(_data.options.gauge.labels.high);
                }

                needsRedraw && _this.redrawGauge();
            },
            redrawGauge : function(){
                _this.updateGaugeMinMax();
                _this.regionsRedrawColors();
            },
            updateGaugeMinMax : function(){
                _data.options.gauge.min = 0;
                _data.options.gauge.max = false;
                var values = [];
                _data.regions.forEach(function(r){
                    var gauge = r.data && r.data[_data.options.regionChoroplethField];
                    gauge != undefined && parseFloat(values.push(gauge));
                });
                if(values.length > 0){
                    _data.options.gauge.min = values.length == 1 ? 0 : Math.min.apply(null,values);
                    _data.options.gauge.max = Math.max.apply(null,values);
                    _data.options.gauge.maxAdjusted = _data.options.gauge.max - _data.options.gauge.min;
                }
            },
            setGaugeGradientCSS: function(){
                _data.$gauge.gradient.css({
                    'background': _data.options.gauge.colors.low,
                    'background': '-moz-linear-gradient(left, ' + _data.options.gauge.colors.low + ' 1%,' + _data.options.gauge.colors.high + ' 100%)',
                    'background': '-webkit-gradient(linear, left top, right top, color-stop(1%,' + _data.options.gauge.colors.low + '), color-stop(100%,' + _data.options.gauge.colors.high + '))',
                    'background': '-webkit-linear-gradient(left, ' + _data.options.gauge.colors.low + ' 1%,' + _data.options.gauge.colors.high + ' 100%)',
                    'background': '-o-linear-gradient(left, ' + _data.options.gauge.colors.low + ' 1%,' + _data.options.gauge.colors.high + ' 100% 100%)',
                    'background': '-ms-linear-gradient(left,  ' + _data.options.gauge.colors.low + ' 1%,' + _data.options.gauge.colors.high + ' 100% 100%)',
                    'background': 'linear-gradient(to right,' + _data.options.gauge.colors.low + ' 1%,' + _data.options.gauge.colors.high + ' 100%)',
                    'filter': 'progid:DXImageTransform.Microsoft.gradient( startColorstr="' + _data.options.gauge.colors.low + '", endColorstr="' + _data.options.gauge.colors.high + '",GradientType=1 )'
                });
            },
            setCss : function(css){
                _data.options.css = css || _data.options.css;
                _this.liveCSS = _this.liveCSS || $('<style></style>').appendTo('body');
                _this.liveCSS.html(_data.options.css);
            },
            setFilters : function(options){

                var _this = this;

                options                              = options || _data.options.filters;
                options.on != undefined              && (options.on = MapSVG.parseBoolean(options.on));
                options.hide != undefined          && (options.hide = MapSVG.parseBoolean(options.hide));
                $.extend(true, _data.options, {filters: options});

                var scrollable = false;

                if(['leftSidebar','rightSidebar','header','footer','custom','mapContainer'].indexOf(_data.options.filters.location)===-1){
                    _data.options.filters.location = 'leftSidebar';
                }

                if(_data.options.filters.on){

                    if(_this.formBuilder){
                        _this.formBuilder.destroy();
                    }

                    if(!_data.$filters){
                        _data.$filters = $('<div class="mapsvg-filters-wrap"></div>');
                    }

                    _data.$filters.empty();
                    _data.$filters.show();

                    _data.$filters.css({
                            'background-color':_data.options.colors.directorySearch,
                    });

                    if(_data.$filtersModal){
                        _data.$filtersModal.css({width: _data.options.filters.width});
                    }

                    if(_data.options.filters.location === 'custom'){
                        _data.$filters.removeClass('mapsvg-filter-container-custom').addClass('mapsvg-filter-container-custom');
                        if($('#'+_data.options.filters.containerId).length){
                            $('#'+_data.options.filters.containerId).append(_data.$filters);
                        } else {
                            _data.$filters.hide();
                            console.error('MapSVG: filter container #'+_data.options.filters.containerId+' does not exists');
                        }
                    } else {
                        if(MapSVG.isPhone){
                            _data.$header.append(_data.$filters);
                            _this.setContainers({header:{on:true}});
                        }else{
                            var location = MapSVG.isPhone ? 'header' : _data.options.filters.location;
                            var $cont = '$'+location;
                            if(_data.options.menu.on && _data.controllers.directory && _data.options.menu.location === _data.options.filters.location){
                                _data.controllers.directory.view.find('.mapsvg-directory-filter-wrap').append(_data.$filters);
                            } else {
                                _data[$cont].append(_data.$filters);
                                _data.controllers.directory && _data.controllers.directory.updateTopShift();
                            }
                        }
                    }

                    _this.loadFiltersController(_data.$filters, false);

                    if(_data.options.filters && _data.options.filters.on || ( _this.database.query.filters && Object.keys(_this.database.query.filters).length > 0)){

                        for(var field_name in _this.database.query.filters){
                            var field_value = _this.database.query.filters[field_name];
                            var _field_name = field_name;
                            var filterField = _this.filtersSchema.getField(_field_name);

                            if(_data.options.filters.on && filterField){
                                filters.find('[data-parameter-name="'+_field_name+'"]').val(field_value);
                            }else{
                                if(filterField){
                                    if(field_name == 'regions'){
                                        // check if there is such filter. If there is then change its value
                                        // if there isn't then add a tag with close button
                                        _field_name = '';
                                        field_value = _this.getRegion(field_value).title || field_value;
                                    } else {
                                        _field_name = filterField && filterField.label;
                                    }
                                    if(field_name !== 'distance'){
                                        filters.append('<div class="mapsvg-filter-tag">'+(_field_name?_field_name+': ':'')+field_value+' <span class="mapsvg-filter-delete" data-filter="'+field_name+'">×</span></div>');
                                    }
                                }
                            }
                        }
                        // this.view.addClass('mapsvg-with-filter');

                    }else{
                        // this.view.removeClass('mapsvg-with-filter');
                    }
                } else {
                    if(_data.$filters){
                        _data.$filters.empty();
                        _data.$filters.hide();
                    }
                }

                if(_data.options.menu.on && _data.controllers.directory && _data.options.menu.location === _data.options.filters.location){
                    _data.controllers.directory.updateTopShift();
                }
            },
            setContainers : function(options){

                var _this = this;

                if(!_data.containersCreated){
                    _data.$wrapAll      = $('<div class="mapsvg-wrap-all"></div>');
                    _data.$wrap         = $('<div class="mapsvg-wrap"></div>');
                    _data.$containers   = {};
                    _data.$mapContainer = $('<div class="mapsvg-map-container"></div>');
                    _data.$leftSidebar  = $('<div class="mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-left"></div>');
                    _data.$rightSidebar = $('<div class="mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-right"></div>');
                    _data.$header       = $('<div class="mapsvg-header mapsvg-top-container"></div>');
                    _data.$footer       = $('<div class="mapsvg-footer mapsvg-top-container"></div>');

                    _data.$wrapAll.insertBefore(_data.$map);

                    _data.$wrapAll.append(_data.$header);
                    _data.$wrapAll.append(_data.$wrap);
                    _data.$wrapAll.append(_data.$footer);


                    _data.$mapContainer.append(_data.$map);

                    _data.$wrap.append(_data.$leftSidebar);
                    _data.$wrap.append(_data.$mapContainer);
                    _data.$wrap.append(_data.$rightSidebar);
                    _data.containersCreated = true;
                }

                options = options || _data.options;
                for(var contName in options){

                    if(options[contName].on !== undefined){
                        options[contName].on = MapSVG.parseBoolean(options[contName].on);
                    }


                    $contName = '$'+contName;


                    if(options[contName].width){
                        if((typeof options[contName].width != 'string') || options[contName].width.indexOf('px')===-1 && options[contName].width.indexOf('%')===-1 && options[contName].width!=='auto'){
                            options[contName].width = options[contName].width+'px';
                        }
                        _data[$contName].css({'flex-basis': options[contName].width});
                    }
                    if(options[contName].height){
                        if((typeof options[contName].height != 'string') || options[contName].height.indexOf('px')===-1 && options[contName].height.indexOf('%')===-1 && options[contName].height!=='auto'){
                            options[contName].height = options[contName].height+'px';
                        }
                        _data[$contName].css({'flex-basis': options[contName].height, height: options[contName].height});
                    }

                    $.extend(true, _data.options, {containers: options});
                    var on = MapSVG.isPhone && ['leftSidebar','rightSidebar'].indexOf(contName) !==-1 ? false : _data.options.containers[contName].on;
                    _data[$contName].toggle(on);


                }

                _this.setDetailsView();

            },
            setDirectory : function(options){
                return _this.setMenu(options);
            },
            shouldBeScrollable: function(container){
                switch (container){
                    case 'mapContainer':
                    case 'leftSidebar':
                    case 'rightSidebar':
                        return true;
                        break;
                    case 'custom':
                        return false;
                        break;
                    case 'header':
                    case 'footer':
                        if(_data.options.containers[container].height && _data.options.containers[container].height !== 'auto' &&  _data.options.containers[container].height !== '100%'){
                            return true;
                        } else {
                            return false;
                        }
                        break;
                    default: return false; break;
                }
            },
            setMenu : function(options){

                var _this = this;

                options                               = options || _data.options.menu;
                options.on != undefined              && (options.on = MapSVG.parseBoolean(options.on));
                options.search != undefined          && (options.search = MapSVG.parseBoolean(options.search));
                options.showMapOnClick != undefined          && (options.showMapOnClick = MapSVG.parseBoolean(options.showMapOnClick));
                options.searchFallback != undefined          && (options.searchFallback = MapSVG.parseBoolean(options.searchFallback));
                options.customContainer != undefined && (options.customContainer = MapSVG.parseBoolean(options.customContainer));

                $.extend(true, _data.options, {menu: options});

                _data.controllers = _data.controllers || {};

                if(!_data.$directory){
                    _data.$directory = $('<div class="mapsvg-directory"></div>');
                }


                // If directory will be scrollable make it absolutely positioned, fill the parent container.
                _data.$directory.toggleClass('flex', _this.shouldBeScrollable(_data.options.menu.location));



                if(_data.options.menu.on){

                    if(!_data.controllers.directory){
                        _data.controllers.directory = new MapSVG.DirectoryController({
                            container: _data.$directory,
                            data: _this.getData(),
                            template: _data.templates.directory,
                            mapsvg: _this,
                            filters: _this.filters,
                            database: _data.options.menu.source === 'regions' ? _this.regionsDatabase : _this.database,
                            scrollable: _this.shouldBeScrollable(_data.options.menu.location),//_data.options.menu.location === 'leftSidebar' || _data.options.menu.location === 'rightSidebar',
                            // position: _data.options.menu.position,
                            // search: _data.options.menu.search,
                            events : {
                                'click': _data.events['click.directoryItem'],
                                'mouseover': _data.events['mouseover.directoryItem'],
                                'mouseout': _data.events['mouseout.directoryItem']
                            }
                        });
                    } else {
                        _data.controllers.directory.database            = _data.options.menu.source === 'regions' ? _this.regionsDatabase : _this.database;
                        _data.controllers.directory.database.sortBy     = _data.options.menu.sortBy;
                        _data.controllers.directory.database.sortDir    = _data.options.menu.sortDirection;
                        if(options.filterout){
                            var f = {};
                            f[_data.options.menu.filterout.field] = _data.options.menu.filterout.val;
                            _data.controllers.directory.database.query.setFilterOut(f);
                        }
                        _data.controllers.directory.scrollable = _this.shouldBeScrollable(_data.options.menu.location)
                    }

                    var $container;
                    if(MapSVG.isPhone){
                        $container = _data.$leftSidebar;
                    }else{
                        $container = _data.options.menu.location !== 'custom' ? _data['$'+_data.options.menu.location] : $('#' + _data.options.menu.containerId);
                    }
                    $container.append(_data.$directory);

                    /*
                    if(!_data.options.menu.customContainer) {
                        if(!_data.$directory){
                            _data.$directory = $('<div class="mapsvg-directory"></div>');

                            if(_data.options.menu.position == 'left')
                                _data.$wrap.css({'padding-left': _data.options.menu.width});
                            else{
                                _data.$wrap.css({'padding-right': _data.options.menu.width});
                                _data.$directory.addClass('mapsvg-directory-right');
                            }
                            _data.$wrap.append(_data.$directory);
                        }
                    } else {
                        _data.$directory = $('#' + _data.options.menu.containerId);
                    }
                    */

                    if(_data.options.colors.directory){
                        _data.$directory.css({
                            'background-color': _data.options.colors.directory
                        });
                    }
                    _this.setFilters();
                    if((_data.options.menu.source === 'regions') || (_data.options.menu.source === 'database' && _this.database.loaded)){
                        if(_data.editMode && (options.sortBy || options.sortDirection || options.filterout)){
                            _data.controllers.directory.database.getAll();
                        }
                        _this.loadDirectory();
                    }

                    // !options.customContainer && _data.$directory.css({width: _data.options.menu.width});
                } else {
                    _data.controllers.directory && _data.controllers.directory.destroy();
                    _data.controllers.directory = null;
                }
            },
            setDatabase : function(options){
                options = options || _data.options.database;
                if(options.pagination){
                    if(options.pagination.on != undefined){
                        options.pagination.on = MapSVG.parseBoolean(options.pagination.on);
                    }
                    if(options.pagination.perpage != undefined){
                        options.pagination.perpage = parseInt(options.pagination.perpage);
                    }
                }
                $.extend(true, _data.options, {database: options});
                if(options.pagination){
                    if(options.pagination.on !== undefined || options.pagination.perpage){
                        var params = {
                            perpage   : _data.options.database.pagination.on ? _data.options.database.pagination.perpage : 0
                        };

                        _this.database.getAll(params);
                    } else {
                        _this.setPagination();
                    }
                }
            },
            setGoogleMaps : function(options){
                var _this = this;

                options    = options || _data.options.googleMaps;
                options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));

                if(!_data.googleMaps){
                    _data.googleMaps = {loaded: false, initialized: false, map: null};
                }

                $.extend(true, _data.options, {googleMaps: options});

                if(_data.options.googleMaps.on){
                    _data.$map.toggleClass('mapsvg-with-google-map', true);
                    // _this.setResponsive(false);
                    // if(!_data.googleMaps.loaded){
                    if(!MapSVG.googleMapsApiLoaded){
                        _this.loadGoogleMapsAPI(
                            function(){
                            _this.setGoogleMaps();
                            },
                            function(){
                              _this.setGoogleMaps({on:false});
                            }
                            );
                    } else {
                        if(!_data.googleMaps.map){
                            _data.$googleMaps = $('<div class="mapsvg-layer mapsvg-layer-gm" id="mapsvg-google-maps-'+_this.id+'"></div>').prependTo(_data.$map);
                            _data.$googleMaps.css({
                                position: 'absolute',
                                top:0,
                                left: 0,
                                bottom: 0,
                                right: 0,
                                'z-index': '0'
                            });
                            _data.googleMaps.map = new google.maps.Map(_data.$googleMaps[0], {
                                mapTypeId: options.type,
                                fullscreenControl: false,
                                keyboardShortcuts: false,
                                mapTypeControl: false,
                                scaleControl: false,
                                scrollwheel: false,
                                streetViewControl: false,
                                zoomControl: false,
                                styles: options.styleJSON

                            });
                            var overlay;
                            USGSOverlay.prototype = new google.maps.OverlayView();


                            /** @constructor */
                            function USGSOverlay(bounds, map) {
                                // Initialize all properties.
                                this.bounds_ = bounds;
                                this.map_ = map;
                                this.setMap(map);
                                this.prevCoords = {
                                    sw:  {x:0,y:0},
                                    sw2: {x:0,y:0},
                                    ne:  {x:0,y:0},
                                    ne2: {x:0,y:0}
                                };
                            }
                            USGSOverlay.prototype.onAdd = function() {

                                var div = document.createElement('div');
                                div.style.borderStyle = 'none';
                                div.style.borderWidth = '0px';
                                div.style.position    = 'absolute';

                                // TODO REMOVE!!!
                                // div.style.background = 'rgba(255,0,0,.5)';

                                this.div_ = div;
                                // Add the element to the "overlayLayer" pane.
                                var panes = this.getPanes();
                                panes.overlayLayer.appendChild(div);
                            };

                            USGSOverlay.prototype.draw = function() {
                                if (_data.isScrolling) return;

                                var overlayProjection = this.getProjection();

                                var geoSW = this.bounds_.getSouthWest();
                                var geoNE = this.bounds_.getNorthEast();

                                var coords = {};

                                coords.sw  = overlayProjection.fromLatLngToDivPixel(geoSW);
                                coords.ne  = overlayProjection.fromLatLngToDivPixel(geoNE);
                                coords.sw2 = overlayProjection.fromLatLngToContainerPixel(geoSW);
                                coords.ne2 = overlayProjection.fromLatLngToContainerPixel(geoNE);

                                // check if map on border between 180/-180 longitude
                                // if(ne.x < sw.x){
                                //     sw.x = sw.x - overlayProjection.getWorldWidth();
                                // }
                                // console.log('NOW sw:'+sw.x+' and ne:'+ne.x+'and W-width:'+overlayProjection.getWorldWidth());

                                // if(ne2.x < sw2.x){
                                //     sw2.x = sw2.x - overlayProjection.getWorldWidth();
                                // }

                                var ww = overlayProjection.getWorldWidth();

                                if(this.prevCoords.sw){

                                    if(coords.ne.x < coords.sw.x){
                                        if(Math.abs(this.prevCoords.sw.x - coords.sw.x) > Math.abs(this.prevCoords.ne.x - coords.ne.x)){
                                            coords.sw.x = coords.sw.x - ww;
                                        }else{
                                            coords.ne.x = coords.ne.x + ww;
                                        }
                                        if(Math.abs(this.prevCoords.sw2.x - coords.sw2.x) > Math.abs(this.prevCoords.ne2.x - coords.ne2.x)){
                                            coords.sw2.x = coords.sw2.x - ww;
                                        }else{
                                            coords.ne2.x = coords.ne2.x + ww;
                                        }
                                    }
                                }

                                for (var i in this.prevCoords){}

                                this.prevCoords = coords;

                                var scale = (coords.ne2.x - coords.sw2.x)/_data.svgDefault.viewBox[2];

                                // var scale = (ne.x - sw.x)/_data.svgDefault.viewBox[2];
                                var vb = [
                                    _data.svgDefault.viewBox[0] - coords.sw2.x/scale,
                                    _data.svgDefault.viewBox[1] - coords.ne2.y/scale,
                                    _data.$map.width()/scale,
                                    _data.$map.outerHeight()/scale
                                ];
                                _this.setViewBox(vb);
                                var div = this.div_;
                                // div.style.background = 'rgba(255,0,0,.5)';
                                div.style.left   = coords.sw.x + 'px';
                                div.style.top    = coords.ne.y + 'px';
                                div.style.width  = (coords.ne.x - coords.sw.x) + 'px';
                                div.style.height = (coords.sw.y - coords.ne.y) + 'px';
                            };

                            var southWest = new google.maps.LatLng(_data.geoViewBox.bottomLat, _data.geoViewBox.leftLon);
                            var northEast = new google.maps.LatLng(_data.geoViewBox.topLat, _data.geoViewBox.rightLon);
                            var bounds = new google.maps.LatLngBounds(southWest,northEast);

                            _data.googleMaps.overlay = new USGSOverlay(bounds, _data.googleMaps.map);

                            if(!_data.options.googleMaps.center || !_data.options.googleMaps.zoom){
                                var southWest = new google.maps.LatLng(_data.geoViewBox.bottomLat, _data.geoViewBox.leftLon);
                                var northEast = new google.maps.LatLng(_data.geoViewBox.topLat, _data.geoViewBox.rightLon);
                                var bounds = new google.maps.LatLngBounds(southWest,northEast);
                                _data.googleMaps.map.fitBounds(bounds);
                            }else{
                                _data.googleMaps.map.setZoom(_data.options.googleMaps.zoom);
                                _data.options.googleMaps.center.lat = parseFloat(_data.options.googleMaps.center.lat);
                                _data.options.googleMaps.center.lng = parseFloat(_data.options.googleMaps.center.lng);
                                _data.googleMaps.map.setCenter(_data.options.googleMaps.center);
                            }
                            _data.options.googleMaps.initialized = true;
                            _data.googleMaps.map.addListener('idle',function(){
                                _data.isZooming = false;
                            });
                            google.maps.event.addListenerOnce(_data.googleMaps.map, 'idle', function(){
                                _data.zoomDelta = _data.options.googleMaps.zoom - _data.zoomLevel;
                                setTimeout(function() {
                                    _data.$map.addClass('mapsvg-fade-in');
                                    setTimeout(function() {
                                        _data.$map.removeClass('mapsvg-google-map-loading');
                                        _data.$map.removeClass('mapsvg-fade-in');
                                        // _data.googleMaps.overlay.draw();
                                        if(!_data.options.googleMaps.center || !_data.options.googleMaps.zoom) {
                                            _data.options.googleMaps.center = _data.googleMaps.map.getCenter().toJSON();
                                            _data.options.googleMaps.zoom = _data.googleMaps.map.getZoom();
                                        }
                                        _this.trigger('googleMapsLoaded');
                                    }, 300);
                                }, 1);
                            });
                            // setTimeout(function(){
                                // _data.googleMaps.map.addListener('bounds_changed',function(){
                                //     console.log('changed');
                                //     // _data.googleMaps.overlay.draw();
                                //     if (!_data.isScrolling)
                                //     setTimeout(function(){
                                //         if (!_data.isScrolling) {
                                //             null;
                                //         }
                                //             // _data.googleMaps.overlay.draw();
                                //             // _this.setViewBoxByGoogleMapBounds();
                                //     },2);
                                // });
                                // _data.googleMaps.map.addListener('zoom_changed',function(){
                                //     setTimeout(function(){
                                //         _this.trigger('zoom');
                                //     },200);
                                // });

                                // _this.setViewBoxByGoogleMapBounds();
                            // },2500);

                        }else{
                            _data.$map.toggleClass('mapsvg-with-google-map', true);
                            _data.$googleMaps && _data.$googleMaps.show();
                            if(options.type){
                                _data.googleMaps.map.setMapTypeId(options.type);
                            }
                        }
                    }
                }else{
                    // TODO: destroy google maps
                    _data.$map.toggleClass('mapsvg-with-google-map', false);
                    _data.$googleMaps && _data.$googleMaps.hide();
                    _data.googleMaps.initialized = false;

                }

            },
            loadGoogleMapsAPI : function(callback, fail){
                window.gm_authFailure = function() {
                    if(MapSVG.GoogleMapBadApiKey){
                        MapSVG.GoogleMapBadApiKey();
                    }else{
                        alert("Google maps API key is incorrect.");
                    }
                };
                _data.googleMapsScript = document.createElement('script');
                _data.googleMapsScript.onload = function(){
                    MapSVG.googleMapsApiLoaded = true;
                    if(typeof callback == 'function')
                        callback();
                };
                var drawing = '';
                if(_data.options.googleMaps.drawingTools){
                    drawing = '&libraries=drawing';
                }
                _data.googleMapsScript.src = 'https://maps.googleapis.com/maps/api/js?key='+_data.options.googleMaps.apiKey+drawing;

                document.head.appendChild(_data.googleMapsScript);
            },
            loadGoogleMapsDrawing : function(callback, fail){
                window.gm_authFailure = function() {
                    if(MapSVG.GoogleMapBadApiKey){
                        MapSVG.GoogleMapBadApiKey();
                    }else{
                        alert("Google maps API key is incorrect.");
                    }
                };
                _data.googleMapsScript = document.createElement('script');
                _data.googleMapsScript.onload = function(){
                    MapSVG.googleMapsApiLoaded = true;
                    if(typeof callback == 'function')
                        callback();
                };

                document.head.appendChild(_data.googleMapsScript);
            },

            loadDetailsView : function(obj){
                // var slide = true;
                var _this = this;
                _this.popover && _this.popover.close();
                if(_this.detailsController)
                    _this.detailsController.destroy();

                _this.detailsController = new MapSVG.DetailsController({
                    // color: _data.options.colors.detailsView,
                    autoresize: MapSVG.isPhone && _data.options.detailsView.mobileFullscreen ? false : _data.options.detailsView.autoresize,
                    container: _data.$details,
                    template: obj instanceof Region ?  _data.templates.detailsViewRegion : _data.templates.detailsView,
                    mapsvg: _this,
                    data: obj instanceof Region ? obj.forTemplate() : obj,
                    modal: (MapSVG.isPhone && _data.options.detailsView.mobileFullscreen) || _this.shouldBeScrollable(_data.options.detailsView.location),
                    scrollable: (MapSVG.isPhone && _data.options.detailsView.mobileFullscreen) || _this.shouldBeScrollable(_data.options.detailsView.location),//['custom','header','footer'].indexOf(_data.options.detailsView.location) === -1,
                    withToolbar: !(MapSVG.isPhone && _data.options.detailsView.mobileFullscreen) && _this.shouldBeScrollable(_data.options.detailsView.location),//['custom','header','footer'].indexOf(_data.options.detailsView.location) === -1,
                    // width: _data.options.detailsView.width,
                    events: {
                            'shown': function(mapsvg){
                                if(_data.events['shown.detailsView']) {
                                    _data.events['shown.detailsView'].call(this, _this);
                                }
                                _this.trigger('detailsShown');
                            },
                            'closed' : function(mapsvg){
                            _this.deselectAllRegions();
                            // _this.controlles.
                            _data.controllers && _data.controllers.directory && _data.controllers.directory.deselectItems();
                            if(_data.events['closed.detailsView']){
                                _data.events['closed.detailsView'].call(this, _this);
                            }
                            _this.trigger('detailsClosed');
                        }
                    }
                });
            },
            loadFiltersModal : function(){

                var _this = this;

                if(_data.options.filters.modalLocation != 'custom'){
                    _data.$filtersModal = _data.$filtersModal || $('<div class="mapsvg-details-container mapsvg-filters-wrap"></div>');
                    _this.setColors();
                    _data.$filtersModal.css({width: _data.options.filters.width});
                    if(MapSVG.isPhone){
                        $('body').append(_data.$filtersModal);
                        _data.$filtersModal.css({width: ''});
                    }else{
                        var $cont = '$'+_data.options.filters.modalLocation;
                        _data[$cont].append(_data.$filtersModal);
                    }
                }else{
                    _data.$filtersModal = $('#'+_data.options.filters.containerId);
                    _data.$filtersModal.css({width: ''});
                }

                _this.loadFiltersController(_data.$filtersModal, true);

            },
            loadFiltersController : function($container, modal){

                modal = modal === undefined ? false : modal;
                var filtersInDirectory, filtersHide;

                if(MapSVG.isPhone){
                    filtersInDirectory = true;
                    filtersHide = _this.filtersSchema.schema.length > 2;
                }else{
                    filtersInDirectory = (_data.options.menu.on && _data.controllers.directory && _data.options.menu.location === _data.options.filters.location);
                    filtersHide = _data.options.filters.hide;
                }
                var scrollable = modal || (!filtersInDirectory && (['leftSidebar','rightSidebar'].indexOf(_data.options.filters.location) !== -1));


                var _filtersController = new MapSVG.FiltersController({
                    container: $container,
                    template: Handlebars.compile('<div class="mapsvg-filters-container"></div>'),
                    mapsvg: _this,
                    data: {},
                    scrollable: scrollable,
                    modal: modal,
                    withToolbar: MapSVG.isPhone ? false : modal === true,
                    width: $container.hasClass('mapsvg-map-container') ? _data.options.filters.width : '100%',
                    events: {
                        'shown': function(mapsvg){
                            var filtersController = this;
                            var formBuilder = new MapSVG.FormBuilder({
                                container: this.contentView,
                                filtersMode: true,
                                schema: _this.filtersSchema.getSchema(),
                                modal: modal,
                                filtersHide: filtersHide,
                                editMode: false,
                                mapsvg: _this,
                                // mediaUploader: mediaUploader,
                                data: _this.database.query.filters,
                                admin: false,
                                events: {
                                    load: function(_formBuilder){

                                        _formBuilder.container.find('.mapsvg-form-builder').css({
                                            padding: _data.options.filters.padding
                                        });

                                        filtersController.updateScroll();

                                        if(_data.options.filters.hide){
                                            var setFiltersCounter = function(){
                                                var filtersCounter = Object.keys(_this.database.query.filters).length;
                                                filtersCounter = filtersCounter > 0 ? filtersCounter : '';
                                                // don't include "searcH" filter into counter since it's always outside of the modal
                                                if(_this.database.query.filters.search && _this.database.query.filters.search.length>0){
                                                    filtersCounter--;
                                                }
                                                filtersCounter = filtersCounter === 0 ? '' : filtersCounter;
                                                _formBuilder && _formBuilder.showFiltersButton && _formBuilder.showFiltersButton.views.result.find('button').html(_this.getData().options.filters.buttonText+' <b>'+filtersCounter+'</b>');
                                            };
                                            setFiltersCounter();

                                            _this.database.on('dataLoaded', function(){
                                                setFiltersCounter();
                                            });
                                        }
                                    }
                                }
                            });
                            formBuilder.view.on('click','.mapsvg-btn-show-filters', function(){
                                _this.loadFiltersModal();
                            });

                            var filterDatabase = _data.options.filters.source === 'regions' ? _this.regionsDatabase : _this.database;

                            // Handle search separately with throttle 400ms
                            formBuilder.view.on('change keyup','input[data-parameter-name="search"]',function(){
                                _this.throttle(_this.textSearch, 600, _this, $(this).val());
                            });


                            formBuilder.view.on('change keyup','select,input[type="radio"],input',function(){

                                var filter = {};
                                var field = $(this).data('parameter-name');

                                if($(this).attr('name') === 'distanceAddress' || field == "search"){
                                    return;
                                }
                                if($(this).attr('name') === 'distanceLatLng' || $(this).attr('name') === 'distanceLength'){
                                    var distanceData = {
                                        units: formBuilder.view.find('[name="distanceUnits"]').val(),
                                        latlng: formBuilder.view.find('[name="distanceLatLng"]').val(),
                                        length: formBuilder.view.find('[name="distanceLength"]').val(),
                                        address: formBuilder.view.find('[name="distanceAddress"]').val()
                                    };
                                    if(distanceData.units && distanceData.length && distanceData.latlng){
                                        filter.distance = distanceData;
                                        var field = formBuilder.mapsvg.filtersSchema.schema.find(function(field){
                                            return field.type === 'distance';
                                        });
                                        var latlng = distanceData.latlng.split(',');
                                        latlng = {lat: parseFloat(latlng[0]), lng: parseFloat(latlng[1])};
                                        MapSVG.distanceSearch = {
                                            latlng: latlng,
                                            units: field.distanceUnits,
                                            unitsLabel: field.distanceUnitsLabel
                                        };
                                    } else {
                                        filter.distance = null;
                                        MapSVG.distanceSearch = null;
                                    }
                                } else {
                                    filter[field] = $(this).val();
                                }

                                filterDatabase.query.setFilters(filter);

                                // _this.formBuilder.view.find('select,input[type="radio"]').each(function(index){
                                //     var field = $(this).data('parameter-name');
                                //     var val = $(this).val();
                                //     filters[field] = val;
                                // });

                                var data = {
                                    filters: filter
                                };
                                if(_data.options.menu.searchFallback){
                                    data.searchFallback = true;
                                }

                                filterDatabase.getAll(data);
                            });
                            // if(_data.events['shown.detailsView']) {
                            //     _data.events['shown.detailsView'].call(this, _this);
                            // }
                            // _this.trigger('detailsShown');
                        },
                        'closed' : function(mapsvg){
                            // _this.deselectAllRegions();
                            // // _this.controlles.
                            // _data.controllers && _data.controllers.directory && _data.controllers.directory.deselectItems();
                            // if(_data.events['closed.detailsView']){
                            //     _data.events['closed.detailsView'].call(this, _this);
                            // }
                            // _this.trigger('detailsClosed');
                        }
                    }
                });
            },
            textSearch : function(text){

                var _this = this;
                var filter = {"search": text};
                var filterDatabase = this.getData().options.filters.source === 'regions' ? this.regionsDatabase : this.database;
                filterDatabase.query.setFilters(filter);

                var data = {
                    filters: filter
                };
                if(this.getData().options.menu.searchFallback){
                    data.searchFallback = true;
                }

                filterDatabase.getAll(data);
            },
            setMenuMarkers : function(options){
                options = options || _data.options.menuMarkers;
                options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
                $.extend(true, _data.options, {menuMarkers: options});

                _data.menuDatabase = _data.options.menu.source == 'regions' ? _data.regionsDatabase : _data.database;

                _data.$menuMarkers && _data.$menuMarkers.off('click.menuMarkers.mapsvg');


                if(_data.options.menuMarkers.on){
                    var menuContainer = $('#'+_data.options.menuMarkers.containerId);
                    if(menuContainer.length){
                        if(!_data.$menuMarkers){
                            if(!menuContainer.is('ul')){
                                _data.$menuMarkers = $('<ul />').appendTo(menuContainer);
                            }else{
                                _data.$menuMarkers = menuContainer;
                            }

                            if(!_data.$menuMarkers.hasClass('mapsvg-menu-markers'))
                                _data.$menuMarkers.addClass('mapsvg-menu-markers');
                        }

                        if(_data.$menuMarkers.children().length===0)
                        // Add links into navigation container
                            _data.markers.sort(function(a,b){
                                return a.id == b.id ? 0 : +(a.id > b.id) || -1;
                            });

                        _data.markers.forEach(function (marker, i) {
                            _data.$menuMarkers.append(_data.options.menuMarkers.template(marker));
                        });

                        _data.$menuMarkers.on('click.menuMarkers.mapsvg','a',function(e){
                            e.preventDefault();
                            var markerID = $(this).attr('href').replace('#','');
                            var marker = _this.getMarker(markerID);
                            var center = marker.getCenter();
                            e = {clientX: center[0], clientY: center[1]};
                            _this.regionClickHandler(e,marker);
                        }).on('mouseover.menuMarkers.mapsvg','a',function(e){
                            e.preventDefault();
                            var markerID = $(this).attr('href').replace('#','');
                            var marker = _this.getMarker(markerID);
                            _data.options.mouseOver && _data.options.mouseOver.call(marker, e, _this);
                        }).on('mouseout.menuMarkers.mapsvg','a',function(e){
                            e.preventDefault();
                            var markerID = $(this).attr('href').replace('#','');
                            var marker = _this.getMarker(markerID);
                            _data.options.mouseOut && _data.options.mouseOut.call(marker, e, _this);
                        });
                    }
                }
            },
            /*
             *
             * END SETTERS
             *
             * */
            getRegion : function(id){
                return _data.regions[_data.regionsDict[id]];
            },
            getMarker : function(id){
                return _data.markers[_data.markersDict[id]];
            },
            checkId : function(id){
                if(_this.getRegion(id))
                    return {error: "This ID is already being used by a Region"};
                else if(_this.getMarker(id))
                    return {error: "This ID is already being used by another Marker"};
                else
                    return true;

            },
            regionsRedrawColors: function(){
                _data.regions.forEach(function(region){
                    region.setFill();
                });
            },
            // destroy
            destroy : function(){
                if(_data.controllers && _data.controllers.directory){
                    _data.controllers.directory.mobileButtons.remove();
                }
                _data.$map.empty().insertBefore(_data.$wrapAll).attr('style','').removeClass('mapsvg mapsvg-responsive');
                _data.$wrapAll.remove();

                instances[_data.$map.attr('id')] = undefined;
                return _this;
            },
            getData : function(){
                return _data;
            },
            fitMarkers : function(){

                var dbObjects = _this.database.getLoaded();

                if(!dbObjects || dbObjects.length === 0){
                    return;
                }


                if(_data.options.googleMaps.on){

                    var lats = []; var lngs = [];

                    if(dbObjects.length > 1){
                        dbObjects.forEach(function(object){
                            if(object.location && object.location.lat && object.location.lng){
                                lats.push(object.location.lat);
                                lngs.push(object.location.lng);
                            }
                        });

                        // calc the min and max lng and lat
                        var minlat = Math.min.apply(null, lats),
                            maxlat = Math.max.apply(null, lats);
                        var minlng = Math.min.apply(null, lngs),
                            maxlng = Math.max.apply(null, lngs);

                        var bbox = new google.maps.LatLngBounds({lat: minlat, lng: minlng},{lat: maxlat, lng: maxlng});
                        _data.googleMaps.map.fitBounds(bbox);
                    } else {
                        if(dbObjects[0].location && dbObjects[0].location.lat && dbObjects[0].location.lng){
                            var coords = {lat: dbObjects[0].location.lat, lng: dbObjects[0].location.lng};
                            if(_data.googleMaps.map){
                                _data.googleMaps.map.setCenter(coords);
                                _data.googleMaps.map.setZoom(17);
                            }
                        }

                    }
                } else {
                    
                    return _this.zoomTo(_data.markers);

                    // var xs = []; var ys = [];
                    //
                    // dbObjects.forEach(function(object){
                    //     if(object.location.marker && object.location.marker.x && object.location.marker.y){
                    //         xs.push(object.location.marker.x);
                    //         ys.push(object.location.marker.y);
                    //     }
                    // });
                    //
                    // // calc the min and max lng and lat
                    // var minx = Math.min.apply(null, xs),
                    //     maxx = Math.max.apply(null, xs);
                    // var miny = Math.min.apply(null, ys),
                    //     maxy = Math.max.apply(null, ys);
                    //
                    //
                    // // var xyNE = [maxx, miny];
                    // // var xySW = [minx, maxy];
                    // // check if map is on border between 180/-180 longitude
                    // // if(xyNE[0] < xySW[0]){
                    // //     var mapPointsWidth = (_data.svgDefault.viewBox[2] / _data.mapLonDelta) * 360;
                    // //     xySW[0] = -(mapPointsWidth - xySW[0]);
                    // // }
                    //
                    // var width  = maxx - minx;
                    // var height = maxy - miny;
                    // var padding = 0;
                    // padding = _this.convertPixelToSVG([padding,0])[0] - _this.convertPixelToSVG([0,0])[0];
                    //
                    // _this.setViewBox([minx-padding, miny-padding, width+(padding*2), height+(padding*2)]);
                }
            },
            fitBounds : function(){

            },
            getUserLocation : function(){
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position){
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        return pos;
                    });
                } else {
                    return false;
                }
            },
            // GET SCALE VALUE
            getScale: function(){

                // var ratio_def = _data.svgDefault.width / _data.svgDefault.height;
                // var ratio_new = _data.options.width / _data.options.height;
                // var scale1, scale2;

                // var size = [_data.$map.width(), _data.$map.outerHeight()];

                // scale2 = size[0] / _data.viewBox[2];
                var scale2 = _data.$map.width() / _data.viewBox[2];

                // if scale = 0 it means that map width = 0 which means that map is hidden.
                // so we set scale = 1 to avoid problems with marker positioning.
                // proper scale will be set after map show up
                return scale2 || 1;
            },
            updateSize : function(){
                _data.scale = _this.getScale();
                _this.popover && _this.popover.adjustPosition();
                _this.markersAdjustPosition();
                if(_data.options.labelsRegions.on){
                    _this.labelsRegionsAdjustPosition();
                }
                _this.mapAdjustStrokes();
                if(_data.directoryWrap)
                    _data.directoryWrap.height(_data.$wrap.outerHeight());
            },
            // GET VIEBOX [x,y,width,height]
            getViewBox : function(){
                return _data.viewBox;
            },
            // SET VIEWBOX BY SIZE
            viewBoxSetBySize : function(width,height){

                width = parseFloat(width);
                height = parseFloat(height);
                _this.setSize(width,height);
                _data._viewBox = _this.viewBoxGetBySize(width,height);
                // _data.options.width = parseFloat(width);
                // _data.options.height = parseFloat(height);

                _this.setViewBox(_data._viewBox);
                $(window).trigger('resize');
                _this.setSize(width,height);

                // _data.whRatio = _data.viewBox[2] / _data.viewBox[3];
                // if(!_data.options.responsive)
                //     _this.setResponsive();

                return _data.viewBox;
            },
            viewBoxGetBySize : function(width, height){


                var new_ratio = width / height;
                var old_ratio = _data.svgDefault.viewBox[2] / _data.svgDefault.viewBox[3];

                var vb = $.extend([],_data.svgDefault.viewBox);

                if (new_ratio != old_ratio){
                    //vb[2] = width*_data.svgDefault.viewBox[2] / _data.svgDefault.width;
                    //vb[3] = height*_data.svgDefault.viewBox[3] / _data.svgDefault.height;
                    if (new_ratio > old_ratio){
                        vb[2] = _data.svgDefault.viewBox[3] * new_ratio;
                        vb[0] = _data.svgDefault.viewBox[0] - ((vb[2] - _data.svgDefault.viewBox[2])/2);
                    }else{
                        vb[3] = _data.svgDefault.viewBox[2] / new_ratio;
                        vb[1] = _data.svgDefault.viewBox[1] - ((vb[3] - _data.svgDefault.viewBox[3])/2);
                    }

                }

                return vb;
            },
            viewBoxReset : function(toInitial){
                if(_data.options.googleMaps.on){
                    if(toInitial){
                        _data.options.googleMaps.center = null;
                        _data.options.googleMaps.zoom = null;
                    }
                    if(!_data.options.googleMaps.center || !_data.options.googleMaps.zoom){
                        var southWest = new google.maps.LatLng(_data.geoViewBox.bottomLat, _data.geoViewBox.leftLon);
                        var northEast = new google.maps.LatLng(_data.geoViewBox.topLat, _data.geoViewBox.rightLon);
                        var bounds = new google.maps.LatLngBounds(southWest,northEast);
                        _data.googleMaps.map.fitBounds(bounds);
                        _data.options.googleMaps.center = _data.googleMaps.map.getCenter().toJSON();
                        _data.options.googleMaps.zoom = _data.googleMaps.map.getZoom();
                    }else{
                        _data.googleMaps.map.setZoom(_data.options.googleMaps.zoom);
                        _data.googleMaps.map.setCenter(_data.options.googleMaps.center);
                    }
                }else{
                    if(toInitial){
                        var v = _data._viewBox || _data.svgDefault.viewBox;
                        _data.zoomLevel = 0;
                        _data._scale = 1;
                        _this.setViewBox(v);
                    }else{
                        _this.setViewBox();
                    }
                }
            },
            getGeoViewBox : function(){
                var v         = _data.viewBox;
                var leftLon   = _this.convertSVGToGeo(v[0],v[1])[1];
                var rightLon  = _this.convertSVGToGeo(v[0]+v[2],v[1])[1];
                var topLat    = _this.convertSVGToGeo(v[0],v[1])[0];
                var bottomLat = _this.convertSVGToGeo(v[0],v[1]+v[3])[0];
                return [leftLon, topLat, rightLon, bottomLat];
            },
            mapAdjustStrokes : function(){
                var _this = this;
                _data.$svg.find('path, polygon, circle, ellipse, rect').each(function(index){
                        if($(this).data('stroke-width')) {
                            $(this).css('stroke-width', $(this).data('stroke-width') / _data.scale);
                        }
                });
            },
            // ZOOM
            zoomIn: function(center){
                if(_data.googleMaps.map){
                    if(!_data.isZooming){
                        _data.isZooming = true;
                        var zoom = _data.googleMaps.map.getZoom();
                        // Limit zoom of Google map to level "17"
                        // If zoom more - browser can't position markers
                        // because css transform/translate is limited to 33mil px
                        var zoom_new = (zoom+1) > 20 ? 20 : zoom+1;
                        _data.googleMaps.map.setZoom(zoom_new);
                    }
                }else if(_data.canZoom){
                    _data.canZoom = false;
                    setTimeout(function(){
                        _data.canZoom = true;
                    }, 700);
                    _this.zoom(1, center);
                }
            },
            zoomOut: function(center){
                if(_data.googleMaps.map){
                    if(!_data.isZooming && _data.googleMaps.map.getZoom()-1 >= _data.options.googleMaps.minZoom) {
                        _data.isZooming = true;
                        var zoom = _data.googleMaps.map.getZoom();
                        var zoom_new = (zoom - 1) < 1 ? 1 : (zoom-1);
                        _data.googleMaps.map.setZoom(zoom_new);
                    }
                }else if(_data.canZoom){
                    _data.canZoom = false;
                    setTimeout(function(){
                        _data.canZoom = true;
                    }, 700);
                    _this.zoom(-1, center);
                }
            },
            touchZoomStart : function (touchScale){

                var touchZoomStart = _data._scale;
                _data.scale  = _data.scale * zoom_k;
                var zoom   = _data._scale;
                _data._scale = _data._scale * zoom_k;


                var vWidth     = _data.viewBox[2];
                var vHeight    = _data.viewBox[3];
                var newViewBox = [];

                newViewBox[2]  = _data._viewBox[2] / _data._scale;
                newViewBox[3]  = _data._viewBox[3] / _data._scale;

                newViewBox[0]  = _data.viewBox[0] + (vWidth - newViewBox[2]) / 2;
                newViewBox[1]  = viewBox[1] + (vHeight - newViewBox[3]) / 2;

                _this.setViewBox(newViewBox);

            },
            touchZoomMove : function(){

            },
            touchZoomEnd : function(){

            },
            zoomTo : function (region, zoomToLevel){

                zoomToLevel = zoomToLevel!=undefined ? parseInt(zoomToLevel) : false;

                if(typeof region == 'string') {
                    region = _this.getRegion(region);
                }

                if(_data.googleMaps.map) {
                    if(region instanceof Marker){
                        var latlng = _this.convertSVGToGeo(region.x, region.y);
                        _data.googleMaps.map.setZoom(zoomToLevel || 1);
                        _data.googleMaps.map.setCenter({lat: latlng[0],lng: latlng[1]});
                    }else{
                        if(region && region.length !== undefined){
                            var rbounds = region[0].getGeoBounds();
                            var southWest = new google.maps.LatLng(rbounds.sw[0], rbounds.sw[1]);
                            var northEast = new google.maps.LatLng(rbounds.ne[0], rbounds.ne[1]);
                            var bounds = new google.maps.LatLngBounds(southWest,northEast);
                            for(var i = 1; i < region.length-1; i++){
                                var rbounds2 = region[i].getGeoBounds();
                                var southWest2 = new google.maps.LatLng(rbounds2.sw[0], rbounds2.sw[1]);
                                var northEast2 = new google.maps.LatLng(rbounds2.ne[0], rbounds2.ne[1]);
                                bounds.extend(southWest2);
                                bounds.extend(northEast2);
                            }
                        } else {
                            var bounds = region.getGeoBounds();
                            var southWest = new google.maps.LatLng(bounds.sw[0], bounds.sw[1]);
                            var northEast = new google.maps.LatLng(bounds.ne[0], bounds.ne[1]);
                            var bounds = new google.maps.LatLngBounds(southWest,northEast);
                        }
                        _data.googleMaps.map.fitBounds(bounds);
                    }
                    return;
                }

                // if(_regionOrCenter.length && _regionOrCenter.length==2){
                //     zoomLevel = zoomLevel || 0;
                //     center = _regionOrCenter;
                //     xy = _this.convertGeoToPixel(center);
                //     var z = _data.zoomLevels[zoomLevel];
                //     _this.setViewBox([xy[0]- z.viewBox[2]/2,xy[1]- z.viewBox[3]/2, z.viewBox[2], z.viewBox[3]]);
                //     _this.updateSize();
                //     _data._scale = z._scale;
                //     _data.zoomLevel = zoomLevel;
                // }else{

                var bbox = [], viewBox, viewBoxPrev = [];

                if(typeof region == 'object' && region.length !== undefined){
                // multiple objects
                    var _bbox;

                    if(region[0] instanceof Region){
                        bbox = region[0].getBBox();
                        var xmin = [bbox[0]];
                        var ymin = [bbox[1]];

                        var w = (bbox[0]+bbox[2]);
                        var xmax = [w];
                        var h = (bbox[1]+bbox[3]);
                        var ymax = [h];
                        if (region.length > 1){
                            for (var i = 1; i < region.length; i++){
                                _bbox = region[i].getBBox();
                                xmin.push(_bbox[0]);
                                ymin.push(_bbox[1]);
                                var _w = _bbox[0]+_bbox[2];
                                var _h = _bbox[1]+_bbox[3];
                                xmax.push(_w);
                                ymax.push(_h);
                            }
                        }
                        xmin = Math.min.apply(Math, xmin);
                        ymin = Math.min.apply(Math, ymin);

                        var w = Math.max.apply(Math, xmax) - xmin;
                        var h = Math.max.apply(Math, ymax) - ymin;
                        bbox = [xmin, ymin, w, h];
                    } else if(region[0] instanceof Marker) {
                        var xs = []; var ys = [];

                        region.forEach(function(object){
                            if(object.location.marker && object.location.marker.x && object.location.marker.y){
                                xs.push(object.location.marker.x);
                                ys.push(object.location.marker.y);
                            }
                        });

                        // calc the min and max lng and lat
                        var minx = Math.min.apply(null, xs),
                            maxx = Math.max.apply(null, xs);
                        var miny = Math.min.apply(null, ys),
                            maxy = Math.max.apply(null, ys);


                        // var xyNE = [maxx, miny];
                        // var xySW = [minx, maxy];
                        // check if map is on border between 180/-180 longitude
                        // if(xyNE[0] < xySW[0]){
                        //     var mapPointsWidth = (_data.svgDefault.viewBox[2] / _data.mapLonDelta) * 360;
                        //     xy
                        //
                        // SW[0] = -(mapPointsWidth - xySW[0]);
                        // }

                        var padding = 10;
                        padding = _this.convertPixelToSVG([padding,0])[0] - _this.convertPixelToSVG([0,0])[0];

                        var width  = maxx - minx;
                        var height = maxy - miny;
                        bbox = [minx-padding, miny-padding, width+padding*2, height+padding*2];
                    }



                }else{
                // single object
                    bbox = region.getBBox();
                }

                if(region instanceof Marker){
                    _data.zoomLevel = zoomToLevel || 1;
                    var vb = _data.zoomLevels[_data.zoomLevel].viewBox;

                    _this.setViewBox([
                        region.x-vb[2]/2,
                        region.y-vb[3]/2,
                        vb[2],
                        vb[3]
                    ]);
                    _data._scale = _data.zoomLevels[_data.zoomLevel]._scale;
                    return;
                }

                var viewBoxPrev = [];
                var searching = true;

                $.each(_data.zoomLevels, function(key, level){
                // while(searching && key < _data.zoomLevels.length-1){
                //     var level = _data.zoomLevels[key];
                    if(searching && (viewBoxPrev && viewBoxPrev.length)){
                        if(
                            (viewBoxPrev[2] > bbox[2] && viewBoxPrev[3] > bbox[3])
                            &&
                            (bbox[2] > level.viewBox[2] || bbox[3] > level.viewBox[3])
                            )
                        {
                            // debugger;
                            _data.zoomLevel = zoomToLevel ? zoomToLevel :  parseInt(key)-1;
                            var vb = _data.zoomLevels[_data.zoomLevel].viewBox;

                            _this.setViewBox([bbox[0]-vb[2]/2+bbox[2]/2,
                                bbox[1]-vb[3]/2+bbox[3]/2,
                                vb[2],
                                vb[3]]);
                            _data._scale = _data.zoomLevels[_data.zoomLevel]._scale;
                            searching = false;

                        }
                    }
                    viewBoxPrev = level && level.viewBox;
                });
            },
            centerOn : function(region, yShift){


                if(_data.options.googleMaps.on){
                    yShift = yShift ? (yShift+12)/_this.getScale() : 0;
                    _data.$map.addClass('scrolling');
                    var latLng = region.getCenterLatLng(yShift);
                    _data.googleMaps.map.setCenter(latLng);
                    setTimeout(function(){
                        _data.$map.removeClass('scrolling');
                    },100);
                }else{
                    yShift = yShift ? (yShift+12)/_this.getScale() : 0;
                    var bbox = region.getBBox();
                    var vb   = _data.viewBox;
                    _this.setViewBox(
                        [bbox[0]-vb[2]/2+bbox[2]/2,
                            bbox[1]-vb[3]/2+bbox[3]/2 - yShift,
                            vb[2],
                            vb[3]]);
                    // _this.updateSize();
                    // _data._scale = _data.zoomLevels[_data.zoomLevel]._scale;
                }

            },
            zoom : function (delta, center, exact){

                var vWidth     = _data.viewBox[2];
                var vHeight    = _data.viewBox[3];
                var newViewBox = [];

                var isInZoomRange = _data.zoomLevel >= _data.options.zoom.limit[0] && _data.zoomLevel <= _data.options.zoom.limit[1];

                if(!exact){
                    // check for zoom limit
                    var d = delta > 0 ? 1 : -1;

                    if(!_data.zoomLevels[_data.zoomLevel+d])
                        return;

                    _data._zoomLevel = _data.zoomLevel;
                    _data._zoomLevel += d;

                    if(isInZoomRange && (_data._zoomLevel > _data.options.zoom.limit[1] || _data._zoomLevel < _data.options.zoom.limit[0]))
                        return false;

                    _data.zoomLevel = _data._zoomLevel;
                    //
                    //var zoom_k = d * _data.options.zoom.delta;
                    //if (zoom_k < 1) zoom_k = -1/zoom_k;
                    //
                    //_data._scale         = _data._scale * zoom_k;
                    //newViewBox[2]  = _data._viewBox[2] / _data._scale;
                    //newViewBox[3]  = _data._viewBox[3] / _data._scale;

                    var z = _data.zoomLevels[_data.zoomLevel];
                    _data._scale         = z._scale;
                    newViewBox           = z.viewBox;
                }else{
                    // var foundZoomLevel = false, i = 1, prevScale, newScale;
                    // prevScale = _data.zoomLevels[0]._scale;
                    // while(!foundZoomLevel){
                    //     if(exact >= prevScale && exact <= _data.zoomLevels[i]._scale){
                    //         foundZoomLevel = _data.zoomLevels[i];
                    //     }
                    //     i++;
                    // }
                    // if(isInZoomRange && (foundZoomLevel > _data.options.zoom.limit[1] || foundZoomLevel < _data.options.zoom.limit[0]))
                    //     return false;

                    // _data._scale    = exact;
                    // _data.zoomLevel = foundZoomLevel;


                    newViewBox[2]  = _data._viewBox[2] / exact;
                    newViewBox[3]  = _data._viewBox[3] / exact;
                }

                var shift = [];
                if(center){
                    var koef = d > 0 ? 0.5 : -1; // 1/2 * (d=1) || 2 * (d=-1)
                    shift = [((center[0] - _data.viewBox[0]) * koef), ((center[1] - _data.viewBox[1]) * koef)];
                    newViewBox[0] = _data.viewBox[0] + shift[0];
                    newViewBox[1] = _data.viewBox[1] + shift[1];
                }else{
                    shift = [(vWidth - newViewBox[2]) / 2, (vHeight - newViewBox[3]) / 2];
                    newViewBox[0]  = _data.viewBox[0] + shift[0];
                    newViewBox[1]  = _data.viewBox[1] + shift[1];
                }
                // Limit scroll to map's boundaries
                if(_data.options.scroll.limit)
                {
                    if(newViewBox[0] < _data.svgDefault.viewBox[0])
                        newViewBox[0] = _data.svgDefault.viewBox[0];
                    else if(newViewBox[0] + newViewBox[2] > _data.svgDefault.viewBox[0] + _data.svgDefault.viewBox[2])
                        newViewBox[0] = _data.svgDefault.viewBox[0]+_data.svgDefault.viewBox[2]-newViewBox[2];

                    if(newViewBox[1] < _data.svgDefault.viewBox[1])
                        newViewBox[1] = _data.svgDefault.viewBox[1];
                    else if(newViewBox[1] + newViewBox[3] > _data.svgDefault.viewBox[1] +_data.svgDefault.viewBox[3])
                        newViewBox[1] = _data.svgDefault.viewBox[1]+_data.svgDefault.viewBox[3]-newViewBox[3];
                }

                _this.setViewBox(newViewBox);
                // _this.trigger('zoom');

            },
            // MARK : DELETE
            markerDelete: function(marker){

                if(_data.editingMarker && _data.editingMarker.id == marker.id){
                    _data.editingMarker = null;
                    delete _data.editingMarker;
                }

                _data.markers.splice(_data.markersDict[marker.id],1);
                _this.updateMarkersDict();
                marker = null;

                if (_data.markers.length == 0)
                    _data.options.markerLastID = 0;
            },
            // MARK : ADD
            markersClusterAdd : function(markersCluster) {
                _data.layers.markers.append(markersCluster.node);
                _data.markersClusters.push(markersCluster);
                markersCluster.adjustPosition();
                // _data.markersClustersDict[markersCluster.cellX+'|'+markersCluster.cellY] = markersCluster;
                // var x = Math.ceil(markersCluster.x * _this.getScale() / 30 );
                // var y = Math.ceil(markersCluster.y * _this.getScale() / 30 );
                // var key = x+'.'+y;
                // _data.markersClustersDict[key] = markersCluster;
            },
            // MARK : ADD
            markerAdd : function(marker) {
                _data.layers.markers.append(marker.node);
                _data.markers.push(marker);
                marker.mapped = true;
                _data.markersDict[marker.id] = _data.markers.length - 1;
                marker.adjustPosition();
            },
            markerRemove : function(marker) {
                marker.node.detach();
                marker.show();
                _data.markers.splice(_data.markersDict[marker.id],1);
                marker.mapped = false;
                _this.updateMarkersDict();
            },
            /*
            markerAddOld : function(opts, create) {

                // Join default marker options with user-defined options
                var options = $.extend(true, {}, markerOptions, opts);

                if(!options.src)
                    return false;

                options.src = MapSVG.safeURL(options.src);

                if (options.width && options.height){
                    if(options.geoCoords) {
                    // Add marker by lat-lon coordinates
                        var xy = _this.convertGeoToSVG(options.geoCoords);
                    }else if (options.xy || (MapSVG.isNumber(options.x) && MapSVG.isNumber(options.y))){
                    // Add marker by SVG x-y coordinates
                        var xy = options.xy || [options.x, options.y];
                    }else{
                        return false;
                    }

                    options.x = xy[0];
                    options.y = xy[1];
                    options.xy = xy;
                    options.id  = options.id || _this.markerId();
                    if(!options.geoCoords && _data.mapIsGeo){
                        options.geoCoords = _this.convertSVGToGeo(options.x, options.y);
                    }

                    var marker = new Marker(options, _this);


                    // TODO add dataobjectsasmarekrs
                    _data.layers.markers.append(marker.node);
                    marker.href && marker.setHref(marker.href);
                    _data.markers.push(marker);
                    _data.markersDict[marker.id] = _data.markers.length - 1;

                    if(create){
                        if(typeof create == 'function'){
                            create(marker);
                        }else{
                            _data.markerEditHandler && _data.markerEditHandler.call(marker);
                        }
                    }


                    return marker;
                }else{
                    var img = new Image();
                    img.onload = function(){
                        options.width = this.width;
                        options.height = this.height;
                        return _this.markerAdd(options, create);
                    };
                    img.src = options.src;
                }
            },
            */
            markerId: function(){
                _data.options.markerLastID = _data.options.markerLastID + 1;
                var id = 'marker_'+(_data.options.markerLastID);
                if(_this.getMarker(id))
                    return _this.markerId();
                else
                    return id;
            },
            labelsRegionsAdjustPosition : function(){
                var dx, dy;
                _data.regions.forEach(function(region){
                    if(!region.center){
                        region.center = region.getCenterSVG();
                    }
                    var pos = _this.convertSVGToPixel([region.center.x, region.center.y]);
                    if(region.textLabel)
                        region.textLabel[0].style.transform = 'translate(-50%,-50%) translate(' + pos[0] + 'px,' + pos[1] + 'px)';
                });
            },
            markersAdjustPosition : function(){
                var dx, dy;

                /*
                var xs = []; var ys = [];

                _data.markers.forEach(function(marker){
                    if(marker && marker.x && marker.y){
                        xs.push(marker.x);
                        ys.push(marker.y);
                    }
                });

                console.log(xs,ys);

                // calc the min and max lng and lat
                var min = _this.convertSVGToPixel([Math.min.apply(null, xs),Math.min.apply(null, ys)]);
                var max = _this.convertSVGToPixel([Math.max.apply(null, xs),Math.max.apply(null, ys)]);
                console.log(min,max);
                */

                // TODO
                // 1. go through matrix and put markers into "cells"
                // 2. push active cells into an array
                // 3. put those cells to the map
                // 4. repeat here (so don't need to do any kind of adjust cells function


                // _data.markersClusters.forEach(function(cluster){
                //     cluster.destroy();
                //     cluster = null;
                // });
                // _data.markersClusters = [];

                _data.markers.forEach(function(marker){
                    marker.adjustPosition(_data.scale);

                    // Add to clusters:
                    // var cluster = _data.markersClusters.find(function(cluster){
                    //     return cluster.canTakeMarker(marker);
                    // });
                    // if(cluster){
                    //     cluster.addMarker(marker);
                    // } else {
                    //     var cluster = new MapSVG.MarkersCluster({
                    //         mapsvg: _this,
                    //         markers: [marker],
                    //         x: marker.x,
                    //         y: marker.y
                    //     });
                    // }
                });
                _data.markersClusters.forEach(function(cluster){
                    cluster.adjustPosition();
                });

            },
            // MARK MOVE & EDIT HANDLERS
            markerMoveStart : function(){
                // storing original coordinates
                this.data('ox', parseFloat(this.attr('x')));
                this.data('oy', parseFloat(this.attr('y')));
            },
            markerMove : function (dx, dy) {
                dx = dx/_data.scale;
                dy = dy/_data.scale;
                this.attr({x: this.data('ox') + dx, y: this.data('oy') + dy});
            },
            markerMoveEnd : function () {
                // if coordinates are same then it was a "click" and we should start editing
                if(this.data('ox') == this.attr('x') && this.data('oy') == this.attr('y')){
                    options.markerEditHandler.call(this);
                }
            },
            setEditingMarker : function (marker) {
                _data.editingMarker = marker;
                if(!_data.editingMarker.mapped){
                    // todo marker gets removed if it's just a new marker
                    _data.editingMarker.needToRemove = true;
                    // _data.editingMarker.node.addClass("mapsvg-editing-marker");
                    _this.markerAdd(_data.editingMarker);
                }
            },
            unsetEditingMarker : function(){
                if(_data.editingMarker.needToRemove){
                    _data.editingMarker.needToRemove = false;
                    _this.markerRemove(_data.editingMarker);
                }
                _data.editingMarker = null;
            },
            getEditingMarker : function(){
                return _data.editingMarker;
            },
            scrollStart : function (e,mapsvg){

                if($(e.target).hasClass('mapsvg-btn-map') || $(e.target).closest('.mapsvg-gauge').length)
                    return false;

                if(_data.editMarkers.on && $(e.target).hasClass('class')=='mapsvg-marker')
                    return false;

                _data.isScrolling = true;

                // _data.$map.css('pointer-events','none');
                _data.$map.addClass('scrolling');

                e.preventDefault();
                if(MapSVG.touchDevice){
                    var ce = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? e.originalEvent.touches[0] : e;
                }else{
                    var ce = e;
                }

                _data.scroll = _data.scroll || {};

                // initial viewbox when scrollning started
                _data.scroll.vxi = _data.viewBox[0];
                _data.scroll.vyi = _data.viewBox[1];
                // mouse coordinates when scrolling started
                _data.scroll.x  = ce.clientX;
                _data.scroll.y  = ce.clientY;
                // mouse delta
                _data.scroll.dx = 0;
                _data.scroll.dy = 0;
                // new viewbox x/y
                _data.scroll.vx = 0;
                _data.scroll.vy = 0;

                // for google maps scroll
                _data.scroll.gx  = ce.clientX;
                _data.scroll.gy  = ce.clientY;

                _data.scroll.tx = _data.scroll.tx || 0;
                _data.scroll.ty = _data.scroll.ty || 0;

                // var max = _this.convertSVGToPixel(_this.convertGeoToSVG([-85,180]));
                // var min = _this.convertSVGToPixel(_this.convertGeoToSVG([85,-180]));
                // _data.scroll.limit = {
                //     maxX: max[0]+_data.$map.width(),
                //     maxY: max[1]+_data.$map.outerHeight(),
                //     minX: min[0],
                //     minY: min[1]
                // };

                if(e.type.indexOf('mouse') === 0 ){
                    $(document).on('mousemove.scroll.mapsvg', _this.scrollMove);
                    if(_data.options.scroll.spacebar){
                        $(document).on('keyup.scroll.mapsvg', function (e) {
                            if (e.keyCode == 32) {
                                _this.scrollEnd(e, mapsvg);
                            }
                        });
                    }else{
                        $(document).on('mouseup.scroll.mapsvg', function(e){
                            _this.scrollEnd(e,mapsvg);
                        });
                    }
                }
                //else
                //    $('body').on('touchmove.scroll.mapsvg', _this.scrollMove).on('touchmove.scroll.mapsvg', function(e){_this.scrollEnd(e,mapsvg);});
            },
            scrollMove :  function (e){

                e.preventDefault();


                // $('body').css({'cursor': 'hand'});

                var ce = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? e.originalEvent.touches[0] : e;


                // TODO: на каждый зум лев считать допустимые translate xy при данном scale
                if(_this.panBy((_data.scroll.gx - ce.clientX),(_data.scroll.gy - ce.clientY))){
                    if(_data.googleMaps.map){
                        // var coords = _this.getCenter();
                        // _data.googleMaps.map.setCenter(coords);
                        // _data.googleMaps.map.panBy((_data.scroll.gx - ce.clientX),(_data.scroll.gy - ce.clientY));
                        var point = _data.googleMaps.map.getCenter();

                        // var overlay = new google.maps.OverlayView();
                        // overlay.draw = function() {};
                        // overlay.setMap(_data.googleMaps.map);

                        var projection = _data.googleMaps.overlay.getProjection();

                        var pixelpoint = projection.fromLatLngToDivPixel(point);
                        pixelpoint.x += _data.scroll.gx - ce.clientX;
                        pixelpoint.y += _data.scroll.gy - ce.clientY;

                        point = projection.fromDivPixelToLatLng(pixelpoint);

                        _data.googleMaps.map.setCenter(point);
                    }
                }

                _data.scroll.gx  = ce.clientX;
                _data.scroll.gy  = ce.clientY;

                // delta x/y
                _data.scroll.dx = (_data.scroll.x - ce.clientX);
                _data.scroll.dy = (_data.scroll.y - ce.clientY);

                // new viewBox x/y
                var vx = parseInt(_data.scroll.vxi + _data.scroll.dx /_data.scale);
                var vy = parseInt(_data.scroll.vyi + _data.scroll.dy /_data.scale);

                // Limit scroll to map boundaries
                if(_data.options.scroll.limit){

                    if(vx < _data.svgDefault.viewBox[0])
                        vx = _data.svgDefault.viewBox[0];
                    else if(_data.viewBox[2] + vx > _data.svgDefault.viewBox[0] + _data.svgDefault.viewBox[2])
                        vx = (_data.svgDefault.viewBox[0]+_data.svgDefault.viewBox[2]-_data.viewBox[2]);

                    if(vy < _data.svgDefault.viewBox[1])
                        vy = _data.svgDefault.viewBox[1];
                    else if(_data.viewBox[3] + vy > _data.svgDefault.viewBox[1] + _data.svgDefault.viewBox[3])
                        vy = (_data.svgDefault.viewBox[1]+_data.svgDefault.viewBox[3]-_data.viewBox[3]);

                }


                _data.scroll.vx = vx;
                _data.scroll.vy = vy;


                // set new viewBox
                // _this.setViewBox([_data.scroll.vx,  _data.scroll.vy, _data.viewBox[2], _data.viewBox[3]]);

            },
            scrollEnd : function (e,mapsvg, noClick){

                // _data.scroll.tx = (_data.scroll.tx - _data.scroll.dx);
                // _data.scroll.ty = (_data.scroll.ty - _data.scroll.dy);

                _data.isScrolling = false;
                _data.googleMaps && _data.googleMaps.overlay && _data.googleMaps.overlay.draw();
                _data.$map.removeClass('scrolling');
                $(document).off('keyup.scroll.mapsvg');
                $(document).off('mousemove.scroll.mapsvg');
                $(document).off('mouseup.scroll.mapsvg');

                // call regionClickHandler if mouse did not move more than 5 pixels
                if (noClick !== true && Math.abs(_data.scroll.dx)<5 && Math.abs(_data.scroll.dy)<5){
                    // _this.popoverOffHandler(e);
                    if(_data.editMarkers.on)
                        _data.clickAddsMarker && _this.markerAddClickHandler(e);
                    else if (_data.region_clicked)
                        _this.regionClickHandler(e, _data.region_clicked);
                }


                _data.viewBox[0] = _data.scroll.vx || _data.viewBox[0];
                _data.viewBox[1] = _data.scroll.vy || _data.viewBox[1] ;


                // _data.$map.css('pointer-events','auto');
                // $('body').css({'cursor': 'default'});

                // if(_data.googleMaps.map) {
                    // fix shift
                    // _this.setViewBoxByGoogleMapBounds();
                // }


            },
            panBy : function(x, y){

                // _data.scroll.tx -= x;
                // _data.scroll.ty -= y;
                var tx = _data.scroll.tx - x;
                var ty = _data.scroll.ty - y;

                if(!_data.options.googleMaps.on && _data.options.scroll.limit){
                    var svg = _data.$svg[0].getBoundingClientRect();
                    var bounds = _data.$map[0].getBoundingClientRect();
                    if(svg.left-x > bounds.left || svg.right-x < bounds.right){
                        tx = _data.scroll.tx;
                    }
                    if(svg.top-y > bounds.top || svg.bottom-y < bounds.bottom){
                        ty = _data.scroll.ty;
                    }
                }

                _data.$scrollpane.css({
                    'transform': 'translate('+tx+'px,'+ty+'px)'
                });

                _data.scroll.tx = tx;
                _data.scroll.ty = ty;
                return true;

            },
            panTo : function(x,y){

            },
            // REMEMBER WHICH REGION WAS CLICKED BEFORE START PANNING
            scrollRegionClickHandler : function (e, region) {
                _data.region_clicked = region;
            },
            touchStart : function (_e,mapsvg){
                // if($(_e.target).hasClass('mapsvg-popover') || $(_e.target).closest('.mapsvg-popover').length ){
                //     return true;
                // }
                _e.preventDefault();
                // _e.stopPropagation();

                // stop scroll and cancel click event
                if(_data.isScrolling){
                    _this.scrollEnd(_e, mapsvg, true);
                }
                var e = _e.originalEvent;

                if(_data.options.zoom.on && e.touches && e.touches.length == 2){
                    _data.touchZoomStartViewBox = _data.viewBox;
                    _data.touchZoomStartScale =  _data.scale;
                    _data.touchZoomEnd   =  1;
                    _data.scaleDistStart = Math.hypot(
                        e.touches[0].pageX - e.touches[1].pageX,
                        e.touches[0].pageY - e.touches[1].pageY);
                }else if(e.touches && e.touches.length == 1){
                    _this.scrollStart(_e,mapsvg);
                }

                $(document).on('touchmove.scroll.mapsvg', function(e){
                    e.preventDefault(); _this.touchMove(e,_this);
                }).on('touchend.scroll.mapsvg', function(e){
                    e.preventDefault(); _this.touchEnd(e,_this);
                });


            },
            touchMove : function (_e, mapsvg){
                // if($(_e.target).hasClass('mapsvg-popover') || $(_e.target).closest('.mapsvg-popover').length ){
                //     return true;
                // }
                _e.preventDefault();
                var e = _e.originalEvent;

                if(_data.options.zoom.on && e.touches && e.touches.length == 2){
                    if(!MapSVG.ios){
                        e.scale = Math.hypot(
                                e.touches[0].pageX - e.touches[1].pageX,
                                e.touches[0].pageY - e.touches[1].pageY)/_data.scaleDistStart;
                    }

                    if(e.scale!=1 && _data.canZoom) {
                        var d = e.scale > 1 ? 1 : -1;

                        var cx = e.touches[0].pageX >= e.touches[1].pageX ? e.touches[0].pageX - (e.touches[0].pageX - e.touches[1].pageX)/2 - _data.$svg.offset().left : e.touches[1].pageX - (e.touches[1].pageX - e.touches[0].pageX)/2 - _data.$svg.offset().left;
                        var cy = e.touches[0].pageY >= e.touches[1].pageY ? e.touches[0].pageY - (e.touches[0].pageY - e.touches[1].pageY) - _data.$svg.offset().top : e.touches[1].pageY - (e.touches[1].pageY - e.touches[0].pageY) - _data.$svg.offset().top;
                        var center = _this.convertPixelToSVG([cx, cy]);

                        if (d > 0)
                            _this.zoomIn(center);
                        else
                            _this.zoomOut(center);
                    }
                }else if(_data.isScrolling){
                    _this.scrollMove(_e);
                }
            },
            touchEnd : function (_e, mapsvg){
                // if($(_e.target).hasClass('mapsvg-popover') || $(_e.target).closest('.mapsvg-popover').length ){
                //     return true;
                // }
                _e.preventDefault();
                var e = _e.originalEvent;
                if(_data.touchZoomStart){
                    _data.touchZoomStart  = false;
                    _data.touchZoomEnd    = false;
                }else if(_data.isScrolling){
                    _this.scrollEnd(_e, mapsvg);
                }

                $(document).off('touchmove.scroll.mapsvg');
                $(document).off('touchend.scroll.mapsvg');


            },
            markersGroupHide : function(group){
                for(var i in _data.markers[group]){
                    _data.markers[group][i].hide();
                }
            },
            markersGroupShow : function(group){
                for(var i in _data.markers[group]){
                    _data.markers[group][i].show();
                }
            },
            regionsGroupSelect : function(group){
                for(var i in _data.markers[group]){
                    _data.markers[group][i].hide();
                }
            },
            regionsGroupUnselect : function(group){
                for(var i in _data.markers[group]){
                    _data.markers[group][i].show();
                }
            },
            // GET ALL MARKERS
            markersGet : function(){
                return _data.markers;
            },
            // GET SELECTED REGION OR ARRAY OF SELECTED REGIONS
            getSelected : function(){
                return _data.selected_id;
            },
            // SELECT REGION
            selectRegion :    function(id, skipDirectorySelection){
                // _this.hidePopover();
                if(typeof id == "string"){
                    var region = _this.getRegion(id);
                }else{
                    var region = id;
                }
                if(!region) return false;
                if(_data.options.multiSelect && !_data.editRegions.on){
                    if(region.selected){
                        _this.deselectRegion(region);
                        if(!skipDirectorySelection && _data.options.menu.on){
                            if(_data.options.menu.source == 'database') {
                                if (region.objects && region.objects.length) {
                                    var ids = region.objects.map(function (obj) {
                                        return obj.id;
                                    });
                                }
                            }else{
                                var ids = [region.id];
                            }
                            _data.controllers.directory.deselectItems(ids);
                        }

                        return;
                    }
                }else if(_data.selected_id.length>0){
                    _this.deselectAllRegions();
                    if(!skipDirectorySelection && _data.options.menu.on){
                        if(_data.options.menu.source == 'database') {
                            if (region.objects && region.objects.length) {
                                var ids = region.objects.map(function (obj) {
                                    return obj.id;
                                });
                            }
                        }else{
                            var ids = [region.id];
                        }
                        _data.controllers.directory.deselectItems();
                    }
                }
                _data.selected_id.push(region.id);
                region.select();
                if(!skipDirectorySelection && _data.options.menu.on && _data.controllers && _data.controllers.directory){
                    if(_data.options.menu.source == 'dataloadbase'){
                        if(region.objects && region.objects.length) {
                            var ids = region.objects.map(function(obj){
                                return obj.id;
                            });
                        }else{
                            var ids = [region.id];
                        }
                    }else{
                        var ids = [region.id];
                    }
                    _data.controllers.directory.selectItems(ids);
                }

                window.location.hash = '!'+region.id;
            },
            deselectAllRegions : function(){
                $.each(_data.selected_id, function(index,id){
                    _this.deselectRegion(_this.getRegion(id));
                });
            },
            deselectRegion : function (region){
                if(!region)
                    region = _this.getRegion(_data.selected_id[0]);
                if(region){
                    region.deselect();
                    var i = $.inArray(region.id, _data.selected_id);
                    _data.selected_id.splice(i,1);
                    // if(MapSVG.browser.ie)//|| MapSVG.browser.firefox)
                    //     _this.mapAdjustStrokes();
                }
                window.location.hash =  window.location.hash.replace(region.id,'');
            },
            highlightRegions : function(regions){
                regions.forEach(function(region){
                    if(!region.selected && !region.disabled){
                        _data.highlightedRegions.push(region);
                        region.highlight();
                    }
                })
            },
            unhighlightRegions : function(){
                _data.highlightedRegions.forEach(function(region){
                    if(!region.selected && !region.disabled)
                        region.unhighlight();
                });
                _data.highlightedRegions = [];
            },
            convertMouseToSVG : function(e){
                var mc = MapSVG.mouseCoords(e);
                var x = mc.x - _data.$svg.offset().left;
                var y = mc.y - _data.$svg.offset().top;
                return _this.convertPixelToSVG([x,y]);
            },
            convertSVGToPixel : function(xy){
                var scale = _this.getScale();
                return [(xy[0]-_data.svgDefault.viewBox[0])*scale, (xy[1]-_data.svgDefault.viewBox[1])*scale];
            },
            convertPixelToSVG : function(xy){
                var scale = _this.getScale();
                return [(xy[0])/scale+_data.svgDefault.viewBox[0], (xy[1])/scale+_data.svgDefault.viewBox[1]];
            },
            convertGeoToSVG: function (coords){

                var lat = parseFloat(coords[0]);
                var lon = parseFloat(coords[1]);
                var x = (lon - _data.geoViewBox.leftLon) * (_data.svgDefault.viewBox[2] / _data.mapLonDelta);

                var lat = lat * 3.14159 / 180;
                // var worldMapWidth = ((_data.svgDefault.width / _data.mapLonDelta) * 360) / (2 * 3.14159);
                var worldMapWidth = ((_data.svgDefault.viewBox[2] / _data.mapLonDelta) * 360) / (2 * 3.14159);
                var mapOffsetY    = (worldMapWidth / 2 * Math.log((1 + Math.sin(_data.mapLatBottomDegree)) / (1 - Math.sin(_data.mapLatBottomDegree))));
                var y = _data.svgDefault.viewBox[3] - ((worldMapWidth / 2 * Math.log((1 + Math.sin(lat)) / (1 - Math.sin(lat)))) - mapOffsetY);

                x += _data.svgDefault.viewBox[0];
                y += _data.svgDefault.viewBox[1];

                return [x, y];
            },
            convertSVGToGeo: function (tx, ty){
                tx -= _data.svgDefault.viewBox[0];
                ty -= _data.svgDefault.viewBox[1];
                /* called worldMapWidth in Raphael's Code, but I think that's the radius since it's the map width or circumference divided by 2*PI  */
                var worldMapRadius = _data.svgDefault.viewBox[2] / _data.mapLonDelta * 360/(2 * Math.PI);
                var mapOffsetY = ( worldMapRadius / 2 * Math.log( (1 + Math.sin(_data.mapLatBottomDegree) ) / (1 - Math.sin(_data.mapLatBottomDegree))  ));
                var equatorY = _data.svgDefault.viewBox[3] + mapOffsetY;
                var a = (equatorY-ty)/worldMapRadius;
                var lat = 180/Math.PI * (2 * Math.atan(Math.exp(a)) - Math.PI/2);
                var lon = _data.geoViewBox.leftLon+tx/_data.svgDefault.viewBox[2]*_data.mapLonDelta;
                lat  = parseFloat(lat.toFixed(6));
                lon  = parseFloat(lon.toFixed(6));
                return [lat,lon];
            },
            convertGeoBoundsToViewBox: function (sw, ne){

                var lat = parseFloat(coords[0]);
                var lon = parseFloat(coords[1]);
                var x = (lon - _data.geoViewBox.leftLon) * (_data.svgDefault.viewBox[2] / _data.mapLonDelta);

                var lat = lat * 3.14159 / 180;
                // var worldMapWidth = ((_data.svgDefault.width / _data.mapLonDelta) * 360) / (2 * 3.14159);
                var worldMapWidth = ((_data.svgDefault.viewBox[2] / _data.mapLonDelta) * 360) / (2 * 3.14159);
                var mapOffsetY = (worldMapWidth / 2 * Math.log((1 + Math.sin(_data.mapLatBottomDegree)) / (1 - Math.sin(_data.mapLatBottomDegree))));
                var y = _data.svgDefault.viewBox[3] - ((worldMapWidth / 2 * Math.log((1 + Math.sin(lat)) / (1 - Math.sin(lat)))) - mapOffsetY);

                x += _data.svgDefault.viewBox[0];
                y += _data.svgDefault.viewBox[1];

                return [x, y];
            },
            // PICK COLOR FROM GRADIENT
            pickGaugeColor: function(gaugeValue) {
                var w = (gaugeValue - _data.options.gauge.min) / _data.options.gauge.maxAdjusted;
                var rgb = [
                    Math.round(_data.options.gauge.colors.diffRGB.r * w + _data.options.gauge.colors.lowRGB.r),
                    Math.round(_data.options.gauge.colors.diffRGB.g * w + _data.options.gauge.colors.lowRGB.g),
                    Math.round(_data.options.gauge.colors.diffRGB.b * w + _data.options.gauge.colors.lowRGB.b),
                    Math.round(_data.options.gauge.colors.diffRGB.a * w + _data.options.gauge.colors.lowRGB.a)
                ];
                return rgb;
            },
            // CHECK IF REGION IS DISABLED
            isRegionDisabled : function (id, svgfill){

                if(_data.options.regions[id] && (_data.options.regions[id].disabled || svgfill == 'none') ){
                    return true;
                }else if(
                    (_data.options.regions[id] == undefined || MapSVG.parseBoolean(_data.options.regions[id].disabled)) &&
                    (_data.options.disableAll || svgfill == 'none' || id == 'labels' || id == 'Labels')

                ){
                    return true;
                }else{
                    return false;
                }
            },
            regionClickHandler : function(e, region, skipPopover){

                _data.region_clicked = null;
                var actions = _data.options.actions;

                if(_data.eventsPrevent['click'])
                    return;

                if(_data.editRegions.on){
                    _this.selectRegion(region.id);
                    _data.regionEditHandler.call(region);
                    return;
                }
                // _this.hidePopover();

                if(region instanceof MarkersCluster){
                    _this.zoomTo(region.markers);
                    return;
                }

                if(region.isRegion()){

                    _this.selectRegion(region.id);

                    if(actions.region.click.zoom){
                        _this.zoomTo(region, actions.region.click.zoomToLevel);
                    }

                    if((_data.editMode || _data.options.menu.on) && actions.region.click.filterDirectory){
                        _this.database.getAll({filters: {regions: region.id}});
                    }

                    if(actions.region.click.showDetails){
                        _this.loadDetailsView(region);
                    }

                    if(actions.region.click.showPopover){
                        if(actions.region.click.zoom){
                            setTimeout(function(){
                                _this.showPopover(region);
                            },400);
                        }else{
                            _this.showPopover(region);
                        }
                    }else if(e && e.type.indexOf('touch')!==-1 && actions.region.touch.showPopover){
                        if(actions.region.click.zoom){
                            setTimeout(function(){
                                _this.showPopover(region);
                            },400);
                        }else{
                            _this.showPopover(region);
                        }
                    }

                    if(actions.region.click.goToLink){
                        var linkParts = actions.region.click.linkField.split('.');
                        var url;
                        if(linkParts.length > 1){
                            var obj = linkParts.shift();
                            var attr = '.'+linkParts.join('.');
                            if(obj == 'Region'){
                                if(region.data)
                                    url = eval('region.data'+attr);
                            }else{
                                if(region.objects && region.objects[0])
                                    url = eval('region.objects[0]'+attr);
                            }

                            if(url && !_data.disableLinks){
                                if(_data.editMode){
                                   alert('Redirect: '+url+'\nLinks are disabled in the preview.');
                                   return true;
                                }
                                if(actions.region.click.newTab){
                                    var win = window.open(url, '_blank');
                                    win.focus();
                                }else{
                                    window.location.href = url;
                                }
                            }
                        }
                    }
                    if(actions.region.click.showAnotherMap){
                        if(_data.editMode){
                            alert('"Show another map" action is disabled in the preview');
                            return true;
                        }
                        var linkParts = actions.region.click.showAnotherMapField.split('.');
                        var url;
                        if(linkParts.length > 1){
                            var obj = linkParts.shift();
                            var attr = '.'+linkParts.join('.');
                            var map_id;
                            if(obj == 'Region'){
                                if(region.data)
                                    map_id = eval('region.data'+attr);
                            }else{
                                if(region.objects && region.objects[0])
                                    map_id = eval('region.objects[0]'+attr);
                            }

                            if(map_id){
                                var container = actions.region.click.showAnotherMapContainerId ? $('#'+actions.region.click.showAnotherMapContainerId) : _data.$map;
                                jQuery.get(ajaxurl, {action:"mapsvg_get",id: map_id},function(data){
                                    // var prevoptions = $.extend({}, container.mapSvg().getData().options);
                                    if(container.find('svg').length)
                                        container.mapSvg().destroy();
                                    eval('var options = '+data);
                                    container.mapSvg(options);
                                    // container.mapSvg().setPrevMap(prevoptions);
                                });
                            }
                        }
                    }
                    if(_data.events['click.region'])
                        _data.events['click.region'].call(region, e, _this);
                }else if(region.isMarker()){

                    var passingObject = region.object;

                    if(actions.marker.click.zoom) {
                        _this.zoomTo(region, actions.marker.click.zoomToLevel);
                    }

                    if((_data.editMode || _data.options.menu.on) && actions.marker.click.filterDirectory)
                        _this.database.getAll({filters: {id: region.object.id}});

                    if(actions.marker.click.showDetails)
                        _this.loadDetailsView(passingObject);
                    if(actions.marker.click.showPopover){
                        if(actions.marker.click.zoom){
                            setTimeout(function(){
                                _this.showPopover(passingObject);
                            },500);
                        }else{
                            _this.showPopover(passingObject);
                        }
                    }else if(e && e.type.indexOf('touch')!==-1 && actions.marker.touch.showPopover){
                        if(actions.marker.click.zoom){
                            setTimeout(function(){
                                _this.showPopover(passingObject);
                            },500);
                        }else{
                            _this.showPopover(passingObject);
                        }
                    }
                    if(actions.marker.click.goToLink){
                        var linkParts = actions.marker.click.linkField.split('.');
                        var url;
                        if(linkParts.length > 1){
                            var obj = linkParts.shift();
                            var attr = '.'+linkParts.join('.');
                            url = eval('passingObject'+attr);
                            if(url && !_data.disableLinks)
                                if(_data.editMode){
                                    alert('Redirect: '+url+'\nLinks are disabled in the preview.');
                                    return true;
                                }
                                if(actions.marker.click.newTab){
                                    var win = window.open(url, '_blank');
                                    win.focus();
                                }else{
                                    window.location.href = url;
                                }
                        }
                    }
                    if(_data.events['click.marker'])
                        _data.events['click.marker'].call(region, e, _this);
                }
            },
            fileExists : function(url){
                if(url.substr(0,4)=="data")
                    return true;
                var http = new XMLHttpRequest();
                http.open('HEAD', url, false);
                http.send();
                return http.status!=404;
            },
            getStyle : function(elem,prop){
                if (elem.currentStyle) {
                    var res= elem.currentStyle.margin;
                } else if (window.getComputedStyle) {
                    if (window.getComputedStyle.getPropertyValue){
                        var res= window.getComputedStyle(elem, null).getPropertyValue(prop)}
                    else{var res =window.getComputedStyle(elem)[prop] };
                }
                return res;
            },
            search: function(str){
                var results = [];
                str = str.toLowerCase();
                _data.regions.forEach(function(r){
                    if(r.id.toLowerCase().indexOf(str) === 0 || r.id.toLowerCase().indexOf('-'+str) !== -1 || (r.title && r.title.toLowerCase().indexOf(str) === 0))
                        results.push({id: r.id, id_no_spaces: r.id_no_spaces});
                });
                return results;
            },
            searchMarkers: function(str){
                var results = [];
                str = str.toLowerCase();
                _data.markers.forEach(function(m){
                    if(m.id.toLowerCase().indexOf(str) === 0)
                        results.push(m.id);
                });
                return results;
            },
            searchData: function(field, str){
                var results = [];
                str = str.toLowerCase();
                _data.options.data.forEach(function(params){
                    for(var i in params){
                        if((''+params[i]).toLowerCase().indexOf(str) === 0 && results.indexOf(params.id)==-1)
                            results.push(params.id);
                    }
                });
                return results;
            },
            hideMarkersExceptOne: function(id){
                _data.markers.forEach(function(m){
                    if(m.id!=id){
                        m.hide();
                    }
                });
                _data.$wrap.addClass('mapsvg-clusters-hidden');
            },
            showMarkers: function(){
                _data.markers.forEach(function(m){
                    m.show();
                });
                _data.$wrap.removeClass('mapsvg-clusters-hidden');
            },
            markerAddClickHandler : function(e){

                // Don't add marker if marker was clicked
                if($(e.target).hasClass('mapsvg-marker')) return false;

                var mc = MapSVG.mouseCoords(e);
                var x  = mc.x - _data.$svg.offset().left;
                var y  = mc.y - _data.$svg.offset().top;
                var xy = _this.convertPixelToSVG([x,y]);
                var latlng = _this.convertSVGToGeo(xy[0], xy[1]);

                if(!$.isNumeric(x) || !$.isNumeric(y))
                    return false;


                var location = new Location({
                    x: xy[0],
                    y: xy[1],
                    lat: latlng[0],
                    lng: latlng[1],
                    img: _this.getMarkerImage()
                });

                // When Form Builder is opened in MapSVG Builder, there could be created marker
                // already so we want to move the marker to a new position on map click
                // instead of creating a new marker
                if(_data.editingMarker){
                    // _data.editingMarker.moveToClick([x,y]);
                    _data.editingMarker.setXy(xy);
                    return;
                }

                var marker = new Marker({
                    location: location,
                    mapsvg: this
                });
                _data.markerEditHandler && _data.markerEditHandler.call(marker);

                // _this.markerAdd(data, true);
            },
            setMarkerImagesDependency : function(){
                var _this = this;
                var locationField = _this.locationField || _this.database.getSchemaField('location');
                if(locationField.markerField && Object.values(locationField.markersByField).length > 0){
                    this.setMarkersByField = true;
                } else {
                    this.setMarkersByField = false;
                }
            },
            getMarkerImage : function(fieldValue){
                var _this = this;
                if(this.setMarkersByField && fieldValue!==undefined && _this.locationField.markersByField[fieldValue]){
                    return _this.locationField.markersByField[fieldValue];
                } else {
                    return _data.options.defaultMarkerImage;
                }
            },
            setDefaultMarkerImage : function(src){
                _data.options.defaultMarkerImage = src;
            },
            setMarkersEditMode : function(on, clickAddsMarker){
                _data.editMarkers.on = MapSVG.parseBoolean(on);
                _data.clickAddsMarker = _data.editMarkers.on;
                _this.setEventHandlers();
            },
            setRegionsEditMode : function(on){
                _data.editRegions.on = MapSVG.parseBoolean(on);
                _this.deselectAllRegions();
                _this.setEventHandlers();
            },
            setEditMode: function(on){
                _data.editMode = on;
            },
            setDataEditMode : function(on){
                _data.editData.on = MapSVG.parseBoolean(on);
                _this.deselectAllRegions();
                _this.setEventHandlers();
            },
            // Adding markers
            setMarkers : function (markers){
                $.each(markers, function(i, marker){
                    _this.markerAdd(marker);
                });
                _data.markers.sort(function(a,b){
                    return a.id == b.id ? 0 : +(a.id > b.id) || -1;
                });
                _data.markers.forEach(function(marker, index){
                    _data.markersDict[marker.id] = index;
                });


            },
            setEventHandler : function(){

            },
            textBr: function(text){
                var htmls = [];
                var lines = text.split(/\n/);
                var tmpDiv = jQuery(document.createElement('div'));
                for (var i = 0 ; i < lines.length ; i++) {
                    htmls.push(tmpDiv.text(lines[i]).html());
                }
                return htmls.join("<br />");
            },
            runUserFunction : function(func){
                try{
                    func();
                }catch(error){
                    console.log("MapSVG user-defined function error: (line "+error.line+"): "+error.message);
                }
            },
            download: function(){

                if(!_data.downloadForm) {
                    _data.downloadForm = $('<form id="mdownload" action="/wp-content/plugins/mapsvg-dev/download.php" method="POST"><input type="hidden" name="svg_file" value="0" /><input type="hidden" name="svg_title"></form>');
                    _data.downloadForm.appendTo('body');
                }
                _data.downloadForm.find('input[name="svg_file"]').val(_data.$svg.prop('outerHTML'));
                _data.downloadForm.find('input[name="svg_title"]').val(_data.options.title);
                setTimeout(function() {
                    jQuery('#mdownload').submit();
                }, 500);
            },
            showTooltip : function(tip){
                // TODO strip HTML comments, spaces and new lines and then check the length
                if (tip.length){
                    _data.tooltip.container.html(tip);
                    _data.tooltip.container.addClass('mapsvg-tooltip-visible');
                }
            },
            popoverAdjustPosition: function(){
                if(!_data.$popover || !_data.$popover.data('point')) return;

                var pos = _this.convertSVGToPixel(_data.$popover.data('point'));

                // pos[0] = pos[0] - (_data.layers.popovers.offset().left - _data.$map.offset().left);
                // pos[1] = pos[1] - (_data.layers.popovers.offset().top - _data.$map.offset().top);

                _data.$popover[0].style.transform = 'translateX(-50%) translate('+pos[0]+'px,'+pos[1]+'px)';
            },
            showPopover : function (object){

                // TODO check why need this:
                // var popoverShown = false;

                var mapObject = object instanceof Region ? object : (object.location && object.location.marker && object.location.marker ? object.location.marker : null);
                if(!mapObject)
                    return;

                var point;
                if(mapObject instanceof Marker){
                    point = {x: mapObject.x, y: mapObject.y};
                }else{
                    point = mapObject.getCenterSVG();
                }
                _this.popover && _this.popover.destroy();
                _this.popover = new MapSVG.PopoverController({
                    container: _data.$popover,
                    point: point,
                    yShift: mapObject instanceof Marker ? mapObject.height : 0,
                    template: object instanceof Region ?  _data.templates.popoverRegion : _data.templates.popoverMarker,
                    mapsvg: _this,
                    data: object instanceof Region ? object.forTemplate() : object,
                    mapObject: mapObject,
                    scrollable: true,
                    withToolbar: MapSVG.isPhone && _data.options.popovers.mobileFullscreen ? false : true,
                    events: {
                        'shown': function(mapsvg){
                            if(_data.options.popovers.centerOn){
                                var shift = this.container.height()/2;
                                if(_data.options.popovers.centerOn && !(MapSVG.isPhone && _data.options.popovers.mobileFullscreen)){
                                    _this.centerOn(mapObject, shift);
                                }
                            }
                            _data.events['shown.popover'] && _data.events['shown.popover'].call(this, _this);
                            _data.popoverShowingFor = mapObject;
                            _this.trigger('popoverShown');
                        },
                        'closed': function(mapsvg){
                            _data.options.popovers.resetViewboxOnClose && _this.viewBoxReset(true);
                            //if(mapObject instanceof Region){
                                _data.popoverShowingFor = null;
                            //}
                            _data.events['closed.popover'] && _data.events['closed.popover'].call(this, mapsvg);
                            _this.trigger('popoverClosed');
                        },
                        'resize': function(){
                            if(_data.options.popovers.centerOn){
                                var shift = this.container.height()/2;
                                if(_data.options.popovers.centerOn && !(MapSVG.isPhone && _data.options.popovers.mobileFullscreen)){
                                    _this.centerOn(mapObject, shift);
                                }
                            }
                         }
                    }
                });

                // var center = mapObject.getCenterSVG();
                // _data.$popover.data('point', [center.x,center.y]);
                // _this.popoverAdjustPosition();

                // _data.$popover.addClass('mapsvg-popover-visible');
                // _data.$popover.addClass('mapsvg-popover-animate');
                //
                // popoverShown = true;

                // $('body').toggleClass('mapsvg-popover-open', popoverShown);
            },
            hidePopover : function(){
                _this.popover && _this.popover.close();
                // $('body').toggleClass('mapsvg-popover-open', false);
            },
            hideTip : function (){
                _data.tooltip.container.removeClass('mapsvg-tooltip-visible');
                //_data.tooltip.container.html('');
            },
            popoverOffHandler : function(e){

                if(_data.isScrolling || $(e.target).closest('.mapsvg-popover').length || $(e.target).hasClass('mapsvg-btn-map'))
                    return;
                this.popover && this.popover.close();
            },
            mouseOverHandler : function(e){

                if(_data.eventsPrevent['mouseover']){
                    return;
                }

                if(_data.options.tooltips.on){
                    var name, data;
                    if(this instanceof Region) {
                        name = 'tooltipRegion';
                        data = this.forTemplate();
                    }
                    if(this instanceof Marker) {
                        name = 'tooltipMarker';
                        data = this.object;
                    }
                    if(_data.popoverShowingFor !== this){
                        _this.showTooltip(_data.templates[name](data));
                    }
                }

                if(_data.options.menu.on){
                    if(_data.options.menu.source == 'database'){
                        if((this instanceof Region) && this.objects.length) {
                            var ids = this.objects.map(function(obj){
                                return obj.id;
                            });
                        }
                        if(this instanceof Marker) {
                            var ids = this.object.id;
                        }
                    }else{
                        if((this instanceof Region)) {
                            var ids = [this.id];
                        }
                        if(this instanceof Marker && this.object.regions && this.object.regions.length) {
                            var ids = this.object.regions.map(function(obj){
                                return obj.id;
                            });
                        }
                    }
                    _data.controllers.directory.highlightItems(ids);
                }

                if(this instanceof Region) {
                    if (!this.selected)
                        this.highlight();
                    if(_data.events['mouseover.region'])
                        _data.events['mouseover.region'].call(this, e, _this);
                }else{
                    if(_data.events['mouseover.marker'])
                        _data.events['mouseover.marker'].call(this, e, _this);

                }
            },
            mouseOutHandler : function(e){

                if(_data.eventsPrevent['mouseout']){
                    return;
                }

                if(_data.options.tooltips.on)
                    _this.hideTip();
                if(this instanceof Region) {
                    if (!this.selected)
                        this.unhighlight();
                    if(_data.events['mouseout.region'])
                        _data.events['mouseout.region'].call(this, e, _this);
                }else{
                    if(_data.events['mouseout.marker'])
                        _data.events['mouseout.marker'].call(this, e, _this);
                }
                if(_data.options.menu.on){
                    _data.controllers.directory.unhighlightItems();
                }

            },
            updateOptions : function(options){
                $.extend(true,_data.options,options);
            },
            updateMarkersDict : function(){
                _data.markersDict = {};
                _data.markers.forEach(function(marker, i){
                    _data.markersDict[marker.id] = i;
                });
            },
            eventsPrevent: function(event){
                _data.eventsPrevent[event] = true;
            },
            eventsRestore: function(event){
                if(event){
                    _data.eventsPrevent[event] = false;
                } else {
                    _data.eventsPrevent = {};
                }

            },
            setEventHandlers : function(){

                _data.$map.off('.common.mapsvg');
                _data.$scrollpane.off('.common.mapsvg');
                $(document).off('keydown.scroll.mapsvg');
                $(document).off('mousemove.scrollInit.mapsvg');
                $(document).off('mouseup.scrollInit.mapsvg');

                var event = MapSVG.touchDevice ? 'touchstart.common.mapsvg' : 'click.common.mapsvg';
                var eventEnd = MapSVG.touchDevice ? 'touchend.common.mapsvg' : 'mouseup.common.mapsvg';


                if(_data.editMarkers.on){

                    var event2 = MapSVG.touchDevice ? 'touchstart.common.mapsvg' : 'mousedown.common.mapsvg';
                    _data.$map.on(event2, '.mapsvg-marker',function(e){
                        e.originalEvent.preventDefault();
                        var marker = _this.getMarker($(this).attr('id'));
                        var startCoords = MapSVG.mouseCoords(e);
                        marker.drag(startCoords, _data.scale, function() {
                            if (_data.mapIsGeo){
                                this.geoCoords = _this.convertSVGToGeo(this.x + this.width / 2, this.y + (this.height-1));
                            }
                            _data.markerEditHandler && _data.markerEditHandler.call(this,true);
                            if(this.onChange)
                                this.onChange.call(this);
                        },function(){
                            _data.markerEditHandler && _data.markerEditHandler.call(this);
                            if(this.onChange)
                                this.onChange.call(this);
                        });
                    });
                }

                // REGIONS
                // if (!MapSVG.touchDevice) {
                    if(!_data.editMarkers.on) {
                        _data.$map.on('mouseover.common.mapsvg', '.mapsvg-region', function (e) {
                            var id = $(this).attr('id');
                            _this.mouseOverHandler.call(_this.getRegion(id), e, _this, options);
                        }).on('mouseleave.common.mapsvg', '.mapsvg-region', function (e) {
                            var id = $(this).attr('id');
                            _this.mouseOutHandler.call(_this.getRegion(id), e, _this, options);
                        });
                    }
                    if(!_data.editRegions.on){
                        _data.$map.on('mouseover.common.mapsvg', '.mapsvg-marker', function (e) {
                            var id = $(this).attr('id');
                            _this.mouseOverHandler.call(_this.getMarker(id), e, _this, options);
                        }).on('mouseleave.common.mapsvg', '.mapsvg-marker', function (e) {
                            var id = $(this).attr('id');
                            _this.mouseOutHandler.call(_this.getMarker(id), e, _this, options);
                        });
                    }
                // }

                if(_data.options.scroll.spacebar){
                    $(document).on('keydown.scroll.mapsvg', function(e) {
                        if(document.activeElement.tagName !=='INPUT' && !_data.isScrolling && e.keyCode == 32){
                            e.preventDefault();
                            _data.$map.addClass('mapsvg-scrollable');
                            $(document).on('mousemove.scrollInit.mapsvg', function(e) {
                                _data.isScrolling = true;
                                $(document).off('mousemove.scrollInit.mapsvg');
                                _this.scrollStart(e,_this);
                            }).on('keyup.scroll.mapsvg', function (e) {
                                if (e.keyCode == 32) {
                                    $(document).off('mousemove.scrollInit.mapsvg');
                                    _data.$map.removeClass('mapsvg-scrollable');
                                }
                            });
                        }
                    });
                }else if (!_data.options.scroll.on) {

                    if(!_data.editMarkers.on) {
                        // if(MapSVG.touchDevice){
                        //     _data.$map.on('touchstart.common.mapsvg', '.mapsvg-region', function (e) {
                        //         _data.touchScrollStart = $('body').scrollTop();
                        //     });
                        //     _data.$map.on('touchend.common.mapsvg', '.mapsvg-region', function (e) {
                        //         if(_data.touchScrollStart == $('body').scrollTop()){
                        //             _this.regionClickHandler.call(_this, e, _this.getRegion($(this).attr('id')));
                        //         }
                        //     });
                        //     _data.$map.on('touchstart.common.mapsvg', '.mapsvg-marker', function (e) {
                        //         _data.touchScrollStart = $('body').scrollTop();
                        //     });
                        //     _data.$map.on('touchend.common.mapsvg', '.mapsvg-marker', function (e) {
                        //         if(_data.touchScrollStart == $('body').scrollTop()){
                        //             _this.regionClickHandler.call(_this, e, _this.getMarker($(this).attr('id')));
                        //         }
                        //     });
                        // }else{
                            _data.$map.on('touchstart.common.mapsvg', '.mapsvg-region', function (e) {
                                _data.touchScrollStart = $('body').scrollTop();
                            });
                            _data.$map.on('touchstart.common.mapsvg', '.mapsvg-marker', function (e) {
                                _data.touchScrollStart = $('body').scrollTop();
                            });

                            _data.$map.on('touchend.common.mapsvg mouseup.common.mapsvg', '.mapsvg-region', function (e) {
                                // e.stopImmediatePropagation();
                                e.preventDefault();
                                if(!_data.touchScrollStart || _data.touchScrollStart == $('body').scrollTop()){
                                    _this.regionClickHandler.call(_this, e, _this.getRegion($(this).attr('id')));
                                }
                                // _this.regionClickHandler.call(_this, e, _this.getRegion($(this).attr('id')));
                            });
                            _data.$map.on('touchend.common.mapsvg mouseup.common.mapsvg','.mapsvg-marker',  function (e) {
                                // e.stopImmediatePropagation();
                                e.preventDefault();
                                if(!_data.touchScrollStart || _data.touchScrollStart == $('body').scrollTop()) {
                                    _this.regionClickHandler.call(_this, e, _this.getMarker($(this).attr('id')));
                                }
                            });
                            _data.$map.on('touchend.common.mapsvg mouseup.common.mapsvg','.mapsvg-marker-cluster',  function (e) {
                                e.preventDefault();
                                if(!_data.touchScrollStart || _data.touchScrollStart == $('body').scrollTop()) {
                                    var cluster = $(this).data("cluster");
                                    _this.zoomTo(cluster.markers);
                                }
                            });
                        // }
                    }else{

                        if(_data.clickAddsMarker)
                            _data.$map.on('touchend.common.mapsvg mouseup.common.mapsvg', function (e) {
                                // e.stopImmediatePropagation();
                                e.preventDefault();
                                _this.markerAddClickHandler(e);
                            });
                    }
                } else {

                    _data.$map.on('touchstart.common.mapsvg mousedown.common.mapsvg', function(e){

                        if($(e.target).hasClass('mapsvg-popover')||$(e.target).closest('.mapsvg-popover').length){
                            return;
                        }
                        // e.stopImmediatePropagation();

                        if(e.type=='touchstart'){
                            e.preventDefault();
                        }

                        if(e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-region')!=-1){
                            var obj = _this.getRegion($(e.target).attr('id'));
                            _this.scrollRegionClickHandler.call(_this, e, obj);
                        }else if(e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-marker')!=-1 && $(e.target).attr('class').indexOf('mapsvg-marker-cluster')===-1){
                            if(_data.editMarkers.on){
                                return;
                            }
                            var obj = _this.getMarker($(e.target).attr('id'));
                            _this.scrollRegionClickHandler.call(_this, e, obj);
                        }else if(e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-marker-cluster')!=-1){
                            if(_data.editMarkers.on){
                                return;
                            }
                            var obj = ($(e.target).data('cluster'));
                            _this.scrollRegionClickHandler.call(_this, e, obj);
                        }
                        if(e.type=='mousedown'){
                            _this.scrollStart(e,_this);
                        }else{
                            _this.touchStart(e,_this);
                        }
                    });

                }
            },
            setLabelsRegions: function(options){

                options = options || _data.options.labelsRegions;
                options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
                $.extend(true, _data.options, {labelsRegions: options});

                if(_data.options.labelsRegions.on){
                    _data.regions.forEach(function (region) {
                        if(!region.textLabel){
                            region.textLabel = jQuery('<div class="mapsvg-region-label" />')
                            _data.$scrollpane.append(region.textLabel);
                        }
                        try{
                            region.textLabel.html(_data.templates.labelRegion(region.forTemplate()));
                        }catch (err) {
                            console.error('MapSVG: Error in the "Region Label" template');
                        }

                    });
                    _this.labelsRegionsAdjustPosition();
                } else {
                    _data.regions.forEach(function (region) {
                        if(region.textLabel){
                            region.textLabel.remove();
                            region.textLabel = null;
                            delete region.textLabel;
                        }
                    });
                }
            },
            deleteLabelsMarkers: function(){
                _data.markers.forEach(function (marker) {
                    if(marker.textLabel){
                        marker.textLabel.remove();
                        marker.textLabel = null;
                        delete marker.textLabel;
                    }
                });
            },
            setLabelsMarkers: function(options){

                options = options || _data.options.labelsMarkers;
                options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
                $.extend(true, _data.options, {labelsMarkers: options});

                if(_data.options.labelsMarkers.on){
                    _data.markers.forEach(function (marker) {
                        if(!marker.textLabel){
                            marker.textLabel = jQuery('<div class="mapsvg-marker-label" data-object-id="'+marker.object.id+'"/>');
                            _data.$scrollpane.append(marker.textLabel);
                        }
                        try{
                            marker.textLabel.html(_data.templates.labelMarker(marker.object));
                        }catch (err) {
                            console.error('MapSVG: Error in the "Marker Label" template');
                        }
                    });
                    _this.markersAdjustPosition();
                } else {
                    _this.deleteLabelsMarkers();
                }
            },
            addLayer: function(name){
                _data.layers[name] = $('<div class="mapsvg-layer mapsvg-layer-'+name+'"></div>');
                _data.$layers.append(_data.layers[name]);
            },
            getDatabaseService: function(){
                return this.database;
            },
            regionAdd: function(svgObject){
                var region = new Region($(svgObject), _data.options, _data.regionID, _this);
                region.setStatus(1);
                _data.regions.push(region);
                _data.regions.sort(function(a,b){
                    return a.id == b.id ? 0 : +(a.id > b.id) || -1;
                });
                _data.regions.forEach(function(region, index){
                    _data.regionsDict[region.id] = index;
                });
                return region;
            },
            regionDelete: function(id){
                var index = _data.regionsDict[id];
                if(index !== undefined){
                    var r = _this.getRegion(id);
                    r.node && r.node.remove();
                    _data.regions.splice(index,1);
                    delete _data.regionsDict[id];
                }else{
                    if($('#'+id).length){
                        $('#'+id).remove();
                    }
                }
            },
            reloadRegions : function(){
                var _this = this;
                _data.regions = [];
                _data.regionsDict = {};
                _data.$svg.find('.mapsvg-region').removeClass('mapsvg-region');
                _data.$svg.find('.mapsvg-region-disabled').removeClass('mapsvg-region-disabled');
                _data.$svg.find('path, polygon, circle, ellipse, rect').each(function(index){
                    if($(this).closest('defs').length)
                        return;
                    if($(this)[0].getAttribute('id')) {
                        if(!_data.options.regionPrefix || (_data.options.regionPrefix && $(this)[0].getAttribute('id').indexOf(_data.options.regionPrefix)===0)){
                            var region = new Region($(this), _data.options, _data.regionID, _this);
                            _data.regions.push(region);
                        }
                    }
                    // if($(this).css('stroke-width')){
                    //     $(this).data('stroke-width', $(this).css('stroke-width').replace('px',''));
                    // }
                });
                _data.regions.sort(function(a,b){
                    return a.id == b.id ? 0 : +(a.id > b.id) || -1;
                });
                _data.regions.forEach(function(region, index){
                    _data.regionsDict[region.id] = index;
                });
            },
            reloadRegionsFull : function(){
                var statuses = _this.regionsDatabase.getSchemaFieldByType('status');
                _this.regionsDatabase.getLoaded().forEach(function(object){
                    var region = _this.getRegion(object.id);
                    if(region){
                        region.data = object;
                        if(statuses && object.status !== undefined && object.status!==null){
                            region.setStatus(object.status);
                            // _this.setRegionStatus(region, object.status);
                        }
                    }
                });
                _this.loadDirectory();
                _this.setGauge();
                _this.setLayersControl();
                _this.setGroups();
                if(_data.options.labelsRegions.on){
                    _this.setLabelsRegions();
                }
            },
            updateOutdatedOptions: function(options){

                // Fix Directory options
                if(options.menu && (options.menu.position || options.menu.customContainer)){
                    if(options.menu.customContainer){
                        options.menu.location = 'custom';
                    } else {
                        options.menu.position = options.menu.position === 'left' ? 'left' : 'right';
                        options.menu.location = options.menu.position ==='left' ? 'leftSidebar' : 'rightSidebar';
                        if(!options.containers || !options.containers[options.menu.location]){
                            options.containers = options.containers || {};
                            options.containers[options.menu.location] = {on: false, width: '200px'};
                        }
                        options.containers[options.menu.location].width = options.menu.width;
                        if(MapSVG.parseBoolean(options.menu.on)){
                            options.containers[options.menu.location].on = true;
                        }
                    }
                    delete options.menu.position;
                    delete options.menu.width;
                    delete options.menu.customContainer;
                }
                // Fix Details View options
                if(options.detailsView && (options.detailsView.location === 'near' || options.detailsView.location === 'top')){
                    options.detailsView.location = 'mapContainer';
                }

            },
            // INIT
            init: function(opts, elem) {

                if(!opts.source) {
                    throw new Error('MapSVG: please provide SVG file source.');
                    return false;
                }


                // cut domain to avoid cross-domain errors
                if(opts.source.indexOf('//')===0)
                    opts.source = opts.source.replace(/^\/\/[^\/]+/, '').replace('//','/');
                else
                    opts.source = opts.source.replace(/^.*:\/\/[^\/]+/, '').replace('//','/');

                /** Setting _data **/
                _data  = {};

                _data.editMode = opts.editMode;
                delete opts.editMode;

                _this.updateOutdatedOptions(opts);

                _data.options = $.extend(true, {}, defaults, opts);


                _this.id = _data.options.db_map_id;
                if(_this.id == 'new')
                    _this.id = null;
                _data.highlightedRegions = [];
                _data.editRegions = {on:false};
                _data.editMarkers = {on:false};
                _data.editData    = {on:false};
                _data.map  = elem;
                _data.$map = $(elem);
                _data.$scrollpane = $('<div class="mapsvg-scrollpane"></div>').appendTo(_data.$map);
                _data.$layers = $('<div class="mapsvg-layers-wrap"></div>').appendTo(_data.$scrollpane);

                _data.whRatio = 0;
                _data.isScrolling = false;
                _data.markerOptions = {};
                _data.svgDefault = {};
                _data.refLength = 0;
                _data.scale  = 1;         // absolute scale
                _data._scale = 1;         // relative scale starting from current zoom level
                _data.selected_id    = [];
                _data.mapData        = {};
                _data.regions        = [];
                _data.regionsDict    = {};
                _data.regionID       = {id: 0};
                _data.markers        = [];
                _data.markersDict    = {};
                _data.markersClusters = [];
                _data.markersClustersDict = [];
                _data._viewBox       = []; // initial viewBox
                _data.viewBox        = []; // current viewBox
                _data.viewBoxZoom    = [];
                _data.viewBoxFind    = undefined;
                _data.zoomLevel      = 0;
                _data.scroll         = {};
                _data.layers         = {};
                _data.geoCoordinates = false;
                _data.geoViewBox     = {leftLon:0, topLat:0, rightLon:0, bottomLat:0};
                _data.eventsPrevent  = {};
                _data.googleMaps     = {loaded: false, initialized: false, map: null};

                _this.setEvents(opts.events);

                // todo remove duplicate beforeload
                if(_data.events.beforeLoad)
                    try{_data.events.beforeLoad.call(_this);}catch(err){}

                if(_data.events.beforeLoad && _data.events.beforeLoad['beforeLoad'] && typeof _data.events.beforeLoad['beforeLoad'] === 'function'){
                    try{
                        _data.events.beforeLoad['beforeLoad'].call( _this)
                    }catch(err){}

                }


                _this.setCss();


                // Set background
                _data.$map.addClass('mapsvg').addClass('no-transitions').css('background',_data.options.colors.background);


                _this.setContainers(_data.options.containers);
                _this.setColors();


                // _data.$ratio = $('<div class="mapsvg-ratio"></div>');
                // _data.$ratio.insertBefore(_data.$map);
                // _data.$ratio.append(_data.$map);

                _data.$loading = $('<div>'+_data.options.loadingText+'</div>').addClass('mapsvg-loading');
                _data.$map.append(_data.$loading);

                // _data.$mapRatioSize = $('<div class="mapsvg-ratio"></div>').insertBefore(_data.$map);
                // _data.$map.appendTo(_data.$mapRatioSize);

                _this.addLayer('markers');
                _this.addLayer('popovers');

                _data.$loading.css({
                    'margin-left': function () {
                        return -($(this).outerWidth(false) / 2)+'px';
                    },
                    'margin-top': function () {
                        return -($(this).outerHeight(false) / 2)+'px';
                    }
                });
                if(_data.options.googleMaps.on){
                    _data.$map.addClass('mapsvg-google-map-loading');
                }

                // Load extension (common things)
                if(_data.options.extension && $().mapSvg.extensions && $().mapSvg.extensions[_data.options.extension]){
                    var ext = $().mapSvg.extensions[_data.options.extension];
                    ext && ext.common(_this);
                }


                // GET the map by ajax request
                $.ajax({url: _data.options.source+'?v='+_data.options.svgFileVersion}).fail(function(resp){
                    if(resp.status == 404){
                        alert('File not found: '+_data.options.source+'\n\nIf you moved MapSVG from another server please read the following tutorial:\nhttps://mapsvg.com/tutorials/4.0.x/6/');
                    } else {
                        alert('Can\'t load SVG file. Please contact support.');
                    }
                }).done(function(xmlData){

                        // Default width/height/viewBox from SVG
                        var svgTag               = $(xmlData).find('svg');
                        _data.$svg               = svgTag;

                        _data.svgDefault.width   = svgTag.attr('width');
                        _data.svgDefault.height  = svgTag.attr('height');
                        _data.svgDefault.viewBox = svgTag.attr('viewBox');

                        if(_data.svgDefault.width && _data.svgDefault.height){
                            _data.svgDefault.width   = parseFloat(_data.svgDefault.width.replace(/px/g,''));
                            _data.svgDefault.height  = parseFloat(_data.svgDefault.height.replace(/px/g,''));
                            _data.svgDefault.viewBox = _data.svgDefault.viewBox ? _data.svgDefault.viewBox.split(' ') : [0,0, _data.svgDefault.width, _data.svgDefault.height];
                        }else if(_data.svgDefault.viewBox){
                            _data.svgDefault.viewBox = _data.svgDefault.viewBox.split(' ');
                            _data.svgDefault.width   = parseFloat(_data.svgDefault.viewBox[2]);
                            _data.svgDefault.height  = parseFloat(_data.svgDefault.viewBox[3]);
                        }else{
                            alert('MapSVG needs width/height or viewBox parameter to be present in SVG file.')
                            return false;
                        }
                        // Get geo-coordinates view  box from SVG file
                        var geo               = svgTag.attr("mapsvg:geoViewBox") || svgTag.attr("mapsvg:geoviewbox");
                        if (geo) {
                            geo = geo.split(" ");
                            if (geo.length == 4){
                                _data.mapIsGeo = true;
                                _data.geoCoordinates = true;

                                _data.geoViewBox = {leftLon: parseFloat(geo[0]),
                                    topLat: parseFloat(geo[1]),
                                    rightLon: parseFloat(geo[2]),
                                    bottomLat: parseFloat(geo[3])
                                };
                                _data.mapLonDelta = _data.geoViewBox.rightLon - _data.geoViewBox.leftLon;
                                _data.mapLatBottomDegree = _data.geoViewBox.bottomLat * 3.14159 / 180;

                            }

                        }

                        $.each(_data.svgDefault.viewBox, function(i,v){
                            _data.svgDefault.viewBox[i] = parseFloat(v);
                        });

                        _data._viewBox  = (_data.options.viewBox.length==4 && _data.options.viewBox ) || _data.svgDefault.viewBox;

                        $.each(_data._viewBox, function(i,v){
                            _data._viewBox[i] = parseFloat(v);
                        });

                        svgTag.attr('preserveAspectRatio','xMidYMid meet');
                        svgTag.removeAttr('width');
                        svgTag.removeAttr('height');

                        //// Adding moving sticky draggable image on background
                        //if(_data.options.scrollBackground)
                        //    _data.background = _data.R.rect(_data.svgDefault.viewBox[0],_data.svgDefault.viewBox[1],_data.svgDefault.viewBox[2],_data.svgDefault.viewBox[3]).attr({fill: _data.options.colors.background});

                        _this.reloadRegions();

                        _data.$scrollpane.append(svgTag);


                        // Set size
                        _this.setSize(_data.options.width, _data.options.height, _data.options.responsive);


                        if(_data.options.disableAll){
                            _this.setDisableAll(true);
                        }



                        // Set viewBox
                        _this.setViewBox(_data._viewBox);
                        _this.setResponsive(_data.options.responsive,true);


                        // var markers = _data.options.markers || _data.options.marks || [];
                        // _this.setMarkers(markers);

                        _this.setScroll(_data.options.scroll, true);

                        _this.setZoom(_data.options.zoom);
                        _this.setGoogleMaps();

                        // _this.setViewBox([0,0,_data.svgDefault.viewBox[0]*2+_data.svgDefault.viewBox[2],_data.svgDefault.viewBox[1]*2+_data.svgDefault.viewBox[3]]);


                        // Set tooltips
                        // tooltipsMode is deprecated, need this for backward compatibility
                        if (_data.options.tooltipsMode)
                            _data.options.tooltips.mode = _data.options.tooltipsMode;
                        _this.setTooltips(_data.options.tooltips);

                        // Set popovers
                        // popover is deprecated (now it's popoverS), need this for backward compatibility
                        if (_data.options.popover)
                            _data.options.popovers = _data.options.popover;
                        _this.setPopovers(_data.options.popovers);

                        if(_data.options.cursor)
                            _this.setCursor(_data.options.cursor);

                        _this.setTemplates(_data.options.templates);


                        // Load extension (frontend things)
                        if(!_data.options.backend && _data.options.extension &&  $().mapSvg.extensions &&  $().mapSvg.extensions[_data.options.extension]){
                            var ext = $().mapSvg.extensions[_data.options.extension];
                            ext && ext.frontend(_this);
                        }

                        _this.filtersSchema = new MapSVG.Filters(_data.options.filtersSchema);

                        // Load data from Database and finish loading
                        _this.database = new MapSVG.DatabaseService({
                            map_id    : _this.id,
                            perpage   : _data.options.database.pagination.on ? _data.options.database.pagination.perpage : 0,
                            sortBy    : _data.options.menu.source == 'database' ? _data.options.menu.sortBy : 'id',
                            sortDir   : _data.options.menu.source == 'database' ?_data.options.menu.sortDirection : 'desc',
                            table     : 'database'
                        }, _this);
                        // _data.events['databaseLoaded'] && _this.database.on('dataLoaded', _data.events['databaseLoaded']);

                        _data.firstDataLoad = true;
                        _this.database.on('dataLoaded', function(){
                            if(_data.firstDataLoad){
                                _this.setMarkerImagesDependency();
                            }
                            _this.addLocations();
                            // _this.addDataObjectsAsMarkers();
                            _this.attachDataToRegions();
                            _this.loadDirectory();
                            if(_data.options.labelsMarkers.on){
                                _this.setLabelsMarkers();
                            }
                            if(!_data.firstDataLoad && _data.options.fitMarkers){

                                _this.fitMarkers();
                            }
                            _data.firstDataLoad = false;
                            _data.events['databaseLoaded'] && _data.events['databaseLoaded'].call(_this);
                        });
                        _this.database.on('schemaChange', function () {
                            // _this.setMarkersClickAsLink();
                            _this.setMarkerImagesDependency();
                            _this.database.getAll();
                        });
                        _this.database.on('update', function(obj){
                            _this.attachDataToRegions(obj);
                        });
                        _this.database.on('create', function(obj){
                            _this.attachDataToRegions(obj);
                        });
                        _this.database.on('delete', function(){
                            _this.attachDataToRegions();
                        });

                        _this.regionsDatabase = new MapSVG.DatabaseService({
                            map_id    : _this.id,
                            perpage   :  0,
                            sortBy    : _data.options.menu.source == 'regions' ? _data.options.menu.sortBy : 'id',
                            sortDir   : _data.options.menu.source == 'regions' ? _data.options.menu.sortDirection : 'desc',
                            table     : 'regions'
                        }, _this);
                        // _data.events['regionsLoaded'] && _this.regionsDatabase.on('dataLoaded', _data.events['regionsLoaded']);
                        _this.regionsDatabase.on('dataLoaded', function(){
                            _this.reloadRegionsFull();
                            _this.loadDirectory();
                            if(_data.events['regionsLoaded']){
                                if(_data.events['regionsLoaded'].length && _data.events['regionsLoaded'].length > 0){
                                    _this.trigger('regionsLoaded', _this);
                                } else {
                                    _data.events['regionsLoaded'].call(_this);
                                }
                            }
                        });

                        _data.menuDatabase = _data.options.menu.source == 'regions' ? _this.regionsDatabase : _this.database;
                        _this.setMenu();
                        _this.setFilters();

                        if(_data.options.menu.filterout.field){
                            var f = {};
                            f[_data.options.menu.filterout.field] = _data.options.menu.filterout.val;
                            if(_data.options.menu.source == 'regions'){
                                // _this.regionsDatabase.query.setFilterOut(f);
                            }else{
                                _this.database.query.setFilterOut(f);
                            }
                        }

                        _this.setEventHandlers();

                        if(!_this.id){
                            _this.final();
                            return;
                        }

                        _this.regionsDatabase.getAll().done(function(regions){
                            if(_data.options.database.loadOnStart || _data.editMode){
                                _this.database.getAll().done(function (data) {
                                    _this.final();
                                });
                            } else {
                                _this.final();
                            }
                        });
                }); // end of SVG LOAD AJAX

                return _this;

            }, // end of init
            final: function(){
                // Select region from URL
                if( match = RegExp('[?&]mapsvg_select=([^&]*)').exec(window.location.search)){
                    var select = decodeURIComponent(match[1].replace(/\+/g, ' '));
                    _this.selectRegion(select);
                }
                if(window.location.hash){
                    var query = window.location.hash.replace('#!','');
                    var region = _this.getRegion(query);
                    if(region && _data.options.actions.map.afterLoad.selectRegion){
                        _this.regionClickHandler(null, region);
                        // mapsvg.selectRegion(id);
                        // check actions, do them
                        // mapsvg.loadDetailsView(region);
                        // if menu is on, select menu items
                        // mapsvg.getData().controllers.directory.selectItems(id);
                    } else {
                        // pass page number to #! like this: #!{query:{},
                    }
                }

                setTimeout(function(){
                    _this.updateSize();
                    setTimeout(function() {
                        _data.$map.removeClass('no-transitions');
                    },200);
                },100);

                if(_data.events['afterLoad'] && typeof _data.events['afterLoad'] === 'function'){
                    try{
                        _data.events['afterLoad'].call(_this);
                    }catch (err){
                        console.log(err);
                    }

                }
                // _this.trigger('afterLoad');
                if (_data.options.afterLoad)
                    _data.options.afterLoad.call(_this);
                if (_data.options.dataLoaded)
                    _data.options.dataLoaded.call();

                _data.$loading.hide();
            }

        }; // end of methods

        var _this = this.methods;



    }; // end of mapSVG class


    /** $.FN **/
    $.fn.mapSvg = function( opts ) {

        var id = $(this).attr('id');

        if(typeof opts == 'object' && instances[id] === undefined){
            instances[id] = new mapSVG(this, opts);
            return instances[id].methods.init(opts, this);
        }else if(instances[id]){
            return instances[id].methods;
        }else{
            return instances;
        }



    }; // end of $.fn.mapSvg

})( jQuery );

// Tiny color
// MapSVG.tinycolor v1.3.0
// https://github.com/bgrins/TinyColor
// Brian Grinstead, MIT License

(function(Math) {

    var trimLeft = /^\s+/,
        trimRight = /\s+$/,
        tinyCounter = 0,
        mathRound = Math.round,
        mathMin = Math.min,
        mathMax = Math.max,
        mathRandom = Math.random;

    function tinycolor (color, opts) {

        color = (color) ? color : '';
        opts = opts || { };

        // If input is already a tinycolor, return itself
        if (color instanceof tinycolor) {
            return color;
        }
        // If we are called as a function, call using new instead
        if (!(this instanceof tinycolor)) {
            return new tinycolor(color, opts);
        }

        var rgb = inputToRGB(color);
        this._originalInput = color,
            this._r = rgb.r,
            this._g = rgb.g,
            this._b = rgb.b,
            this._a = rgb.a,
            this._roundA = mathRound(100*this._a) / 100,
            this._format = opts.format || rgb.format;
        this._gradientType = opts.gradientType;

        // Don't let the range of [0,255] come back in [0,1].
        // Potentially lose a little bit of precision here, but will fix issues where
        // .5 gets interpreted as half of the total, instead of half of 1
        // If it was supposed to be 128, this was already taken care of by `inputToRgb`
        if (this._r < 1) { this._r = mathRound(this._r); }
        if (this._g < 1) { this._g = mathRound(this._g); }
        if (this._b < 1) { this._b = mathRound(this._b); }

        this._ok = rgb.ok;
        this._tc_id = tinyCounter++;
    }

    tinycolor.prototype = {
        isDark: function() {
            return this.getBrightness() < 128;
        },
        isLight: function() {
            return !this.isDark();
        },
        isValid: function() {
            return this._ok;
        },
        getOriginalInput: function() {
            return this._originalInput;
        },
        getFormat: function() {
            return this._format;
        },
        getAlpha: function() {
            return this._a;
        },
        getBrightness: function() {
            //http://www.w3.org/TR/AERT#color-contrast
            var rgb = this.toRgb();
            return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
        },
        getLuminance: function() {
            //http://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef
            var rgb = this.toRgb();
            var RsRGB, GsRGB, BsRGB, R, G, B;
            RsRGB = rgb.r/255;
            GsRGB = rgb.g/255;
            BsRGB = rgb.b/255;

            if (RsRGB <= 0.03928) {R = RsRGB / 12.92;} else {R = Math.pow(((RsRGB + 0.055) / 1.055), 2.4);}
            if (GsRGB <= 0.03928) {G = GsRGB / 12.92;} else {G = Math.pow(((GsRGB + 0.055) / 1.055), 2.4);}
            if (BsRGB <= 0.03928) {B = BsRGB / 12.92;} else {B = Math.pow(((BsRGB + 0.055) / 1.055), 2.4);}
            return (0.2126 * R) + (0.7152 * G) + (0.0722 * B);
        },
        setAlpha: function(value) {
            this._a = boundAlpha(value);
            this._roundA = mathRound(100*this._a) / 100;
            return this;
        },
        toHsv: function() {
            var hsv = rgbToHsv(this._r, this._g, this._b);
            return { h: hsv.h * 360, s: hsv.s, v: hsv.v, a: this._a };
        },
        toHsvString: function() {
            var hsv = rgbToHsv(this._r, this._g, this._b);
            var h = mathRound(hsv.h * 360), s = mathRound(hsv.s * 100), v = mathRound(hsv.v * 100);
            return (this._a == 1) ?
            "hsv("  + h + ", " + s + "%, " + v + "%)" :
            "hsva(" + h + ", " + s + "%, " + v + "%, "+ this._roundA + ")";
        },
        toHsl: function() {
            var hsl = rgbToHsl(this._r, this._g, this._b);
            return { h: hsl.h * 360, s: hsl.s, l: hsl.l, a: this._a };
        },
        toHslString: function() {
            var hsl = rgbToHsl(this._r, this._g, this._b);
            var h = mathRound(hsl.h * 360), s = mathRound(hsl.s * 100), l = mathRound(hsl.l * 100);
            return (this._a == 1) ?
            "hsl("  + h + ", " + s + "%, " + l + "%)" :
            "hsla(" + h + ", " + s + "%, " + l + "%, "+ this._roundA + ")";
        },
        toHex: function(allow3Char) {
            return rgbToHex(this._r, this._g, this._b, allow3Char);
        },
        toHexString: function(allow3Char) {
            return '#' + this.toHex(allow3Char);
        },
        toHex8: function() {
            return rgbaToHex(this._r, this._g, this._b, this._a);
        },
        toHex8String: function() {
            return '#' + this.toHex8();
        },
        toRgb: function() {
            return { r: mathRound(this._r), g: mathRound(this._g), b: mathRound(this._b), a: this._a };
        },
        toRgbString: function() {
            return (this._a == 1) ?
            "rgb("  + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ")" :
            "rgba(" + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ", " + this._roundA + ")";
        },
        toPercentageRgb: function() {
            return { r: mathRound(bound01(this._r, 255) * 100) + "%", g: mathRound(bound01(this._g, 255) * 100) + "%", b: mathRound(bound01(this._b, 255) * 100) + "%", a: this._a };
        },
        toPercentageRgbString: function() {
            return (this._a == 1) ?
            "rgb("  + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%)" :
            "rgba(" + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%, " + this._roundA + ")";
        },
        toName: function() {
            if (this._a === 0) {
                return "transparent";
            }

            if (this._a < 1) {
                return false;
            }

            return hexNames[rgbToHex(this._r, this._g, this._b, true)] || false;
        },
        toFilter: function(secondColor) {
            var hex8String = '#' + rgbaToHex(this._r, this._g, this._b, this._a);
            var secondHex8String = hex8String;
            var gradientType = this._gradientType ? "GradientType = 1, " : "";

            if (secondColor) {
                var s = tinycolor(secondColor);
                secondHex8String = s.toHex8String();
            }

            return "progid:DXImageTransform.Microsoft.gradient("+gradientType+"startColorstr="+hex8String+",endColorstr="+secondHex8String+")";
        },
        toString: function(format) {
            var formatSet = !!format;
            format = format || this._format;

            var formattedString = false;
            var hasAlpha = this._a < 1 && this._a >= 0;
            var needsAlphaFormat = !formatSet && hasAlpha && (format === "hex" || format === "hex6" || format === "hex3" || format === "name");

            if (needsAlphaFormat) {
                // Special case for "transparent", all other non-alpha formats
                // will return rgba when there is transparency.
                if (format === "name" && this._a === 0) {
                    return this.toName();
                }
                return this.toRgbString();
            }
            if (format === "rgb") {
                formattedString = this.toRgbString();
            }
            if (format === "prgb") {
                formattedString = this.toPercentageRgbString();
            }
            if (format === "hex" || format === "hex6") {
                formattedString = this.toHexString();
            }
            if (format === "hex3") {
                formattedString = this.toHexString(true);
            }
            if (format === "hex8") {
                formattedString = this.toHex8String();
            }
            if (format === "name") {
                formattedString = this.toName();
            }
            if (format === "hsl") {
                formattedString = this.toHslString();
            }
            if (format === "hsv") {
                formattedString = this.toHsvString();
            }

            return formattedString || this.toHexString();
        },
        clone: function() {
            return tinycolor(this.toString());
        },

        _applyModification: function(fn, args) {
            var color = fn.apply(null, [this].concat([].slice.call(args)));
            this._r = color._r;
            this._g = color._g;
            this._b = color._b;
            this.setAlpha(color._a);
            return this;
        },
        lighten: function() {
            return this._applyModification(lighten, arguments);
        },
        brighten: function() {
            return this._applyModification(brighten, arguments);
        },
        darken: function() {
            return this._applyModification(darken, arguments);
        },
        desaturate: function() {
            return this._applyModification(desaturate, arguments);
        },
        saturate: function() {
            return this._applyModification(saturate, arguments);
        },
        greyscale: function() {
            return this._applyModification(greyscale, arguments);
        },
        spin: function() {
            return this._applyModification(spin, arguments);
        },

        _applyCombination: function(fn, args) {
            return fn.apply(null, [this].concat([].slice.call(args)));
        },
        analogous: function() {
            return this._applyCombination(analogous, arguments);
        },
        complement: function() {
            return this._applyCombination(complement, arguments);
        },
        monochromatic: function() {
            return this._applyCombination(monochromatic, arguments);
        },
        splitcomplement: function() {
            return this._applyCombination(splitcomplement, arguments);
        },
        triad: function() {
            return this._applyCombination(triad, arguments);
        },
        tetrad: function() {
            return this._applyCombination(tetrad, arguments);
        }
    };

// If input is an object, force 1 into "1.0" to handle ratios properly
// String input requires "1.0" as input, so 1 will be treated as 1
    tinycolor.fromRatio = function(color, opts) {
        if (typeof color == "object") {
            var newColor = {};
            for (var i in color) {
                if (color.hasOwnProperty(i)) {
                    if (i === "a") {
                        newColor[i] = color[i];
                    }
                    else {
                        newColor[i] = convertToPercentage(color[i]);
                    }
                }
            }
            color = newColor;
        }

        return tinycolor(color, opts);
    };

// Given a string or object, convert that input to RGB
// Possible string inputs:
//
//     "red"
//     "#f00" or "f00"
//     "#ff0000" or "ff0000"
//     "#ff000000" or "ff000000"
//     "rgb 255 0 0" or "rgb (255, 0, 0)"
//     "rgb 1.0 0 0" or "rgb (1, 0, 0)"
//     "rgba (255, 0, 0, 1)" or "rgba 255, 0, 0, 1"
//     "rgba (1.0, 0, 0, 1)" or "rgba 1.0, 0, 0, 1"
//     "hsl(0, 100%, 50%)" or "hsl 0 100% 50%"
//     "hsla(0, 100%, 50%, 1)" or "hsla 0 100% 50%, 1"
//     "hsv(0, 100%, 100%)" or "hsv 0 100% 100%"
//
    function inputToRGB(color) {

        var rgb = { r: 0, g: 0, b: 0 };
        var a = 1;
        var ok = false;
        var format = false;

        if (typeof color == "string") {
            color = stringInputToObject(color);
        }

        if (typeof color == "object") {
            if (isValidCSSUnit(color.r) && isValidCSSUnit(color.g) && isValidCSSUnit(color.b)) {
                rgb = rgbToRgb(color.r, color.g, color.b);
                ok = true;
                format = String(color.r).substr(-1) === "%" ? "prgb" : "rgb";
            }
            else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.v)) {
                color.s = convertToPercentage(color.s);
                color.v = convertToPercentage(color.v);
                rgb = hsvToRgb(color.h, color.s, color.v);
                ok = true;
                format = "hsv";
            }
            else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.l)) {
                color.s = convertToPercentage(color.s);
                color.l = convertToPercentage(color.l);
                rgb = hslToRgb(color.h, color.s, color.l);
                ok = true;
                format = "hsl";
            }

            if (color.hasOwnProperty("a")) {
                a = color.a;
            }
        }

        a = boundAlpha(a);

        return {
            ok: ok,
            format: color.format || format,
            r: mathMin(255, mathMax(rgb.r, 0)),
            g: mathMin(255, mathMax(rgb.g, 0)),
            b: mathMin(255, mathMax(rgb.b, 0)),
            a: a
        };
    }


// Conversion Functions
// --------------------

// `rgbToHsl`, `rgbToHsv`, `hslToRgb`, `hsvToRgb` modified from:
// <http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript>

// `rgbToRgb`
// Handle bounds / percentage checking to conform to CSS color spec
// <http://www.w3.org/TR/css3-color/>
// *Assumes:* r, g, b in [0, 255] or [0, 1]
// *Returns:* { r, g, b } in [0, 255]
    function rgbToRgb(r, g, b){
        return {
            r: bound01(r, 255) * 255,
            g: bound01(g, 255) * 255,
            b: bound01(b, 255) * 255
        };
    }

// `rgbToHsl`
// Converts an RGB color value to HSL.
// *Assumes:* r, g, and b are contained in [0, 255] or [0, 1]
// *Returns:* { h, s, l } in [0,1]
    function rgbToHsl(r, g, b) {

        r = bound01(r, 255);
        g = bound01(g, 255);
        b = bound01(b, 255);

        var max = mathMax(r, g, b), min = mathMin(r, g, b);
        var h, s, l = (max + min) / 2;

        if(max == min) {
            h = s = 0; // achromatic
        }
        else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch(max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }

            h /= 6;
        }

        return { h: h, s: s, l: l };
    }

// `hslToRgb`
// Converts an HSL color value to RGB.
// *Assumes:* h is contained in [0, 1] or [0, 360] and s and l are contained [0, 1] or [0, 100]
// *Returns:* { r, g, b } in the set [0, 255]
    function hslToRgb(h, s, l) {
        var r, g, b;

        h = bound01(h, 360);
        s = bound01(s, 100);
        l = bound01(l, 100);

        function hue2rgb(p, q, t) {
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        if(s === 0) {
            r = g = b = l; // achromatic
        }
        else {
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }

        return { r: r * 255, g: g * 255, b: b * 255 };
    }

// `rgbToHsv`
// Converts an RGB color value to HSV
// *Assumes:* r, g, and b are contained in the set [0, 255] or [0, 1]
// *Returns:* { h, s, v } in [0,1]
    function rgbToHsv(r, g, b) {

        r = bound01(r, 255);
        g = bound01(g, 255);
        b = bound01(b, 255);

        var max = mathMax(r, g, b), min = mathMin(r, g, b);
        var h, s, v = max;

        var d = max - min;
        s = max === 0 ? 0 : d / max;

        if(max == min) {
            h = 0; // achromatic
        }
        else {
            switch(max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return { h: h, s: s, v: v };
    }

// `hsvToRgb`
// Converts an HSV color value to RGB.
// *Assumes:* h is contained in [0, 1] or [0, 360] and s and v are contained in [0, 1] or [0, 100]
// *Returns:* { r, g, b } in the set [0, 255]
    function hsvToRgb(h, s, v) {

        h = bound01(h, 360) * 6;
        s = bound01(s, 100);
        v = bound01(v, 100);

        var i = Math.floor(h),
            f = h - i,
            p = v * (1 - s),
            q = v * (1 - f * s),
            t = v * (1 - (1 - f) * s),
            mod = i % 6,
            r = [v, q, p, p, t, v][mod],
            g = [t, v, v, q, p, p][mod],
            b = [p, p, t, v, v, q][mod];

        return { r: r * 255, g: g * 255, b: b * 255 };
    }

// `rgbToHex`
// Converts an RGB color to hex
// Assumes r, g, and b are contained in the set [0, 255]
// Returns a 3 or 6 character hex
    function rgbToHex(r, g, b, allow3Char) {

        var hex = [
            pad2(mathRound(r).toString(16)),
            pad2(mathRound(g).toString(16)),
            pad2(mathRound(b).toString(16))
        ];

        // Return a 3 character hex if possible
        if (allow3Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1)) {
            return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0);
        }

        return hex.join("");
    }

// `rgbaToHex`
// Converts an RGBA color plus alpha transparency to hex
// Assumes r, g, b and a are contained in the set [0, 255]
// Returns an 8 character hex
    function rgbaToHex(r, g, b, a) {

        var hex = [
            pad2(convertDecimalToHex(a)),
            pad2(mathRound(r).toString(16)),
            pad2(mathRound(g).toString(16)),
            pad2(mathRound(b).toString(16))
        ];

        return hex.join("");
    }

// `equals`
// Can be called with any tinycolor input
    tinycolor.equals = function (color1, color2) {
        if (!color1 || !color2) { return false; }
        return tinycolor(color1).toRgbString() == tinycolor(color2).toRgbString();
    };

    tinycolor.random = function() {
        return tinycolor.fromRatio({
            r: mathRandom(),
            g: mathRandom(),
            b: mathRandom()
        });
    };


// Modification Functions
// ----------------------
// Thanks to less.js for some of the basics here
// <https://github.com/cloudhead/less.js/blob/master/lib/less/functions.js>

    function desaturate(color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.s -= amount / 100;
        hsl.s = clamp01(hsl.s);
        return tinycolor(hsl);
    }

    function saturate(color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.s += amount / 100;
        hsl.s = clamp01(hsl.s);
        return tinycolor(hsl);
    }

    function greyscale(color) {
        return tinycolor(color).desaturate(100);
    }

    function lighten (color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.l += amount / 100;
        hsl.l = clamp01(hsl.l);
        return tinycolor(hsl);
    }

    function brighten(color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var rgb = tinycolor(color).toRgb();
        rgb.r = mathMax(0, mathMin(255, rgb.r - mathRound(255 * - (amount / 100))));
        rgb.g = mathMax(0, mathMin(255, rgb.g - mathRound(255 * - (amount / 100))));
        rgb.b = mathMax(0, mathMin(255, rgb.b - mathRound(255 * - (amount / 100))));
        return tinycolor(rgb);
    }

    function darken (color, amount) {
        amount = (amount === 0) ? 0 : (amount || 10);
        var hsl = tinycolor(color).toHsl();
        hsl.l -= amount / 100;
        hsl.l = clamp01(hsl.l);
        return tinycolor(hsl);
    }

// Spin takes a positive or negative amount within [-360, 360] indicating the change of hue.
// Values outside of this range will be wrapped into this range.
    function spin(color, amount) {
        var hsl = tinycolor(color).toHsl();
        var hue = (hsl.h + amount) % 360;
        hsl.h = hue < 0 ? 360 + hue : hue;
        return tinycolor(hsl);
    }

// Combination Functions
// ---------------------
// Thanks to jQuery xColor for some of the ideas behind these
// <https://github.com/infusion/jQuery-xcolor/blob/master/jquery.xcolor.js>

    function complement(color) {
        var hsl = tinycolor(color).toHsl();
        hsl.h = (hsl.h + 180) % 360;
        return tinycolor(hsl);
    }

    function triad(color) {
        var hsl = tinycolor(color).toHsl();
        var h = hsl.h;
        return [
            tinycolor(color),
            tinycolor({ h: (h + 120) % 360, s: hsl.s, l: hsl.l }),
            tinycolor({ h: (h + 240) % 360, s: hsl.s, l: hsl.l })
        ];
    }

    function tetrad(color) {
        var hsl = tinycolor(color).toHsl();
        var h = hsl.h;
        return [
            tinycolor(color),
            tinycolor({ h: (h + 90) % 360, s: hsl.s, l: hsl.l }),
            tinycolor({ h: (h + 180) % 360, s: hsl.s, l: hsl.l }),
            tinycolor({ h: (h + 270) % 360, s: hsl.s, l: hsl.l })
        ];
    }

    function splitcomplement(color) {
        var hsl = tinycolor(color).toHsl();
        var h = hsl.h;
        return [
            tinycolor(color),
            tinycolor({ h: (h + 72) % 360, s: hsl.s, l: hsl.l}),
            tinycolor({ h: (h + 216) % 360, s: hsl.s, l: hsl.l})
        ];
    }

    function analogous(color, results, slices) {
        results = results || 6;
        slices = slices || 30;

        var hsl = tinycolor(color).toHsl();
        var part = 360 / slices;
        var ret = [tinycolor(color)];

        for (hsl.h = ((hsl.h - (part * results >> 1)) + 720) % 360; --results; ) {
            hsl.h = (hsl.h + part) % 360;
            ret.push(tinycolor(hsl));
        }
        return ret;
    }

    function monochromatic(color, results) {
        results = results || 6;
        var hsv = tinycolor(color).toHsv();
        var h = hsv.h, s = hsv.s, v = hsv.v;
        var ret = [];
        var modification = 1 / results;

        while (results--) {
            ret.push(tinycolor({ h: h, s: s, v: v}));
            v = (v + modification) % 1;
        }

        return ret;
    }

// Utility Functions
// ---------------------

    tinycolor.mix = function(color1, color2, amount) {
        amount = (amount === 0) ? 0 : (amount || 50);

        var rgb1 = tinycolor(color1).toRgb();
        var rgb2 = tinycolor(color2).toRgb();

        var p = amount / 100;
        var w = p * 2 - 1;
        var a = rgb2.a - rgb1.a;

        var w1;

        if (w * a == -1) {
            w1 = w;
        } else {
            w1 = (w + a) / (1 + w * a);
        }

        w1 = (w1 + 1) / 2;

        var w2 = 1 - w1;

        var rgba = {
            r: rgb2.r * w1 + rgb1.r * w2,
            g: rgb2.g * w1 + rgb1.g * w2,
            b: rgb2.b * w1 + rgb1.b * w2,
            a: rgb2.a * p  + rgb1.a * (1 - p)
        };

        return tinycolor(rgba);
    };


// Readability Functions
// ---------------------
// <http://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef (WCAG Version 2)

// `contrast`
// Analyze the 2 colors and returns the color contrast defined by (WCAG Version 2)
    tinycolor.readability = function(color1, color2) {
        var c1 = tinycolor(color1);
        var c2 = tinycolor(color2);
        return (Math.max(c1.getLuminance(),c2.getLuminance())+0.05) / (Math.min(c1.getLuminance(),c2.getLuminance())+0.05);
    };

// `isReadable`
// Ensure that foreground and background color combinations meet WCAG2 guidelines.
// The third argument is an optional Object.
//      the 'level' property states 'AA' or 'AAA' - if missing or invalid, it defaults to 'AA';
//      the 'size' property states 'large' or 'small' - if missing or invalid, it defaults to 'small'.
// If the entire object is absent, isReadable defaults to {level:"AA",size:"small"}.

// *Example*
//    tinycolor.isReadable("#000", "#111") => false
//    tinycolor.isReadable("#000", "#111",{level:"AA",size:"large"}) => false
    tinycolor.isReadable = function(color1, color2, wcag2) {
        var readability = tinycolor.readability(color1, color2);
        var wcag2Parms, out;

        out = false;

        wcag2Parms = validateWCAG2Parms(wcag2);
        switch (wcag2Parms.level + wcag2Parms.size) {
            case "AAsmall":
            case "AAAlarge":
                out = readability >= 4.5;
                break;
            case "AAlarge":
                out = readability >= 3;
                break;
            case "AAAsmall":
                out = readability >= 7;
                break;
        }
        return out;

    };

// `mostReadable`
// Given a base color and a list of possible foreground or background
// colors for that base, returns the most readable color.
// Optionally returns Black or White if the most readable color is unreadable.
// *Example*
//    tinycolor.mostReadable(tinycolor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:false}).toHexString(); // "#112255"
//    tinycolor.mostReadable(tinycolor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:true}).toHexString();  // "#ffffff"
//    tinycolor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"large"}).toHexString(); // "#faf3f3"
//    tinycolor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"small"}).toHexString(); // "#ffffff"
    tinycolor.mostReadable = function(baseColor, colorList, args) {
        var bestColor = null;
        var bestScore = 0;
        var readability;
        var includeFallbackColors, level, size ;
        args = args || {};
        includeFallbackColors = args.includeFallbackColors ;
        level = args.level;
        size = args.size;

        for (var i= 0; i < colorList.length ; i++) {
            readability = tinycolor.readability(baseColor, colorList[i]);
            if (readability > bestScore) {
                bestScore = readability;
                bestColor = tinycolor(colorList[i]);
            }
        }

        if (tinycolor.isReadable(baseColor, bestColor, {"level":level,"size":size}) || !includeFallbackColors) {
            return bestColor;
        }
        else {
            args.includeFallbackColors=false;
            return tinycolor.mostReadable(baseColor,["#fff", "#000"],args);
        }
    };


// Big List of Colors
// ------------------
// <http://www.w3.org/TR/css3-color/#svg-color>
    var names = tinycolor.names = {
        aliceblue: "f0f8ff",
        antiquewhite: "faebd7",
        aqua: "0ff",
        aquamarine: "7fffd4",
        azure: "f0ffff",
        beige: "f5f5dc",
        bisque: "ffe4c4",
        black: "000",
        blanchedalmond: "ffebcd",
        blue: "00f",
        blueviolet: "8a2be2",
        brown: "a52a2a",
        burlywood: "deb887",
        burntsienna: "ea7e5d",
        cadetblue: "5f9ea0",
        chartreuse: "7fff00",
        chocolate: "d2691e",
        coral: "ff7f50",
        cornflowerblue: "6495ed",
        cornsilk: "fff8dc",
        crimson: "dc143c",
        cyan: "0ff",
        darkblue: "00008b",
        darkcyan: "008b8b",
        darkgoldenrod: "b8860b",
        darkgray: "a9a9a9",
        darkgreen: "006400",
        darkgrey: "a9a9a9",
        darkkhaki: "bdb76b",
        darkmagenta: "8b008b",
        darkolivegreen: "556b2f",
        darkorange: "ff8c00",
        darkorchid: "9932cc",
        darkred: "8b0000",
        darksalmon: "e9967a",
        darkseagreen: "8fbc8f",
        darkslateblue: "483d8b",
        darkslategray: "2f4f4f",
        darkslategrey: "2f4f4f",
        darkturquoise: "00ced1",
        darkviolet: "9400d3",
        deeppink: "ff1493",
        deepskyblue: "00bfff",
        dimgray: "696969",
        dimgrey: "696969",
        dodgerblue: "1e90ff",
        firebrick: "b22222",
        floralwhite: "fffaf0",
        forestgreen: "228b22",
        fuchsia: "f0f",
        gainsboro: "dcdcdc",
        ghostwhite: "f8f8ff",
        gold: "ffd700",
        goldenrod: "daa520",
        gray: "808080",
        green: "008000",
        greenyellow: "adff2f",
        grey: "808080",
        honeydew: "f0fff0",
        hotpink: "ff69b4",
        indianred: "cd5c5c",
        indigo: "4b0082",
        ivory: "fffff0",
        khaki: "f0e68c",
        lavender: "e6e6fa",
        lavenderblush: "fff0f5",
        lawngreen: "7cfc00",
        lemonchiffon: "fffacd",
        lightblue: "add8e6",
        lightcoral: "f08080",
        lightcyan: "e0ffff",
        lightgoldenrodyellow: "fafad2",
        lightgray: "d3d3d3",
        lightgreen: "90ee90",
        lightgrey: "d3d3d3",
        lightpink: "ffb6c1",
        lightsalmon: "ffa07a",
        lightseagreen: "20b2aa",
        lightskyblue: "87cefa",
        lightslategray: "789",
        lightslategrey: "789",
        lightsteelblue: "b0c4de",
        lightyellow: "ffffe0",
        lime: "0f0",
        limegreen: "32cd32",
        linen: "faf0e6",
        magenta: "f0f",
        maroon: "800000",
        mediumaquamarine: "66cdaa",
        mediumblue: "0000cd",
        mediumorchid: "ba55d3",
        mediumpurple: "9370db",
        mediumseagreen: "3cb371",
        mediumslateblue: "7b68ee",
        mediumspringgreen: "00fa9a",
        mediumturquoise: "48d1cc",
        mediumvioletred: "c71585",
        midnightblue: "191970",
        mintcream: "f5fffa",
        mistyrose: "ffe4e1",
        moccasin: "ffe4b5",
        navajowhite: "ffdead",
        navy: "000080",
        oldlace: "fdf5e6",
        olive: "808000",
        olivedrab: "6b8e23",
        orange: "ffa500",
        orangered: "ff4500",
        orchid: "da70d6",
        palegoldenrod: "eee8aa",
        palegreen: "98fb98",
        paleturquoise: "afeeee",
        palevioletred: "db7093",
        papayawhip: "ffefd5",
        peachpuff: "ffdab9",
        peru: "cd853f",
        pink: "ffc0cb",
        plum: "dda0dd",
        powderblue: "b0e0e6",
        purple: "800080",
        rebeccapurple: "663399",
        red: "f00",
        rosybrown: "bc8f8f",
        royalblue: "4169e1",
        saddlebrown: "8b4513",
        salmon: "fa8072",
        sandybrown: "f4a460",
        seagreen: "2e8b57",
        seashell: "fff5ee",
        sienna: "a0522d",
        silver: "c0c0c0",
        skyblue: "87ceeb",
        slateblue: "6a5acd",
        slategray: "708090",
        slategrey: "708090",
        snow: "fffafa",
        springgreen: "00ff7f",
        steelblue: "4682b4",
        tan: "d2b48c",
        teal: "008080",
        thistle: "d8bfd8",
        tomato: "ff6347",
        turquoise: "40e0d0",
        violet: "ee82ee",
        wheat: "f5deb3",
        white: "fff",
        whitesmoke: "f5f5f5",
        yellow: "ff0",
        yellowgreen: "9acd32"
    };

// Make it easy to access colors via `hexNames[hex]`
    var hexNames = tinycolor.hexNames = flip(names);


// Utilities
// ---------

// `{ 'name1': 'val1' }` becomes `{ 'val1': 'name1' }`
    function flip(o) {
        var flipped = { };
        for (var i in o) {
            if (o.hasOwnProperty(i)) {
                flipped[o[i]] = i;
            }
        }
        return flipped;
    }

// Return a valid alpha value [0,1] with all invalid values being set to 1
    function boundAlpha(a) {
        a = parseFloat(a);

        if (isNaN(a) || a < 0 || a > 1) {
            a = 1;
        }

        return a;
    }

// Take input from [0, n] and return it as [0, 1]
    function bound01(n, max) {
        if (isOnePointZero(n)) { n = "100%"; }

        var processPercent = isPercentage(n);
        n = mathMin(max, mathMax(0, parseFloat(n)));

        // Automatically convert percentage into number
        if (processPercent) {
            n = parseInt(n * max, 10) / 100;
        }

        // Handle floating point rounding errors
        if ((Math.abs(n - max) < 0.000001)) {
            return 1;
        }

        // Convert into [0, 1] range if it isn't already
        return (n % max) / parseFloat(max);
    }

// Force a number between 0 and 1
    function clamp01(val) {
        return mathMin(1, mathMax(0, val));
    }

// Parse a base-16 hex value into a base-10 integer
    function parseIntFromHex(val) {
        return parseInt(val, 16);
    }

// Need to handle 1.0 as 100%, since once it is a number, there is no difference between it and 1
// <http://stackoverflow.com/questions/7422072/javascript-how-to-detect-number-as-a-decimal-including-1-0>
    function isOnePointZero(n) {
        return typeof n == "string" && n.indexOf('.') != -1 && parseFloat(n) === 1;
    }

// Check to see if string passed in is a percentage
    function isPercentage(n) {
        return typeof n === "string" && n.indexOf('%') != -1;
    }

// Force a hex value to have 2 characters
    function pad2(c) {
        return c.length == 1 ? '0' + c : '' + c;
    }

// Replace a decimal with it's percentage value
    function convertToPercentage(n) {
        if (n <= 1) {
            n = (n * 100) + "%";
        }

        return n;
    }

// Converts a decimal to a hex value
    function convertDecimalToHex(d) {
        return Math.round(parseFloat(d) * 255).toString(16);
    }
// Converts a hex value to a decimal
    function convertHexToDecimal(h) {
        return (parseIntFromHex(h) / 255);
    }

    var matchers = (function() {

        // <http://www.w3.org/TR/css3-values/#integers>
        var CSS_INTEGER = "[-\\+]?\\d+%?";

        // <http://www.w3.org/TR/css3-values/#number-value>
        var CSS_NUMBER = "[-\\+]?\\d*\\.\\d+%?";

        // Allow positive/negative integer/number.  Don't capture the either/or, just the entire outcome.
        var CSS_UNIT = "(?:" + CSS_NUMBER + ")|(?:" + CSS_INTEGER + ")";

        // Actual matching.
        // Parentheses and commas are optional, but not required.
        // Whitespace can take the place of commas or opening paren
        var PERMISSIVE_MATCH3 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";
        var PERMISSIVE_MATCH4 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";

        return {
            CSS_UNIT: new RegExp(CSS_UNIT),
            rgb: new RegExp("rgb" + PERMISSIVE_MATCH3),
            rgba: new RegExp("rgba" + PERMISSIVE_MATCH4),
            hsl: new RegExp("hsl" + PERMISSIVE_MATCH3),
            hsla: new RegExp("hsla" + PERMISSIVE_MATCH4),
            hsv: new RegExp("hsv" + PERMISSIVE_MATCH3),
            hsva: new RegExp("hsva" + PERMISSIVE_MATCH4),
            hex3: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
            hex6: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,
            hex8: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/
        };
    })();

// `isValidCSSUnit`
// Take in a single string / number and check to see if it looks like a CSS unit
// (see `matchers` above for definition).
    function isValidCSSUnit(color) {
        return !!matchers.CSS_UNIT.exec(color);
    }

// `stringInputToObject`
// Permissive string parsing.  Take in a number of formats, and output an object
// based on detected format.  Returns `{ r, g, b }` or `{ h, s, l }` or `{ h, s, v}`
    function stringInputToObject(color) {

        color = color.replace(trimLeft,'').replace(trimRight, '').toLowerCase();
        var named = false;
        if (names[color]) {
            color = names[color];
            named = true;
        }
        else if (color == 'transparent') {
            return { r: 0, g: 0, b: 0, a: 0, format: "name" };
        }

        // Try to match string input using regular expressions.
        // Keep most of the number bounding out of this function - don't worry about [0,1] or [0,100] or [0,360]
        // Just return an object and let the conversion functions handle that.
        // This way the result will be the same whether the tinycolor is initialized with string or object.
        var match;
        if ((match = matchers.rgb.exec(color))) {
            return { r: match[1], g: match[2], b: match[3] };
        }
        if ((match = matchers.rgba.exec(color))) {
            return { r: match[1], g: match[2], b: match[3], a: match[4] };
        }
        if ((match = matchers.hsl.exec(color))) {
            return { h: match[1], s: match[2], l: match[3] };
        }
        if ((match = matchers.hsla.exec(color))) {
            return { h: match[1], s: match[2], l: match[3], a: match[4] };
        }
        if ((match = matchers.hsv.exec(color))) {
            return { h: match[1], s: match[2], v: match[3] };
        }
        if ((match = matchers.hsva.exec(color))) {
            return { h: match[1], s: match[2], v: match[3], a: match[4] };
        }
        if ((match = matchers.hex8.exec(color))) {
            return {
                a: convertHexToDecimal(match[1]),
                r: parseIntFromHex(match[2]),
                g: parseIntFromHex(match[3]),
                b: parseIntFromHex(match[4]),
                format: named ? "name" : "hex8"
            };
        }
        if ((match = matchers.hex6.exec(color))) {
            return {
                r: parseIntFromHex(match[1]),
                g: parseIntFromHex(match[2]),
                b: parseIntFromHex(match[3]),
                format: named ? "name" : "hex"
            };
        }
        if ((match = matchers.hex3.exec(color))) {
            return {
                r: parseIntFromHex(match[1] + '' + match[1]),
                g: parseIntFromHex(match[2] + '' + match[2]),
                b: parseIntFromHex(match[3] + '' + match[3]),
                format: named ? "name" : "hex"
            };
        }

        return false;
    }

    function validateWCAG2Parms(parms) {
        // return valid WCAG2 parms for isReadable.
        // If input parms are invalid, return {"level":"AA", "size":"small"}
        var level, size;
        parms = parms || {"level":"AA", "size":"small"};
        level = (parms.level || "AA").toUpperCase();
        size = (parms.size || "small").toLowerCase();
        if (level !== "AA" && level !== "AAA") {
            level = "AA";
        }
        if (size !== "small" && size !== "large") {
            size = "small";
        }
        return {"level":level, "size":size};
    }

    MapSVG.tinycolor = tinycolor;
// Node: Export function
    if (typeof module !== "undefined" && module.exports) {
        module.exports = tinycolor;
    }
// AMD/requirejs: Define the module
    else if (typeof define === 'function' && define.amd) {
        define(function () {return tinycolor;});
    }
// Browser: Expose to window
    else {
        // MapSVG.tinycolor = tinycolor;
    }

})(Math, MapSVG);

// if (typeof module !== "undefined" && module.exports) {
//     module.exports = {
//         MapSVG: MapSVG,
//
//     };
// }
