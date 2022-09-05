/**
 * Global MapSVG class. It contains all other MapSVG classes and some static methods.
 * @constructor
 * @example
 * var mapsvg = MapSVG.get(0); // get first map instance
 * var mapsvg2 = MapSVG.get(1); // get second map instance
 * var mapsvg3 = MapSVG.getById(123); // get map by ID
 *
 * var mapsvg = new MapSVG.Map("my-container",{
 *   source: "/path/to/map.svg"
 * });
 *
 * var marker = new MapSVG.Marker({
 *   location: location,
 *   mapsvg: mapsvg
 * });
 *
 * if(MapSVG.isPhone){
 *  // do something special for mobile devices
 * }
 *
 *
 */
var MapSVG = function() {

};

(function( $ ) {

/**
 * Keeps loaded HBS templates
 * @type {Array}
 * @private
 * @static
 * @property
 */
MapSVG.templatesLoaded = {};

/**
 * Keeps URLs
 * @type {Array}
 * @private
 * @static
 * @property
 */
if(typeof mapsvg_paths !== "undefined"){
    MapSVG.urls = mapsvg_paths;
} else {
    MapSVG.urls = {};
}

/**
 * Keeps map instances
 * @type {Array}
 * @private
 * @static
 * @property
 */
MapSVG.instances = [];

MapSVG.userAgent = navigator.userAgent.toLowerCase();

/**
 * Determines if current device is touch-device
 * @type {boolean}
 * @static
 * @property
 */
MapSVG.touchDevice =
    (('ontouchstart' in window)
        || (navigator.MaxTouchPoints > 0)
        || (navigator.msMaxTouchPoints > 0));
    // (MapSVG.userAgent.indexOf("ipad") > -1) ||
    // (MapSVG.userAgent.indexOf("iphone") > -1) ||
    // (MapSVG.userAgent.indexOf("ipod") > -1) ||
    // (MapSVG.userAgent.indexOf("android") > -1);

/**
 * Determines if current device is iOS-device
 * @type {boolean}
 * @static
 * @property
 */
MapSVG.ios =
    (MapSVG.userAgent.indexOf("ipad") > -1) ||
    (MapSVG.userAgent.indexOf("iphone") > -1) ||
    (MapSVG.userAgent.indexOf("ipod") > -1);

/**
 * Determines if current device is Android-device
 * @type {boolean}
 * @static
 * @property
 */
MapSVG.android = MapSVG.userAgent.indexOf("android");

/**
 * Determines if current device is mobile-device
 * @type {boolean}
 * @static
 * @property
 */
MapSVG.isPhone = window.matchMedia("only screen and (max-width: 812px)").matches;

/**
 * Keeps browser information
 * @type {object}
 * @static
 * @property
 */
MapSVG.browser = {};
MapSVG.browser.ie = MapSVG.userAgent.indexOf("msie") > -1 || MapSVG.userAgent.indexOf("trident") > -1 || MapSVG.userAgent.indexOf("edge") > -1 ? {} : false;
MapSVG.browser.firefox = MapSVG.userAgent.indexOf("firefox") > -1;

/**
 * Contains device information
 * @type {object}
 * @static
 * @property
 */
var ua = window.navigator.userAgent;
MapSVG.device = {};
MapSVG.device.ios = !!ua.match(/iPad/i) || !!ua.match(/iPhone/i);
MapSVG.device.android = !!ua.match(/Android/i);

if (!String.prototype.trim) {
    String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};
}

/**
 * Converts mouse event object to x/y coordinates
 * @param e
 * @returns {{x: *, y: *}}
 */
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

/**
 * Adds new instance of the map
 * @param {MapSVG.Map} mapsvg
 */
MapSVG.addInstance = function(mapsvg){
    MapSVG.instances.push(mapsvg);
};

MapSVG.get = function(index){
    return MapSVG.instances[index];
};

MapSVG.getById = function(id){
    var instance = MapSVG.instances.filter(function(i){ return i.id == id });
    if(instance.length > 0){
        return instance[0];
    }
};

MapSVG.getByContainerId = function(id){
    var instance = MapSVG.instances.filter(function(i){ return i.$map.attr('id') == id });
    if(instance.length > 0){
        return instance[0];
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

if (!Object.values) {
    Object.values = function(object){
        return Object.keys(object).map(function(k){
            return object[k]
        });
    }
}


})( jQuery );


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
(function( $ ) {
 /**
 * Database class that gets and stores data from MySQL tables (Regions, DB Objects) connected to the current map.
 * two instances of this class are available in MapSVG.Map instance via .getDatabase() and .getDatabaseRegions().
 * @param {object} options
 * @param {MapSVG.Map} mapsvg
 * @constructor
 *
 * @example
 * var database = mapsvg.getDatabase();
 * var objects = database.getLoaded();
 *
 * var regionsDatabase = mapsvg.getDatabaseRegions();
 * var regionsData = regionsDatabase.getLoaded();
 */
MapSVG.DatabaseService = function(options, mapsvg){
    var _this = this;
    this.mapsvg         = mapsvg;
    this.map_id         = options.map_id;
    this.table          = options.table;
    this.perpage        = parseInt(options.perpage) || 0;
    this.sortBy         = options.sortBy  || null;
    this.sortDir        = options.sortDir || null;
    this.type           = options.type    || 'mysql'; // mysql / local
    this.loaded         = false;
    if(this.type == 'local')
        this.dbObject = options.dbObject;
    this.server = this.type == 'mysql' ? new MapSVG.ServerMySQL({map_id: this.map_id, table: this.table, perpage: this.perpage, mapsvg: this.mapsvg}) : new MapSVG.ServerLocal({storage : this.dbObject});
    this.lastChangeTime = Date.now();
    this.cols           = [];
    this.schemaDict     = {};
    this.index          = {};
    this.page           = 1;
    this.query          = new MapSVG.DatabaseQuery();
    this.query.set({
       sort: [{field: this.sortBy, order: this.sortDir}],
       perpage: this.perpage
    });
    this.events         = {
        'create': [],
        'update': [],
        'change': [],
        'schemaChange': [],
        'dataLoaded': [],
        'beforeLoad': []
    }; // array of callbacks
    this.schema         = new MapSVG.DatabaseSchema(options, mapsvg);
    this.schema.on('change',function(){
        _this.getAll();
        _this.trigger('schemaChange');
    });

};

MapSVG.DatabaseService.setQuery = function(params){
};
MapSVG.DatabaseService.prototype.setPerpage = function(perpage) {
    this.server.perpage = perpage;
};

MapSVG.DatabaseService.prototype.lastChangeTime = function() {
    return this.lastChangeTime;
};

MapSVG.DatabaseService.prototype.onFirstPage = function() {
    return this.query.page === 1;
};
MapSVG.DatabaseService.prototype.onLastPage = function() {
    return this.server.hasMoreRecords === false;
};

MapSVG.DatabaseService.prototype.import = function(data, convertLatlngToAddress){
    var _this = this;

    var locationField = _this.getSchemaFieldByType('location');
    var language = 'en';
    if(locationField && locationField.language){
        language = locationField.language;
    }

    data = this.formatCSV(data);
    return this.server.import(data, language, convertLatlngToAddress).done(function(){
        _this.getAll();
    });
};
MapSVG.DatabaseService.prototype.create = function(obj){
    var _this = this;
    return this.server.create(obj).done(function(){
        _this.trigger('create', obj);
        _this.trigger('change', obj);
    });
};
MapSVG.DatabaseService.prototype.update = function(obj){
    var _this = this;
    return this.server.update(obj).done(function(){
        _this.trigger('update', obj);
        _this.trigger('change', obj);
    });
};
MapSVG.DatabaseService.prototype.delete = function(id){
    var _this = this;
    return this.server.delete(id).done(function(){
        _this.trigger('delete', id);
        _this.trigger('change', id);
    });
};
MapSVG.DatabaseService.prototype.clear = function(id){
    var _this = this;
    return this.server.clear().done(function(){
        _this.getAll();
        _this.trigger('delete');
        _this.trigger('change');
    });
};
/**
 * Returns one of the loaded objects by ID.
 * Objects are usually loaded with pagination and this method
 * doesn't return an object if it's outside of the currently loaded page.
 * @param {string} id
 * @returns {object}
 * @example
 * var obejct = mapsvg.getDatabase().getLoadedObject(2);
 */
MapSVG.DatabaseService.prototype.getLoadedObject = function(id){
    var _this = this;
    return this.server.getLoadedObject(id);
};

/**
 * Loads objects from remote server, from the table connected to current MapSVG map.
 * This method triggers reloading markers on the map and reloading items in the directory.
 * You can add a custom event handler using MapSVG "dataLoaded" event.
 * @param params
 * @returns {Deferred}
 * @example
 * mapsvg.getDatabase().getAll({
 *   page: 1,
 *   perpage: 30,
 *   sortBy: 'id',
 *   sortDir: 'DESC',
 *   filters: {regions: "US-TX"}
 * }).done(function(objects){
 *   // do something
 * });
 */
MapSVG.DatabaseService.prototype.getAll = function(params){
    var _this = this;

    _this.trigger('beforeLoad', params);

    if(this.mapsvg.options.database.noFiltersNoLoad && Object.keys(_this.query.filters).length === 0){
        defer = $.Deferred();
        var obj = this.rows;
        defer.promise();
        defer.resolve(obj);
        _this.server.fill({objects: []});
        _this.trigger('dataLoaded');
        return defer;
    }

    if(typeof params == 'object' && Object.keys(params).length && !params.page)
        _this.query.page = 1;

    _this.query.set(params);

    this.page = params && params.page? parseInt(params.page) : this.page;

    var data = _this.query.get();

    if(!this.schema.loaded()){
        data.with_schema = true;
    }
    if(this.mapsvg.editMode && typeof data.filterout !== 'undefined'){
        delete data.filterout;
    }
    return this.server.get(data).done(function(_data){
        if(!_this.schema.loaded() && _data.schema){
            _this.schema.set(_data.schema);
        }
        _this.loaded = true;
        _this.trigger('dataLoaded');
    });
};

/**
 * Fill local database storage with preloaded data
 * @param {Object} data
 */
MapSVG.DatabaseService.prototype.fill = function(data){
    var _this = this;
    this.server.fill(data);
    if(!_this.schema.loaded() && data.schema){
        _this.schema.set(data.schema);
    }
    _this.loaded = true;
    _this.trigger('dataLoaded');
};


/**
 * Returns all loaded objects
 * @returns {Array}
 */
MapSVG.DatabaseService.prototype.getLoaded = function(){
    return this.server.rows;
};

MapSVG.DatabaseService.prototype.formatData = function(data){
    var _this = this;
    return data; //JSON.stringify(data);
};

MapSVG.DatabaseService.prototype.formatCSV = function(data){

    var _this = this;
    var newdata = [];

    data.forEach(function(object, index){
       var newObject = {};
       for(var key in object){
           var field = _this.schema.getField(key);
           if(key === 'post'){
               field = {type: "post"}
           }
           if(field !== undefined ){
               switch (field.type){
                   case "region":
                       newObject[key] = object[key].split(',')
                           .map(function(regionId){
                               return regionId.trim();
                           }).filter(function(rId) {
                               return  (_this.mapsvg.getRegion(rId) !== undefined || _this.mapsvg.regions.find(function(item){ return item.title === rId }) !== undefined);
                           }).map(function(rId){
                               var r = _this.mapsvg.getRegion(rId);
                               if(typeof r === 'undefined'){
                                   r = _this.mapsvg.regions.find(function(item){ return item.title === rId });
                               }
                               return {id: r.id, title: r.title}
                           });
                       break;
                   case "location":
                       var latLngRegex = /^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/g;
                       if(object[key].match(latLngRegex)){
                           var coords = object[key].split(',').map(function(n){ return parseFloat(n); });
                           if(coords.length == 2 && (coords[0] > -90 && coords[0] < 90) && (coords[1] > -180 && coords[1] < 180)){
                               newObject[key] = {lat: coords[0], lng: coords[1]};
                           } else {
                               newObject[key] = '';
                           }
                       } else if(object[key]){
                           newObject[key] = {address: object[key]};
                       }

                       if(typeof newObject[key] == 'object'){
                           newObject[key].img = _this.mapsvg.getData().options.defaultMarkerImage;
                       }

                       break;
                   case "select":
                       var field = _this.schema.getField(key);
                       if(field.multiselect){
                           var labels = _this.schema.getField(key).options.map(function(f){
                              return f.label;
                           });
                           newObject[key] = object[key].split(',')
                               .map(function(label){
                                   return label.trim();
                               }).filter(function(label) {
                                   return  labels.indexOf(label) !== -1;
                               }).map(function(label){
                                   return _this.schema.getField(key).options.filter(function(option){
                                       return option.label == label;
                                   })[0];
                               });
                       } else {
                           newObject[key] = object[key];
                       }
                       break;
                   case "radio":
                   case "text":
                   case "textarea":
                   case "status":
                   default:
                       newObject[key] = object[key];
                       break;
               }
           }
       }
       data[index] = newObject;
    });

    return data;
};

MapSVG.DatabaseService.prototype.on = function(event, callback){
    this.lastChangeTime = Date.now();
    if(!this.events[event])
        this.events[event] = [];
    this.events[event].push(callback);
};
MapSVG.DatabaseService.prototype.trigger = function(event, params){
    var _this = this;
    if(this.events[event] && this.events[event].length)
        this.events[event].forEach(function(callback){
            callback && callback(params);
        });
};
MapSVG.DatabaseService.prototype.onSchemaChange = function(callback){
    this.onSchemaChangeCallbacks.push(callback);
};
/**
 * Returns table schema
 * @param options
 * @returns {object}
 */
MapSVG.DatabaseService.prototype.getSchema = function(options){
    return this.schema.get(options);
};
MapSVG.DatabaseService.prototype.getSchemaField = function(field){
    return this.schema.getField(field);
};
MapSVG.DatabaseService.prototype.getSchemaFieldByType = function(type){
    return this.schema.getFieldByType(type);
};
MapSVG.DatabaseService.prototype.setSchema = function(options){
    return this.schema.set(options);
};
MapSVG.DatabaseService.prototype.loadSchema = function(options){
    return this.schema.load(options);
};
MapSVG.DatabaseService.prototype.saveSchema = function(options){
    return this.schema.save(options);
};
MapSVG.DatabaseService.prototype.getColumns = function(options){
    return this.schema.getColumns(options);
};



/* SCHEMA */
MapSVG.DatabaseSchema = function(options, mapsvg){
    this.mapsvg         = mapsvg;
    this.map_id         = options.map_id;
    this.table          = options.table;
    this.lastChangeTime = Date.now();
    this.cols           = [];
    this.schema         = [];
    this.schemaDict     = {};
    this.events         = {
        'create': [],
        'update': [],
        'change': []
    }; // array of callbacks
};

MapSVG.DatabaseSchema.prototype.loaded = function(options){
    return this.schema.length!==0;
};
MapSVG.DatabaseSchema.prototype.set = function(options){
    var _this = this;

    if(options){
        _this.schema = options.map(function(field){
            field.visible = MapSVG.parseBoolean(field.visible);
            if(field.type == 'region'){
                field.options = [];
                field.optionsDict = {};
                _this.mapsvg.regions.forEach(function(region){
                    field.options.push({id: region.id, title: region.title});
                    field.optionsDict[region.id] = region.title ? region.title : region.id;
                });
            }
            _this.schemaDict[field.name] = field;
            return field;
        });
    }

};
MapSVG.DatabaseSchema.prototype.save = function(fields){
    var _this = this;
    this.set(fields);
    for(var i in this.schema){
        if(!this.schema[i])
            this.schema.splice(i,1);
    }
    fields = JSON.stringify(fields);
    fields = fields.replace(/select/g,"!mapsvg-encoded-slct");
    fields = fields.replace(/table/g,"!mapsvg-encoded-tbl");
    fields = fields.replace(/database/g,"!mapsvg-encoded-db");
    fields = fields.replace(/varchar/g,"!mapsvg-encoded-vc");

    return $.post(ajaxurl, {action: 'mapsvg_save_schema', schema: fields, map_id: this.map_id, table: this.table, _wpnonce: MapSVG.nonce}).done(function(){
        _this.trigger('change');
    });
};
MapSVG.DatabaseSchema.prototype.get = function(options){
    return this.schema;
};
MapSVG.DatabaseSchema.prototype.getField = function(field){

    if(field=='id'){
        return {name: 'id', visible: true, type: 'id'};
    }

    return this.schemaDict[field];
};
MapSVG.DatabaseSchema.prototype.getFieldByType = function(type){
    var f = null;
    this.schema.forEach(function(field){
       if(field.type === type)
           f = field;
    });
    return f;
};
MapSVG.DatabaseSchema.prototype.load = function(options){
    var _this = this;
    return $.get(ajaxurl, {action: 'mapsvg_get_schema', map_id: this.map_id, table: this.table}, null, 'json')
        .done(function(schema){
            _this.set(schema);
        });
};
MapSVG.DatabaseSchema.prototype.getColumns = function (filters) {

    filters = filters || {};

    var _this = this;
    var columns = this.get().slice(0); // clone array
    if(this.table == 'regions')
        columns.unshift({name: 'title', visible: true, type: 'title'}); // add Title column to the beginning
    var needAddId = true;
    columns.forEach(function(col){
        if(col.name == 'id'){
            needAddId = false;
        }
    });
    if(needAddId)
        columns.unshift({name: 'id', visible: true, type: 'id'}); // add ID column to the beginning
    var needfilters = Object.keys(filters).length !== 0;
    var results = [];

    if(needfilters){
        var filterpass;
        columns.forEach(function(obj){
            filterpass = true;
            for(var param in filters) {
                filterpass = (obj[param] == filters[param]);
            }
            filterpass && results.push(obj);
        });
    } else {
        results = columns;
    }

    return results;
};
MapSVG.DatabaseSchema.prototype.on = function(event, callback){
    this.lastChangeTime = Date.now();
    if(!this.events[event])
        this.events = {};
    this.events[event].push(callback);
};
MapSVG.DatabaseSchema.prototype.trigger = function(event){
    var _this = this;
    if(this.events[event] && this.events[event].length)
        this.events[event].forEach(function(callback){
            callback && callback();
        });
};

MapSVG.Server = function(params){
    this.index = {};
    this.hasMoreRecords = false;
};
MapSVG.Server.prototype.reindex = function(){
    var _this = this;
    this.index = {};
    this.rows && this.rows.forEach(function(obj, index) {
        _this.index[obj.id] = index;
    });
};
MapSVG.Server.prototype.getLoadedObject = function(id){
    var index = this.index[id];
    if(index != undefined) {
        return this.rows[index];
    }else{
        return null;
    }
};


MapSVG.ServerMySQL = function(params){
    this.map_id = params.map_id;
    this.table = params.table;
    this.perpage = params.perpage;
    this.rows = [];
    MapSVG.Server.call(this);
};
MapSVG.extend(MapSVG.ServerMySQL, MapSVG.Server);

MapSVG.ServerMySQL.prototype.formatData = function(_object){
    var object = {};
    for(var i in _object){
        if(_object[i] && (typeof _object[i] == 'object' || typeof _object[i] == 'function') && _object[i].getOptions!=undefined){
            object[i] = _object[i].getOptions();
        }else{
            if(_object[i] !== null && _object[i] !== undefined && !(typeof _object[i] == 'object' && _object[i].length===0)){
                object[i] = _object[i];
            }else{
                object[i] = '';
            }

        }
    }
    return JSON.stringify(object);
};

MapSVG.ServerMySQL.prototype.actionData = function(action, data){
    data.action  = action;
    data.map_id  = this.map_id;
    data.table   =  this.table;
    data.perpage = this.perpage;
    data._wpnonce = MapSVG.nonce;
    return data;
};

MapSVG.ServerMySQL.prototype.fill = function(_data){
    var _this = this;
    if(_data.objects && _data.objects.length){
        _this.hasMoreRecords = this.perpage && (_data.objects.length > this.perpage) ? true : false;
        if(_this.hasMoreRecords){
            _data.objects.pop();
        }
        _this.rows = _data.objects;
    }else{
        _this.hasMoreRecords = false;
        _this.rows = [];
    }
    _this.reindex();
};

MapSVG.ServerMySQL.prototype.get = function(data){
    var _that = this;
    if(data.perpage !== undefined){
        this.perpage = data.perpage;
    }
    return $.getJSON(ajaxurl, this.actionData('mapsvg_data_get_all', data)).done(function(_data){
        _that.fill(_data);
    });
};
MapSVG.ServerMySQL.prototype.import = function(data, language, convertLatlngToAddress){
    var _this = this;

    var i,j,temparray,chunk = 50;
    var chunks = [];

    for (i=0,j=data.length; i<j; i+=chunk) {
        temparray = data.slice(i,i+chunk);
        chunks.push(temparray);
    }

    if(chunks.length > 0){

        var delay = 0;
        var delayPlus = chunks[0][0] && chunks[0][0].location ? 1000 : 0;

        defer = $.Deferred();
        defer.promise();

        _this.completeChunks = 0;

        chunks.forEach(function(chunk){
            delay += delayPlus;
            setTimeout(function(){
                var _data = JSON.stringify(chunk);
                $.post(ajaxurl, _this.actionData('mapsvg_data_import', {data: _data, language: language, convertLatlngToAddress: convertLatlngToAddress}), null, 'json').done(function(_data){

                    _this.completeChunk(chunks, defer);

                });
            }, delay);
        });
    }


    return defer;

};

MapSVG.ServerMySQL.prototype.completeChunk = function(chunks, defer){
    var _this = this;
    _this.completeChunks++;
    if(_this.completeChunks === chunks.length){
        defer.resolve();
    }
};

MapSVG.ServerMySQL.prototype.create = function(data){
    var _this = this;
    this.rows.push(data);
    var newObj = $.extend({}, data);
    newObj.post && delete newObj.post;
    var _data = this.formatData(newObj);
    return $.post(ajaxurl, this.actionData('mapsvg_data_create', {data: _data}), null, 'json').done(function(resp){

    }).then(function(resp){
        data.id = resp.id;
        _this.reindex();
       return  data;
    });
};
MapSVG.ServerMySQL.prototype.update = function(data){
    var index = this.index[data.id];
    if(index!==undefined){
        $.extend(this.rows[index], data);
        var newObj = $.extend({}, data);
        newObj.post && delete newObj.post;
        var _data = this.formatData(newObj);
        return $.post(ajaxurl, this.actionData('mapsvg_data_update', {data: _data}), null, 'json');
    }
};
MapSVG.ServerMySQL.prototype.delete = function(id){
    var _this = this;
    var index = this.index[id];
    this.rows.splice(index,1);
    return $.post(ajaxurl, this.actionData('mapsvg_data_delete', {data: {id: id}}), null, 'json').done(function(){
        _this.reindex();
    });
};
MapSVG.ServerMySQL.prototype.clear = function(id){
    var _this = this;
    // var index = this.index[id];
    // this.rows.splice(index,1);
    return $.post(ajaxurl, this.actionData('mapsvg_data_clear', {data: {}}), null, 'json').done(function(){
        this.rows = [];
        _this.reindex();
    });
};

MapSVG.ServerLocal = function(params){
    this.rows = params.storage;
    MapSVG.Server.call(this, params);
    this.reindex();
};
MapSVG.extend(MapSVG.ServerLocal, MapSVG.Server);

MapSVG.ServerLocal.prototype.get = function(data){
    defer = $.Deferred();
    var obj = this.rows;
    defer.promise();
    defer.resolve(obj);
    return defer;
};
MapSVG.ServerLocal.prototype.create = function(data){
    if(data.id){
        var index = this.index[data.id];
        if(this.rows[index]){
            return this.update(data);
        }
    }else{
        data.id = this.getId();
    }

    this.rows.push(data);
    this.reindex();

    defer = $.Deferred();
    defer.promise();
    defer.resolve(data);

    return defer;
};
MapSVG.ServerLocal.prototype.update = function(data){
    defer = $.Deferred();
    var index = this.index[data.id];

    if(this.rows[index]){
        // $.extend(true, this.rows[index], data);
        for(var key in data){
            this.rows[index][key] = data[key];
        }
        this.rows[index] = data;
    } else {
        this.rows.push(data);
    }
    this.reindex();

    defer.promise();
    defer.resolve(data);
    return defer;
};
MapSVG.ServerLocal.prototype.delete = function(id){
    var index = this.index[id];
    this.rows.splice(index,1);
    this.reindex();
    defer = $.Deferred();
    defer.promise();
    defer.resolve(this.storage);
    return defer;
};
MapSVG.ServerLocal.prototype.getId = function(){
    var t = this.rows.map(function(obj){
        return obj.id;
    });
    if(!t.length)
        t = [0];
    return !t ? 1 : Math.max.apply(null, t)+1;
};

MapSVG.Filters = function(fields){
    this.schema = {};
    this.schemaDict = {};
    this.fields = {};
    this.events         = {
        'change': []
    }; // array of callbacks
    this.setSchema(fields);
};
MapSVG.Filters.prototype.set = function(fields){
    this.fields = fields;
};
MapSVG.Filters.prototype.save = function(fields){
    this.fields = fields;
};
MapSVG.Filters.prototype.reset = function(fields){
    this.fields = {};
};
MapSVG.Filters.prototype.get = function(fields){
    return this.fields;
};
MapSVG.Filters.prototype.getField = function(field){
    return this.schemaDict[field];
};

MapSVG.Filters.prototype.getSchema = function(fields){
    return this.schema;
};
MapSVG.Filters.prototype.setSchema = function(fields){
    var _this = this;
    if(fields)
        fields.forEach(function(field){
            var paramName = field.parameterName.split('.')[1];
            _this.schemaDict[paramName] = field;
        });
    this.schema = fields;
    return this.schema;
};
MapSVG.Filters.prototype.on = function(event, callback){
    if(!this.events[event])
        this.events[event] = [];
    this.events[event].push(callback);
};
MapSVG.Filters.prototype.trigger = function(event){
    var _this = this;
    if(this.events[event] && this.events[event].length)
        this.events[event].forEach(function(callback){
            callback && callback(_this);
        });
};


MapSVG.DatabaseQuery = function(options){
    options = options || {};
    this.sort           = options.sort   || {};
    this.sortBy         = options.sortBy || null;
    this.sortDir        = options.sortDir || null;
    this.page           = options.page || 1;
    this.perpage        = options.perpage || 0;
    this.search         = options.search;
    this.searchField    = options.searchField;
    this.filters        = options.filters || {};
    this.filterout      = options.filterout || {};
};
MapSVG.DatabaseQuery.prototype.set = function(fields){
    var _this = this;
    for(var key in fields){
        if(key == 'filters'){
            _this.setFilters(fields[key]);
        }else{
            _this[key] = fields[key];
        }
    }
};
MapSVG.DatabaseQuery.prototype.get = function(){
    return {
        search: this.search,
        searchField: this.searchField,
        searchFallback: this.searchFallback,
        filters: this.filters,
        filterout: this.filterout,
        page: this.page,
        sort: this.sort,
        perpage: this.perpage
    };
};

MapSVG.DatabaseQuery.prototype.setFilters = function(fields){
    var _this = this;
    for(var key in fields){
        if(fields[key] === null || fields[key] === "" || fields[key] === undefined){
            if(_this.filters[key]){
                delete _this.filters[key];
            }
        } else {
            _this.filters[key] = fields[key];
        }
    }
};
MapSVG.DatabaseQuery.prototype.setFilterOut = function(fields){
    var _this = this;
    for(var key in fields){
        _this.filterout[key] = fields[key];
    }
};
MapSVG.DatabaseQuery.prototype.resetFilters = function(fields){
    this.filters = {};
};
MapSVG.DatabaseQuery.prototype.setFilterField = function(field, value){
    this.filters[field] = value;
};

})( jQuery );
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

(function( $ ) {
/**
 * Creates a container with a list of objects near the map
 * @class
 * @extends MapSVG.Controller
 * @param options - List of options
 *
 * @example
 * var directory = mapsvg.controllers.directory;
 *
 *  // Toggle map/list view on mobile devices
 * if(MapSVG.isPhone){
 *   directory.toggle(true); // show directory
 * }
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

/**
 * Returns a HTML content for the Directory toolbar
 * @returns {string} HTML content
 */
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

/**
 * Does all required actions when the view is loaded: adds mobile buttons for mobile devices.
 * @private
 */
MapSVG.DirectoryController.prototype.viewDidLoad = function() {

    var _this = this;
    this.menuBtn = $('<div class="mapsvg-button-menu"><i class="mapsvg-icon-menu"></i> ' + this.mapsvg.getData().options.mobileView.labelList + '</div>');
    this.mapBtn  = $('<div class="mapsvg-button-map"><i class="mapsvg-icon-map"></i> '   + this.mapsvg.getData().options.mobileView.labelMap  + '</div>');

    // Make directory hidden by default on mobiles
    if(MapSVG.isPhone && _this.mapsvg.options.menu.hideOnMobile){
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
                }).filter(function(r){
                    return r !== undefined;
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
                    if(_this.mapsvg.getData().options.actions.directoryItem.click.zoom){
                        setTimeout(function(){
                            _this.mapsvg.showPopover(region);
                        },500);
                    }else{
                        _this.mapsvg.showPopover(region);
                    }
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
                if(_this.mapsvg.getData().options.actions.directoryItem.click.zoomToMarker){
                    setTimeout(function(){
                        _this.mapsvg.showPopover(detailsViewObject);
                    },500);
                }else{
                    _this.mapsvg.showPopover(detailsViewObject);
                }
            }
            if(_this.mapsvg.getData().options.actions.directoryItem.click.fireMarkerOnClick){
                var events = _this.mapsvg.getData().events;
                if(events && events['click.marker'])
                    events && events['click.marker'].call(marker, e, _this.mapsvg);
            }
            _this.mapsvg.selectMarker(marker);
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
        var marker;

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
            if(detailsViewObject.location){
                marker = detailsViewObject.location.marker;
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
        if(marker){
            _this.mapsvg.highlightMarker(marker);
            if(_this.mapsvg.options.actions.directoryItem.hover.centerOnMarker){
                _this.mapsvg.centerOn(marker);
            }
        }
        _this.events['mouseover'] && _this.events['mouseover'].call($(this), e, eventObject, _this.mapsvg);
    }).on('mouseout.menu.mapsvg',  '.mapsvg-directory-item', function (e) {

        var objID = $(this).data('object-id');
        var regions;
        var detailsViewObject;
        var eventObject;
        var marker;

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
            if(detailsViewObject.location){
                marker = detailsViewObject.location.marker;
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
        if(marker){
            _this.mapsvg.unhighlightMarker(marker);
        }


        _this.events['mouseout'] && _this.events['mouseout'].call($(this), e, eventObject, _this.mapsvg);

    });

    this.contentView.on('click','.mapsvg-category-item',function(){


        var panel = $(this).next('.mapsvg-category-block');

        if (panel[0].style.maxHeight || panel.hasClass('active')) {
            panel[0].style.maxHeight = null;
        } else {
            panel[0].style.maxHeight = panel[0].scrollHeight + "px";
        }

        if($(this).hasClass('active')){
            $(this).toggleClass('active', false);
            $(this).next('.mapsvg-category-block').addClass('collapsed').removeClass('active');
        } else {
            if(_this.mapsvg.options.menu.categories.collapseOther){
                $(this).parent().find('.mapsvg-category-item.active').removeClass('active');
                $(this).parent().find('.mapsvg-category-block.active').removeClass('active').addClass('collapsed');
            }
            $(this).toggleClass('active', true);
            $(this).next('.mapsvg-category-block').removeClass('collapsed').addClass('active');
        }

        var panels = $('.mapsvg-category-block.collapsed');
        panels.each(function(i,panel){
            panel.style.maxHeight = null;
        });

    });


};

/**
 * Highlights directory items
 * @param {array} ids - A list of object IDs
 */
MapSVG.DirectoryController.prototype.highlightItems = function(ids){
    var _this = this;
    if(typeof ids != 'object')
        ids = [ids];
    ids.forEach(function(id){
        _this.view.find('#mapsvg-directory-item-'+id).addClass('hover');
    });
};

/**
 * Unhighlights directory items
 */
MapSVG.DirectoryController.prototype.unhighlightItems = function(){
    this.view.find('.mapsvg-directory-item').removeClass('hover');
};

/**
 * Highlights directory items
 * @param {array} ids - A list of object IDs
 */
MapSVG.DirectoryController.prototype.selectItems = function(ids){
    var _this = this;
    if(typeof ids != 'object')
        ids = [ids];
    ids.forEach(function(id){
        _this.view.find('#mapsvg-directory-item-'+id).addClass('selected');
    });

    _this.scrollable && _this.contentWrap.nanoScroller({scrollTo: _this.view.find('#mapsvg-directory-item-'+ids[0])});
};

/**
 * Deselects directory items
 */
MapSVG.DirectoryController.prototype.deselectItems = function(){
    this.view.find('.mapsvg-directory-item').removeClass('selected');
};

/**
 * Remove items
 */
MapSVG.DirectoryController.prototype.removeItems = function(ids){
    this.view.find('#mapsvg-directory-item-'+ids).remove();
};

/**
 * @deprecated
 */
MapSVG.DirectoryController.prototype.addFilter = function(field){
    // var schema = this.database.getSchema();
};

/**
 * Filter out directory items
 */
MapSVG.DirectoryController.prototype.filterOut = function(items){

    var _this = this;

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

    return items;
}
/**
 * Loads items from a source defined in settings  to the directory. The source can be "Regions" or "Database".
 */
MapSVG.DirectoryController.prototype.loadItemsToDirectory = function(){

    var items;
    var _this = this;

    if(!_this.database.loaded) return false;

    // If "categories" option is enabled, then:
    if(_this.mapsvg.options.menu.categories && _this.mapsvg.options.menu.categories.on && _this.mapsvg.options.menu.categories.groupBy){
        // Get category field to group objects by
        var categoryField = _this.mapsvg.options.menu.categories.groupBy;
        // Get the list of categories
        if(_this.database.getSchemaField(categoryField) === undefined ||_this.database.getSchemaField(categoryField).options === undefined){
            return false;
        }
        var categories = _this.database.getSchemaField(categoryField).options;
        // Get the list of items for every category
        items = categories.map(function(category){
            var dbItems = _this.database.getLoaded();
            dbItems = _this.filterOut(dbItems);
            var catItems = dbItems.filter(function(object){
                if(categoryField === 'regions'){
                    var objectRegions = (typeof object[categoryField] !== 'undefined' && object[categoryField].length) ? object[categoryField] : [];
                    var objectRegionIDs = objectRegions.map(function(region){
                        return region.id;
                    });
                    return objectRegionIDs.indexOf(category.id) !== -1;
                } else {
                    return object[categoryField] == category.value;
                }
            });
            category.counter = catItems.length;
            if(categoryField === 'regions'){
                category.label = category.title;
                category.value = category.id;
            }

            return {category: category, items: catItems};
        });
        // Filter out empty categories, if needed:
        if(_this.mapsvg.options.menu.categories.hideEmpty){
            items = items.filter(function(item){
               return item.category.counter > 0;
            });
        }
    // If categories are not enabled then just get the list of DB items:
    } else {
        items = this.database.getLoaded();
    }

    try{
        this.contentView.html( this.templates.main({'items': items}) );
    }catch (err) {
        console.error('MapSVG: Error in the "Directory item" template');
        console.error(err);
    }
    if(items.length == 0){
        this.contentView.html('<div class="mapsvg-no-results">'+this.mapsvg.getData().options.menu.noResultsText+'</div>');
    }

    if(_this.mapsvg.options.menu.categories.on){
        if(_this.mapsvg.options.menu.categories.collapse && items.length > 1){
            this.contentView.find('.mapsvg-category-block').addClass('collapsed');
        } else if(_this.mapsvg.options.menu.categories.collapse && items.length === 1){
            this.contentView.find('.mapsvg-category-item').addClass('active');
            this.contentView.find('.mapsvg-category-block').addClass('active');
            var panel = this.contentView.find('.mapsvg-category-block');
            if(panel.length > 0){
                panel[0].style.maxHeight = panel.scrollHeight + "px";
            }
        } else if(!_this.mapsvg.options.menu.categories.collapse){
            this.contentView.find('.mapsvg-category-item').addClass('active');
            this.contentView.find('.mapsvg-category-block').addClass('active');
            var panels = this.contentView.find('.mapsvg-category-block');
            panels.each(function(i,panel){
                panel.style.maxHeight = panel.scrollHeight + "px";
            });
        }
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

/**
 * @deprecated
 */
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

/**
 * Toggles view between map and directory on mobile devices
 * @param {bool} on - If "true", directory is shown and map is hidden, and vice-versa.
 */
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

/**
 * Adds pagination buttons to the directory.
 * @param {string} pager - HTML string with the buttons
 */
MapSVG.DirectoryController.prototype.addPagination = function(pager){
    this.contentView.append('<div class="mapsvg-pagination-container"></div>');
    this.contentView.find('.mapsvg-pagination-container').html(pager);
};

})( jQuery );
(function( $ ) {
/**
 * FiltersController class adds filters for the database.
 * @param options
 * @constructor
 */
MapSVG.FiltersController = function(options){
    MapSVG.DetailsController.call(this, options);
};
MapSVG.extend(MapSVG.FiltersController, MapSVG.DetailsController);

})( jQuery );

(function( $ ) {
/**
 * Location class. Contains lat/lon, x/y coordinates, image, address and marker.
 * @param {object} options
 * @param {MapSVG.Map} mapsvg
 * @constructor
 *
 * @example
 * var location = new MapSVG.Location({
 *   lat: 55.22,
 *   lng: 64.12,
 *   img: "/path/to/image.png"
 *  });
 *
 * var marker = new MapSVG.Marker({
 *   location: location,
 *   mapsvg: mapsvgInstance
 * });
 *
 */
MapSVG.Location  = function (options, mapsvg){
    this.img = options.img;
    this.setImage(this.img);
    this.lat = options.lat!==undefined ? parseFloat(options.lat) : undefined;
    this.lng = options.lng!==undefined ? parseFloat(options.lng) : undefined;
    this.x   = options.x!==undefined ? parseFloat(options.x) : undefined;
    this.y   = options.y!==undefined ? parseFloat(options.y) : undefined;
    this.address = new MapSVG.LocationAddress(options.address);
};

/**
 * Sets image of the location
 * @param {string} img - URL of the image
 * @private
 */
MapSVG.Location.prototype.setImage = function(img){
    var src = img.split('/').pop();
    if(img.indexOf('uploads')!==-1){
        src = 'uploads/'+src;
    }
    this.img = src;
};

/**
 * Sets geo-coordinates of the location
 * @param {Array} latlng - [lat,lon]
 */
MapSVG.Location.prototype.setLatLng = function(latlng){
    this.lat = latlng.lat;
    this.lng = latlng.lng;
};
/**
 * @private
 */
MapSVG.Location.prototype.__defineGetter__('markerImageUrl', function(){
    if ((this.img && this.img.indexOf('uploads/') === 0)){
        return MapSVG.urls.uploads+'markers/'+(this.img.replace('uploads/',''));
    } else {
        return MapSVG.urls.root+'markers/'+(this.img || '_pin_default.png');
    }
});

/**
 * Returns JSON of the Location
 * @returns {{img: *, lat: *, lng: *, x: *, y: *, address: *}}
 */
MapSVG.Location.prototype.toJSON = function(){
    return {
        img: this.img,
        lat: this.lat,
        lng: this.lng,
        x: this.x,
        y: this.y,
        address: this.address
    };
};

})( jQuery );
(function( $ ) {
/**
 * LocationAddress class stores address field from Google Geocoding service
 * @param {object} fields
 * @constructor
 */
MapSVG.LocationAddress = function(fields){
    for(var i in fields){
        this[i] = fields[i];
    }
};

MapSVG.LocationAddress.prototype.__defineGetter__('state', function(){
    return this.country_short === 'US' ? this.administrative_area_level_1 : null;
});
MapSVG.LocationAddress.prototype.__defineGetter__('state_short', function(){
    return this.country_short === 'US' ? this.administrative_area_level_1_short : null;
});

MapSVG.LocationAddress.prototype.__defineGetter__('county', function(){
    return this.country_short === 'US' ? this.administrative_area_level_2 : null;
});

MapSVG.LocationAddress.prototype.__defineGetter__('zip', function(){
    return this.postal_code;
});

})( jQuery );
(function( $ ) {
/**
 * MapSVG.Map class. Creates a new map inside of the given HTML container, which is typically a DIV element.
 * @constructor
 *
 * @param {string} id - ID of the map container
 * @param {MapSVG.MapOptions} options - map settings
 *
 * @example
 * // Create the map in <div id="my-container-id"></div>:
 * var mapsvg = new MapSVG.Map("my-container-id", {
 *   source: "/path/to/map.svg",
 *   zoom: {on: true},
 *   scroll: {on: true}
 * });
 *
 * // Get map instance by container ID
 * var mapsvg = MapSVG.getById("my-container-id");
 *
 * // Get map instance by index number
 * var mapsvg_1 = MapSVG.get(0); // first map on the page
 * var mapsvg_2 = MapSVG.get(1); // second map.
 *
 */
MapSVG.Map = function(id, options){

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
    this.defaults = {
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
            clustersHoverText: "",
            markers: {
                base:      {opacity:100, saturation: 100},
                hovered:   {opacity:100, saturation: 100},
                unhovered: {opacity:40, saturation: 100},
                active:    {opacity:100, saturation: 100},
                inactive:  {opacity:40, saturation: 100},
            }
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
        zoom                : {on: true, limit: [0,10], delta: 2, buttons: {on: true, location: 'right'}, mousewheel: true, fingers: true},
        scroll              : {on: true, limit: false, background: false, spacebar: false},
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
            '  // var regions = mapsvg.regions;\n'+
            '  // var dbObjects = mapsvg.database.getLoaded();\n'+
            '}',
            'beforeLoad' : 'function(){\n' +
            '  // var mapsvg = this;\n'+
            '  // var settings = mapsvg.options;\n' +
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
        css: "#mapsvg-map-%id% .mapsvg-tooltip {\n\n}\n" +
        "#mapsvg-map-%id% .mapsvg-popover {\n\n}\n" +
        "#mapsvg-map-%id% .mapsvg-details-container {\n\n}\n" +
        "#mapsvg-map-%id% .mapsvg-directory-item {\n\n}\n" +
        "#mapsvg-map-%id% .mapsvg-region-label {\n" +
        "  /* background-color: rgba(255,255,255,.6); */\n" +
        "  font-size: 11px;\n" +
        "  padding: 3px 5px;\n" +
        "  border-radius: 4px;\n" +
        "}\n" +
        "#mapsvg-map-%id% .mapsvg-marker-label {\n" +
        "  padding: 3px 5px;\n" +
        "  /*\n" +
        "  border-radius: 4px;\n" +
        "  background-color: white;\n" +
        "  margin-top: -4px;\n" +
        "  */\n}\n" +
        "#mapsvg-map-%id% .mapsvg-filters-wrap {\n\n}\n" +
        "\n\n\n\n\n\n"
        ,
        templates           : {
            popoverRegion: defRegionTemplate,
            popoverMarker: defDBTemplate,
            tooltipRegion: '<!-- Region fields are available in this template -->\n{{id}} - {{title}}',
            tooltipMarker: '<!-- DB Object fields are available in this template -->\n{{title}}',
            directoryItem: dirItemItemTemplate,
            directoryCategoryItem: '<!-- Available fields: "label", "value", "counter" -->\n<span class="mapsvg-category-label">{{label}}</span>\n<span class="mapsvg-category-counter">{{counter}}</span>\n<span class="mapsvg-chevron"></span>',
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
            clearButtonText: 'Clear all',
            clearButton: false,
            padding: ''
        },
        menu                : {
            on: false,
            hideOnMobile: true,
            location: 'leftSidebar',
            locationMobile: 'leftSidebar',
            search: false,
            containerId: '',
            searchPlaceholder: "Search...",
            searchFallback: false,
            source: 'database',
            showFirst: 'map',
            showMapOnClick: true,
            minHeight: '400',
            sortBy: 'id',
            sortDirection: 'desc',
            categories: {
                on: false,
                groupBy: '',
                hideEmpty: true,
                collapse: true,
                collapseOther: true
            },
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
                    selectRegion: false
                }
            },
            region: {
                click: {
                    addIdToUrl: false,
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
                },
                hover: {
                    centerOnMarker: false
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
        fitMarkers: false,
        fitMarkersOnStart: false,
        controls: {
            location: 'right',
            zoom: true,
            zoomReset: false,
            userLocation: false
        }
    };

    // Default marker style
    this.markerOptions = {'src': MapSVG.urls.root+'markers/pin1_red.png'};

    this.init(options, $("#"+id));
};

MapSVG.Map.prototype = {

    /**
     * @deprecated
     * @private
     */
    setMarkersClickAsLink: function(){
        var _this = this;
        this.database.loadSchema().done(function(schema){
            if(schema){
                schema.forEach(function(field){
                    if(field.type == 'marker'){
                        _this.markerIsLink = MapSVG.parseBoolean(field.isLink);
                        _this.markerUrlField = field.urlField;
                    }
                });
            }
        });
    },
    /**
     * Sets visiblity switches for groups of SVG objects
     * @private
     */
    setGroups: function(){
        var _this = this;
        _this.groups = _this.options.groups;
        _this.groups.forEach(function(g){
            g.objects && g.objects.length && g.objects.forEach(function(obj){
                _this.$svg.find('#'+obj.value).toggle(g.visible);
            });
        });
    },
    /**
     * Sets visibility switches
     * @param {object} options - Options
     * @private
     */
    setLayersControl : function(options){
        var _this = this;
        if(options)
            $.extend(true, _this.options.layersControl, options);
        if(_this.options.layersControl.on){
            if(!_this.$layersControl){
                _this.$layersControl = $('<div class="mapsvg-layers-control"></div>');
                _this.$layersControlLabel = $('<div class="mapsvg-layers-label"></div>').appendTo(_this.$layersControl);
                _this.$layersControlListWrap = $('<div class="mapsvg-layers-list-wrap"></div>').appendTo(_this.$layersControl);
                _this.$layersControlListNano = $('<div class="nano"></div>').appendTo(_this.$layersControlListWrap);
                _this.$layersControlList = $('<div class="mapsvg-layers-list nano-content"></div>').appendTo(_this.$layersControlListNano);
                _this.$layersControl.appendTo(_this.$mapContainer);
            }
            _this.$layersControl.show();
            _this.$layersControlLabel.html(_this.options.layersControl.label);
            _this.$layersControlList.empty();
            _this.$layersControl.removeClass('mapsvg-top-left mapsvg-top-right mapsvg-bottom-left mapsvg-bottom-right');
            _this.$layersControl.addClass('mapsvg-'+_this.options.layersControl.position);
            if(_this.options.menu.on && !_this.options.menu.customContainer && _this.options.layersControl.position.indexOf('left')!==-1){
                _this.$layersControl.css('left', _this.options.menu.width);
            }
            // if(!_this.options.layersControl.expanded && !_this.$layersControl.hasClass('closed')){
            //     _this.$layersControl.addClass('closed')
            // }
            _this.$layersControl.css({'max-height': _this.options.layersControl.maxHeight});

            _this.options.groups.forEach(function(g){
                var item = $('<div class="mapsvg-layers-item" data-group-id="'+g.id+'">' +
                    '<input type="checkbox" class="ios8-switch ios8-switch-sm" '+(g.visible?'checked':'')+' />' +
                    '<label>'+g.title+'</label> ' +
                    '</div>').appendTo(_this.$layersControlList);
            });
            _this.$layersControlListNano.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
            _this.$layersControl.off();
            _this.$layersControl.on('click','.mapsvg-layers-item', function() {
                var id = $(this).data('group-id');
                var input = $(this).find('input');
                input.prop('checked', !input.prop('checked'));
                _this.options.groups.forEach(function(g){
                    if(g.id === id) g.visible = !g.visible;
                });
                _this.setGroups();
            });
            _this.$layersControlLabel.on('click',function(){
                _this.$layersControl.toggleClass('closed');
            });

            _this.$layersControl.toggleClass('closed',!_this.options.layersControl.expanded);

        }else{
            _this.$layersControl && _this.$layersControl.hide();
        }

    },
    /**
     * @deprecated
     * @private
     */
    setFloorsControl : function(options){
        var _this = this;
        if(options)
            $.extend(true, _this.options.floorsControl, options);
        if(_this.options.floorsControl.on){
            if(!_this.$floorsControl){
                _this.$floorsControl = $('<div class="mapsvg-floors-control"></div>');
                _this.$floorsControlLabel = $('<div class="mapsvg-floors-label"></div>').appendTo(_this.$floorsControl);
                _this.$floorsControlListWrap = $('<div class="mapsvg-floors-list-wrap"></div>').appendTo(_this.$floorsControl);
                _this.$floorsControlListNano = $('<div class="nano"></div>').appendTo(_this.$floorsControlListWrap);
                _this.$floorsControlList = $('<div class="mapsvg-floors-list nano-content"></div>').appendTo(_this.$floorsControlListNano);
                _this.$floorsControl.appendTo(_this.$map);
            }
            _this.$floorsControlLabel.html(_this.options.floorsControl.label);
            _this.$floorsControlList.empty();
            _this.$floorsControl.removeClass('mapsvg-top-left mapsvg-top-right mapsvg-bottom-left mapsvg-bottom-right')
            _this.$floorsControl.addClass('mapsvg-'+_this.options.floorsControl.position);
            // if(!_this.options.floorsControl.expanded && !_this.$floorsControl.hasClass('closed')){
            //     _this.$floorsControl.addClass('closed')
            // }
            _this.$floorsControl.css({'max-height': _this.options.floorsControl.maxHeight});

            _this.options.floors.forEach(function(f){
                var item = $('<div class="mapsvg-floors-item" data-floor-id="'+f.object_id+'">' +
                    '<label>'+f.title+'</label> ' +
                    '</div>').appendTo(_this.$floorsControlList);
            });
            _this.$floorsControlListNano.nanoScroller({preventPageScrolling: true, iOSNativeScrolling: true});
            _this.$floorsControl.off();
            _this.$floorsControl.on('click','.mapsvg-floors-item', function() {
                var id = $(this).data('floor-id');
                _this.setFloor(id);
            });
            _this.$floorsControlLabel.on('click',function(){
                _this.$floorsControl.toggleClass('closed');
            });

            _this.$floorsControl.toggleClass('closed',!_this.options.floorsControl.expanded);

        }else{
            _this.$floorsControl && _this.$floorsControl.hide();
        }

    },
    /**
     * @deprecated
     * @private
     */
    setFloor: function(id){
        var _this = this;
        _this.$floorsControl.find('.mapsvg-floors-item').toggleClass('active',false);
        _this.$floorsControl.find('[data-floor-id="'+id+'"]').toggleClass('active',true);
        _this.options.floors.forEach(function(floor){
            _this.$svg.find('#'+floor.object_id).hide();
        });
        var floor = _this.$svg.find('#'+id);
        floor.show();
        floor = new MapObject(floor, _this);
        var bbox = floor.getBBox();
        _this._viewBox = bbox;
        _this.setViewBox(_this._viewBox);
        _this.zoomLevels = null;
        _this.zoomLevel = 1;
        _this.setZoom();
        floor = null;
    },
    /**
     * @deprecated
     * @private
     */
    getGroupSelectOptions: function(){
        var _this = this;
        var id;
        var optionGroups = [];
        var options = [];
        var options2 = [];

        _this.$svg.find('g').each(function(index){
            if(id = $(this)[0].getAttribute('id')){
                // _this.groups.push(id);
                options.push({label: id, value: id});
            }
        });
        optionGroups.push({title: "SVG Layers / Groups", options: options});

        _this.$svg.find('path,ellipse,circle,polyline,polygon,rectangle,img,text').each(function(index){
            if(id = $(this)[0].getAttribute('id')){
                // _this.groups.push(id);
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

        // If "Load DB on start" is off then
        // don't load directory to prevent 'no records found' message from appearing
        if(_this.options.menu.source === 'database' && !_this.database.loaded){
            return false;
        }
        if(_this.options.menu.on){
            _this.controllers.directory.loadItemsToDirectory();
        }
        _this.setPagination();
    },
    /**
     * Adds pagination controls to the map and/or directory
     */
    setPagination : function(){
        var _this = this;

        _this.$pagerMap && _this.$pagerMap.empty().remove();
        _this.$pagerDir && _this.$pagerDir.empty().remove();

        if(_this.options.database.pagination.on && _this.options.database.pagination.perpage !== 0){

            _this.$directory.toggleClass('mapsvg-with-pagination', (['directory','both'].indexOf(_this.options.database.pagination.showIn)!==-1));
            _this.$map.toggleClass('mapsvg-with-pagination', (['map','both'].indexOf(_this.options.database.pagination.showIn)!==-1));

            if(_this.options.menu.on){
                _this.$pagerDir = _this.getPagination();
                _this.controllers.directory.addPagination(_this.$pagerDir);
            }
            _this.$pagerMap = _this.getPagination();
            _this.$map.append(_this.$pagerMap);
        }
    },
    /**
     * Generates HTML with pagination buttons and attaches callback event on button click
     * @param {function} callback - Callback function that should be called on click on a next/prev page button
     */
    getPagination : function(callback){
        var _this = this;

        // pager && (pager.empty().remove());
        var pager = $('<nav class="mapsvg-pagination"><ul class="pager"><!--<li class="mapsvg-first"><a href="#">First</a></li>--><li class="mapsvg-prev"><a href="#">&larr; '+_this.options.database.pagination.prev+' '+_this.options.database.pagination.perpage+'</a></li><li class="mapsvg-next"><a href="#">'+_this.options.database.pagination.next+' '+_this.options.database.pagination.perpage+' &rarr;</a></li><!--<li class="mapsvg-last"><a href="#">Last</a></li>--></ul></nav>');

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
            _this.database.getAll({page: _this.database.query.page+1}).done(function(){
                callback && callback();
            });
        }).on('click','.mapsvg-prev:not(.disabled)',function(e){
            e.preventDefault();
            if(_this.database.onFirstPage())
                return;
            _this.database.getAll({page: _this.database.query.page-1}).done(function(){
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
    /**
     * Deletes all markers from the map
     */
    deleteMarkers: function(){
        var _this = this;
        while(_this.markers.length){
            _this.markers[0].delete && _this.markers[0].delete();
        }
    },
    /**
     * Deletes all markers from the map
     */
    deleteClusters: function(){
        var _this = this;
        if(_this.markersClusters)
            while(_this.markersClusters.length){
                _this.markersClusters[0].destroy && _this.markersClusters[0].destroy();
                _this.markersClusters.splice(0,1);
            }
        _this.clusters = {};
        _this.markersClusters = [];
        _this.markersClustersDict = {};
    },
    /**
     * Adds locations from the database to the map - as markers or as clusters if clustering is enabled.
     * @private
     */
    addLocations: function(){

        var _this = this;

        _this.firstDataLoad = _this.firstDataLoad === undefined;

        var dbObjects  = this.database.getLoaded();
        var locationField = this.database.getSchemaFieldByType('location');
        if(!locationField){
            return false;
        }
        locationField = locationField.name;

        if(locationField) {

            if(_this.firstDataLoad){
                _this.setMarkerImagesDependency();
            }

            _this.deleteMarkers();
            _this.deleteClusters();

            _this.clusters = {};
            _this.clustersByZoom = [];
            _this.markersClusters = [];
            _this.markersClustersDict = {};



            if(dbObjects && dbObjects.length > 0){
                dbObjects.forEach(function(object){
                    if(object[locationField] && !(object[locationField] instanceof Location) && ((object[locationField].lat && object[locationField].lng) || (object[locationField].x && object[locationField].y))){
                        object[locationField].img = _this.getMarkerImage(object, object[locationField]);
                        object[locationField] = new MapSVG.Location(object[locationField]);
                        if((object[locationField].lat && object[locationField].lng) || (object[locationField].x && object[locationField].y)){
                            var marker = new MapSVG.Marker({
                                location: object[locationField],
                                object: object,
                                mapsvg: _this
                            });
                        }
                    }
                });
                if(_this.options.clustering.on){
                    _this.startClusterizer();
                } else {
                    dbObjects.forEach(function(object){
                        if(object.location && object.location.marker){
                            _this.markerAdd(object.location.marker);
                        }
                    });
                    _this.mayBeFitMarkers();
                }
            }
        }
    },
    /**
     * Stores a set of markers/clusters for a certain zoom level for later use.
     * This method gets called by a worker thread that calculates markers/clusters for all zoom levels.
     * @param zoomLevel
     * @param clusters
     * @private
     */
    addClustersFromWorker : function(zoomLevel, clusters){

        var _this = this;

        _this.clustersByZoom[zoomLevel] = [];
        for(var cell in clusters){
            var markers = clusters[cell].markers.map(function(marker){
                // todo check if location & marker exists
                return _this.database.getLoadedObject(marker.id).location.marker;
            });
            _this.clustersByZoom[zoomLevel].push( new MapSVG.MarkersCluster({
                mapsvg: _this,
                markers: markers,
                x: clusters[cell].x,
                y: clusters[cell].y,
                cellX: clusters[cell].cellX,
                cellY: clusters[cell].cellY
            }));

        }
        if(_this.zoomLevel === zoomLevel){
            _this.clusterizeMarkers();
        }
    },
    /**
     * Starts clusterizer worker in a separate thread
     * @private
     */
    startClusterizer : function(){

        var _this = this;

        if(!_this.database || _this.database.getLoaded().length === 0){
            return;
        }
        var locationField = _this.database.getSchemaFieldByType('location');
        if(!locationField){
            return false;
        }


        if(!_this.clusterizerWorker){

            _this.clusterizerWorker = new Worker(MapSVG.urls.root+"js/clustering.js");

            // Receive messages from postMessage() calls in the Worker
            _this.clusterizerWorker.onmessage = function(evt) {
                if(evt.data.clusters){
                    _this.addClustersFromWorker(evt.data.zoomLevel, evt.data.clusters);
                }
            };
        }

        // Pass data to the WebWorker
        _this.clusterizerWorker.postMessage({
            objects: _this.database.getLoaded().filter(function(o){
                return o.location && o.location.marker;
            }).map(function(o){
                return {
                    id: o.id,
                    x: o.location.marker.x,
                    y: o.location.marker.y
                }
            }),
            cellSize: 50,
            mapWidth: _this.$map.width(),
            zoomLevels: _this.zoomLevels,
            zoomLevel: _this.zoomLevel,
            zoomDelta: _this.zoomDelta,
            svgViewBox: _this.svgDefault.viewBox
        });

        _this.on("zoom", function(){
            _this.clusterizerWorker.postMessage({
                message: "zoom",
                zoomLevel: _this.zoomLevel
            });
        });

    },
    /**
     * Starts clustering markers on the map
     * @property {boolean} skipFitMarkers - Don't do "fit markers" action. Used to prevent fitting markers
     * on changinh zoom level.
     * @private
     */
    clusterizeMarkers : function(skipFitMarkers){
        var _this = this;

        _this.layers.markers.children().each(function(i,obj){
            $(obj).detach();
        });
        _this.markers = [];
        _this.markersDict = {};
        _this.markersClusters = [];
        _this.markersClustersDict = {};

        // _this.$map.addClass('no-transitions-markers');


        _this.clustersByZoom && _this.clustersByZoom[_this.zoomLevel] && _this.clustersByZoom[_this.zoomLevel].forEach(function(cluster){

            // Don't clusterize on google maps with zoom level >= 17
            if(_this.options.googleMaps.on && _this.googleMaps.map && _this.googleMaps.map.getZoom()>=17){
                _this.markerAdd(cluster.markers[0]);

            } else {
                if(cluster.markers.length > 1) {
                    _this.markersClusterAdd(cluster);
                } else {
                    _this.markerAdd(cluster.markers[0]);
                }
            }
        });

        if(_this.editingMarker){
            _this.markerAdd(_this.editingMarker);
        }

        if(!skipFitMarkers){
            _this.mayBeFitMarkers();
        }

        if(_this.options.labelsMarkers.on){
            _this.setLabelsMarkers();
        }

        // _this.$map.removeClass('no-transitions-markers');
    },
    /**
     * Returns URL of the mapsvg.css file
     * @returns {string} URL
     * @private
     */
    getCssUrl: function(){
        var _this = this;
        return MapSVG.urls.root+'css/mapsvg.css';
    },
    /**
     * Checks if the map is geo-calibrated
     * @returns {boolean}
     */
    isGeo: function(){
        var _this = this;
        return _this.mapIsGeo;
    },
    /**
     * Converts a string to function
     * @param string
     * @returns {function|object} Function or object {error: "error text"}
     * @private
     *
     */
    functionFromString: function(string){
        var _this = this;
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
    /**
     * Returns map options.
     * @param {bool} forTemplate - If options should be formatted for use in a template
     * @param {bool} forWeb - If options should be formatted for use in the web
     * @param {object} optionsDelta - used by backend WP admin only, part of options from the previous view mode
     * @returns {object}
     */
    getOptions: function(forTemplate, forWeb, optionsDelta) {

        var _this = this;

        var options = $.extend(true, {}, _this.options);

        $.extend(true, options, optionsDelta);

        options.viewBox = _this._viewBox;
        options.filtersSchema = _this.filtersSchema.getSchema();
        if (options.filtersSchema) {
            options.filtersSchema.forEach(function (field) {
                if (field.type == 'distance') {
                    field.value = '';
                }
            });
        }

        delete options.markers;

        if (forTemplate){
            options.svgFilename = options.source.split('/').pop();
            options.svgFiles = MapSVG.svgFiles;
        }

        if(forWeb)
            $.each(options,function(key,val){
                if(JSON.stringify(val)==JSON.stringify(_this.defaults[key]))
                    delete options[key];
            });
        delete options.backend;

        return options;
    },

    /**
     * Adds an event handler
     * @param {string} event - Event name
     * @param callback - Callback function
     *
     * @example
     * mapsvg.on("zoom", function(){
     *   console.log("The map was zoomed!");
     * })
     */
    on: function(event, callback) {
        var _this = this;
        this.lastChangeTime = Date.now();
        if (!_this.events[event])
            _this.events[event] = [];
        _this.events[event].push(callback);
    },
    /**
     * Removes an event handler
     * @param {string} event - Event name
     *
     * @example
     * mapsvg.off("zoom");
     */
    off: function(event) {
        var _this = this;
        for(var eventName in _this.events){
            if(_this.events[eventName] && _this.events[eventName].length > 0){
                if(eventName.indexOf(event) === 0 && event.length <= eventName){
                    _this.events[eventName] = [];
                }
            }
        }
    },
    /**
     * Triggers an event
     * @param {string} event - Event name
     */
    trigger: function(event){
        var _this = this;
        for(var eventName in _this.events){
            if(_this.events[eventName] && _this.events[eventName].length > 0){
                var eventNameReal = eventName.split('.')[0];
                if(eventNameReal.indexOf(event)===0){
                    _this.events[eventName].forEach(function(callback){
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
    /**
     * Sets even handlers
     * @param {object} functions - Callbacks {eventName: function, eventName: function, ...}
     */
    setEvents: function(functions){
        var _this = this;
        _this.events = _this.events || {};

        for (var eventName in functions) {
            if (typeof functions[eventName] === 'string') {
                var func = functions[eventName] != "" ? this.functionFromString(functions[eventName]) : null;

                if (func && !func.error && !(func instanceof TypeError || func instanceof SyntaxError) )
                    _this.events[eventName] = func;
                else
                    _this.events[eventName] = null;
            } else if(typeof functions[eventName] === 'function') {
                _this.events[eventName] = functions[eventName];
            }
            if(eventName.indexOf('directory')!==-1){
                var event = eventName.split('.')[0];
                if(_this.controllers && _this.controllers.directory){
                    _this.controllers.directory.events[event] = _this.events[eventName];
                }
            }
        }

        $.extend(true, _this.options.events, functions);
    },
    /**
     * Sets map actions that should be performed on Region click. Marker click, etc.
     * @param options
     *
     * @example
     * mapsvg.setActions({
     *   "click.region": {
     *     showPopover: true,
     *     showDetails: false
     *   }
     * })
     */
    setActions : function(options){
        var _this = this;
        $.extend(true, _this.options.actions, options);
    },
    /**
     * Sets Details View options
     * @param {object} options - Options
     */
    setDetailsView: function(options){
        var _this = this;

        options = options || _this.options.detailsView;
        $.extend(true, _this.options.detailsView, options);


        // Since 5.0.0: no top/near locations.
        if(_this.options.detailsView.location === 'top' && _this.options.menu.position === 'left'){
            _this.options.detailsView.location = 'leftSidebar';
        } else if(_this.options.detailsView.location === 'top' && _this.options.menu.position === 'right'){
            _this.options.detailsView.location = 'rightSidebar';
        }
        if(_this.options.detailsView.location === 'near'){
            _this.options.detailsView.location = 'map';
        }

        if(!_this.$details){
            _this.$details   = $('<div class="mapsvg-details-container"></div>');
        }

        _this.$details.toggleClass('mapsvg-details-container-relative', !(MapSVG.isPhone && _this.options.detailsView.mobileFullscreen) && !_this.shouldBeScrollable(_this.options.detailsView.location));


        if(_this.options.detailsView.location === 'custom'){
            $('#'+_this.options.detailsView.containerId).append(_this.$details);
        } else {
            if(MapSVG.isPhone && _this.options.detailsView.mobileFullscreen){
                $('body').append(_this.$details);
                _this.$details.addClass('mapsvg-container-fullscreen')
            }else{
                var $cont = '$'+_this.options.detailsView.location;
                _this[$cont].append(_this.$details);
            }
            if(_this.options.detailsView.margin){
                _this.$details.css('margin',_this.options.detailsView.margin);
            }
            _this.$details.css('width',_this.options.detailsView.width);
        }
    },
    /**
     * Sets mobile view options
     * @param options
     */
    setMobileView: function(options){
        var _this = this;
        $.extend(true, _this.options.mobileView, options);
    },
    /**
     * Attaches DB Objects to Regions
     * @param object
     */
    attachDataToRegions: function(object){
        var _this = this;
        _this.regions.forEach(function(region){
            region.objects = [];
        });
        _this.database.getLoaded().forEach(function(obj, index){
            if(obj.regions && obj.regions.length){
                if(typeof obj.regions === 'object'){
                    obj.regions.forEach(function(region){
                        var r = _this.getRegion(region.id);
                        if(r)
                            r.objects.push(obj);
                    });
                }
            }
        });

    },
    /**
     * Sets templates body
     * @param {object} templates - Key:value pairs of template names and HTML content
     */
    setTemplates: function(templates){
        var _this = this;
        _this.templates = _this.templates || {};
        for (var name in templates){
            if(name != undefined){
                _this.options.templates[name] = templates[name];
                var t = _this.options.templates[name];
                if(name == 'directoryItem' || name == 'directoryCategoryItem'){
                    var dirItemTemplate = _this.options.templates.directoryItem;
                    t = '{{#each items}}<div id="mapsvg-directory-item-{{id}}" class="mapsvg-directory-item" data-object-id="{{id}}">'+dirItemTemplate+'</div>{{/each}}';
                    if(_this.options.menu.categories && _this.options.menu.categories.on && _this.options.menu.categories.groupBy){
                        var t2 = _this.options.templates['directoryCategoryItem'];
                        t = '{{#each items}}{{#with category}}<div id="mapsvg-category-item-{{value}}" class="mapsvg-category-item" data-category-value="{{value}}">'+t2+'</div><div class="mapsvg-category-block" data-category-id="{{value}}">{{/with}}'+t+'</div>{{/each}}';
                    }
                    name = 'directory';
                }


                try {
                    _this.templates[name] = Handlebars.compile(t, {strict: false});
                } catch(err) {
                    console.error(err);
                    _this.templates[name] = Handlebars.compile("", {strict: false});
                }

                if(_this.editMode && ((name == 'directory' || name == 'directoryCategoryItem') && _this.controllers && _this.controllers.directory)){
                    _this.controllers.directory.templates.main = _this.templates[name];
                    _this.loadDirectory();
                }
            }
        }
    },
    /**
     * Sets status of a Region
     * @param {Region} region
     * @param {number } status
     */
    setRegionStatus : function(region, status){
        var _this = this;
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
    /**
     * Updates map settings.
     * @param {object} options - Map options
     *
     * @example
     * mapsvg.update({
     *   popovers: {on: true},
     *   colors: {
     *     background: "red"
     *   }
     * });
     */
    update : function(options){
        var _this = this;
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
                        _this.options.regions[id] = _this.options.regions[id] || {};
                        _this.options.regions[id].disabled = region.disabled;
                    }
                });
            }else if (key == "markers"){
                $.each(options.markers, function(id, markerOptions){
                    var marker = _this.getMarker(id);
                    marker && marker.update(markerOptions);
                });
            }else{
                var setter = 'set'+MapSVG.ucfirst(key);
                if (typeof _this[setter] == 'function')
                    this[setter](options[key]);
                else{
                    _this.options[key] = options[key];
                }
            }
        }
    },
    /**
     * Sets map title
     * @param {string} title - Map title
     * @private
     */
    setTitle: function(title){
        var _this = this;
        title && (_this.options.title = title);
    },
    /**
     * Adds MapSVG add-ons
     * @param extension
     * @private
     */
    setExtension: function(extension){
        var _this = this;
        if(extension){
            _this.options.extension = extension;
        }else{
            delete _this.options.extension;
        }
    },
    /**
     * Disables/enables redirection by link on click on a region or marker
     * when "Go to link..." feature is enabled
     * Used to prevent redirection in the map editor.
     * @param {bool} on - true (enable redirection) of false (disable)
     */
    setDisableLinks: function(on){
        var _this = this;
        on = MapSVG.parseBoolean(on);
        if(on){
            _this.$map.on('click.a.mapsvg','a',function(e){
                e.preventDefault();
            });
        }else{
            _this.$map.off('click.a.mapsvg');
        }
        _this.disableLinks = on;
    },
    /**
     * Sets loading text message
     * @param {string} val - "Loading map..." text
     */
    setLoadingText: function(val){
        var _this = this;
        _this.options.loadingText = val
    },
    /**
     * Enable or disable lock aspect ratio. Used in Map Editor.
     * @param {bool} onoff
     * @private
     */
    setLockAspectRatio: function(onoff){
        var _this = this;
        _this.options.lockAspectRatio =  MapSVG.parseBoolean(onoff);
    },
    /**
     * @deprecated
     * @private
     */
    setOnClick: function(h){
        var _this = this;
        _this.options.onClick = h || undefined;
    },
    /**
     * @deprecated
     * @private
     */
    setMouseOver: function(h){
        var _this = this;
        _this.options.mouseOver = h || undefined;
    },
    /**
     * @deprecated
     * @private
     */
    setMouseOut: function(h){
        var _this = this;
        _this.options.mouseOut = h || undefined;
    },
    /**
     * @deprecated
     * @private
     */
    setBeforeLoad: function(h){
        var _this = this;
        _this.options.beforeLoad = h || undefined;
    },
    /**
     * @deprecated
     * @private
     */
    setAfterLoad: function(h){
        var _this = this;
        _this.options.afterLoad = h || undefined;
    },
    /**
     * @deprecated
     * @private
     */
    setPopoverShown: function(h){
        var _this = this;
        _this.options.popoverShown = h || undefined;
    },
    /**
     * Sets callback for marker click. Used in Map Editor to show an object editing window.
     * @private
     */
    setMarkerEditHandler : function(handler){
        var _this = this;
        _this.markerEditHandler = handler;
    },
    /**
     * Sets the Region field that is used for choropleth map
     * @param {string} field - Region's field name
     */
    setRegionChoroplethField : function(field){
        var _this = this;
        _this.options.regionChoroplethField = field;
        _this.redrawGauge();
    },
    /**
     * Sets callback function that is called on click on a Region.
     * Used in the Map Editor on click on a Region in the "Edit regions" map mode.
     * @param {function} handler
     */
    setRegionEditHandler : function(handler){

        var _this = this;

        _this.regionEditHandler = handler;
    },
    /**
     * Disables all Regions if "true" is passed.
     * @param {bool} on
     */
    setDisableAll: function(on){

        var _this = this;

        on = MapSVG.parseBoolean(on);
        $.extend(true, _this.options, {disableAll:on});
        _this.$map.toggleClass('mapsvg-disabled-regions', on);
    },
    /**
     * @deprecated
     * @private
     */
    setRegionStatuses : function(_statuses){

        var _this = this;

        _this.options.regionStatuses = _statuses;
        var colors = {};
        for(var status in _this.options.regionStatuses){
            colors[status] = _this.options.regionStatuses[status].color.length ? _this.options.regionStatuses[status].color : undefined;
        }
        _this.setColors({status: colors});
    },
    /**
     * @deprecated
     * @private
     */
    setColorsIgnore : function(val){

        var _this = this;

        _this.options.colorsIgnore = MapSVG.parseBoolean(val);
        _this.regionsRedrawColors();
    },
    /**
     * Adds # hash at the beginning of HEX color value
     * @param {String} color
     * @returns {String}
     * @private
     */
    fixColorHash: function(color){
        var hexColorNoHash = new RegExp(/^([0-9a-f]{3}|[0-9a-f]{6})$/i);
        if(color && color.match(hexColorNoHash) !== null){
            color = '#'+color;
        }
        return color;
    },
    /**
     * Sets color settings (background, regions, containers, etc.)
     * @param {object} colors
     *
     * @example
     * mapsvg.setColors({
     *   background: "#EEEEEE",
     *   hover: "10",
     *   selected: "20",
     *   leftSidebar: "#FF2233",
     *   detailsView: "#FFFFFF"
     * });
     */
    setColors : function(colors){

        var _this = this;

        for(var i in colors){
            if(i === 'status'){
                for(var s in colors[i]){
                    _this.fixColorHash(colors[i][s]);
                }
            } else {
                if(typeof colors[i] == 'string'){
                    _this.fixColorHash(colors[i]);
                }
            }
        }

        $.extend(true, _this.options, {colors:colors});

        if(colors && colors.status)
            _this.options.colors.status = colors.status;

        if(_this.options.colors.markers){
            for(var z in _this.options.colors.markers){
                for(var x in _this.options.colors.markers[z]){
                    _this.options.colors.markers[z][x] = parseInt(_this.options.colors.markers[z][x]);
                }
            }
        }

        if(_this.options.colors.background)
            _this.$map.css({'background': _this.options.colors.background});
        if(_this.options.colors.hover)
            _this.options.colors.hover = (_this.options.colors.hover == ""+parseInt(_this.options.colors.hover)) ? parseInt(_this.options.colors.hover) : _this.options.colors.hover;
        if(_this.options.colors.selected)
            _this.options.colors.selected = (_this.options.colors.selected == ""+parseInt(_this.options.colors.selected)) ? parseInt(_this.options.colors.selected) : _this.options.colors.selected;

        _this.$leftSidebar.css({'background-color': _this.options.colors.leftSidebar});
        _this.$rightSidebar.css({'background-color': _this.options.colors.rightSidebar});
        _this.$header.css({'background-color': _this.options.colors.header});
        _this.$footer.css({'background-color': _this.options.colors.footer});


        if(_this.$details && _this.options.colors.detailsView !== undefined){
            _this.$details.css({'background-color': _this.options.colors.detailsView});
        }
        if(_this.$directory && _this.options.colors.directory !== undefined){
            _this.$directory.css({'background-color': _this.options.colors.directory});
        }
        if(_this.$filtersModal && _this.options.colors.modalFilters !== undefined){
            _this.$filtersModal.css({'background-color': _this.options.colors.modalFilters});
        }

        if(_this.$filters && _this.options.colors.directorySearch){
            _this.$filters.css({
                'background-color': _this.options.colors.directorySearch
            })
        }else if(_this.$filters) {
            _this.$filters.css({
                'background-color': ''
            })
        }

        _this.clusterCSS = _this.clusterCSS || $('<style></style>').appendTo('body');
        var css = '';
        if(_this.options.colors.clusters){
            css += "background-color: "+_this.options.colors.clusters+";";
        }
        if(_this.options.colors.clustersBorders){
            css += "border-color: "+_this.options.colors.clustersBorders+";";
        }
        if(_this.options.colors.clustersText){
            css += "color: "+_this.options.colors.clustersText+";";
        }
        _this.clusterCSS.html(".mapsvg-marker-cluster {"+css+"}");

        _this.clusterHoverCSS = _this.clusterHoverCSS || $('<style></style>').appendTo('head');
        var cssHover = "";
        if(_this.options.colors.clustersHover){
            cssHover += "background-color: "+_this.options.colors.clustersHover+";";
        }
        if(_this.options.colors.clustersHoverBorders){
            cssHover += "border-color: "+_this.options.colors.clustersHoverBorders+";";
        }
        if(_this.options.colors.clustersHoverText){
            cssHover += "color: "+_this.options.colors.clustersHoverText+";";
        }
        _this.clusterHoverCSS.html(".mapsvg-marker-cluster:hover {"+cssHover+"}");

        _this.markersCSS = _this.markersCSS || $('<style></style>').appendTo('head');
        var markerCssText = '.mapsvg-with-marker-active .mapsvg-marker {\n' +
            '  opacity: '+_this.options.colors.markers.inactive.opacity/100+';\n' +
            '  -webkit-filter: grayscale('+(100-_this.options.colors.markers.inactive.saturation)+'%);\n' +
            '  filter: grayscale('+(100-_this.options.colors.markers.inactive.saturation)+'%);\n' +
            '}\n' +
            '.mapsvg-with-marker-active .mapsvg-marker-active {\n' +
            '  opacity: '+_this.options.colors.markers.active.opacity/100+';\n' +
            '  -webkit-filter: grayscale('+(100-_this.options.colors.markers.active.saturation)+'%);\n' +
            '  filter: grayscale('+(100-_this.options.colors.markers.active.saturation)+'%);\n' +
            '}\n' +
            '.mapsvg-with-marker-hover .mapsvg-marker {\n' +
            '  opacity: '+_this.options.colors.markers.unhovered.opacity/100+';\n' +
            '  -webkit-filter: grayscale('+(100-_this.options.colors.markers.unhovered.saturation)+'%);\n' +
            '  filter: grayscale('+(100-_this.options.colors.markers.unhovered.saturation)+'%);\n' +
            '}\n' +
            '.mapsvg-with-marker-hover .mapsvg-marker-hover {\n' +
            '  opacity: '+_this.options.colors.markers.hovered.opacity/100+';\n' +
            '  -webkit-filter: grayscale('+(100-_this.options.colors.markers.hovered.saturation)+'%);\n' +
            '  filter: grayscale('+(100-_this.options.colors.markers.hovered.saturation)+'%);\n' +
            '}\n';
        _this.markersCSS.html(markerCssText);



        $.each(_this.options.colors,function(key, color){
            if(color === null || color == "")
                delete _this.options.colors[key];
        });

        _this.regionsRedrawColors();
    },
    /**
     * Sets tooltips options.
     * @param {object} options
     * @example
     * mapsvg.setTooltips({
     *   on: true,
     *   position: "bottom-left",
     *   maxWidth: "300"
     * })
     */
    setTooltips : function (options) {

        var _this = this;

        if(options.on !== undefined)
            options.on = MapSVG.parseBoolean(options.on);

        $.extend(true, _this.options, {tooltips: options});

        _this.tooltip = _this.tooltip || {posOriginal: {}, posShifted: {}, posShiftedPrev: {}, mirror: {}};
        _this.tooltip.posOriginal    = {};
        _this.tooltip.posShifted     = {};
        _this.tooltip.posShiftedPrev = {};
        _this.tooltip.mirror         = {};


        if(_this.tooltip.container){
            _this.tooltip.container[0].className = _this.tooltip.container[0].className.replace(/(^|\s)mapsvg-tt-\S+/g, '');
        }else{
            _this.tooltip.container = $('<div />').addClass('mapsvg-tooltip');
            _this.$map.append(_this.tooltip.container);
        }


        var ex = _this.options.tooltips.position.split('-');
        if(ex[0].indexOf('top')!=-1 || ex[0].indexOf('bottom')!=-1){
            _this.tooltip.posOriginal.topbottom = ex[0];
        }
        if(ex[0].indexOf('left')!=-1 || ex[0].indexOf('right')!=-1){
            _this.tooltip.posOriginal.leftright = ex[0];
        }
        if(ex[1]){
            _this.tooltip.posOriginal.leftright = ex[1];
        }

        var event = 'mousemove.tooltip.mapsvg-'+_this.$map.attr('id');
        _this.tooltip.container.addClass('mapsvg-tt-'+_this.options.tooltips.position);

        _this.tooltip.container.css({'min-width': _this.options.tooltips.minWidth+'px', 'max-width': _this.options.tooltips.maxWidth+'px'});

        $('body').off(event).on(event, function(e) {

            MapSVG.mouse = MapSVG.mouseCoords(e);

            _this.tooltip.container[0].style.left = (e.clientX + $(window).scrollLeft() - _this.$map.offset().left) +'px';
            _this.tooltip.container[0].style.top  = (e.clientY + $(window).scrollTop()  - _this.$map.offset().top)  +'px';

            var m = {x: e.clientX + $(window).scrollLeft(), y: e.clientY + $(window).scrollTop()};

            var tbbox = _this.tooltip.container[0].getBoundingClientRect();
            var mbbox = _this.$wrap[0].getBoundingClientRect();
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

            if(_this.tooltip.mirror.top || _this.tooltip.mirror.bottom){
                // may be cancel mirroring
                if(_this.tooltip.mirror.top && m.y > _this.tooltip.mirror.top){
                    _this.tooltip.mirror.top    = false;
                    delete _this.tooltip.posShifted.topbottom;
                }else if(_this.tooltip.mirror.bottom && m.y < _this.tooltip.mirror.bottom){
                    _this.tooltip.mirror.bottom = false;
                    delete _this.tooltip.posShifted.topbottom;
                }
            }else{
                // may be need mirroring

                if(tbbox.bottom < mbbox.top + tbbox.height){
                    _this.tooltip.posShifted.topbottom = 'bottom';
                    _this.tooltip.mirror.top    = m.y;
                }else if(tbbox.top > mbbox.bottom - tbbox.height){
                    _this.tooltip.posShifted.topbottom = 'top';
                    _this.tooltip.mirror.bottom = m.y;
                }
            }

            if(_this.tooltip.mirror.right || _this.tooltip.mirror.left){
                // may be cancel mirroring

                if(_this.tooltip.mirror.left && m.x > _this.tooltip.mirror.left){
                    _this.tooltip.mirror.left  = false;
                    delete _this.tooltip.posShifted.leftright;
                }else if(_this.tooltip.mirror.right && m.x < _this.tooltip.mirror.right){
                    _this.tooltip.mirror.right = false;
                    delete _this.tooltip.posShifted.leftright;
                }
            }else{
                // may be need mirroring
                if(tbbox.right < mbbox.left + tbbox.width){
                    _this.tooltip.posShifted.leftright = 'right';
                    _this.tooltip.mirror.left = m.x;
                }else if(tbbox.left > mbbox.right - tbbox.width){
                    _this.tooltip.posShifted.leftright = 'left';
                    _this.tooltip.mirror.right = m.x;
                }
            }

            var pos  = $.extend({}, _this.tooltip.posOriginal, _this.tooltip.posShifted);
            var _pos = [];
            pos.topbottom && _pos.push(pos.topbottom);
            pos.leftright && _pos.push(pos.leftright);
            pos = _pos.join('-');

            if(_this.tooltip.posShifted.topbottom!=_this.tooltip.posOriginal.topbottom  || _this.tooltip.posShifted.leftright!=_this.tooltip.posOriginal.leftright){
                _this.tooltip.container[0].className = _this.tooltip.container[0].className.replace(/(^|\s)mapsvg-tt-\S+/g, '');
                _this.tooltip.container.addClass('mapsvg-tt-'+pos);
                _this.tooltip.posShiftedPrev = pos;
            }
        });
    },
    /**
     * Sets popover options.
     * @param {object} options
     * @example
     * mapsvg.setPopovers({
     *   on: true,
     *   width: 300, // pixels
     *   maxWidth: 70, // percents of map container
     *   maxHeight: 70, // percents of map container
     *   mobileFullscreen: true
     * });
     */
    setPopovers : function (options){

        var _this = this;

        if(options.on !== undefined)
            options.on = MapSVG.parseBoolean(options.on);

        $.extend(_this.options.popovers, options);

        if(!_this.$popover) {
            _this.$popover = $('<div />').addClass('mapsvg-popover');
            _this.layers.popovers.append(_this.$popover);
        }
        _this.$popover.css({
            width: _this.options.popovers.width + (_this.options.popovers.width == 'auto' ? '' : 'px'),
            'max-width': _this.options.popovers.maxWidth + '%',
            'max-height': _this.options.popovers.maxHeight*_this.$wrap.outerHeight()/100+'px'
        });

        if(_this.options.popovers.mobileFullscreen && MapSVG.isPhone){
            $('body').toggleClass('mapsvg-fullscreen-popovers', true);
            _this.$popover.appendTo('body');
        }
    },
    /**
     * Sets region prefix
     * @param {string} prefix
     * @private
     */
    setRegionPrefix : function(prefix){

        var _this = this;

        _this.options.regionPrefix = prefix;
    },
    /**
     * Sets initial viewbox
     * @param {array} v - [x,y,width,height]
     * @private
     */
    setInitialViewBox : function(v){

        var _this = this;

        if(typeof v == 'string')
            v = v.trim().split(' ');
        _this._viewBox = [parseFloat(v[0]), parseFloat(v[1]), parseFloat(v[2]), parseFloat(v[3])];
        if(_this.options.googleMaps.on){
            _this.options.googleMaps.center = _this.googleMaps.map.getCenter().toJSON();
            _this.options.googleMaps.zoom = _this.googleMaps.map.getZoom();
        }
        _this.zoomLevel = 0;
    },
    /**
     * Sets viewbox on map start and calculates initial width/height ratio
     * @private
     */
    setViewBoxOnStart : function(){

        var _this = this;

        _this.viewBoxFull = _this.svgDefault.viewBox;
        _this.viewBoxFake = _this.viewBox;
        _this.whRatioFull = _this.viewBoxFull[2] / _this.viewBox[2];
        _this.$svg[0].setAttribute('viewBox',_this.viewBoxFull.join(' '));
        if((MapSVG.device.ios || MapSVG.device.android) && _this.svgDefault.viewBox[2]>1500){
            _this.iosDownscaleFactor = _this.svgDefault.viewBox[2] > 9999 ? 100 : 10;
            _this.$svg.css({'width': _this.svgDefault.viewBox[2]/ _this.iosDownscaleFactor });
        } else {
            _this.$svg.css({'width': _this.svgDefault.viewBox[2]});
        }
        _this.vbstart = 1;
    },
    /**
     * Sets map viewbox
     * @param {array} v - [x,y,width,height]
     * @param {bool} skipAdjustments - if false is passed, map doesn't get redrawn
     */
    setViewBox : function(v,skipAdjustments){

        var _this = this;

        if(typeof v == 'string'){
            v = v.trim().split(' ');
        }
        var d = (v && v.length==4) ? v : _this.svgDefault.viewBox;
        var isZooming = parseFloat(d[2]) != _this.viewBox[2] || parseFloat(d[3]) != _this.viewBox[3];
        _this.viewBox = [parseFloat(d[0]), parseFloat(d[1]), parseFloat(d[2]), parseFloat(d[3])];
        _this.whRatio = _this.viewBox[2] / _this.viewBox[3];

        !_this.vbstart && _this.setViewBoxOnStart();

        if(!v){
            _this._viewBox = _this.viewBox;
            _this._scale = 1;
        }

        var p = _this.options.padding;

        if(p.top){
            _this.viewBox[1] -= p.top;
            _this.viewBox[3] += p.top;
        }
        if(p.right){
            _this.viewBox[2] += p.right;
        }
        if(p.bottom){
            _this.viewBox[3] += p.bottom;
        }
        if(p.left){
            _this.viewBox[0] -= p.left;
            _this.viewBox[2] += p.left;
        }

        _this.scale = _this.getScale();
        _this.superScale = _this.whRatioFull*_this.svgDefault.viewBox[2]/_this.viewBox[2];

        var w = _this.svgDefault.viewBox[2] / _this.$map.width();
        _this.superScale = _this.superScale / w;
        if((MapSVG.device.ios || MapSVG.device.android) && _this.svgDefault.viewBox[2]>1500){
            _this.superScale *= _this.iosDownscaleFactor;
        }

        _this.scroll = _this.scroll || {};
        _this.scroll.tx = Math.round((_this.svgDefault.viewBox[0]-_this.viewBox[0])*_this.scale);
        _this.scroll.ty = Math.round((_this.svgDefault.viewBox[1]-_this.viewBox[1])*_this.scale);


        if(isZooming) {
            if(!_this.options.googleMaps.on){
                _this.enableMarkersAnimation();
            }
            // _this.throttle(_this.enableMarkersAnimation, 400, _this);
        }

        _this.$scrollpane.css({
            'transform': 'translate('+_this.scroll.tx+'px,'+_this.scroll.ty+'px)'
        });
        _this.$svg.css({
            'transform': 'scale('+_this.superScale+')'
        });

        if(isZooming && !skipAdjustments){
            _this.updateSize();
        }

        if(isZooming){
            if(!_this.options.googleMaps.on) {
                setTimeout(function(){
                        _this.disableMarkersAnimation()
                }, 400);
            }
            if(_this.options.clustering.on){
                _this.throttle(_this.clusterizeOnZoom, 400, _this);
            } else {
                _this.trigger('zoom');
            }
        }

        return true;
    },
    /**
     * Turns on marker animations
     * @private
     */
    enableMarkersAnimation: function() {
        this.$map.removeClass('no-transitions-markers')
    },
    /**
     * Turns off marker animations
     * @private
     */
    disableMarkersAnimation: function() {
        var _this = this;
        _this.$map.addClass('no-transitions-markers')
    },
    /**
     * Event handler that clusterizes markers on zoom
     * @event
     * @private
     */

    clusterizeOnZoom: function(){

        var _this = this;

        if(this.options.googleMaps.on && this.googleMaps.map && this.zoomDelta) {
            this.zoomLevel = this.googleMaps.map.getZoom() - this.zoomDelta;
        }
        this.trigger('zoom');
        this.clusterizeMarkers(true);
    },
    /**
     * Delays method execution.
     * For example, it can be used in search input fields to prevent sending an ajax request on each key press.
     * @param {function} method
     * @param {number} delay
     * @param {object} scope
     * @param {array} params
     * @private
     */
    throttle: function(method, delay, scope, params) {

        var _this = this;

        clearTimeout(method._tId);
        method._tId = setTimeout(function(){
            method.call(scope, params);
        }, delay);
    },
    /**
     * @deprecated
     * @param {array }bbox
     * @private
     */
    setViewBoxReal : function(bbox){

        var _this = this;

        _this.viewBoxFull = bbox;
        _this.viewBoxFake = bbox;
        _this.whRatioFull = _this.viewBoxFull[2] / _this.viewBox[2];

        _this.viewBox = bbox;
        _this.svgDefault.viewBox = _this.viewBox;
        _this.viewBoxFull = bbox;
        _this.viewBoxFake = _this.viewBox;
        _this.whRatioFull = _this.viewBoxFull[2] / _this.viewBox[2];
        _this.$svg[0].setAttribute('viewBox',_this.viewBoxFull.join(' '));

        _this.scale   = _this.getScale();

        var tx = (-bbox[0])*_this.scale;
        var ty = (-bbox[1])*_this.scale;
        _this.$layers.css({
            'transform': 'translate('+tx+'px,'+ty+'px)'
        });
        _this.zoomLevel = 0;
        _this.setViewBox(bbox);
    },
    /**
     * Sets SVG viewBox by Google Maps bounds.
     * Used to overlay SVG map on Google Map.
     */
    setViewBoxByGoogleMapBounds : function(){

        var _this = this;

        var googleMapBounds = _this.googleMaps.map.getBounds();
        if(!googleMapBounds) return;
        var googleMapBoundsJSON = googleMapBounds.toJSON();

        if(googleMapBoundsJSON.west==-180 && googleMapBoundsJSON.east==180){
            var center = _this.googleMaps.map.getCenter().toJSON();
        }
        var ne = [googleMapBounds.getNorthEast().lat(), googleMapBounds.getNorthEast().lng()];
        var sw = [googleMapBounds.getSouthWest().lat(), googleMapBounds.getSouthWest().lng()];

        var xyNE = _this.convertGeoToSVG(ne);
        var xySW = _this.convertGeoToSVG(sw);

        // check if map on border between 180/-180 longitude
        if(xyNE[0] < xySW[0]){
            var mapPointsWidth = (_this.svgDefault.viewBox[2] / _this.mapLonDelta) * 360;
            xySW[0] = -(mapPointsWidth - xySW[0]);
        }

        var width  = xyNE[0] - xySW[0];
        var height = xySW[1] - xyNE[1];
        _this.setViewBox([xySW[0], xyNE[1], width, height]);

    },
    /**
     * Redraws the map.
     * Must be called when the map is shown after being hidden.
     */
    redraw: function(){

        var _this = this;

        if(MapSVG.browser.ie){
            _this.$svg.css({height: _this.svgDefault.viewBox[3]});
        }

        if(_this.options.googleMaps.on && _this.googleMaps.map){
            // var center = _this.googleMaps.map.getCenter();
            google.maps.event.trigger(_this.googleMaps.map, 'resize');
            // _this.googleMaps.map.setCenter(center);
            // _this.setViewBoxByGoogleMapBounds();
        }else{
            _this.setViewBox(_this.viewBox);
        }
        _this.$popover && _this.$popover.css({
            'max-height': _this.options.popovers.maxHeight*_this.$wrap.outerHeight()/100+'px'
        });
        if(this.controllers && this.controllers.directory){
            this.controllers.directory.updateTopShift();
            this.controllers.directory.updateScroll();
        }
        _this.updateSize();
    },
    /**
     * Sets map padding.
     * @param {number} options - Padding in pixels
     */
    setPadding: function(options){

        var _this = this;

        options = options || _this.options.padding;
        for(var i in options){
            options[i] = options[i] ? parseInt(options[i]) : 0;
        }
        $.extend(_this.options.padding, options);

        _this.setViewBox();
        _this.trigger('sizeChange');
    },
    /**
     * Sets map size.
     * Can accept both or just 1 parameter - width or height. The missing parameter will be calcualted.
     * @param {number} width
     * @param {number} height
     * @param {bool} responsive
     * @returns {number[]} - [width, height]
     */
    setSize : function( width, height, responsive ){

        var _this = this;

        // Convert strings to numbers
        _this.options.width      = parseFloat(width);
        _this.options.height     = parseFloat(height);
        _this.options.responsive = responsive!=null && responsive!=undefined  ? MapSVG.parseBoolean(responsive) : _this.options.responsive;

        // Calculate width and height
        if ((!_this.options.width && !_this.options.height)){
            _this.options.width	 = _this.svgDefault.width;
            _this.options.height = _this.svgDefault.height;
        }else if (!_this.options.width && _this.options.height){
            _this.options.width	 = parseInt(_this.options.height * _this.svgDefault.width / _this.svgDefault.height);
        }else if (_this.options.width && !_this.options.height){
            _this.options.height = parseInt(_this.options.width * _this.svgDefault.height/_this.svgDefault.width);
        }

        _this.whRatio      = _this.options.width / _this.options.height;
        _this.scale        = _this.getScale();

        _this.setResponsive(responsive);

        if(_this.markers)
            _this.markersAdjustPosition();
        if(_this.options.labelsRegions.on){
            _this.labelsRegionsAdjustPosition();
        }


        return [_this.options.width, _this.options.height];
    },
    /**
     * Sets map responsiveness on/off. When map is responsive, it takes the full width of the parent container.
     * @param {bool} on
     */
    setResponsive : function(on){

        var _this = this;

        on = on != undefined ? MapSVG.parseBoolean(on) : _this.options.responsive;

        _this.$map.css({
            'width': '100%',
            'height': '0',
            'padding-bottom': (_this.viewBox[3]*100/_this.viewBox[2])+'%'
        });
        if(on){
            _this.$wrap.css({
                'width': '100%',
                'height': 'auto'
            });
        }else{
            _this.$wrap.css({
                'width': _this.options.width+'px',
                'height': _this.options.height+'px'
            });
        }
        $.extend(true, _this.options, {responsive: on});

        if(!_this.resizeSensor){
            _this.resizeSensor = new MapSVG.ResizeSensor(_this.$map[0], function(){
                _this.redraw();
            });
        }

        _this.redraw();
    },
    /**
     * Sets map scroll options.
     * @param {object} options - scroll options
     * @param {bool} skipEvents - used by Map Editor to prevent re-setting event handlers.
     * @example
     * mapsvg.setScroll({
     *   on: true,
     *   limit: false // limit scroll to map bounds
     * });
     */
    setScroll : function(options, skipEvents){

        var _this = this;

        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        options.limit != undefined && (options.limit = MapSVG.parseBoolean(options.limit));
        $.extend(true, _this.options, {scroll: options});
        !skipEvents && _this.setEventHandlers();
    },
    /**
     * Sets map zoom options.
     * @param {object} options - zoom options
     * @example
     * mapsvg.setZoom({
     *   on: true,
     *   mousewheel: true,
     *   limit: [-5,10], // allow -5 zoom steps back and +20 zoom steps up from initial position.
     * });
     */
    setZoom : function (options){

        var _this = this;

        options = options || {};
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        options.fingers != undefined && (options.fingers = MapSVG.parseBoolean(options.fingers));
        options.mousewheel != undefined && (options.mousewheel = MapSVG.parseBoolean(options.mousewheel));

        // delta = 1.2 changed to delta = 2 since introducing Google Maps + smooth zoom
        options.delta = 2;

        // options.delta && (options.delta = parseFloat(options.delta));

        if(options.limit){
            if(typeof options.limit == 'string')
                options.limit = options.limit.split(';');
            options.limit = [parseInt(options.limit[0]),parseInt(options.limit[1])];
        }
        if(!_this.zoomLevels){
            _this.setZoomLevels();
        }

        $.extend(true, _this.options, {zoom: options});
        //(options.buttons && options.buttons.on) && (options.buttons.on = MapSVG.parseBoolean(options.buttons.on));
        _this.$map.off('mousewheel.mapsvg');


        if(_this.options.zoom.mousewheel){
            // var lastZoomTime = 0;
            // var zoomTimeDelta = 0;

            if(MapSVG.browser.firefox){
                _this.firefoxScroll = { insideIframe: false };

                _this.$map.on('mouseenter', function() {
                    _this.firefoxScroll.insideIframe = true;
                    _this.firefoxScroll.scrollX = window.scrollX;
                    _this.firefoxScroll.scrollY = window.scrollY;
                }).on('mouseleave', function() {
                    _this.firefoxScroll.insideIframe = false;
                });

                $(document).scroll(function() {
                    if (_this.firefoxScroll.insideIframe)
                        window.scrollTo(_this.firefoxScroll.scrollX, _this.firefoxScroll.scrollY);
                });
            }

            _this.$map.on('mousewheel.mapsvg',function(event, delta, deltaX, deltaY) {
                if($(event.target).hasClass('mapsvg-popover') || $(event.target).closest('.mapsvg-popover').length)
                    return;
                // zoomTimeDelta = Date.now() - lastZoomTime;
                // lastZoomTime = Date.now();
                event.preventDefault();
                var d = delta > 0 ? 1 : -1;
                var m = MapSVG.mouseCoords(event);
                m.x = m.x - _this.$svg.offset().left;
                m.y = m.y - _this.$svg.offset().top;

                var center = _this.convertPixelToSVG([m.x, m.y]);
                d > 0 ? _this.zoomIn(center) : _this.zoomOut(center);
                // _this.zoom(d);
                return false;
            });
        }
        _this.canZoom = true;
    },
    /**
     * Sets map control buttons.
     * @param {object} options - control button options
     * @example
     * mapsvg.setControls({
     *   location: 'right',
     *   zoom: true,
     *   zoomReset: true,
     *   userLocation: true
     * });
     */
    setControls : function (options){

        var _this = this;

        options = options || {};
        $.extend(true, _this.options, {controls: options});
        _this.options.controls.zoom = MapSVG.parseBoolean(_this.options.controls.zoom);
        _this.options.controls.zoomReset = MapSVG.parseBoolean(_this.options.controls.zoomReset);
        _this.options.controls.userLocation = MapSVG.parseBoolean(_this.options.controls.userLocation);

        var loc = _this.options.controls.location || 'right';

        if(!_this.$controls){

            var buttons = $('<div />').addClass('mapsvg-buttons');

            var zoomGroup = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);
            var zoomIn = $('<div />').addClass('mapsvg-btn-map mapsvg-in');

            zoomIn.on('touchend click',function(e){
                if(e.cancelable){
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.zoomIn();
            });

            var zoomOut = $('<div />').addClass('mapsvg-btn-map mapsvg-out');
            zoomOut.on('touchend click',function(e){
                if(e.cancelable){
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.zoomOut();
            });
            zoomGroup.append(zoomIn).append(zoomOut);

            var location = $('<div />').addClass('mapsvg-btn-map mapsvg-btn-location');
            location.on('touchend click',function(e){
                if(e.cancelable){
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.showUserLocation(function(location){
                    if(_this.options.scroll.on){
                        _this.centerOn(location.marker);
                    }
                });
            });

            var userLocationIcon = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 447.342 447.342" style="enable-background:new 0 0 447.342 447.342;" xml:space="preserve"><path d="M443.537,3.805c-3.84-3.84-9.686-4.893-14.625-2.613L7.553,195.239c-4.827,2.215-7.807,7.153-7.535,12.459 c0.254,5.305,3.727,9.908,8.762,11.63l129.476,44.289c21.349,7.314,38.125,24.089,45.438,45.438l44.321,129.509 c1.72,5.018,6.325,8.491,11.63,8.762c5.306,0.271,10.244-2.725,12.458-7.535L446.15,18.429 C448.428,13.491,447.377,7.644,443.537,3.805z"/></svg>';
            location.html(userLocationIcon);

            var locationGroup = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);
            locationGroup.append(location);

            var zoomResetIcon = '<svg height="14px" version="1.1" viewBox="0 0 14 14" width="14px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#000000" transform="translate(-215.000000, -257.000000)"><g id="fullscreen" transform="translate(215.000000, 257.000000)"><path d="M2,9 L0,9 L0,14 L5,14 L5,12 L2,12 L2,9 L2,9 Z M0,5 L2,5 L2,2 L5,2 L5,0 L0,0 L0,5 L0,5 Z M12,12 L9,12 L9,14 L14,14 L14,9 L12,9 L12,12 L12,12 Z M9,0 L9,2 L12,2 L12,5 L14,5 L14,0 L9,0 L9,0 Z" /></g></g></g></svg>';
            var zoomResetButton = $('<div />').html(zoomResetIcon).addClass('mapsvg-btn-map mapsvg-btn-zoom-reset');
            zoomResetButton.on('touchend click',function(e){
                if(e.cancelable){
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.viewBoxReset(true);
            });
            var zoomResetGroup = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);
            zoomResetGroup.append(zoomResetButton);


            _this.$controls = buttons;
            _this.controls = {
                zoom: zoomGroup,
                userLocation: locationGroup,
                zoomReset: zoomResetGroup
            };
            _this.$map.append(_this.$controls);
        }

        _this.controls.zoom.toggle(_this.options.controls.zoom);
        _this.controls.userLocation.toggle(_this.options.controls.userLocation);
        _this.controls.zoomReset.toggle(_this.options.controls.zoomReset);

        _this.$controls.removeClass('left');
        _this.$controls.removeClass('right');
        loc == 'right' && _this.$controls.addClass('right')
        ||
        loc == 'left' && _this.$controls.addClass('left');

        // (_this.options.controls.on &&  loc!='hide') ? _this.zoomButtons.show() : _this.zoomButtons.hide();
    },
    /**
     * Calcualtes map viewBox parameter for each zoom level
     * @private
     */
    setZoomLevels : function(){

        var _this = this;

        _this.zoomLevels = {};

        var _scale = 1;
        for(var i = 0; i <= 20; i++){
            _this.zoomLevels[i+''] = {
                _scale: _scale,
                viewBox: [0,0,_this._viewBox[2] /_scale, _this._viewBox[3] /_scale]
            };
            _scale = _scale * _this.options.zoom.delta;

        }
        _scale = 1;
        for(var i = 0; i >= -20; i--){
            _this.zoomLevels[i+''] = {
                _scale: _scale,
                viewBox: [0,0,_this._viewBox[2] /_scale, _this._viewBox[3] /_scale]
            };
            _scale = _scale / _this.options.zoom.delta;

        }
    },
    /**
     * Sets zoom buttons.
     * This method is called at the end of "setZoom" method.
     * @deprecated
     * @private
     */
    setZoomButtons : function(){

        var _this = this;

        var loc = _this.options.zoom.buttons.location || 'hide';
        if(!_this.zoomButtons){

            var buttons = $('<div />').addClass('mapsvg-buttons');
            var group = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);

            buttons.zoomIn = $('<div />').addClass('mapsvg-btn-map mapsvg-in');

            buttons.zoomIn.on('touchend click',function(e){
                e.stopPropagation();
                _this.zoomIn();
            });

            buttons.zoomOut = $('<div />').addClass('mapsvg-btn-map mapsvg-out');
            buttons.zoomOut.on('touchend click',function(e){
                e.stopPropagation();
                _this.zoomOut();
            });
            group.append(buttons.zoomIn).append(buttons.zoomOut);

            buttons.location = $('<div />').addClass('mapsvg-btn-map mapsvg-btn-location');
            buttons.location.on('touchend click',function(e){
                e.stopPropagation();
                _this.showUserLocation(function(){
                    if(_this.options.scroll.on){
                        _this.centerOn(location.marker);
                    }
                });
            });
            buttons.append(buttons.location);
            var userLocationButton = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 447.342 447.342" style="enable-background:new 0 0 447.342 447.342;" xml:space="preserve"><path d="M443.537,3.805c-3.84-3.84-9.686-4.893-14.625-2.613L7.553,195.239c-4.827,2.215-7.807,7.153-7.535,12.459 c0.254,5.305,3.727,9.908,8.762,11.63l129.476,44.289c21.349,7.314,38.125,24.089,45.438,45.438l44.321,129.509 c1.72,5.018,6.325,8.491,11.63,8.762c5.306,0.271,10.244-2.725,12.458-7.535L446.15,18.429 C448.428,13.491,447.377,7.644,443.537,3.805z"/></svg>';
            buttons.location.html(userLocationButton);

            _this.zoomButtons = buttons;
            _this.$map.append(_this.zoomButtons);
        }
        _this.zoomButtons.removeClass('left');
        _this.zoomButtons.removeClass('right');
        loc == 'right' && _this.zoomButtons.addClass('right')
        ||
        loc == 'left' && _this.zoomButtons.addClass('left');

        (_this.options.zoom.on &&  loc!='hide') ? _this.zoomButtons.show() : _this.zoomButtons.hide();
    },
    /**
     * Seems to be unused?
     * @deprecated
     * @param on
     * @private
     */
    setManualRegions : function(on){

        var _this = this;

        _this.options.manualRegions = MapSVG.parseBoolean(on);
    },
    /**
     * Sets mouse pointer cursor style on hover on regions / markers.
     * @param {string} type - "pointer" or "default"
     */
    setCursor : function(type){

        var _this = this;

        type = type == 'pointer' ? 'pointer' : 'default';
        _this.options.cursor = type;
        if(type == 'pointer')
            _this.$map.addClass('mapsvg-cursor-pointer');
        else
            _this.$map.removeClass('mapsvg-cursor-pointer');
    },
    /**
     * Enables/disables "multiselect" option that allows to select multiple regions.
     * @param {bool} on
     * @param {bool} deselect - If true, deselects currently selected Regions
     */
    setMultiSelect : function (on, deselect){

        var _this = this;

        _this.options.multiSelect = MapSVG.parseBoolean(on);
        if(deselect !== false)
            _this.deselectAllRegions();
    },
    /**
     * Sets choropleth map options
     * @param {object} options
     *
     * @example
     * mapsvg.setGauge({
     *   on: true,
     *   colors: {
     *     high: "#FF0000",
     *     low: "#00FF00"
     *   }
     *   labels: {
     *     high: "high",
     *     low: "low"
     *   }
     * });
     */
    setGauge : function (options){

        var _this = this;

        options = options || _this.options.gauge;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        $.extend(true, _this.options, {gauge: options});

        var needsRedraw = false;

        if(!_this.$gauge){
            _this.$gauge = {};
            _this.$gauge.gradient = $('<td>&nbsp;</td>').addClass('mapsvg-gauge-gradient');
            _this.setGaugeGradientCSS();
            _this.$gauge.container = $('<div />').addClass('mapsvg-gauge').hide();
            _this.$gauge.table = $('<table />');
            var tr = $('<tr />');
            _this.$gauge.labelLow = $('<td>'+_this.options.gauge.labels.low+'</td>');
            _this.$gauge.labelHigh = $('<td>'+_this.options.gauge.labels.high+'</td>');
            tr.append(_this.$gauge.labelLow);
            tr.append(_this.$gauge.gradient);
            tr.append(_this.$gauge.labelHigh);
            _this.$gauge.table.append(tr);
            _this.$gauge.container.append(_this.$gauge.table);
            _this.$map.append(_this.$gauge.container);
        }

        if (!_this.options.gauge.on && _this.$gauge.container.is(":visible")){
            _this.$gauge.container.hide();
            needsRedraw = true;
        }else if(_this.options.gauge.on && !_this.$gauge.container.is(":visible")){
            _this.$gauge.container.show();
            needsRedraw = true;
            _this.regionsDatabase.on('change',function(){
                _this.redrawGauge();
            });
        }

        if(options.colors){
            _this.options.gauge.colors.lowRGB = MapSVG.tinycolor(_this.options.gauge.colors.low).toRgb();
            _this.options.gauge.colors.highRGB = MapSVG.tinycolor(_this.options.gauge.colors.high).toRgb();
            _this.options.gauge.colors.diffRGB = {
                r: _this.options.gauge.colors.highRGB.r - _this.options.gauge.colors.lowRGB.r,
                g: _this.options.gauge.colors.highRGB.g - _this.options.gauge.colors.lowRGB.g,
                b: _this.options.gauge.colors.highRGB.b - _this.options.gauge.colors.lowRGB.b,
                a: _this.options.gauge.colors.highRGB.a - _this.options.gauge.colors.lowRGB.a
            };
            needsRedraw = true;
            _this.$gauge && _this.setGaugeGradientCSS();
        }

        if(options.labels){
            _this.$gauge.labelLow.html(_this.options.gauge.labels.low);
            _this.$gauge.labelHigh.html(_this.options.gauge.labels.high);
        }

        needsRedraw && _this.redrawGauge();
    },
    /**
     * Redraws choropleth map colors
     * @private
     */
    redrawGauge : function(){

        var _this = this;

        _this.updateGaugeMinMax();
        _this.regionsRedrawColors();
    },
    /**
     * Updates min/max values of Region choropleth fields
     * @private
     */
    updateGaugeMinMax : function(){

        var _this = this;

        _this.options.gauge.min = 0;
        _this.options.gauge.max = false;
        var values = [];
        _this.regions.forEach(function(r){
            var gauge = r.data && r.data[_this.options.regionChoroplethField];
            gauge != undefined && parseFloat(values.push(gauge));
        });
        if(values.length > 0){
            _this.options.gauge.min = values.length == 1 ? 0 : Math.min.apply(null,values);
            _this.options.gauge.max = Math.max.apply(null,values);
            _this.options.gauge.maxAdjusted = _this.options.gauge.max - _this.options.gauge.min;
        }
    },
    /**
     * Sets gradient for choropleth map
     * @private
     */
    setGaugeGradientCSS: function(){

        var _this = this;

        _this.$gauge.gradient.css({
            'background': _this.options.gauge.colors.low,
            'background': '-moz-linear-gradient(left, ' + _this.options.gauge.colors.low + ' 1%,' + _this.options.gauge.colors.high + ' 100%)',
            'background': '-webkit-gradient(linear, left top, right top, color-stop(1%,' + _this.options.gauge.colors.low + '), color-stop(100%,' + _this.options.gauge.colors.high + '))',
            'background': '-webkit-linear-gradient(left, ' + _this.options.gauge.colors.low + ' 1%,' + _this.options.gauge.colors.high + ' 100%)',
            'background': '-o-linear-gradient(left, ' + _this.options.gauge.colors.low + ' 1%,' + _this.options.gauge.colors.high + ' 100% 100%)',
            'background': '-ms-linear-gradient(left,  ' + _this.options.gauge.colors.low + ' 1%,' + _this.options.gauge.colors.high + ' 100% 100%)',
            'background': 'linear-gradient(to right,' + _this.options.gauge.colors.low + ' 1%,' + _this.options.gauge.colors.high + ' 100%)',
            'filter': 'progid:DXImageTransform.Microsoft.gradient( startColorstr="' + _this.options.gauge.colors.low + '", endColorstr="' + _this.options.gauge.colors.high + '",GradientType=1 )'
        });
    },
    /**
     * Sets custom map CSS.
     * CSS is added as <style>...</style> tag in the page <header>
     * @param {string} css
     * @private
     */
    setCss : function(css){

        var _this = this;

        _this.options.css = css || _this.options.css.replace(/%id%/g,this.id);
        _this.liveCSS = _this.liveCSS || $('<style></style>').appendTo('head');
        _this.liveCSS.html(_this.options.css);
    },
    /**
     * Sets filter options
     * @param {object} options
     * @example
     * mapsvg.setFilters{{
     *   on: true,
     *   location: "leftSidebar"
     * }};
     */
    setFilters : function(options){

        var _this = this;

        options                              = options || _this.options.filters;
        options.on != undefined              && (options.on = MapSVG.parseBoolean(options.on));
        options.hide != undefined          && (options.hide = MapSVG.parseBoolean(options.hide));
        $.extend(true, _this.options, {filters: options});

        var scrollable = false;

        if(['leftSidebar','rightSidebar','header','footer','custom','mapContainer'].indexOf(_this.options.filters.location)===-1){
            _this.options.filters.location = 'leftSidebar';
        }

        if(_this.options.filters.on){

            if(_this.formBuilder){
                _this.formBuilder.destroy();
            }

            if(!_this.$filters){
                _this.$filters = $('<div class="mapsvg-filters-wrap"></div>');
            }

            _this.$filters.empty();
            _this.$filters.show();

            _this.$filters.css({
                'background-color':_this.options.colors.directorySearch,
            });

            if(_this.$filtersModal){
                _this.$filtersModal.css({width: _this.options.filters.width});
            }

            if(_this.options.filters.location === 'custom'){
                _this.$filters.removeClass('mapsvg-filter-container-custom').addClass('mapsvg-filter-container-custom');
                if($('#'+_this.options.filters.containerId).length){
                    $('#'+_this.options.filters.containerId).append(_this.$filters);
                } else {
                    _this.$filters.hide();
                    console.error('MapSVG: filter container #'+_this.options.filters.containerId+' does not exists');
                }
            } else {
                if(MapSVG.isPhone){
                    _this.$header.append(_this.$filters);
                    _this.setContainers({header:{on:true}});
                }else{
                    var location = MapSVG.isPhone ? 'header' : _this.options.filters.location;
                    var $cont = '$'+location;
                    if(_this.options.menu.on && _this.controllers.directory && _this.options.menu.location === _this.options.filters.location){
                        _this.controllers.directory.view.find('.mapsvg-directory-filter-wrap').append(_this.$filters);
                        _this.controllers.directory.updateTopShift();
                    } else {
                        _this[$cont].append(_this.$filters);
                        _this.controllers.directory && _this.controllers.directory.updateTopShift();
                    }
                }
            }

            _this.loadFiltersController(_this.$filters, false);

            _this.updateFiltersState();
        } else {
            if(_this.$filters){
                _this.$filters.empty();
                _this.$filters.hide();
            }
        }

        if(_this.options.menu.on && _this.controllers.directory && _this.options.menu.location === _this.options.filters.location){
            _this.controllers.directory.updateTopShift();
        }
    },
    /**
     * Updates filters in drop-downs. For example, when region is clicked and data is filtered -
     * it selects corresponding region in the drop-down filter
     * @private
     */
    updateFiltersState : function(){

        var _this = this;

        _this.$filterTags && _this.$filterTags.empty();

        if(_this.options.filters && _this.options.filters.on || ( _this.database.query.filters && Object.keys(_this.database.query.filters).length > 0)){

            for(var field_name in _this.database.query.filters){
                var field_value = _this.database.query.filters[field_name];
                var _field_name = field_name;
                var filterField = _this.filtersSchema.getField(_field_name);

                if(_this.options.filters.on && filterField){
                    _this.$filters.find('select[data-parameter-name="'+_field_name+'"],radio[data-parameter-name="\'+_field_name+\'"]')
                        .data('ignoreSelect2Change', true)
                        .val(field_value)
                        .trigger('change');
                }else{
                        if(field_name == 'regions'){
                            // check if there is such filter. If there is then change its value
                            // if there isn't then add a tag with close button
                            _field_name = '';
                            field_value = _this.getRegion(field_value).title || field_value;
                        } else {
                            _field_name = filterField && filterField.label;
                        }
                        if(field_name !== 'distance'){
                            if(!_this.$filterTags){
                                _this.$filterTags = $('<div class="mapsvg-filter-tags"></div>');
                                // TODO If filtersmodeal on then dont append
                                if(_this.$filters){
                                    // _this.$filters.append(_this.$filterTags);
                                } else {
                                    if(_this.options.menu.on && _this.controllers.directory){
                                        _this.controllers.directory.toolbarView.append(_this.$filterTags);
                                        _this.controllers.directory.updateTopShift();
                                    } else {
                                        _this.$map.append(_this.$filterTags);
                                        if(_this.options.zoom.buttons.on){
                                            if(_this.options.layersControl.on){
                                                if(_this.options.layersControl.position=='top-left'){
                                                    _this.$filterTags.css({
                                                        right: 0,
                                                        bottom: 0
                                                    });
                                                } else {
                                                    _this.$filterTags.css({
                                                        bottom: 0
                                                    });
                                                }
                                            } else {
                                                if(_this.options.zoom.buttons.location=='left'){
                                                    _this.$filterTags.css({
                                                        right: 0
                                                    });
                                                }
                                            }
                                        }
                                    }
                                }

                                _this.$filterTags.on('click','.mapsvg-filter-delete',function(e){
                                    var filterField = $(this).data('filter');
                                    $(this).parent().remove();
                                    _this.database.query.filters[filterField] = null;
                                    delete _this.database.query.filters[filterField];
                                    _this.deselectAllRegions();
                                    _this.loadDataObjects();
                                });
                            }
                            _this.$filterTags.append('<div class="mapsvg-filter-tag">'+(_field_name?_field_name+': ':'')+field_value+' <span class="mapsvg-filter-delete" data-filter="'+field_name+'">×</span></div>');
                        }
                }
            }
            // this.view.addClass('mapsvg-with-filter');

        }else{
            // this.view.removeClass('mapsvg-with-filter');
        }
    },
    /**
     * Sets container options: leftSidebar, rightSidebar, header, footer.
     * @param {obejct} options
     *
     * @example
     * mapsvg.setContainers({
     *   leftSidebar: {
     *     on: true,
     *     width: "300px"
     *   }
     * });
     */
    setContainers : function(options){

        var _this = this;

        if(!_this.containersCreated){
            _this.$wrapAll      = $('<div class="mapsvg-wrap-all"></div>').attr('id', 'mapsvg-map-'+this.id).attr('data-map-id', this.id);
            _this.$wrap         = $('<div class="mapsvg-wrap"></div>');
            _this.$containers   = {};
            _this.$mapContainer = $('<div class="mapsvg-map-container"></div>');
            _this.$leftSidebar  = $('<div class="mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-left"></div>');
            _this.$rightSidebar = $('<div class="mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-right"></div>');
            _this.$header       = $('<div class="mapsvg-header mapsvg-top-container"></div>');
            _this.$footer       = $('<div class="mapsvg-footer mapsvg-top-container"></div>');

            _this.$wrapAll.insertBefore(_this.$map);

            _this.$wrapAll.append(_this.$header);
            _this.$wrapAll.append(_this.$wrap);
            _this.$wrapAll.append(_this.$footer);


            _this.$mapContainer.append(_this.$map);

            _this.$wrap.append(_this.$leftSidebar);
            _this.$wrap.append(_this.$mapContainer);
            _this.$wrap.append(_this.$rightSidebar);
            _this.containersCreated = true;
        }

        options = options || _this.options;
        for(var contName in options){

            if(options[contName].on !== undefined){
                options[contName].on = MapSVG.parseBoolean(options[contName].on);
            }


            $contName = '$'+contName;


            if(options[contName].width){
                if((typeof options[contName].width != 'string') || options[contName].width.indexOf('px')===-1 && options[contName].width.indexOf('%')===-1 && options[contName].width!=='auto'){
                    options[contName].width = options[contName].width+'px';
                }
                _this[$contName].css({'flex-basis': options[contName].width});
            }
            if(options[contName].height){
                if((typeof options[contName].height != 'string') || options[contName].height.indexOf('px')===-1 && options[contName].height.indexOf('%')===-1 && options[contName].height!=='auto'){
                    options[contName].height = options[contName].height+'px';
                }
                _this[$contName].css({'flex-basis': options[contName].height, height: options[contName].height});
            }

            $.extend(true, _this.options, {containers: options});

            var on = _this.options.containers[contName].on;

            if(MapSVG.isPhone && _this.options.menu.hideOnMobile && _this.options.menu.location === contName && ['leftSidebar','rightSidebar'].indexOf(contName) !==-1 ) {
                on = false;
            } else if(MapSVG.isPhone && _this.options.menu.location === 'custom' && ['leftSidebar','rightSidebar'].indexOf(contName) !==-1 ){
                // hide sidebars on mobiles if there's no directory in there because filters are always moved to header (or custom) on mobiles
                on = false;
                _this.$wrapAll.addClass('mapsvg-hide-map-list-buttons');
            } else if (MapSVG.isPhone && !_this.options.menu.hideOnMobile && _this.options.menu.location === contName && ['leftSidebar','rightSidebar'].indexOf(contName) !==-1 ){
                _this.$wrapAll.addClass('mapsvg-hide-map-list-buttons');
                _this.$wrapAll.addClass('mapsvg-directory-visible');
            }

            _this[$contName].toggle(on);


        }

        _this.setDetailsView();

    },
    /**
     * Checks if container should be scrollable
     * @param {string} container - mapContainer / leftSidebar / rightSidebar / custom / header / footer
     * @returns {boolean}
     * @private
     */
    shouldBeScrollable: function(container){

        var _this = this;

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
                if(_this.options.containers[container].height && _this.options.containers[container].height !== 'auto' &&  _this.options.containers[container].height !== '100%'){
                    return true;
                } else {
                    return false;
                }
                break;
            default: return false; break;
        }
    },
    /**
     * Sets directory options.
     * @alias setMenu
     * @param {object} options
     * @returns {*|void}
     *
     * @example
     * mapsvg.setDirectory({
     *   on: true,
     *   container: "leftSidebar"
     * });
     */
    setDirectory : function(options){

        var _this = this;

        return _this.setMenu(options);
    },
    /**
     * Sets menu (directory) options
     * @param options
     * @private
     */
    setMenu : function(options){

        var _this = this;

        options                               = options || _this.options.menu;
        options.on != undefined              && (options.on = MapSVG.parseBoolean(options.on));
        options.search != undefined          && (options.search = MapSVG.parseBoolean(options.search));
        options.showMapOnClick != undefined          && (options.showMapOnClick = MapSVG.parseBoolean(options.showMapOnClick));
        options.searchFallback != undefined          && (options.searchFallback = MapSVG.parseBoolean(options.searchFallback));
        options.customContainer != undefined && (options.customContainer = MapSVG.parseBoolean(options.customContainer));

        $.extend(true, _this.options, {menu: options});

        _this.controllers = _this.controllers || {};

        if(!_this.$directory){
            _this.$directory = $('<div class="mapsvg-directory"></div>');
        }

        // If directory will be scrollable make it absolutely positioned, fill the parent container.
        _this.$directory.toggleClass('flex', _this.shouldBeScrollable(_this.options.menu.location));

        if(_this.options.menu.on){

            if(!_this.controllers.directory){
                _this.controllers.directory = new MapSVG.DirectoryController({
                    container: _this.$directory,
                    data: _this.getData(),
                    template: _this.templates.directory,
                    mapsvg: _this,
                    filters: _this.filters,
                    database: _this.options.menu.source === 'regions' ? _this.regionsDatabase : _this.database,
                    scrollable: _this.shouldBeScrollable(_this.options.menu.location),//_this.options.menu.location === 'leftSidebar' || _this.options.menu.location === 'rightSidebar',
                    // position: _this.options.menu.position,
                    // search: _this.options.menu.search,
                    events : {
                        'click': _this.events['click.directoryItem'],
                        'mouseover': _this.events['mouseover.directoryItem'],
                        'mouseout': _this.events['mouseout.directoryItem']
                    }
                });
            } else {
                _this.controllers.directory.database            = _this.options.menu.source === 'regions' ? _this.regionsDatabase : _this.database;
                _this.controllers.directory.database.query.set({
                    sort: [{field: _this.options.menu.sortBy, order: _this.options.menu.sortDirection}]
                });
                // _this.controllers.directory.database.query.sortBy     = _this.options.menu.sortBy;
                // _this.controllers.directory.database.query.sortDir    = _this.options.menu.sortDirection;
                _this.controllers.directory.scrollable = _this.shouldBeScrollable(_this.options.menu.location)
                if(options.filterout){
                    var f = {};
                    f[_this.options.menu.filterout.field] = _this.options.menu.filterout.val;
                    _this.controllers.directory.database.query.setFilterOut(f);
                }
            }


            var $container;
            if(MapSVG.isPhone && _this.options.menu.hideOnMobile){
                $container = _this.$leftSidebar;
            // }else if(MapSVG.isPhone && !_this.options.menu.hideOnMobile) {
            //     $container = _this.options.menu.locationMobile ? _this['$'+_this.options.menu.locationMobile] :_this['$'+ _this.options.menu.location];
            //     if(_this.options.menu.locationMobile !== _this.options.menu.location){
            //         var options = {};
            //         options[_this.options.menu.locationMobile] = {on: true};
            //         _this.setContainers(options);
            //     }
            }else{
                $container = _this.options.menu.location !== 'custom' ? _this['$'+_this.options.menu.location] : $('#' + _this.options.menu.containerId);
            }
            $container.append(_this.$directory);

            /*
                if(!_this.options.menu.customContainer) {
                    if(!_this.$directory){
                        _this.$directory = $('<div class="mapsvg-directory"></div>');

                        if(_this.options.menu.position == 'left')
                            _this.$wrap.css({'padding-left': _this.options.menu.width});
                        else{
                            _this.$wrap.css({'padding-right': _this.options.menu.width});
                            _this.$directory.addClass('mapsvg-directory-right');
                        }
                        _this.$wrap.append(_this.$directory);
                    }
                } else {
                    _this.$directory = $('#' + _this.options.menu.containerId);
                }
                */

            if(_this.options.colors.directory){
                _this.$directory.css({
                    'background-color': _this.options.colors.directory
                });
            }
            _this.setFilters();
            _this.setTemplates({directoryItem: _this.options.templates.directoryItem});
            if((_this.options.menu.source === 'regions' && _this.regionsDatabase.loaded) || (_this.options.menu.source === 'database' && _this.database.loaded)){
                if(_this.editMode && (options.sortBy || options.sortDirection || options.filterout)){
                    _this.controllers.directory.database.getAll();
                }
                _this.loadDirectory();
            }

            // !options.customContainer && _this.$directory.css({width: _this.options.menu.width});
        } else {
            _this.controllers.directory && _this.controllers.directory.destroy();
            _this.controllers.directory = null;
        }
    },
    /**
     * Sets database options.
     * @param {object} options
     * @example
     * mapsvg.setDatabase({
     *   pagination: {on: true, perpage: 30}
     * });
     */
    setDatabase : function(options){

        var _this = this;

        options = options || _this.options.database;

        if(options.pagination){
            if(options.pagination.on != undefined){
                options.pagination.on = MapSVG.parseBoolean(options.pagination.on);
            }
            if(options.pagination.perpage != undefined){
                options.pagination.perpage = parseInt(options.pagination.perpage);
            }
        }
        $.extend(true, _this.options, {database: options});
        if(options.pagination){
            if(options.pagination.on !== undefined || options.pagination.perpage){
                var params = {
                    perpage   : _this.options.database.pagination.on ? _this.options.database.pagination.perpage : 0
                };

                _this.database.getAll(params);
            } else {
                _this.setPagination();
            }
        }
    },
    /**
     * Sets Google Map options.
     * @param {object} options
     * @example
     * mapsvg.setGoogleMaps({
     *   on: true,
     *   type: "terrain"
     * });
     *
     * // Get Google Maps instance:
     * var gm = mapsvg.googleMaps.map;
     */
    setGoogleMaps : function(options){

        var _this = this;

        options    = options || _this.options.googleMaps;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));

        if(!_this.googleMaps){
            _this.googleMaps = {loaded: false, initialized: false, map: null};
        }

        $.extend(true, _this.options, {googleMaps: options});

        if(_this.options.googleMaps.on){
            _this.$map.toggleClass('mapsvg-with-google-map', true);
            // _this.setResponsive(false);
            // if(!_this.googleMaps.loaded){
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
                if(!_this.googleMaps.map){
                    _this.$googleMaps = $('<div class="mapsvg-layer mapsvg-layer-gm" id="mapsvg-google-maps-'+_this.id+'"></div>').prependTo(_this.$map);
                    _this.$googleMaps.css({
                        position: 'absolute',
                        top:0,
                        left: 0,
                        bottom: 0,
                        right: 0,
                        'z-index': '0'
                    });
                    _this.googleMaps.map = new google.maps.Map(_this.$googleMaps[0], {
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

                    USGSOverlay.prototype.draw = function(t) {

                        // return;

                        if (_this.isScrolling) return;


                        var overlayProjection = this.getProjection();
                        if(!overlayProjection) return;

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

                        var scale = (coords.ne2.x - coords.sw2.x)/_this.svgDefault.viewBox[2];

                        // var scale = (ne.x - sw.x)/_this.svgDefault.viewBox[2];
                        var vb = [
                            _this.svgDefault.viewBox[0] - coords.sw2.x/scale,
                            _this.svgDefault.viewBox[1] - coords.ne2.y/scale,
                            _this.$map.width()/scale,
                            _this.$map.outerHeight()/scale
                        ];
                        _this.setViewBox(vb);
                        // var div = this.div_;
                        // div.style.background = 'rgba(255,0,0,.5)';
                        // div.style.left   = coords.sw.x + 'px';
                        // div.style.top    = coords.ne.y + 'px';
                        // div.style.width  = (coords.ne.x - coords.sw.x) + 'px';
                        // div.style.height = (coords.sw.y - coords.ne.y) + 'px';
                    };

                    var southWest = new google.maps.LatLng(_this.geoViewBox.bottomLat, _this.geoViewBox.leftLon);
                    var northEast = new google.maps.LatLng(_this.geoViewBox.topLat, _this.geoViewBox.rightLon);
                    var bounds = new google.maps.LatLngBounds(southWest,northEast);

                    _this.googleMaps.overlay = new USGSOverlay(bounds, _this.googleMaps.map);

                    if(!_this.options.googleMaps.center || !_this.options.googleMaps.zoom){
                        var southWest = new google.maps.LatLng(_this.geoViewBox.bottomLat, _this.geoViewBox.leftLon);
                        var northEast = new google.maps.LatLng(_this.geoViewBox.topLat, _this.geoViewBox.rightLon);
                        var bounds = new google.maps.LatLngBounds(southWest,northEast);
                        _this.googleMaps.map.fitBounds(bounds, 0);
                    }else{
                        _this.googleMaps.map.setZoom(_this.options.googleMaps.zoom);
                        _this.options.googleMaps.center.lat = parseFloat(_this.options.googleMaps.center.lat);
                        _this.options.googleMaps.center.lng = parseFloat(_this.options.googleMaps.center.lng);
                        _this.googleMaps.map.setCenter(_this.options.googleMaps.center);
                    }
                    _this.options.googleMaps.initialized = true;
                    _this.googleMaps.map.addListener('idle',function(){
                        _this.isZooming = false;
                    });
                    google.maps.event.addListenerOnce(_this.googleMaps.map, 'idle', function(){

                        setTimeout(function() {
                            _this.$map.addClass('mapsvg-fade-in');
                            setTimeout(function() {
                                _this.$map.removeClass('mapsvg-google-map-loading');
                                _this.$map.removeClass('mapsvg-fade-in');
                                // _this.googleMaps.overlay.draw();
                                if(!_this.options.googleMaps.center || !_this.options.googleMaps.zoom) {
                                    _this.options.googleMaps.center = _this.googleMaps.map.getCenter().toJSON();
                                    _this.options.googleMaps.zoom = _this.googleMaps.map.getZoom();
                                }
                                _this.zoomDelta = _this.options.googleMaps.zoom - _this.zoomLevel;
                                _this.trigger('googleMapsLoaded');
                            }, 300);
                        }, 1);
                    });
                    // setTimeout(function(){
                    // _this.googleMaps.map.addListener('bounds_changed',function(){
                    //     _this.googleMaps.overlay.draw(1);
                        // if (!_this.isScrolling)
                        // setTimeout(function(){
                        //     if (!_this.isScrolling) {
                        //         null;
                        //     }
                        //         // _this.googleMaps.overlay.draw();
                        //         // _this.setViewBoxByGoogleMapBounds();
                        // },2);
                    // });
                    // _this.googleMaps.map.addListener('zoom_changed',function(){
                    //     setTimeout(function(){
                    //         _this.trigger('zoom');
                    //     },200);
                    // });

                    // _this.setViewBoxByGoogleMapBounds();
                    // },2500);

                }else{
                    _this.$map.toggleClass('mapsvg-with-google-map', true);
                    _this.$googleMaps && _this.$googleMaps.show();
                    if(options.type){
                        _this.googleMaps.map.setMapTypeId(options.type);
                    }
                }
            }
        }else{
            // TODO: destroy google maps
            _this.$map.toggleClass('mapsvg-with-google-map', false);
            _this.$googleMaps && _this.$googleMaps.hide();
            _this.googleMaps.initialized = false;

        }

    },
    /**
     * Loads Google Maps API (js file)
     * @param {function} callback - called on file load
     * @param fail
     * @private
     */
    loadGoogleMapsAPI : function(callback, fail){

        var _this = this;

        if(window.google !== undefined && google.maps){
            MapSVG.googleMapsApiLoaded = true;
        }

        if(MapSVG.googleMapsApiLoaded){
            if(typeof callback == 'function'){
                callback();
            }
            return;
        }

        MapSVG.googleMapsLoadCallbacks = MapSVG.googleMapsLoadCallbacks || [];
        if(typeof callback == 'function') {
            MapSVG.googleMapsLoadCallbacks.push(callback);
        }

        if(MapSVG.googleMapsApiIsLoading){
            return;
        }
        MapSVG.googleMapsApiIsLoading = true;

        window.gm_authFailure = function() {
            if(MapSVG.GoogleMapBadApiKey){
                MapSVG.GoogleMapBadApiKey();
            }else{
                if(_this.editMode) {
                    alert("Google maps API key is incorrect.");
                } else {
                    console.error("MapSVG: Google maps API key is incorrect.");
                }
            }
        };
        _this.googleMapsScript = document.createElement('script');
        _this.googleMapsScript.onload = function(){
            MapSVG.googleMapsApiLoaded = true;
            MapSVG.googleMapsLoadCallbacks.forEach(function(_callback){
                if(typeof callback == 'function')
                    _callback();
            });
        };
        var gmLibraries = [];
        if(_this.options.googleMaps.drawingTools){
            gmLibraries.push('drawing');
        }
        if(_this.options.googleMaps.geometry){
            gmLibraries.push('geometry');
        }
        var libraries = '';
        if(gmLibraries.length > 0 ){
            libraries = '&libraries='+ gmLibraries.join(',');
        }
        _this.googleMapsScript.src = 'https://maps.googleapis.com/maps/api/js?language=en&key='+_this.options.googleMaps.apiKey+libraries;

        document.head.appendChild(_this.googleMapsScript);
    },
    /**
     * Loads Details View for an object (Region or DB Object)
     * @param {Region|object} obj
     *
     * @example
     * var region = mapsvg.getRegion("US-TX");
     * mapsvg.loadDetailsView(region);
     *
     * var object = mapsvg.database.getLoadedObject(12);
     * mapsvg.loadDetailsView(object);
     */
    loadDetailsView : function(obj){

        var _this = this;

        _this.popover && _this.popover.close();
        if(_this.detailsController)
            _this.detailsController.destroy();

        _this.detailsController = new MapSVG.DetailsController({
            // color: _this.options.colors.detailsView,
            autoresize: MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom' ? false : _this.options.detailsView.autoresize,
            container: _this.$details,
            template: obj instanceof MapSVG.Region ?  _this.templates.detailsViewRegion : _this.templates.detailsView,
            mapsvg: _this,
            data: obj instanceof MapSVG.Region ? obj.forTemplate() : obj,
            modal: (MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom'),
            scrollable: (MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom') || _this.shouldBeScrollable(_this.options.detailsView.location),//['custom','header','footer'].indexOf(_this.options.detailsView.location) === -1,
            withToolbar: !(MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom') && _this.shouldBeScrollable(_this.options.detailsView.location),//['custom','header','footer'].indexOf(_this.options.detailsView.location) === -1,
            // width: _this.options.detailsView.width,
            events: {
                'shown': function(mapsvg){
                    if(_this.events['shown.detailsView']) {
                        try{
                            _this.events['shown.detailsView'].call(this, _this);
                        }catch(err){
                            console.log(err);
                        }
                    }
                    _this.trigger('detailsShown');
                },
                'closed' : function(mapsvg){
                    _this.deselectAllRegions();
                    _this.deselectAllMarkers();
                    // _this.controlles.
                    _this.controllers && _this.controllers.directory && _this.controllers.directory.deselectItems();
                    if(_this.events['closed.detailsView']){
                        try {
                            _this.events['closed.detailsView'].call(this, _this);
                        }catch(err){
                            console.log(err);
                        }
                    }
                    _this.trigger('detailsClosed');
                }
            }
        });
    },
    /**
     * Loads modal window with filters
     * @private
     */
    loadFiltersModal : function(){

        var _this = this;

        if(_this.options.filters.modalLocation != 'custom'){
            _this.$filtersModal = _this.$filtersModal || $('<div class="mapsvg-details-container mapsvg-filters-wrap"></div>');
            _this.setColors();
            _this.$filtersModal.css({width: _this.options.filters.width});
            if(MapSVG.isPhone){
                $('body').append(_this.$filtersModal);
                _this.$filtersModal.css({width: ''});
            }else{
                var $cont = '$'+_this.options.filters.modalLocation;
                _this[$cont].append(_this.$filtersModal);
            }
        }else{
            _this.$filtersModal = $('#'+_this.options.filters.containerId);
            _this.$filtersModal.css({width: ''});
        }

        _this.loadFiltersController(_this.$filtersModal, true);

    },
    /**
     * Loads filters controller into a provided container
     * @param {object} $container - Container, jQuery object
     * @param {bool} modal - If filter should be in a modal window
     */
    loadFiltersController : function($container, modal){

        var _this = this;

        if(!_this.filtersSchema.getSchema()){
            return;
        }

        var filterDatabase = _this.options.filters.source === 'regions' ? _this.regionsDatabase : _this.database;

        modal = modal === undefined ? false : modal;
        var filtersInDirectory, filtersHide;

        if(MapSVG.isPhone){
            filtersInDirectory = true;
            filtersHide = _this.options.filters.hideOnMobile; //_this.filtersSchema.schema.length > 2;
        }else{
            filtersInDirectory = (_this.options.menu.on && _this.controllers.directory && _this.options.menu.location === _this.options.filters.location);
            filtersHide = _this.options.filters.hide;
        }
        var scrollable = modal || (!filtersInDirectory && (['leftSidebar','rightSidebar'].indexOf(_this.options.filters.location) !== -1));


        var _filtersController = new MapSVG.FiltersController({
            container: $container,
            template: Handlebars.compile('<div class="mapsvg-filters-container"></div>'),
            mapsvg: _this,
            data: {},
            scrollable: scrollable,
            modal: modal,
            withToolbar: MapSVG.isPhone ? false : modal === true,
            width: $container.hasClass('mapsvg-map-container') ? _this.options.filters.width : '100%',
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
                        data: filterDatabase.query.filters,
                        admin: false,
                        events: {
                            load: function(_formBuilder){

                                _formBuilder.container.find('.mapsvg-form-builder').css({
                                    padding: _this.options.filters.padding
                                });

                                filtersController.updateScroll();

                                if(filtersHide){
                                    var setFiltersCounter = function(){
                                        var filtersCounter = Object.keys(filterDatabase.query.filters).length;
                                        filtersCounter = filtersCounter > 0 ? filtersCounter : '';
                                        // don't include "searcH" filter into counter since it's always outside of the modal
                                        if(filterDatabase.query.filters.search && filterDatabase.query.filters.search.length>0){
                                            filtersCounter--;
                                        }
                                        filtersCounter = filtersCounter === 0 ? '' : filtersCounter;
                                        _formBuilder && _formBuilder.showFiltersButton && _formBuilder.showFiltersButton.views.result.find('button').html(_this.getData().options.filters.showButtonText+' <b>'+filtersCounter+'</b>');
                                    };
                                    setFiltersCounter();

                                    filterDatabase.on('dataLoaded', function(){
                                        setFiltersCounter();
                                    });
                                }
                                if(_this.options.filters.clearButton){
                                    var clearButton = $('<div class="form-group mapsvg-filters-reset-container"><button class="btn btn-default mapsvg-filters-reset">'+_this.options.filters.clearButtonText+'</button></div>');
                                    _formBuilder.container.find('.mapsvg-data-form-view').append(clearButton);
                                    clearButton.on('click',function(){
                                        _formBuilder.container.find('input')
                                            .not(':button, :submit, :reset, :hidden, :checkbox, :radio')
                                            .val('')
                                            .prop('selected', false);
                                        _formBuilder.container.find('input[type="radio"]').prop('checked', false);
                                        _formBuilder.container.find('input[type="checkbox"]').prop('checked', false);
                                        filterDatabase.query.filters = {};
                                        _this.deselectAllRegions();
                                        _formBuilder.container.find('select').val('').trigger('change.select2');
                                        filterDatabase.getAll();
                                    });

                                }
                                _this.controllers.directory && _this.controllers.directory.updateTopShift();
                            }
                        }
                    });
                    formBuilder.view.on('click','.mapsvg-btn-show-filters', function(){
                        _this.loadFiltersModal();
                    });


                    // Handle search separately with throttle 400ms
                    formBuilder.view.on('paste keyup','input[data-parameter-name="search"]',function(){
                        _this.throttle(_this.textSearch, 600, _this, $(this));
                    });


                    formBuilder.view.on('change paste keyup','select,input[type="radio"],input',function(){

                        if($(this).data('ignoreSelect2Change')){
                            $(this).data('ignoreSelect2Change', false);
                            return;
                        }

                        var filter = {};
                        var field = $(this).data('parameter-name');

                        if($(this).attr('data-parameter-name')=="search"){
                            return;
                        }

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
                            var field = formBuilder.mapsvg.filtersSchema.schema.find(function(field){
                                return field.type === 'distance';
                            });
                            if(field.country){
                                distanceData.country = field.country;
                            }
                            if(distanceData.units && distanceData.length && distanceData.latlng){
                                filter.distance = distanceData;
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
                        } else if ($(this).closest('.mapsvg-checkbox-group').length > 0){

                            filter[field] = []
                            $(this).closest('.mapsvg-checkbox-group').find('input[type="checkbox"]:checked').each(function(i,el){
                                filter[field].push($(el).val());
                            });


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
                        // if(_this.options.menu.searchFallback){
                        //     data.searchFallback = true;
                        // }

                        filterDatabase.getAll(data);
                    });
                    // if(_this.events['shown.detailsView']) {
                    //     _this.events['shown.detailsView'].call(this, _this);
                    // }
                    // _this.trigger('detailsShown');
                },
                'closed' : function(mapsvg){
                    // _this.deselectAllRegions();
                    // // _this.controlles.
                    // _this.controllers && _this.controllers.directory && _this.controllers.directory.deselectItems();
                    // if(_this.events['closed.detailsView']){
                    //     _this.events['closed.detailsView'].call(this, _this);
                    // }
                    // _this.trigger('detailsClosed');
                }
            }
        });
    },
    /**
     * Event handler for text search input
     * @event
     * @param text
     * @private
     */
    textSearch : function(elem){

        var _this = this;
        var filter = {"search": elem.val()};
        var filterDatabase = this.getData().options.filters.source === 'regions' ? this.regionsDatabase : this.database;
        filterDatabase.query.setFilters(filter);

        var data = {
            filters: filter
        };

        data.searchFallback = MapSVG.parseBoolean(elem.attr('data-fallback'));

        filterDatabase.getAll(data);
    },
    /**
     * Finds a Region by ID
     * @param {string} id - Region ID
     * @returns {Region}

     * @example
     * var region = mapsvg.getRegion("US-TX");
     */
    getRegion : function(id){
        var _this = this;
        return _this.regions[_this.regionsDict[id]];
    },
    /**
     * Returns all Regions
     * @returns {MapSVG.Region[]}
     */
    getRegions : function(id){
        return _this.regions;
    },
    /**
     * Finds a Marker by ID
     * @param {string} id - Marker ID
     * @returns {Marker}

     * @example
     * var marker = mapsvg.getMarker("marker_12");
     */
    getMarker : function(id){
        var _this = this;
        return _this.markers[_this.markersDict[id]];
    },
    /**
     * Checks if IDs is unique
     * @param id
     * @returns {bool|string} - true if ID is available, {error: "error text"} if not.
     * @private
     */
    checkId : function(id){
        var _this = this;
        if(_this.getRegion(id))
            return {error: "This ID is already being used by a Region"};
        else if(_this.getMarker(id))
            return {error: "This ID is already being used by another Marker"};
        else
            return true;

    },
    /**
     * Redraws colors of regions.
     * Used when Region statuses are loaded from the database or when choropleth map is enabled.
     */
    regionsRedrawColors: function(){
        var _this = this;
        _this.regions.forEach(function(region){
            region.setFill();
        });
    },
    /**
     * Destroys the map and all related containers.
     * @returns {MapSVG.Map}
     */
    destroy : function(){
        var _this = this;
        if(_this.controllers && _this.controllers.directory){
            _this.controllers.directory.mobileButtons.remove();
        }
        _this.$map.empty().insertBefore(_this.$wrapAll).attr('style','').removeClass('mapsvg mapsvg-responsive');

        _this.popover && _this.popover.close();

        if(_this.detailsController)
            _this.detailsController.destroy();

        _this.$wrapAll.remove();

        return _this;
    },
    /**
     * Was used previously to return _data that cotained all map properties.
     * Now properties are stored in the map instance itself.
     * @returns {MapSVG.Map}
     * @deprecated
     */
    getData: function(){
        return this;
    },
    /**
     * Checks if fitmarkers() action can be performed and does it
     * @private
     */
    mayBeFitMarkers: function(){

        var _this = this;

        if(!this.lastTimeFitWas){
            this.lastTimeFitWas = Date.now() - 99999;
        }

        this.fitDelta = Date.now() - this.lastTimeFitWas;

        if(this.fitDelta > 1000 && !_this.firstDataLoad && !_this.fitOnDataLoadDone && _this.options.fitMarkers){
            _this.fitMarkers();
            _this.fitOnDataLoadDone = true;
        }
        if(_this.firstDataLoad && _this.options.fitMarkersOnStart){
            _this.firstDataLoad = false;
            if(_this.options.googleMaps.on && !_this.options.googleMaps.map){
                _this.on('googleMapsLoaded', function(){
                    _this.fitMarkers();
                });
            }else{
                _this.fitMarkers();
            }
        }

        this.lastTimeFitWas = Date.now();
    },
    /**
     * Changes maps viewBox to fit loaded markers.
     */
    fitMarkers : function(){

        var _this = this;

        var dbObjects = _this.database.getLoaded();

        if(!dbObjects || dbObjects.length === 0){
            return;
        }

        if(_this.options.googleMaps.on && typeof google !== "undefined"){

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
                _this.googleMaps.map.fitBounds(bbox, 0);
            } else {
                if(dbObjects[0].location && dbObjects[0].location.lat && dbObjects[0].location.lng){
                    var coords = {lat: dbObjects[0].location.lat, lng: dbObjects[0].location.lng};
                    if(_this.googleMaps.map){
                        _this.googleMaps.map.setCenter(coords);
                        var max = _this.googleMaps.zoomLimit ? 17 : 20;
                        _this.googleMaps.map.setZoom(max);
                    }
                }

            }
        } else {
            if(_this.getOptions().clustering.on){
                return _this.zoomTo(_this.markersClusters.concat(_this.markers));
            } else {
                return _this.zoomTo(_this.markers);
            }
        }
    },
    /**
     * Shows user current location
     * Works only under HTTPS connection
     * @returns {boolean|object} - "false" if geolocation is unavailable, or {lat: float, lng: float} if it's available
     */
    showUserLocation : function(callback){

        var _this = this;

        this.getUserLocation(function(latlng){

            _this.userLocation = null;
            _this.userLocation = new MapSVG.Location({
                lat: latlng.lat,
                lng: latlng.lng,
                img: mapsvg_paths.root+'/markers/user-location.svg'
            });
            _this.userLocationMarker && _this.userLocationMarker.delete();
            _this.userLocationMarker = new MapSVG.Marker({
                location: _this.userLocation,
                mapsvg: _this,
                width: 15,
                height: 15
            });
            _this.userLocationMarker.node.addClass('mapsvg-user-location');
            _this.userLocationMarker.centered = true;
            _this.$scrollpane.append(_this.userLocationMarker.node);
            _this.userLocationMarker.adjustPosition();
            callback && callback(_this.userLocation);
        });
    },
    /**
     * Gets user's current location by using browser's HTML5 geolocation feature.
     * Works only under HTTPS connection!
     * @returns {boolean|object} - "false" if geolocation is unavailable, or {lat: float, lng: float} if it's available
     */
    getUserLocation : function(callback){

        var _this = this;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                callback && callback(pos);
            });
        } else {
            return false;
        }
    },
    /**
     * Returns current SVG scale related to screen - map screen pixels to SVG points ratio.
     * Example: if SVG current viewBox width is 600 and the map is shown in a 300px container,
     * the map scale is 0.5 (300/600 = 0.5)
     * @returns {number}
     */
    getScale: function(){

        var _this = this;

        var scale2 = _this.$map.width() / _this.viewBox[2];

        return scale2 || 1;
    },
    /**
     * Updates size of the map and containers
     * @private
     */
    updateSize : function(){

        var _this = this;

        _this.scale = _this.getScale();
        _this.popover && _this.popover.adjustPosition();
        _this.markersAdjustPosition();
        if(_this.options.labelsRegions.on){
            _this.labelsRegionsAdjustPosition();
        }
        _this.mapAdjustStrokes();
        if(_this.directoryWrap)
            _this.directoryWrap.height(_this.$wrap.outerHeight());
    },
    /**
     * Returns current viewBox
     * @returns {array} - [x,y,width,height]
     */
    getViewBox : function(){
        return this.viewBox;
    },
    /**
     * Sets map container size and viewBox size.
     * This method should be used when you need to change both map container size and viewBox size.
     * @param {number} width
     * @param {number} height
     * @returns {Array} - viewBox, [x,y,width,height]
     */
    viewBoxSetBySize : function(width,height){

        var _this = this;

        width = parseFloat(width);
        height = parseFloat(height);
        _this.setSize(width,height);
        _this._viewBox = _this.viewBoxGetBySize(width,height);
        // _this.options.width = parseFloat(width);
        // _this.options.height = parseFloat(height);

        _this.setViewBox(_this._viewBox);
        $(window).trigger('resize');
        _this.setSize(width,height);
        _this.setZoomLevels();

        // _this.whRatio = _this.viewBox[2] / _this.viewBox[3];
        // if(!_this.options.responsive)
        //     _this.setResponsive();

        return _this.viewBox;
    },
    /**
     * Returns viewBox for a provided width/height
     * @param width
     * @param height
     * @returns {Array} - viewBox, [x,y,width,height]
     * @private
     */
    viewBoxGetBySize : function(width, height){

        var _this = this;

        var new_ratio = width / height;
        var old_ratio = _this.svgDefault.viewBox[2] / _this.svgDefault.viewBox[3];

        var vb = $.extend([],_this.svgDefault.viewBox);

        if (new_ratio != old_ratio){
            //vb[2] = width*_this.svgDefault.viewBox[2] / _this.svgDefault.width;
            //vb[3] = height*_this.svgDefault.viewBox[3] / _this.svgDefault.height;
            if (new_ratio > old_ratio){
                vb[2] = _this.svgDefault.viewBox[3] * new_ratio;
                vb[0] = _this.svgDefault.viewBox[0] - ((vb[2] - _this.svgDefault.viewBox[2])/2);
            }else{
                vb[3] = _this.svgDefault.viewBox[2] / new_ratio;
                vb[1] = _this.svgDefault.viewBox[1] - ((vb[3] - _this.svgDefault.viewBox[3])/2);
            }

        }

        return vb;
    },
    /**
     * Resets map zoom and scroll to initial position.
     * @param {bool} toInitial - Set to "true" if you want to reset to initial viewBox that was set by user;
     * set to "false" to reset to original SVG viewBox as defined in the SVG file
     * @returns {Array} - viewBox, [x,y,width,height]
     */
    viewBoxReset : function(toInitial){

        var _this = this;

        if(_this.options.googleMaps.on && _this.googleMaps.map){
            if(!toInitial){
                _this.options.googleMaps.center = null;
                _this.options.googleMaps.zoom = null;
            }
            if(!_this.options.googleMaps.center || !_this.options.googleMaps.zoom){
                var southWest = new google.maps.LatLng(_this.geoViewBox.bottomLat, _this.geoViewBox.leftLon);
                var northEast = new google.maps.LatLng(_this.geoViewBox.topLat, _this.geoViewBox.rightLon);
                var bounds = new google.maps.LatLngBounds(southWest,northEast);
                _this.googleMaps.map.fitBounds(bounds, 0);
                _this.options.googleMaps.center = _this.googleMaps.map.getCenter().toJSON();
                _this.options.googleMaps.zoom = _this.googleMaps.map.getZoom();
            }else{
                _this.googleMaps.map.setZoom(_this.options.googleMaps.zoom);
                _this.googleMaps.map.setCenter(_this.options.googleMaps.center);
            }
        }else{
            if(toInitial){
                var v = _this._viewBox || _this.svgDefault.viewBox;
                _this.zoomLevel = 0;
                _this._scale = 1;
                _this.setViewBox(v);
            }else{
                _this.setViewBox();
            }
        }
        return this.viewBox;
    },
    /**
     * Returns geo-bounds of the map.
     * @returns {number[]} - [leftLon, topLat, rightLon, bottomLat]
     */
    getGeoViewBox : function(){

        var _this = this;

        var v         = _this.viewBox;
        var leftLon   = _this.convertSVGToGeo(v[0],v[1])[1];
        var rightLon  = _this.convertSVGToGeo(v[0]+v[2],v[1])[1];
        var topLat    = _this.convertSVGToGeo(v[0],v[1])[0];
        var bottomLat = _this.convertSVGToGeo(v[0],v[1]+v[3])[0];
        return [leftLon, topLat, rightLon, bottomLat];
    },
    /**
     * Adjusts stroke widths on zoom change to keeps their widths the same on all zoom levels.
     */
    mapAdjustStrokes : function(){

        var _this = this;

        _this.$svg.find('path, polygon, circle, ellipse, rect').each(function(index){
            if($(this).data('stroke-width')) {
                $(this).css('stroke-width', $(this).data('stroke-width') / _this.scale);
            }
        });
    },
    /**
     * Zooms-in the map
     * @param {Array} center - [x,y] center point (optional)
     */
    zoomIn: function(center){

        var _this = this;

        if(_this.googleMaps.map){
            if(!_this.isZooming){
                var currentZoomInRange = _this.zoomLevel >= _this.options.zoom.limit[0] && _this.zoomLevel <= _this.options.zoom.limit[1];
                var zoom = _this.googleMaps.map.getZoom();
                // Limit zoom of Google map to level "17"
                // If zoom more - browser can't position markers
                // because css transform/translate is limited to 33mil px
                // var google_zoom_new = (zoom+1) > 17 ? 17 : zoom+1;
                var max = _this.googleMaps.zoomLimit ? 17 : 20;
                var google_zoom_new = (zoom+1) > max ? max : zoom+1;
                var svg_zoom_new = google_zoom_new - _this.zoomDelta;

                var newZoomInInRange = svg_zoom_new >= _this.options.zoom.limit[0] && svg_zoom_new <= _this.options.zoom.limit[1];
                if(currentZoomInRange && !newZoomInInRange){
                    return false;
                }
                _this.isZooming = true;
                _this.googleMaps.map.setZoom(google_zoom_new);

                if(center){
                    center = _this.convertSVGToGeo(center[0],center[1]);
                    _this.googleMaps.map.setCenter({lat: center[0], lng: center[1]});
                }

                _this.zoomLevel = svg_zoom_new;
            }
        }else if(_this.canZoom){
            _this.canZoom = false;
            setTimeout(function(){
                _this.canZoom = true;
            }, 700);
            _this.zoom(1, center);
        }
    },
    /**
     * Zooms-out the map
     * @param {Array} center - [x,y] center point (optional)
     */
    zoomOut: function(center){

        var _this = this;

        if(_this.googleMaps.map){
            if(!_this.isZooming && _this.googleMaps.map.getZoom()-1 >= _this.options.googleMaps.minZoom) {

                var currentZoomInRange = _this.zoomLevel >= _this.options.zoom.limit[0] && _this.zoomLevel <= _this.options.zoom.limit[1];

                var zoom = _this.googleMaps.map.getZoom();
                var google_zoom_new = (zoom - 1) < 1 ? 1 : (zoom-1);
                var svg_zoom_new = google_zoom_new - _this.zoomDelta;

                var newZoomInInRange = svg_zoom_new >= _this.options.zoom.limit[0] && svg_zoom_new <= _this.options.zoom.limit[1];

                if(currentZoomInRange && !newZoomInInRange){
                    return false;
                }
                _this.isZooming = true;
                _this.googleMaps.map.setZoom(google_zoom_new);
                _this.zoomLevel = svg_zoom_new;
            }
        }else if(_this.canZoom){
            _this.canZoom = false;
            setTimeout(function(){
                _this.canZoom = true;
            }, 700);
            _this.zoom(-1, center);
        }
    },
    /**
     * Event handler, start zoom on 2-fingers touch
     * @event
     * @param touchScale
     * @private
     * @deprecated
     */
    _touchZoomStart : function (touchScale){

        var _this = this;

        var touchZoomStart = _this._scale;
        _this.scale  = _this.scale * zoom_k;
        var zoom   = _this._scale;
        _this._scale = _this._scale * zoom_k;


        var vWidth     = _this.viewBox[2];
        var vHeight    = _this.viewBox[3];
        var newViewBox = [];

        newViewBox[2]  = _this._viewBox[2] / _this._scale;
        newViewBox[3]  = _this._viewBox[3] / _this._scale;

        newViewBox[0]  = _this.viewBox[0] + (vWidth - newViewBox[2]) / 2;
        newViewBox[1]  = viewBox[1] + (vHeight - newViewBox[3]) / 2;

        _this.setViewBox(newViewBox);

    },
    /**
     * Event handler, zoom on 2-fingers touch
     * @event
     * @param touchScale
     * @private
     */
    touchZoomMove : function(){

    },
    /**
     * Event handler, zoom on 2-fingers touch
     * @event
     * @param touchScale
     * @private
     */
    touchZoomEnd : function(){

    },
    /**
     * Zooms to Region, Marker or array of Markers.
     * @param {String|MapSVG.Region|MapSVG.Marker|MapSVG.Marker[]|MapSVG.MarkersCluster[]} mapObjects - Region ID, Region, Marker, array of Markers, array of MarkerClusters
     * @param {number} zoomToLevel - Zoom level
     *
     * @example
     * var region = mapsvg.getRegion("US-TX");
     * mapsvg.zoomTo(region);
     */
    zoomTo : function (mapObjects, zoomToLevel){

        var _this = this;

        zoomToLevel = zoomToLevel != undefined ? parseInt(zoomToLevel) : false;

        // If string if provided - its a region ID.
        if(typeof mapObjects == 'string') {
            mapObjects = _this.getRegion(mapObjects);
        }

        if(_this.googleMaps.map) {
            if(mapObjects instanceof MapSVG.Marker){
                var latlng = _this.convertSVGToGeo(mapObjects.x, mapObjects.y);
                _this.googleMaps.map.setZoom(zoomToLevel || 1);
                _this.googleMaps.map.setCenter({lat: latlng[0],lng: latlng[1]});
                this.zoomLevel = this.googleMaps.map.getZoom() - this.zoomDelta;
            }else{
                if(mapObjects && mapObjects.length !== undefined){
                    var rbounds = mapObjects[0].getGeoBounds();
                    var southWest = new google.maps.LatLng(rbounds.sw[0], rbounds.sw[1]);
                    var northEast = new google.maps.LatLng(rbounds.ne[0], rbounds.ne[1]);
                    var bounds = new google.maps.LatLngBounds(southWest,northEast);
                    for(var i = 1; i < mapObjects.length-1; i++){
                        var rbounds2 = mapObjects[i].getGeoBounds();
                        var southWest2 = new google.maps.LatLng(rbounds2.sw[0], rbounds2.sw[1]);
                        var northEast2 = new google.maps.LatLng(rbounds2.ne[0], rbounds2.ne[1]);
                        bounds.extend(southWest2);
                        bounds.extend(northEast2);
                    }
                } else {
                    var bounds = mapObjects.getGeoBounds();
                    var southWest = new google.maps.LatLng(bounds.sw[0], bounds.sw[1]);
                    var northEast = new google.maps.LatLng(bounds.ne[0], bounds.ne[1]);
                    var bounds = new google.maps.LatLngBounds(southWest,northEast);
                }
                _this.googleMaps.map.fitBounds(bounds, 0);
                if(_this.googleMaps.zoomLimit && (_this.googleMaps.map.getZoom() > 17)){
                    _this.googleMaps.map.setZoom(17);
                }
                this.zoomLevel = this.googleMaps.map.getZoom() - this.zoomDelta;
            }
            return;
        }

        var bbox = [], viewBox, viewBoxPrev = [];

        if(mapObjects instanceof MapSVG.Marker || mapObjects instanceof MapSVG.MarkersCluster){
            return _this.zoomToMarkerOrCluster(mapObjects, zoomToLevel);
        }

        if(typeof mapObjects == 'object' && mapObjects.length !== undefined){
            // multiple objects
            var _bbox;

            if(mapObjects[0] instanceof MapSVG.Region){
                bbox = mapObjects[0].getBBox();
                var xmin = [bbox[0]];
                var ymin = [bbox[1]];

                var w = (bbox[0]+bbox[2]);
                var xmax = [w];
                var h = (bbox[1]+bbox[3]);
                var ymax = [h];
                if (mapObjects.length > 1){
                    for (var i = 1; i < mapObjects.length; i++){
                        _bbox = mapObjects[i].getBBox();
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
            } else if(mapObjects[0] instanceof MapSVG.Marker || mapObjects[0] instanceof MapSVG.MarkersCluster) {
                var xs = []; var ys = [];

                if(mapObjects.length === 1){
                    return _this.zoomToMarkerOrCluster(mapObjects[0]);
                }

                mapObjects.forEach(function(object){
                    xs.push(object.x);
                    ys.push(object.y);
                });

                // calc the min and max lng and lat
                var minx = Math.min.apply(null, xs),
                    maxx = Math.max.apply(null, xs);
                var miny = Math.min.apply(null, ys),
                    maxy = Math.max.apply(null, ys);

                var padding = 10;
                padding = _this.convertPixelToSVG([padding,0])[0] - _this.convertPixelToSVG([0,0])[0];

                var width  = maxx - minx;
                var height = maxy - miny;
                bbox = [minx-padding, miny-padding, width+padding*2, height+padding*2];
            }

        }else{
            // single object
            bbox = mapObjects.getBBox();
        }

        var viewBoxPrev = [];
        var searching = true;

        $.each(_this.zoomLevels, function(key, level){
            // while(searching && key < _this.zoomLevels.length-1){
            //     var level = _this.zoomLevels[key];
            if(searching && (viewBoxPrev && viewBoxPrev.length)){
                if(
                    (viewBoxPrev[2] > bbox[2] && viewBoxPrev[3] > bbox[3])
                    &&
                    (bbox[2] > level.viewBox[2] || bbox[3] > level.viewBox[3])
                )
                {
                    _this.zoomLevel = zoomToLevel ? zoomToLevel :  parseInt(key)-1;
                    var vb = _this.zoomLevels[_this.zoomLevel].viewBox;

                    _this.setViewBox([bbox[0]-vb[2]/2+bbox[2]/2,
                        bbox[1]-vb[3]/2+bbox[3]/2,
                        vb[2],
                        vb[3]]);
                    _this._scale = _this.zoomLevels[_this.zoomLevel]._scale;
                    searching = false;

                }
            }
            viewBoxPrev = level && level.viewBox;
        });
    },
    /**
     * Zooms to a single marker or cluster
     * @param mapObject
     * @private
     */
    zoomToMarkerOrCluster : function(mapObject, zoomToLevel){
    
        var _this = this;
    
        _this.zoomLevel = zoomToLevel || 1;
        var vb = _this.zoomLevels[_this.zoomLevel].viewBox;

        _this.setViewBox([
            mapObject.x-vb[2]/2,
            mapObject.y-vb[3]/2,
            vb[2],
            vb[3]
        ]);
        _this._scale = _this.zoomLevels[_this.zoomLevel]._scale;
        return;
    },
    /**
     * Centers map on Region or Marker.
     * @param {MapSVG.Region|MapSVG.Marker} region - Region or Marker
     * @param {number} yShift - Vertical shift from center. Used by showPopover method to fit popover in the map container
     */
    centerOn : function(region, yShift){

        var _this = this;

        if(_this.options.googleMaps.on){
            yShift = yShift ? (yShift+12)/_this.getScale() : 0;
            _this.$map.addClass('scrolling');
            var latLng = region.getCenterLatLng(yShift);
            _this.googleMaps.map.panTo(latLng);
            setTimeout(function(){
                _this.$map.removeClass('scrolling');
            },100);
        }else{
            yShift = yShift ? (yShift+12)/_this.getScale() : 0;
            var bbox = region.getBBox();
            var vb   = _this.viewBox;
            _this.setViewBox(
                [bbox[0]-vb[2]/2+bbox[2]/2,
                    bbox[1]-vb[3]/2+bbox[3]/2 - yShift,
                    vb[2],
                    vb[3]]);
            // _this.updateSize();
            // _this._scale = _this.zoomLevels[_this.zoomLevel]._scale;
        }

    },
    /**
     * Zooms the map
     * @param {number} delta - 1/-1
     * @param {number[]} center - [x,y] zoom center point
     * @param {number} exact - (optional) Exact scale value
     * @returns {boolean}
     */
    zoom : function (delta, center, exact){

        var _this = this;

        var vWidth     = _this.viewBox[2];
        var vHeight    = _this.viewBox[3];
        var newViewBox = [];

        var isInZoomRange = _this.zoomLevel >= _this.options.zoom.limit[0] && _this.zoomLevel <= _this.options.zoom.limit[1];

        if(!exact){
            // check for zoom limit
            var d = delta > 0 ? 1 : -1;

            if(!_this.zoomLevels[_this.zoomLevel+d])
                return;

            _this._zoomLevel = _this.zoomLevel;
            _this._zoomLevel += d;

            if(isInZoomRange && (_this._zoomLevel > _this.options.zoom.limit[1] || _this._zoomLevel < _this.options.zoom.limit[0]))
                return false;

            _this.zoomLevel = _this._zoomLevel;
            //
            //var zoom_k = d * _this.options.zoom.delta;
            //if (zoom_k < 1) zoom_k = -1/zoom_k;
            //
            //_this._scale         = _this._scale * zoom_k;
            //newViewBox[2]  = _this._viewBox[2] / _this._scale;
            //newViewBox[3]  = _this._viewBox[3] / _this._scale;

            var z = _this.zoomLevels[_this.zoomLevel];
            _this._scale         = z._scale;
            newViewBox           = z.viewBox;
        }else{
            // var foundZoomLevel = false, i = 1, prevScale, newScale;
            // prevScale = _this.zoomLevels[0]._scale;
            // while(!foundZoomLevel){
            //     if(exact >= prevScale && exact <= _this.zoomLevels[i]._scale){
            //         foundZoomLevel = _this.zoomLevels[i];
            //     }
            //     i++;
            // }
            // if(isInZoomRange && (foundZoomLevel > _this.options.zoom.limit[1] || foundZoomLevel < _this.options.zoom.limit[0]))
            //     return false;

            // _this._scale    = exact;
            // _this.zoomLevel = foundZoomLevel;


            newViewBox[2]  = _this._viewBox[2] / exact;
            newViewBox[3]  = _this._viewBox[3] / exact;
        }

        var shift = [];
        if(center){
            var koef = d > 0 ? 0.5 : -1; // 1/2 * (d=1) || 2 * (d=-1)
            shift = [((center[0] - _this.viewBox[0]) * koef), ((center[1] - _this.viewBox[1]) * koef)];
            newViewBox[0] = _this.viewBox[0] + shift[0];
            newViewBox[1] = _this.viewBox[1] + shift[1];
        }else{
            shift = [(vWidth - newViewBox[2]) / 2, (vHeight - newViewBox[3]) / 2];
            newViewBox[0]  = _this.viewBox[0] + shift[0];
            newViewBox[1]  = _this.viewBox[1] + shift[1];
        }
        // Limit scroll to map's boundaries
        if(_this.options.scroll.limit)
        {
            if(newViewBox[0] < _this.svgDefault.viewBox[0])
                newViewBox[0] = _this.svgDefault.viewBox[0];
            else if(newViewBox[0] + newViewBox[2] > _this.svgDefault.viewBox[0] + _this.svgDefault.viewBox[2])
                newViewBox[0] = _this.svgDefault.viewBox[0]+_this.svgDefault.viewBox[2]-newViewBox[2];

            if(newViewBox[1] < _this.svgDefault.viewBox[1])
                newViewBox[1] = _this.svgDefault.viewBox[1];
            else if(newViewBox[1] + newViewBox[3] > _this.svgDefault.viewBox[1] +_this.svgDefault.viewBox[3])
                newViewBox[1] = _this.svgDefault.viewBox[1]+_this.svgDefault.viewBox[3]-newViewBox[3];
        }

        _this.setViewBox(newViewBox);
        // _this.trigger('zoom');

    },
    /**
     * Deletes a marker
     * @param {MapSVG.Marker} marker
     */
    markerDelete: function(marker){

        var _this = this;

        if(_this.editingMarker && _this.editingMarker.id == marker.id){
            _this.editingMarker = null;
            delete _this.editingMarker;
        }

        _this.markers.splice(_this.markersDict[marker.id],1);
        _this.updateMarkersDict();
        marker = null;

        if (_this.markers.length == 0)
            _this.options.markerLastID = 0;
    },
    /**
     * Adds a marker cluster
     * @param {MapSVG.MarkerCluster} markersCluster
     */
    markersClusterAdd : function(markersCluster) {
        var _this = this;
        _this.layers.markers.append(markersCluster.node);
        _this.markersClusters.push(markersCluster);
        markersCluster.adjustPosition();
    },
    /** Adds a marker to the map.
     * @param {MapSVG.Marker} marker
     * @example
     */
    markerAdd : function(marker) {

        var _this = this;

        marker.node.hide();
        marker.adjustPosition();
        _this.layers.markers.append(marker.node);
        _this.markers.push(marker);
        marker.mapped = true;
        _this.markersDict[marker.id] = _this.markers.length - 1;
        setTimeout(function(){
            marker.node.show();
        },100);
    },
    /**
     * Removes marker from the map
     * @param marker
     */
    markerRemove : function(marker) {

        var _this = this;

        marker.node.detach();
        marker.show();
        _this.markers.splice(_this.markersDict[marker.id],1);
        marker.mapped = false;
        _this.updateMarkersDict();
    },
    /**
     * Generates new Marker ID
     * @returns {*}
     * @private
     */
    markerId: function(){

        var _this = this;

        _this.options.markerLastID = _this.options.markerLastID + 1;
        var id = 'marker_'+(_this.options.markerLastID);
        if(_this.getMarker(id))
            return _this.markerId();
        else
            return id;
    },
    /**
     * Adjusts position of Region Labels
     */
    labelsRegionsAdjustPosition : function(){

        var _this = this;

        var dx, dy;
        if(!_this.$map.is(":visible")){
            return;
        }
        _this.regions.forEach(function(region){
            if(!region.center){
                region.center = region.getCenterSVG();
            }
            var pos = _this.convertSVGToPixel([region.center.x, region.center.y]);
            if(region.textLabel)
                region.textLabel[0].style.transform = 'translate(-50%,-50%) translate(' + pos[0] + 'px,' + pos[1] + 'px)';
        });
    },
    /**
     * Adjusts position of Markers and MarkerClusters.
     * This method is called on zoom or when map container is resized.
     */
    markersAdjustPosition : function(){

        var _this = this;

        _this.markers.forEach(function(marker){
            marker.adjustPosition(_this.scale);
        });
        _this.markersClusters.forEach(function(cluster){
            cluster.adjustPosition();
        });
        if(_this.userLocationMarker){
            _this.userLocationMarker.adjustPosition();
        }
    },
    /**
     * Drag marker event handler. Used in Map Editor.
     * @event
     * @private
     */
    markerMoveStart : function(){

        var _this = this;

        // storing original coordinates
        this.data('ox', parseFloat(this.attr('x')));
        this.data('oy', parseFloat(this.attr('y')));
    },
    /**
     * Drag marker event handler. Used in Map Editor.
     * @event
     * @private
     */
    markerMove : function (dx, dy) {

        var _this = this;

        dx = dx/_this.scale;
        dy = dy/_this.scale;
        this.attr({x: this.data('ox') + dx, y: this.data('oy') + dy});
    },
    /**
     * Drag marker event handler. Used in Map Editor.
     * @event
     * @private
     */
    markerMoveEnd : function () {

        var _this = this;

        // if coordinates are same then it was a "click" and we should start editing
        if(this.data('ox') == this.attr('x') && this.data('oy') == this.attr('y')){
            options.markerEditHandler.call(this);
        }
    },
    /**
     * Sets marker into "edit mode".
     * Used in Map Editor.
     * @param {MapSVG.Marker} marker
     * @private
     */
    setEditingMarker : function (marker) {

        var _this = this;

        _this.editingMarker = marker;
        if(!_this.editingMarker.mapped){
            // todo marker gets removed if it's just a new marker
            _this.editingMarker.needToRemove = true;
            // _this.editingMarker.node.addClass("mapsvg-editing-marker");
            _this.markerAdd(_this.editingMarker);
        }
    },
    /**
     * Disables marker "edit mode".
     * Used in Map Editor.
     * @private
     */
    unsetEditingMarker : function(){
        var _this = this;
        if(_this.editingMarker && _this.editingMarker.needToRemove){
            _this.markerRemove(_this.editingMarker);
        }
        _this.editingMarker = null;
    },
    /**
     * Returns marker that is currently in the "edit mode"
     * Used in Map Editor.
     * @private
     */
    getEditingMarker : function(){
        var _this = this;
        return _this.editingMarker;
    },
    /**
     * Event handler - called when map scroll starts
     * @param e
     * @param mapsvg
     * @returns {boolean}
     * @event
     * @private
     */
    scrollStart : function (e,mapsvg){

        var _this = this;

        if($(e.target).hasClass('mapsvg-btn-map') || $(e.target).closest('.mapsvg-gauge').length)
            return false;

        if(_this.editMarkers.on && $(e.target).hasClass('class')=='mapsvg-marker')
            return false;

        e.preventDefault();
        var ce = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? e.originalEvent.touches[0] : e;

        _this.scrollStarted = true;

        _this.scroll = _this.scroll || {};

        // initial viewbox when scrollning started
        _this.scroll.vxi = _this.viewBox[0];
        _this.scroll.vyi = _this.viewBox[1];
        // mouse coordinates when scrolling started
        _this.scroll.x  = ce.clientX;
        _this.scroll.y  = ce.clientY;
        // mouse delta
        _this.scroll.dx = 0;
        _this.scroll.dy = 0;
        // new viewbox x/y
        _this.scroll.vx = 0;
        _this.scroll.vy = 0;

        // for google maps scroll
        _this.scroll.gx  = ce.clientX;
        _this.scroll.gy  = ce.clientY;

        _this.scroll.tx = _this.scroll.tx || 0;
        _this.scroll.ty = _this.scroll.ty || 0;

        // var max = _this.convertSVGToPixel(_this.convertGeoToSVG([-85,180]));
        // var min = _this.convertSVGToPixel(_this.convertGeoToSVG([85,-180]));
        // _this.scroll.limit = {
        //     maxX: max[0]+_this.$map.width(),
        //     maxY: max[1]+_this.$map.outerHeight(),
        //     minX: min[0],
        //     minY: min[1]
        // };

        if(e.type.indexOf('mouse') === 0 ){
            $(document).on('mousemove.scroll.mapsvg', function(e){
                _this.scrollMove(e);
            });
            if(_this.options.scroll.spacebar){
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
    /**
     * Event handler - called when map scroll moves
     * @param e
     * @event
     * @private
     */
    scrollMove :  function (e){

        var _this = this;

        e.preventDefault();

        if(!_this.isScrolling){
            _this.isScrolling = true;
            // _this.$map.css('pointer-events','none');
            _this.$map.addClass('scrolling');
        }

        var ce = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? e.originalEvent.touches[0] : e;

        // TODO: на каждый зум лев считать допустимые translate xy при данном scale
        var scrolled = _this.panBy((_this.scroll.gx - ce.clientX),(_this.scroll.gy - ce.clientY));
        if(_this.googleMaps.map && (scrolled.x || scrolled.y)){

            var point = _this.googleMaps.map.getCenter();

            var projection = _this.googleMaps.overlay.getProjection();

            var pixelpoint = projection.fromLatLngToDivPixel(point);
            pixelpoint.x += scrolled.x ? _this.scroll.gx - ce.clientX : 0;
            pixelpoint.y += scrolled.y ? _this.scroll.gy - ce.clientY : 0;

            point = projection.fromDivPixelToLatLng(pixelpoint);

            _this.googleMaps.map.setCenter(point);
        }

        _this.scroll.gx  = ce.clientX;
        _this.scroll.gy  = ce.clientY;

        // delta x/y
        _this.scroll.dx = (_this.scroll.x - ce.clientX);
        _this.scroll.dy = (_this.scroll.y - ce.clientY);

        // new viewBox x/y
        var vx = parseInt(_this.scroll.vxi + _this.scroll.dx /_this.scale);
        var vy = parseInt(_this.scroll.vyi + _this.scroll.dy /_this.scale);

        // Limit scroll to map boundaries
        if(_this.options.scroll.limit){

            if(vx < _this.svgDefault.viewBox[0])
                vx = _this.svgDefault.viewBox[0];
            else if(_this.viewBox[2] + vx > _this.svgDefault.viewBox[0] + _this.svgDefault.viewBox[2])
                vx = (_this.svgDefault.viewBox[0]+_this.svgDefault.viewBox[2]-_this.viewBox[2]);

            if(vy < _this.svgDefault.viewBox[1])
                vy = _this.svgDefault.viewBox[1];
            else if(_this.viewBox[3] + vy > _this.svgDefault.viewBox[1] + _this.svgDefault.viewBox[3])
                vy = (_this.svgDefault.viewBox[1]+_this.svgDefault.viewBox[3]-_this.viewBox[3]);

        }


        _this.scroll.vx = vx;
        _this.scroll.vy = vy;


        // set new viewBox
        // _this.setViewBox([_this.scroll.vx,  _this.scroll.vy, _this.viewBox[2], _this.viewBox[3]]);

    },
    /**
     * Event handler - called when map scroll ends
     * @param e
     * @param mapsvg
     * @param noClick
     * @returns {boolean}
     * @event
     * @private
     */
    scrollEnd : function (e,mapsvg, noClick){

        var _this = this;

        // _this.scroll.tx = (_this.scroll.tx - _this.scroll.dx);
        // _this.scroll.ty = (_this.scroll.ty - _this.scroll.dy);


        setTimeout(function(){
            _this.scrollStarted = false;
            _this.isScrolling = false;
        }, 100);
        _this.googleMaps && _this.googleMaps.overlay && _this.googleMaps.overlay.draw();
        _this.$map.removeClass('scrolling');
        $(document).off('keyup.scroll.mapsvg');
        $(document).off('mousemove.scroll.mapsvg');
        $(document).off('mouseup.scroll.mapsvg');

        // call regionClickHandler if mouse did not move more than 5 pixels
        if (noClick !== true && Math.abs(_this.scroll.dx)<5 && Math.abs(_this.scroll.dy)<5){
            // _this.popoverOffHandler(e);
            if(_this.editMarkers.on)
                _this.clickAddsMarker && _this.markerAddClickHandler(e);
            else if (_this.region_clicked)
                _this.regionClickHandler(e, _this.region_clicked);
        }


        _this.viewBox[0] = _this.scroll.vx || _this.viewBox[0];
        _this.viewBox[1] = _this.scroll.vy || _this.viewBox[1] ;


        // _this.$map.css('pointer-events','auto');
        // $('body').css({'cursor': 'default'});

        // if(_this.googleMaps.map) {
        // fix shift
        // _this.setViewBoxByGoogleMapBounds();
        // }
    },
    /**
     * Shift the map by x,y pixels
     * @param {number} x
     * @param {number} y
     */
    panBy : function(x, y){

        var _this = this;

        var tx = _this.scroll.tx - x;
        var ty = _this.scroll.ty - y;

        var scrolled = {x:true, y:true};

        //!_this.options.googleMaps.on &&
        if(_this.options.scroll.limit){
            var svg = _this.$svg[0].getBoundingClientRect();
            var bounds = _this.$map[0].getBoundingClientRect();
            if((svg.left-x > bounds.left && x < 0) || (svg.right-x < bounds.right && x > 0)){
                tx = _this.scroll.tx;
                scrolled.x = false;
            }
            if((svg.top-y > bounds.top && y < 0) || (svg.bottom-y < bounds.bottom && y > 0)){
                ty = _this.scroll.ty;
                scrolled.y = false;
            }
        }

        _this.$scrollpane.css({
            'transform': 'translate('+tx+'px,'+ty+'px)'
        });

        _this.scroll.tx = tx;
        _this.scroll.ty = ty;
        return scrolled;
    },
    /**
     * Save the Region ID that was clicked before starting map scroll.
     * It is used to trigger .regionClickHandler() later if scroll was less than 5px
     * @param {Object} e - Event object
     * @param {MapSVG.Region} region - Clicked Region
     * @private
     */
    scrollRegionClickHandler : function (e, region) {
        var _this = this;
        _this.region_clicked = region;
    },
    /**
     * Event hanlder
     * @param _e
     * @param mapsvg
     * @private
     * @event
     */
    touchStart : function (_e,mapsvg){

        var _this = this;

        // if($(_e.target).hasClass('mapsvg-popover') || $(_e.target).closest('.mapsvg-popover').length ){
        //     return true;
        // }
        _e.preventDefault();
        // _e.stopPropagation();

        // stop scroll and cancel click event
        if(_this.scrollStarted){
            _this.scrollEnd(_e, mapsvg, true);
        }
        var e = _e.originalEvent;

        if(_this.options.zoom.fingers && e.touches && e.touches.length == 2){
            // _this.touchZoomStartViewBox = _this.viewBox;
            // _this.touchZoomStartScale =  _this.scale;
            _this.touchZoomStart   =  true;
            _this.scaleDistStart = Math.hypot(
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
    /**
     * Event hanlder
     * @param _e
     * @param mapsvg
     * @private
     * @event
     */
    touchMove : function (_e, mapsvg){

        var _this = this;
        // if($(_e.target).hasClass('mapsvg-popover') || $(_e.target).closest('.mapsvg-popover').length ){
        //     return true;
        // }
        _e.preventDefault();
        var e = _e.originalEvent;

        if(_this.options.zoom.fingers && e.touches && e.touches.length == 2){
            if(!MapSVG.ios){
                e.scale = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY)/_this.scaleDistStart;
            }

            if(e.scale!=1 && _this.canZoom) {
                var d = e.scale > 1 ? 1 : -1;

                var cx = e.touches[0].pageX >= e.touches[1].pageX ? e.touches[0].pageX - (e.touches[0].pageX - e.touches[1].pageX)/2 - _this.$svg.offset().left : e.touches[1].pageX - (e.touches[1].pageX - e.touches[0].pageX)/2 - _this.$svg.offset().left;
                var cy = e.touches[0].pageY >= e.touches[1].pageY ? e.touches[0].pageY - (e.touches[0].pageY - e.touches[1].pageY) - _this.$svg.offset().top : e.touches[1].pageY - (e.touches[1].pageY - e.touches[0].pageY) - _this.$svg.offset().top;
                var center = _this.convertPixelToSVG([cx, cy]);

                if (d > 0)
                    _this.zoomIn(center);
                else
                    _this.zoomOut(center);
            }
        }else if(e.touches && e.touches.length == 1){
            _this.scrollMove(_e);
        }
    },
    /**
     * Event hanlder
     * @param _e
     * @param mapsvg
     * @private
     * @event
     */
    touchEnd : function (_e, mapsvg){

        var _this = this;

        // if($(_e.target).hasClass('mapsvg-popover') || $(_e.target).closest('.mapsvg-popover').length ){
        //     return true;
        // }
        _e.preventDefault();
        var e = _e.originalEvent;
        if(_this.touchZoomStart){
            _this.touchZoomStart  = false;
        }else if(_this.scrollStarted){
            _this.scrollEnd(_e, mapsvg);
        }

        $(document).off('touchmove.scroll.mapsvg');
        $(document).off('touchend.scroll.mapsvg');
    },
    /**
     * Returns array of IDs of selected Regions
     * @returns {String[]}
     */
    getSelected : function(){

        var _this = this;

        return _this.selected_id;
    },
    /**
     * Selects a Region
     * @param {MapSVG.Region|string} id - Region or ID
     * @param {bool} skipDirectorySelection
     * @returns {boolean}
     */
    selectRegion :    function(id, skipDirectorySelection){

        var _this = this;

        // _this.hidePopover();
        if(typeof id == "string"){
            var region = _this.getRegion(id);
        }else{
            var region = id;
        }
        if(!region) return false;

        if(_this.options.multiSelect && !_this.editRegions.on){
            if(region.selected){
                _this.deselectRegion(region);
                if(!skipDirectorySelection && _this.options.menu.on){
                    if(_this.options.menu.source == 'database') {
                        if (region.objects && region.objects.length) {
                            var ids = region.objects.map(function (obj) {
                                return obj.id;
                            });
                        }
                    }else{
                        var ids = [region.id];
                    }
                    _this.controllers.directory.deselectItems(ids);
                }

                return;
            }
        }else if(_this.selected_id.length>0){
            _this.deselectAllRegions();
            if(!skipDirectorySelection && _this.options.menu.on){
                if(_this.options.menu.source == 'database') {
                    if (region.objects && region.objects.length) {
                        var ids = region.objects.map(function (obj) {
                            return obj.id;
                        });
                    }
                }else{
                    var ids = [region.id];
                }
                _this.controllers.directory.deselectItems();
            }
        }

        _this.selected_id.push(region.id);
        region.select();

        var skip = _this.options.actions.region.click.filterDirectory;

        if(!skip && !skipDirectorySelection && _this.options.menu.on && _this.controllers && _this.controllers.directory){
            if(_this.options.menu.source == 'database'){
                if(region.objects && region.objects.length) {
                    var ids = region.objects.map(function(obj){
                        return obj.id;
                    });
                } else {
                    var ids = [region.id];
                }
            }else{
                var ids = [region.id];
            }
            _this.controllers.directory.selectItems(ids);
        }

        if(_this.options.actions.region.click.addIdToUrl && !_this.options.actions.region.click.showAnotherMap){
            window.location.hash = "/m/"+region.id;
            // history.replaceState(null, null, region.id);
            // history.replaceState("", document.title, window.location.pathname
            //     + window.location.search + '#m_'+region.id);

        }
    },
    /**
     * Deselects all Regions
     */
    deselectAllRegions : function(){

        var _this = this;

        $.each(_this.selected_id, function(index,id){
            _this.deselectRegion(_this.getRegion(id));
        });
    },
    /**
     * Deselects one Region
     * @param {MapSVG.Region} region
     */
    deselectRegion : function (region){

        var _this = this;

        if(!region)
            region = _this.getRegion(_this.selected_id[0]);
        if(region){
            region.deselect();
            var i = $.inArray(region.id, _this.selected_id);
            _this.selected_id.splice(i,1);
            // if(MapSVG.browser.ie)//|| MapSVG.browser.firefox)
            //     _this.mapAdjustStrokes();
        }
        if(_this.options.actions.region.click.addIdToUrl){
            // window.location.hash = ' '; //window.location.hash.replace(region.id,'');
            // history.replaceState("", document.title, window.location.pathname
            //     + window.location.search);
            if(window.location.hash.indexOf(region.id) !== -1){
                history.replaceState(null, null, ' ');
            }
        }
    },
    /**
     * Highlights an array of Regions (used on mouseover)
     * @param {MapSVG.Region[]} regions
     */
    highlightRegions : function(regions){

        var _this = this;

        regions.forEach(function(region){
            if(region && !region.selected && !region.disabled){
                _this.highlightedRegions.push(region);
                region.highlight();
            }
        })
    },
    /**
     * Unhighlights all Regions  (used on mouseout)
     */
    unhighlightRegions : function(){

        var _this = this;

        _this.highlightedRegions.forEach(function(region){
            if(region && !region.selected && !region.disabled)
                region.unhighlight();
        });
        _this.highlightedRegions = [];
    },
    /**
     * Selects a Marker
     * @param {MapSVG.Marker} marker
     * @returns {boolean}
     */
    selectMarker :    function(marker){

        var _this = this;

        if(!(marker instanceof MapSVG.Marker))
            return false;

        _this.deselectAllMarkers();
        marker.select();
        _this.selected_marker = marker;

        _this.layers.markers.addClass('mapsvg-with-marker-active');

        if(_this.options.menu.on && _this.options.menu.source == 'database'){
            _this.controllers.directory.deselectItems();
            _this.controllers.directory.selectItems(marker.object.id);
        }
    },
    /**
     * Deselects all Markers
     */
    deselectAllMarkers : function(){

        var _this = this;
        _this.selected_marker && _this.selected_marker.deselect();
        _this.layers.markers.removeClass('mapsvg-with-marker-active');
    },
    /**
     * Deselects one Marker
     * @param {MapSVG.Marker} marker
     */
    deselectMarker : function (marker){

        var _this = this;

        if(marker){
            marker.deselect();
        }
    },
    /**
     * Highlight marker
     * @param {MapSVG.Marker} marker
     */
    highlightMarker : function(marker){

        var _this = this;

        _this.layers.markers.addClass('mapsvg-with-marker-hover');
        marker.highlight();
        _this.highlighted_marker = marker;
    },
    /**
     * Unhighlights all Regions  (used on mouseout)
     */
    unhighlightMarker : function(){

        var _this = this;

        _this.layers.markers.removeClass('mapsvg-with-marker-hover');
        _this.highlighted_marker && _this.highlighted_marker.unhighlight();
    },
    /**
     * Converts mouse pointer coordinates to SVG coordinates
     * @param {object} e - Event object
     * @returns {Array} - [x,y] SVG coordinates
     */
    convertMouseToSVG : function(e){

        var _this = this;

        var mc = MapSVG.mouseCoords(e);
        var x = mc.x - _this.$svg.offset().left;
        var y = mc.y - _this.$svg.offset().top;
        return _this.convertPixelToSVG([x,y]);
    },
    /**
     * Converts SVG coordinates to pixel coodinates relative to map container
     * @param {Array} xy - [x,y] SVG coordinates
     * @returns {number[]} - [x,y] Pixel coordinates
     */
    convertSVGToPixel : function(xy){

        var _this = this;

        var scale = _this.getScale();

        var shiftX = 0, shiftY = 0;

        if(_this.options.googleMaps.on){
            if((_this.viewBox[0]-_this.svgDefault.viewBox[0]) > _this.svgDefault.viewBox[2]){
                var worldMapWidth = ((_this.svgDefault.viewBox[2] / _this.mapLonDelta) * 360);
                shiftX = worldMapWidth * Math.floor((_this.viewBox[0]-_this.svgDefault.viewBox[0]) / _this.svgDefault.viewBox[2]);
            }
        }

        return [(xy[0]-_this.svgDefault.viewBox[0]+shiftX)*scale, (xy[1]-_this.svgDefault.viewBox[1]+shiftY)*scale];
    },
    /**
     * Converts pixel coordinates (relative to map container) to SVG coordinates
     * @param {Array} xy - [x,y] Pixel coordinates
     * @returns {number[]} - [x,y] SVG coordinates
     */
    convertPixelToSVG : function(xy){

        var _this = this;

        var scale = _this.getScale();
        return [(xy[0])/scale+_this.svgDefault.viewBox[0], (xy[1])/scale+_this.svgDefault.viewBox[1]];
    },
    /**
     * Converts geo-coordinates (latitude/lognitude) to SVG coordinates
     * @param {Array} coords - [lat,lon] Geo-coodinates
     * @returns {number[]} - [x,y] SVG coordinates
     */
    convertGeoToSVG: function (coords){

        var _this = this;

        var lat = parseFloat(coords[0]);
        var lon = parseFloat(coords[1]);
        var x = (lon - _this.geoViewBox.leftLon) * (_this.svgDefault.viewBox[2] / _this.mapLonDelta);

        var lat = lat * 3.14159 / 180;
        // var worldMapWidth = ((_this.svgDefault.width / _this.mapLonDelta) * 360) / (2 * 3.14159);
        var worldMapWidth = ((_this.svgDefault.viewBox[2] / _this.mapLonDelta) * 360) / (2 * 3.14159);
        var mapOffsetY    = (worldMapWidth / 2 * Math.log((1 + Math.sin(_this.mapLatBottomDegree)) / (1 - Math.sin(_this.mapLatBottomDegree))));
        var y = _this.svgDefault.viewBox[3] - ((worldMapWidth / 2 * Math.log((1 + Math.sin(lat)) / (1 - Math.sin(lat)))) - mapOffsetY);

        x += _this.svgDefault.viewBox[0];
        y += _this.svgDefault.viewBox[1];

        return [x, y];
    },
    /**
     * Converts SVG coordinates to geo-coordinates (latitude/lognitude).
     * @param {number} tx - SVG x coordinate
     * @param {number} ty - SVG y coordinate
     * @returns {number[]} - [lat,lon] Geo-coordinates
     */
    convertSVGToGeo: function (tx, ty){

        var _this = this;

        tx -= _this.svgDefault.viewBox[0];
        ty -= _this.svgDefault.viewBox[1];
        /* called worldMapWidth in Raphael's Code, but I think that's the radius since it's the map width or circumference divided by 2*PI  */
        var worldMapRadius = _this.svgDefault.viewBox[2] / _this.mapLonDelta * 360/(2 * Math.PI);
        var mapOffsetY = ( worldMapRadius / 2 * Math.log( (1 + Math.sin(_this.mapLatBottomDegree) ) / (1 - Math.sin(_this.mapLatBottomDegree))  ));
        var equatorY = _this.svgDefault.viewBox[3] + mapOffsetY;
        var a = (equatorY-ty)/worldMapRadius;
        var lat = 180/Math.PI * (2 * Math.atan(Math.exp(a)) - Math.PI/2);
        var lon = _this.geoViewBox.leftLon+tx/_this.svgDefault.viewBox[2]*_this.mapLonDelta;
        lat  = parseFloat(lat.toFixed(6));
        lon  = parseFloat(lon.toFixed(6));
        return [lat,lon];
    },
    /**
     * Converts map geo-boundaries to viewBox
     * @param sw
     * @param ne
     * @returns {*[]}
     *
     * @private
     * @deprecated
     */
    convertGeoBoundsToViewBox: function (sw, ne){

        var _this = this;

        var lat = parseFloat(coords[0]);
        var lon = parseFloat(coords[1]);
        var x = (lon - _this.geoViewBox.leftLon) * (_this.svgDefault.viewBox[2] / _this.mapLonDelta);

        var lat = lat * 3.14159 / 180;
        // var worldMapWidth = ((_this.svgDefault.width / _this.mapLonDelta) * 360) / (2 * 3.14159);
        var worldMapWidth = ((_this.svgDefault.viewBox[2] / _this.mapLonDelta) * 360) / (2 * 3.14159);
        var mapOffsetY = (worldMapWidth / 2 * Math.log((1 + Math.sin(_this.mapLatBottomDegree)) / (1 - Math.sin(_this.mapLatBottomDegree))));
        var y = _this.svgDefault.viewBox[3] - ((worldMapWidth / 2 * Math.log((1 + Math.sin(lat)) / (1 - Math.sin(lat)))) - mapOffsetY);

        x += _this.svgDefault.viewBox[0];
        y += _this.svgDefault.viewBox[1];

        return [x, y];
    },
    /**
     * For choropleth map: returns color for given number value
     * @param {number} gaugeValue - Number value
     * @returns {number[]} - [r,g,b,a] Color values, 0-255.
     */
    pickGaugeColor: function(gaugeValue) {

        var _this = this;

        var w = (gaugeValue - _this.options.gauge.min) / _this.options.gauge.maxAdjusted;
        var rgb = [
            Math.round(_this.options.gauge.colors.diffRGB.r * w + _this.options.gauge.colors.lowRGB.r),
            Math.round(_this.options.gauge.colors.diffRGB.g * w + _this.options.gauge.colors.lowRGB.g),
            Math.round(_this.options.gauge.colors.diffRGB.b * w + _this.options.gauge.colors.lowRGB.b),
            Math.round(_this.options.gauge.colors.diffRGB.a * w + _this.options.gauge.colors.lowRGB.a)
        ];
        return rgb;
    },
    /**
     * Checks if Region should be disabled
     * @param {string} id - Region ID
     * @param {string} svgfill - Region color ("fill" attribute)
     * @returns {boolean} - "true" if Region should be disabled.
     */
    isRegionDisabled : function (id, svgfill){

        var _this = this;

        if(_this.options.regions[id] && (_this.options.regions[id].disabled || svgfill == 'none') ){
            return true;
        }else if(
            (_this.options.regions[id] == undefined || MapSVG.parseBoolean(_this.options.regions[id].disabled)) &&
            (_this.options.disableAll || svgfill == 'none' || id == 'labels' || id == 'Labels')

        ){
            return true;
        }else{
            return false;
        }
    },
    /**
     * Event handler that is fired on click on a Region / Marker / MarkerCluster
     * @param {object} e - Event object
     * @param {MapSVG.Region|MapSVG.Marker|MapSVG.MarkerCluster} region
     * @returns {boolean}
     */
    regionClickHandler : function(e, region){

        var _this = this;

        _this.region_clicked = null;
        var actions = _this.options.actions;

        if(_this.eventsPreventList['click'])
            return;

        if(_this.editRegions.on){
            _this.selectRegion(region.id);
            _this.regionEditHandler.call(region);
            return;
        }
        // _this.hidePopover();

        if(region instanceof MapSVG.MarkersCluster){
            _this.zoomTo(region.markers);
            return;
        }

        if(region.isRegion()){

            _this.selectRegion(region.id);

            if(actions.region.click.zoom){
                _this.zoomTo(region, actions.region.click.zoomToLevel);
            }

            if(actions.region.click.filterDirectory){
                _this.database.getAll({filters: {regions: region.id}}).done(function(){
                    if(_this.popover){
                        _this.popover.redraw(region.forTemplate());
                    }
                    if(_this.detailsController){
                        _this.detailsController.redraw(region.forTemplate());
                    }
                });
                _this.updateFiltersState();
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
                        if(region.data){
                            try {
                                url = eval('region.data'+attr);
                            }catch(err){
                                console.log("No such field as region.data"+attr);
                            }
                        }
                    }else{
                        if(region.objects && region.objects[0]){
                            try {
                                url = eval('region.objects[0]'+attr);
                            }catch(err){
                                console.log("No such field as region.objects[0]"+attr);
                            }
                        }
                    }

                    if(url && !_this.disableLinks){
                        if(_this.editMode){
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
                if(_this.editMode){
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
                        var container = actions.region.click.showAnotherMapContainerId ? $('#'+actions.region.click.showAnotherMapContainerId) : _this.$map;
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
            if(_this.events['click.region'])
                try{
                    _this.events['click.region'].call(region, e, _this);
                }catch(err){
                    console.log(err);
                }
        }else if(region.isMarker()){

            _this.selectMarker(region);

            var passingObject = region.object;

            if(actions.marker.click.zoom) {
                _this.zoomTo(region, actions.marker.click.zoomToLevel);
            }

            if(actions.marker.click.filterDirectory){
                _this.database.getAll({filters: {id: region.object.id}});
                _this.updateFiltersState();
            }


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
                    try {
                        url = eval('passingObject'+attr);
                    }catch(err){
                        console.log("MapSVG: No such field as passingObject"+attr);
                    }
                    if(url && !_this.disableLinks)
                        if(_this.editMode){
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
            if(_this.events['click.marker']){
                try {
                    _this.events['click.marker'].call(region, e, _this);
                } catch (err) {
                    console.log(err);
                }
            }
        }
    },
    /**
     * Checks if file exists by provided URL.
     * @param {string} url
     * @returns {boolean}
     *
     * @private
     */
    fileExists : function(url){

        var _this = this;

        if(url.substr(0,4)=="data")
            return true;
        var http = new XMLHttpRequest();
        http.open('HEAD', url, false);
        http.send();
        return http.status!=404;
    },
    /**
     * Returns CSS style of an SVG element
     * @param elem
     * @param prop
     * @returns {*|string} - CSS
     *
     * @private
     * @deprecated
     */
    getStyle : function(elem,prop){

        var _this = this;

        if (elem.currentStyle) {
            var res= elem.currentStyle.margin;
        } else if (window.getComputedStyle) {
            if (window.getComputedStyle.getPropertyValue){
                var res= window.getComputedStyle(elem, null).getPropertyValue(prop)}
            else{var res =window.getComputedStyle(elem)[prop] };
        }
        return res;
    },
    /**
     * Hides all markers except one.
     * Used in Map Editor.
     * @param {string} id - ID of the Marker
     * @private
     */
    hideMarkersExceptOne: function(id){

        var _this = this;

        _this.markers.forEach(function(m){
            if(m.id!=id){
                m.hide();
            }
        });
        // _this.$wrap.addClass('mapsvg-clusters-hidden');
        _this.$wrap.addClass('mapsvg-edit-marker-mode');
    },
    /**
     * Shows all markers after .hideMarkersExceptOne()
     * Used in Map Editor.
     * @private
     */
    showMarkers: function(){

        var _this = this;

        _this.markers.forEach(function(m){
            m.show();
        });
        // _this.$wrap.removeClass('mapsvg-clusters-hidden');
        _this.$wrap.removeClass('mapsvg-edit-marker-mode');

    },
    /**
     * Event handler that creates marker on click on the map.
     * Used in the Map Editor.
     * @param {object} e - Event object
     * @returns {boolean}
     * @private
     */
    markerAddClickHandler : function(e){

        var _this = this;

        // Don't add marker if marker was clicked
        if($(e.target).hasClass('mapsvg-marker')) return false;

        var mc = MapSVG.mouseCoords(e);
        var x  = mc.x - _this.$svg.offset().left;
        var y  = mc.y - _this.$svg.offset().top;
        var xy = _this.convertPixelToSVG([x,y]);
        var latlng = _this.convertSVGToGeo(xy[0], xy[1]);

        if(!$.isNumeric(x) || !$.isNumeric(y))
            return false;


        var location = new MapSVG.Location({
            x: xy[0],
            y: xy[1],
            lat: latlng[0],
            lng: latlng[1],
            img: _this.options.defaultMarkerImage
        });

        // When Form Builder is opened in MapSVG Builder, there could be created marker
        // already so we want to move the marker to a new position on map click
        // instead of creating a new marker
        if(_this.editingMarker){
            // _this.editingMarker.moveToClick([x,y]);
            _this.editingMarker.setXy(xy);
            return;
        }

        var marker = new MapSVG.Marker({
            location: location,
            mapsvg: this
        });
        _this.markerAdd(marker);

        _this.markerEditHandler && _this.markerEditHandler.call(marker);

    },
    /**
     * Sets default marker image.
     * Used in Map Editor.
     * @param {string} src - Image URL
     * @private
     */
    setDefaultMarkerImage : function(src){

        var _this = this;

        _this.options.defaultMarkerImage = src;
    },
    /**
     * Checks and remebers if marker images should be set by a field value
     * @private
     */
    setMarkerImagesDependency : function(){
        var _this = this;
        var locationField = _this.locationField || _this.database.getSchemaField('location');
        if(locationField.markersByFieldEnabled && locationField.markerField && Object.values(locationField.markersByField).length > 0){
            this.setMarkersByField = true;
            this.locationField = locationField;
        } else {
            this.setMarkersByField = false;
        }
    },
    /**
     * Returns default marker image or marker image by field value if such option is enabled
     * @param {Number|String} fieldValue
     * @returns {String} URL of marker image
     * @private
     */
    getMarkerImage : function(fieldValueOrObject, location){
        var _this = this;

        var fieldValue;

        if(this.setMarkersByField){
            if(typeof fieldValueOrObject === 'object'){
                fieldValue = fieldValueOrObject[_this.locationField.markerField];
            } else {
                fieldValue = fieldValueOrObject;
            }
            if(_this.locationField.markersByField[fieldValue]){
                return _this.locationField.markersByField[fieldValue];
            }
        }

        return (location && location.img) ?  location.img : (this.options.defaultMarkerImage ? this.options.defaultMarkerImage : mapsvg_paths.root+'markers/_pin_default.png');
    },
    /**
     * Sets on/off "edit markers" mode
     * @param {bool} on - on/off
     * @param {bool} clickAddsMarker - defines if click on the map should add a marker
     * @private
     */
    setMarkersEditMode : function(on, clickAddsMarker){

        var _this = this;

        _this.editMarkers.on = MapSVG.parseBoolean(on);
        _this.clickAddsMarker = _this.editMarkers.on;
        _this.setEventHandlers();
    },
    /**
     * Sets on/off "edit regions" mode
     * Used in Map Editor.
     * @param {bool} on - on/off
     *
     * @private
     */
    setRegionsEditMode : function(on){

        var _this = this;

        _this.editRegions.on = MapSVG.parseBoolean(on);
        _this.deselectAllRegions();
        _this.setEventHandlers();
    },
    /**
     * Enables edit mode (which means that the map is going to be shown in the Map Editor)
     * Used in the Map Editor.
     * @param {bool} on
     * @private
     */
    setEditMode: function(on){

        var _this = this;

        _this.editMode = on;
    },
    /**
     * Enables "Edit objects" mode
     * Used in the Map Editor.
     * @param {bool} on
     * @private
     */
    setDataEditMode : function(on){

        var _this = this;

        _this.editData.on = MapSVG.parseBoolean(on);
        _this.deselectAllRegions();
        _this.setEventHandlers();
    },
    /**
     * Downloads SVG file.
     * @private
     */
    download: function(){

        var _this = this;

        if(!_this.downloadForm) {
            _this.downloadForm = $('<form id="mdownload" action="/wp-content/plugins/mapsvg-dev/download.php" method="POST"><input type="hidden" name="svg_file" value="0" /><input type="hidden" name="svg_title"></form>');
            _this.downloadForm.appendTo('body');
        }
        _this.downloadForm.find('input[name="svg_file"]').val(_this.$svg.prop('outerHTML'));
        _this.downloadForm.find('input[name="svg_title"]').val(_this.options.title);
        setTimeout(function() {
            jQuery('#mdownload').submit();
        }, 500);
    },
    /**
     * Shows the tooltip with provided HTML content.
     * @param {string} html - HTML content
     */
    showTooltip : function(html){

        var _this = this;

        // TODO strip HTML comments, spaces and new lines and then check the length
        if (html.length){
            _this.tooltip.container.html(html);
            _this.tooltip.container.addClass('mapsvg-tooltip-visible');
        }
    },
    /**
     * Adjusts popover position. User on zoom and when map container is resized.
     */
    popoverAdjustPosition: function(){

        var _this = this;

        if(!_this.$popover || !_this.$popover.data('point')) return;

        var pos = _this.convertSVGToPixel(_this.$popover.data('point'));

        // pos[0] = pos[0] - (_this.layers.popovers.offset().left - _this.$map.offset().left);
        // pos[1] = pos[1] - (_this.layers.popovers.offset().top - _this.$map.offset().top);

        _this.$popover[0].style.transform = 'translateX(-50%) translate('+pos[0]+'px,'+pos[1]+'px)';
    },
    /**
     * Shows a popover for provided Region or DB Object.
     * @param {MapSVG.Region|object} object - Region or DB Object
     *
     * @example
     * var region = mapsvg.getRegion("US-TX");
     * mapsvg.showPopover(region);
     */
    showPopover : function (object){

        var _this = this;

        // TODO check why need this:
        // var popoverShown = false;

        var mapObject = object instanceof MapSVG.Region ? object : (object.location && object.location.marker && object.location.marker ? object.location.marker : null);
        if(!mapObject)
            return;

        var point;
        if(mapObject instanceof MapSVG.Marker){
            point = {x: mapObject.x, y: mapObject.y};
        }else{
            point = mapObject.getCenterSVG();
        }
        _this.popover && _this.popover.destroy();
        _this.popover = new MapSVG.PopoverController({
            container: _this.$popover,
            point: point,
            yShift: mapObject instanceof MapSVG.Marker ? mapObject.height : 0,
            template: object instanceof MapSVG.Region ?  _this.templates.popoverRegion : _this.templates.popoverMarker,
            mapsvg: _this,
            data: object instanceof MapSVG.Region ? object.forTemplate() : object,
            mapObject: mapObject,
            scrollable: true,
            withToolbar: MapSVG.isPhone && _this.options.popovers.mobileFullscreen ? false : true,
            events: {
                'shown': function(mapsvg){
                    if(_this.options.popovers.centerOn){
                        var shift = this.container.height()/2;
                        if(_this.options.popovers.centerOn && !(MapSVG.isPhone && _this.options.popovers.mobileFullscreen)){
                            _this.centerOn(mapObject, shift);
                        }
                    }
                    try {
                        _this.events['shown.popover'] && _this.events['shown.popover'].call(this, _this);
                    } catch(err) {
                        console.log(err);
                    }
                    _this.popoverShowingFor = mapObject;
                    _this.trigger('popoverShown');
                },
                'closed': function(mapsvg){
                    _this.options.popovers.resetViewboxOnClose && _this.viewBoxReset(true);
                    //if(mapObject instanceof MapSVG.Region){
                    _this.popoverShowingFor = null;
                    //}
                    try {
                        _this.events['closed.popover'] && _this.events['closed.popover'].call(this, mapsvg);
                    } catch(err) {
                        console.log(err);
                    }
                    _this.trigger('popoverClosed');
                },
                'resize': function(){
                    if(_this.options.popovers.centerOn){
                        var shift = this.container.height()/2;
                        if(_this.options.popovers.centerOn && !(MapSVG.isPhone && _this.options.popovers.mobileFullscreen)){
                            _this.centerOn(mapObject, shift);
                        }
                    }
                }
            }
        });
    },
    /**
     * Hides the popover
     */
    hidePopover : function(){

        var _this = this;

        _this.popover && _this.popover.close();
        // $('body').toggleClass('mapsvg-popover-open', false);
    },
    /**
     * Hides the tooltip
     */
    hideTip : function (){

        var _this = this;

        _this.tooltip.container.removeClass('mapsvg-tooltip-visible');
        //_this.tooltip.container.html('');
    },
    /**
     * Event handler that catches clicks outside of the popover and closes the popover.
     * @param {object} e - Event object
     */
    popoverOffHandler : function(e){

        var _this = this;

        if(_this.isScrolling || $(e.target).closest('.mapsvg-popover').length || $(e.target).hasClass('mapsvg-btn-map'))
            return;
        this.popover && this.popover.close();
    },
    /**
     * Mouseover event handler for Regions and Markers
     * @param {object} e - Event object
     * @param {MapSVG.Marker|MapSVG.Region} object
     * @private
     */
    mouseOverHandler : function(e, object){

        var _this = this;

        if(_this.eventsPreventList['mouseover']){
            return;
        }

        if(_this.options.tooltips.on){
            var name, data;
            if(object instanceof MapSVG.Region) {
                name = 'tooltipRegion';
                data = object.forTemplate();
            }
            if(object instanceof MapSVG.Marker) {
                name = 'tooltipMarker';
                data = object.object;
            }
            if(_this.popoverShowingFor !== object){
                _this.showTooltip(_this.templates[name](data));
            }
        }

        if(_this.options.menu.on){
            if(_this.options.menu.source == 'database'){
                if((object instanceof MapSVG.Region) && object.objects.length) {
                    var ids = object.objects.map(function(obj){
                        return obj.id;
                    });
                }
                if(object instanceof MapSVG.Marker) {
                    var ids = object.object ? object.object.id : [];
                }
            }else{
                if((object instanceof MapSVG.Region)) {
                    var ids = [object.id];
                }
                if(this instanceof MapSVG.Marker && object.object.regions && object.object.regions.length) {
                    var ids = object.object.regions.map(function(obj){
                        return obj.id;
                    });
                }
            }
            _this.controllers.directory.highlightItems(ids);
        }

        if(object instanceof MapSVG.Region) {
            if (!object.selected)
                object.highlight();
            if(_this.events['mouseover.region']){
                try {
                    _this.events['mouseover.region'].call(object, e, _this);
                } catch(err) {
                    console.log(err);
                }
            }
        }else{
            _this.highlightMarker(object);
            if(_this.events['mouseover.marker']){
                try {
                    _this.events['mouseover.marker'].call(object, e, _this);
                } catch(err) {
                    console.log(err);
                }
            }

        }
    },
    /**
     * Mouseout event handler for Regions and Markers
     * @param {object} e - Event object
     * @param {MapSVG.Marker|MapSVG.Region} object
     * @private
     */
    mouseOutHandler : function(e, object){

        var _this = this;

        if(_this.eventsPreventList['mouseout']){
            return;
        }

        if(_this.options.tooltips.on)
            _this.hideTip();
        if(object instanceof MapSVG.Region) {
            if (!object.selected)
                object.unhighlight();
            if(_this.events['mouseout.region']){
                try {
                    _this.events['mouseout.region'].call(object, e, _this);
                } catch (err) {
                    console.log(err);
                }
            }
        }else{
            _this.unhighlightMarker(object);
            if(_this.events['mouseout.marker']){
                try {
                    _this.events['mouseout.marker'].call(object, e, _this);
                } catch (err) {
                    console.log(err);
                }
            }
        }
        if(_this.options.menu.on){
            if(_this.options.menu.source == 'database'){
                if(object instanceof MapSVG.Marker) {
                    var ids = object.object ? object.object.id : [];
                }
            }
            _this.controllers.directory.unhighlightItems();
        }

    },
    /**
     * Updates Markers index
     * @private
     */
    updateMarkersDict : function(){

        var _this = this;

        _this.markersDict = {};
        _this.markers.forEach(function(marker, i){
            _this.markersDict[marker.id] = i;
        });
    },
    /**
     * Prevents provided event from being fired
     * @param {string} event - Event name
     * @private
     */
    eventsPrevent: function(event){

        var _this = this;

        _this.eventsPreventList[event] = true;
    },
    /**
     * Restores event disabled by .eventsPrevent()
     * @param {string} event - Event name
     * @private
     */
    eventsRestore: function(event){

        var _this = this;
        
        if(event){
            _this.eventsPreventList[event] = false;
        } else {
            _this.eventsPreventList = {};
        }

    },
    /**
     * Sets all event handlers
     * @private
     */
    setEventHandlers : function(){

        var _this = this;

        _this.$map.off('.common.mapsvg');
        _this.$scrollpane.off('.common.mapsvg');
        $(document).off('keydown.scroll.mapsvg');
        $(document).off('mousemove.scrollInit.mapsvg');
        $(document).off('mouseup.scrollInit.mapsvg');

        if(_this.editMarkers.on){

            _this.$map.on('touchstart.common.mapsvg mousedown.common.mapsvg', '.mapsvg-marker',function(e){
                e.originalEvent.preventDefault();
                var marker = _this.getMarker($(this).attr('id'));
                var startCoords = MapSVG.mouseCoords(e);
                marker.drag(startCoords, _this.scale, function() {
                    if (_this.mapIsGeo){
                        this.geoCoords = _this.convertSVGToGeo(this.x + this.width / 2, this.y + (this.height-1));
                    }
                    _this.markerEditHandler && _this.markerEditHandler.call(this,true);
                    if(this.onChange)
                        this.onChange.call(this);
                },function(){
                    _this.markerEditHandler && _this.markerEditHandler.call(this);
                    if(this.onChange)
                        this.onChange.call(this);
                });
            });
        }

        // REGIONS
        if(!_this.editMarkers.on) {
            _this.$map.on('mouseover.common.mapsvg', '.mapsvg-region', function (e) {
                var id = $(this).attr('id');
                _this.mouseOverHandler.call(_this, e, _this.getRegion(id));
            }).on('mouseleave.common.mapsvg', '.mapsvg-region', function (e) {
                var id = $(this).attr('id');
                _this.mouseOutHandler.call(_this, e, _this.getRegion(id));
            });
        }
        if(!_this.editRegions.on){
            _this.$map.on('mouseover.common.mapsvg', '.mapsvg-marker', function (e) {
                var id = $(this).attr('id');
                _this.mouseOverHandler.call(_this, e, _this.getMarker(id));
            }).on('mouseleave.common.mapsvg', '.mapsvg-marker', function (e) {
                var id = $(this).attr('id');
                _this.mouseOutHandler.call(_this, e, _this.getMarker(id));
            });
        }

        if(_this.options.scroll.spacebar){
            $(document).on('keydown.scroll.mapsvg', function(e) {
                if(document.activeElement.tagName !=='INPUT' && !_this.isScrolling && e.keyCode == 32){
                    e.preventDefault();
                    _this.$map.addClass('mapsvg-scrollable');
                    $(document).on('mousemove.scrollInit.mapsvg', function(e) {
                        _this.isScrolling = true;
                        $(document).off('mousemove.scrollInit.mapsvg');
                        _this.scrollStart(e,_this);
                    }).on('keyup.scroll.mapsvg', function (e) {
                        if (e.keyCode == 32) {
                            $(document).off('mousemove.scrollInit.mapsvg');
                            _this.$map.removeClass('mapsvg-scrollable');
                        }
                    });
                }
            });
        }else if (!_this.options.scroll.on) {

            if(!_this.editMarkers.on) {
                _this.$map.on('touchstart.common.mapsvg', '.mapsvg-region', function (e) {
                    _this.touchScrollStart = $(window).scrollTop();
                });
                _this.$map.on('touchstart.common.mapsvg', '.mapsvg-marker', function (e) {
                    _this.touchScrollStart = $(window).scrollTop();
                });

                _this.$map.on('touchend.common.mapsvg mouseup.common.mapsvg', '.mapsvg-region', function (e) {
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                    if(_this.touchScrollStart === undefined || _this.touchScrollStart === $(window).scrollTop()){
                        _this.regionClickHandler.call(_this, e, _this.getRegion($(this).attr('id')));
                    }
                    // _this.regionClickHandler.call(_this, e, _this.getRegion($(this).attr('id')));
                });
                _this.$map.on('touchend.common.mapsvg mouseup.common.mapsvg','.mapsvg-marker',  function (e) {
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                    if(_this.touchScrollStart === undefined || _this.touchScrollStart === $(window).scrollTop()){
                        _this.regionClickHandler.call(_this, e, _this.getMarker($(this).attr('id')));
                    }
                });
                _this.$map.on('touchend.common.mapsvg mouseup.common.mapsvg','.mapsvg-marker-cluster',  function (e) {
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                    if(!_this.touchScrollStart || _this.touchScrollStart == $(window).scrollTop()) {
                        var cluster = $(this).data("cluster");
                        _this.zoomTo(cluster.markers);
                    }
                });
                // }
            }else{

                if(_this.clickAddsMarker)
                    _this.$map.on('touchend.common.mapsvg mouseup.common.mapsvg', function (e) {
                        // e.stopImmediatePropagation();
                        if (e.cancelable) {
                            e.preventDefault();
                        }
                        _this.markerAddClickHandler(e);
                    });
            }
        } else {

            _this.$map.on('touchstart.common.mapsvg mousedown.common.mapsvg', function(e){

                if($(e.target).hasClass('mapsvg-popover')||$(e.target).closest('.mapsvg-popover').length){
                    // Prevent even dobule firing touchstart+mousedown on clicking popover close button
                    if($(e.target).hasClass('mapsvg-popover-close')){
                        if(e.type=='touchstart'){
                            if (e.cancelable) {
                                e.preventDefault();
                            }
                        }
                    }
                    return;
                }

                if(e.type=='touchstart'){
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                }

                if(e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-region')!=-1){
                    var obj = _this.getRegion($(e.target).attr('id'));
                    _this.scrollRegionClickHandler.call(_this, e, obj);
                }else if(e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-marker')!=-1 && $(e.target).attr('class').indexOf('mapsvg-marker-cluster')===-1){
                    if(_this.editMarkers.on){
                        return;
                    }
                    var obj = _this.getMarker($(e.target).attr('id'));
                    _this.scrollRegionClickHandler.call(_this, e, obj);
                }else if(e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-marker-cluster')!=-1){
                    if(_this.editMarkers.on){
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

        var _this = this;

        options = options || _this.options.labelsRegions;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        $.extend(true, _this.options, {labelsRegions: options});

        if(_this.options.labelsRegions.on){
            _this.regions.forEach(function (region) {
                if(!region.textLabel){
                    region.textLabel = jQuery('<div class="mapsvg-region-label" />')
                    _this.$scrollpane.append(region.textLabel);
                }
                try{
                    region.textLabel.html(_this.templates.labelRegion(region.forTemplate()));
                }catch (err) {
                    console.error('MapSVG: Error in the "Region Label" template');
                }

            });
            _this.labelsRegionsAdjustPosition();
        } else {
            _this.regions.forEach(function (region) {
                if(region.textLabel){
                    region.textLabel.remove();
                    region.textLabel = null;
                    delete region.textLabel;
                }
            });
        }
    },
    /**
     * Deletes Marker lables
     */
    deleteLabelsMarkers: function(){

        var _this = this;

        _this.markers.forEach(function (marker) {
            if(marker.textLabel){
                marker.textLabel.remove();
                marker.textLabel = null;
                delete marker.textLabel;
            }
        });
    },
    /**
     * Sets Marker labels
     * @param {object} options
     */
    setLabelsMarkers: function(options){

        var _this = this;

        options = options || _this.options.labelsMarkers;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        $.extend(true, _this.options, {labelsMarkers: options});

        if(_this.options.labelsMarkers.on){
            _this.markers.forEach(function (marker) {
                if(!marker.textLabel){
                    marker.textLabel = jQuery('<div class="mapsvg-marker-label" data-object-id="'+marker.object.id+'"/>');
                    _this.$scrollpane.append(marker.textLabel);
                }
                try{
                    marker.textLabel.html(_this.templates.labelMarker(marker.object));
                }catch (err) {
                    console.error('MapSVG: Error in the "Marker Label" template');
                }
            });
            _this.markersAdjustPosition();
        } else {
            _this.deleteLabelsMarkers();
        }
    },
    /**
     * Adds a layer to the map that may contain some objects.
     * @param {string} name - Layer name
     * @private
     */
    addLayer: function(name){

        var _this = this;

        _this.layers[name] = $('<div class="mapsvg-layer mapsvg-layer-'+name+'"></div>');
        _this.$layers.append(_this.layers[name]);
        return _this.layers[name];
    },
    /**
     * Returns database service
     * @returns {MapSVG.DatabaseService}
     * @private
     */
    getDatabaseService: function(){

        var _this = this;

        return this.database;
    },
    /**
     * Returns Database
     * @returns {MapSVG.DatabaseService}
     * @example
     * var objects = mapsvg.getDatabase().getLoaded();
     * var object  = mapsvg.getDatabase().getLoadedObject(12);
     *
     * // Filter DB by region "US-TX". This automatically triggers markers and directory reloading
     * mapsvg.getDatabase().getAll({filters: {regions: "US-TX"}});
     */
    getDb: function(){

        var _this = this;

        return this.database;
    },
    /**
     * Returns Regions database
     * @returns {MapSVG.DatabaseService}
     */
    getDbRegions: function(){

        var _this = this;

        return this.regionsDatabase;
    },
    /**
     * Adds an SVG object as Region to MapSVG
     * @param {object} svgObject - SVG Object
     * @returns {MapSVG.Region}
     * @private
     */
    regionAdd: function(svgObject){

        var _this = this;

        var region = new MapSVG.Region($(svgObject), _this.options, _this.regionID, _this);
        region.setStatus(1);
        _this.regions.push(region);
        _this.regions.sort(function(a,b){
            return a.id == b.id ? 0 : +(a.id > b.id) || -1;
        });
        _this.regions.forEach(function(region, index){
            _this.regionsDict[region.id] = index;
        });
        return region;
    },
    /**
     * Deletes a Region from the map
     * @param {string} id - Region ID
     * @private
     */
    regionDelete: function(id){

        var index = _this.regionsDict[id];

        if(index !== undefined){
            var r = _this.getRegion(id);
            r.node && r.node.remove();
            _this.regions.splice(index,1);
            delete _this.regionsDict[id];
        }else{
            if($('#'+id).length){
                $('#'+id).remove();
            }
        }
    },
    /**
     * Reloads Regions from SVG file
     */
    reloadRegions : function(){

        var _this = this;

        _this.regions = [];
        _this.regionsDict = {};
        _this.$svg.find('.mapsvg-region').removeClass('mapsvg-region');
        _this.$svg.find('.mapsvg-region-disabled').removeClass('mapsvg-region-disabled');
        _this.$svg.find('path, polygon, circle, ellipse, rect').each(function(index){
            if($(this).closest('defs').length)
                return;
            if($(this)[0].getAttribute('id')) {
                if(!_this.options.regionPrefix || (_this.options.regionPrefix && $(this)[0].getAttribute('id').indexOf(_this.options.regionPrefix)===0)){
                    var region = new MapSVG.Region($(this), _this.options, _this.regionID, _this);
                    _this.regions.push(region);
                }
            }
            // if($(this).css('stroke-width')){
            //     $(this).data('stroke-width', $(this).css('stroke-width').replace('px',''));
            // }
        });
        _this.regions.sort(function(a,b){
            return a.id == b.id ? 0 : +(a.id > b.id) || -1;
        });
        _this.regions.forEach(function(region, index){
            _this.regionsDict[region.id] = index;
        });
    },
    /**
     * Reloads Regions data
     */
    reloadRegionsFull : function(){

        var _this = this;

        var statuses = _this.regionsDatabase.getSchemaFieldByType('status');

        _this.regions.forEach(function(region){
            var r = _this.regionsDatabase.getLoadedObject(region.id);

            if(r){
                region.data = r;
                if(statuses && r.status !== undefined && r.status!==null){
                    region.setStatus(r.status);
                    // _this.setRegionStatus(region, object.status);
                }
            } else {
                if(_this.options.filters.filteredRegionsStatus!==null && _this.options.filters.filteredRegionsStatus!=='' && _this.options.filters.filteredRegionsStatus!==undefined){
                    region.setStatus(_this.options.filters.filteredRegionsStatus);
                }
            }
        });

        /*
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
         */

        _this.loadDirectory();
        _this.setGauge();
        _this.setLayersControl();
        _this.setGroups();
        if(_this.options.labelsRegions.on){
            _this.setLabelsRegions();
        }
    },
    /**
     * Fix "fit markers" view world screen offset
     * @private
     */
    fixMarkersWorldScreen: function(){
        var _this = this;

        if(_this.googleMaps.map) setTimeout(function(){
            var markers = {left: 0, right: 0};
            if(_this.markers.length > 1){
                _this.markers.forEach(function(m){
                    if(m.node.offset().left < (_this.$map.offset().left + _this.$map.width() / 2)){
                        markers.left++;
                    }else{
                        markers.right++;
                    }
                });

                if(markers.left === 0 || markers.right === 0){
                    var k = markers.left === 0 ? 1 : -1;
                    var ww = ((_this.svgDefault.viewBox[2] / _this.mapLonDelta) * 360) * _this.getScale();
                    _this.googleMaps.map.panBy(k*ww,0);
                }
            }
        }, 600);
    },
    /**
     * Updates map options from old version of MapSVG
     * @param options
     * @private
     */
    updateOutdatedOptions: function(options){

        var _this = this;

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
        // Transfer zoom options to controls options
        if(!options.controls){
            options.controls = {};
            options.controls.zoom = options.zoom && options.zoom.on && options.zoom.buttons.location!=='hide';
            options.controls.location = options.zoom && options.zoom.buttons.location !== 'hide' ? options.zoom.buttons.location : 'right';
        }
        // Transfer zoom options to controls options
        if(options.colors && !options.colors.markers){
            options.colors.markers = {
                base:      {opacity:100, saturation: 100},
                hovered:   {opacity:100, saturation: 100},
                unhovered: {opacity:100,  saturation: 100},
                active:    {opacity:100, saturation: 100},
                inactive:  {opacity:100,  saturation: 100}
            };
        }
    },
    /**
     * Initialization. Map rendering happens here. Gets called on map creation.
     * @param {object} opts - Map options
     * @param elem
     * @returns {*}
     * @private
     */
    init: function(opts, elem) {

        var _this = this;

        if(!opts.source) {
            throw new Error('MapSVG: please provide SVG file source.');
            return false;
        }

        // Extensions:
        // 1. Create Element.remove() function if not exists
        if (!('remove' in Element.prototype)) {
            Element.prototype.remove = function() {
                if (this.parentNode) {
                    this.parentNode.removeChild(this);
                }
            };
        }
        // 2. Hypot
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
        // 2. SVG getTransformToElement
        SVGElement.prototype.getTransformToElement = SVGElement.prototype.getTransformToElement || function(toElement) {
            return toElement.getScreenCTM().inverse().multiply(this.getScreenCTM());
        };



        // cut domain to avoid cross-domain errors
        if(opts.source.indexOf('//')===0)
            opts.source = opts.source.replace(/^\/\/[^\/]+/, '').replace('//','/');
        else
            opts.source = opts.source.replace(/^.*:\/\/[^\/]+/, '').replace('//','/');

        _this.editMode = opts.editMode;
        delete opts.editMode;

        _this.updateOutdatedOptions(opts);

        _this.options = $.extend(true, {}, _this.defaults, opts);


        _this.id = _this.options.db_map_id;
        if(_this.id == 'new')
            _this.id = null;
        _this.highlightedRegions = [];
        _this.editRegions = {on:false};
        _this.editMarkers = {on:false};
        _this.editData    = {on:false};
        _this.map  = elem;
        _this.$map = $(elem);
        _this.$scrollpane = $('<div class="mapsvg-scrollpane"></div>').appendTo(_this.$map);
        _this.$layers = $('<div class="mapsvg-layers-wrap"></div>').appendTo(_this.$scrollpane);

        _this.whRatio = 0;
        _this.isScrolling = false;
        _this.markerOptions = {};
        _this.svgDefault = {};
        _this.refLength = 0;
        _this.scale  = 1;         // absolute scale
        _this._scale = 1;         // relative scale starting from current zoom level
        _this.selected_id    = [];
        _this.mapData        = {};
        _this.regions        = [];
        _this.regionsDict    = {};
        _this.regionID       = {id: 0};
        _this.markers        = [];
        _this.markersDict    = {};
        _this.markersClusters = [];
        _this.markersClustersDict = [];
        _this._viewBox       = []; // initial viewBox
        _this.viewBox        = []; // current viewBox
        _this.viewBoxZoom    = [];
        _this.viewBoxFind    = undefined;
        _this.zoomLevel      = 0;
        _this.scroll         = {};
        _this.layers         = {};
        _this.geoCoordinates = false;
        _this.geoViewBox     = {leftLon:0, topLat:0, rightLon:0, bottomLat:0};
        _this.eventsPreventList  = {};
        _this.googleMaps     = {loaded: false, initialized: false, map: null, zoomLimit: true};

        _this.setEvents(opts.events);

        // todo remove duplicate beforeload
        if(_this.events.beforeLoad)
            try{_this.events.beforeLoad.call(_this);}catch(err){}

        if(_this.events.beforeLoad && _this.events.beforeLoad['beforeLoad'] && typeof _this.events.beforeLoad['beforeLoad'] === 'function'){
            try{
                _this.events.beforeLoad['beforeLoad'].call( _this)
            }catch(err){
                console.log(err);
            }

        }


        _this.setCss();


        // Set background
        _this.$map.addClass('mapsvg').addClass('no-transitions').css('background',_this.options.colors.background);

        // _this.disableMarkersAnimation();

        _this.setContainers(_this.options.containers);
        _this.setColors();


        // _this.$ratio = $('<div class="mapsvg-ratio"></div>');
        // _this.$ratio.insertBefore(_this.$map);
        // _this.$ratio.append(_this.$map);

        _this.$loading = $('<div>'+_this.options.loadingText+'</div>').addClass('mapsvg-loading');
        _this.$map.append(_this.$loading);

        // _this.$mapRatioSize = $('<div class="mapsvg-ratio"></div>').insertBefore(_this.$map);
        // _this.$map.appendTo(_this.$mapRatioSize);

        _this.addLayer('markers');
        _this.addLayer('popovers');

        _this.$loading.css({
            'margin-left': function () {
                return -($(this).outerWidth(false) / 2)+'px';
            },
            'margin-top': function () {
                return -($(this).outerHeight(false) / 2)+'px';
            }
        });
        if(_this.options.googleMaps.on){
            _this.$map.addClass('mapsvg-google-map-loading');
        }

        // Load extension (common things)
        if(_this.options.extension && $().mapSvg.extensions && $().mapSvg.extensions[_this.options.extension]){
            var ext = $().mapSvg.extensions[_this.options.extension];
            ext && ext.common(_this);
        }


        // GET the map by ajax request
        $.ajax({url: _this.options.source+'?v='+_this.options.svgFileVersion}).fail(function(resp){
            if(resp.status == 404){
                var msg = 'MapSVG: file not found - '+_this.options.source+'\n\nIf you moved MapSVG from another server please read the following docs page: https://mapsvg.com/docs/installation/moving';
                if(_this.editMode) {
                    alert(msg);
                } else {
                    console.error(msg);
                }
            } else {
                var msg = 'MapSVG: can\'t load SVG file for unknown reason. Please contact support: https://mapsvg.ticksy.com';
                if(_this.editMode) {
                    alert(msg);
                } else {
                    console.error(msg);
                }
            }
        }).done(function(xmlData){

            // Default width/height/viewBox from SVG
            var svgTag               = $(xmlData).find('svg');
            _this.$svg               = svgTag;

            _this.svgDefault.width   = svgTag.attr('width');
            _this.svgDefault.height  = svgTag.attr('height');
            _this.svgDefault.viewBox = svgTag.attr('viewBox');

            if(_this.svgDefault.width && _this.svgDefault.height){
                _this.svgDefault.width   = parseFloat(_this.svgDefault.width.replace(/px/g,''));
                _this.svgDefault.height  = parseFloat(_this.svgDefault.height.replace(/px/g,''));
                _this.svgDefault.viewBox = _this.svgDefault.viewBox ? _this.svgDefault.viewBox.split(' ') : [0,0, _this.svgDefault.width, _this.svgDefault.height];
            }else if(_this.svgDefault.viewBox){
                _this.svgDefault.viewBox = _this.svgDefault.viewBox.split(' ');
                _this.svgDefault.width   = parseFloat(_this.svgDefault.viewBox[2]);
                _this.svgDefault.height  = parseFloat(_this.svgDefault.viewBox[3]);
            }else{
                var msg = 'MapSVG: width/height and viewBox are missing in the SVG file. Can\'t parse the file because of that.';
                if(_this.editMode){
                    alert(msg);
                } else {
                    console.error(msg);
                }
                return false;
            }
            // Get geo-coordinates view  box from SVG file
            var geo               = svgTag.attr("mapsvg:geoViewBox") || svgTag.attr("mapsvg:geoviewbox");
            if (geo) {
                geo = geo.split(" ");
                if (geo.length == 4){
                    _this.mapIsGeo = true;
                    _this.geoCoordinates = true;

                    _this.geoViewBox = {leftLon: parseFloat(geo[0]),
                        topLat: parseFloat(geo[1]),
                        rightLon: parseFloat(geo[2]),
                        bottomLat: parseFloat(geo[3])
                    };
                    _this.mapLonDelta = _this.geoViewBox.rightLon - _this.geoViewBox.leftLon;
                    _this.mapLatBottomDegree = _this.geoViewBox.bottomLat * 3.14159 / 180;

                }

            }

            $.each(_this.svgDefault.viewBox, function(i,v){
                _this.svgDefault.viewBox[i] = parseFloat(v);
            });

            _this._viewBox  = (_this.options.viewBox.length==4 && _this.options.viewBox ) || _this.svgDefault.viewBox;

            $.each(_this._viewBox, function(i,v){
                _this._viewBox[i] = parseFloat(v);
            });

            svgTag.attr('preserveAspectRatio','xMidYMid meet');
            svgTag.removeAttr('width');
            svgTag.removeAttr('height');

            //// Adding moving sticky draggable image on background
            //if(_this.options.scrollBackground)
            //    _this.background = _this.R.rect(_this.svgDefault.viewBox[0],_this.svgDefault.viewBox[1],_this.svgDefault.viewBox[2],_this.svgDefault.viewBox[3]).attr({fill: _this.options.colors.background});

            _this.reloadRegions();

            _this.$scrollpane.append(svgTag);


            // Set size
            _this.setSize(_this.options.width, _this.options.height, _this.options.responsive);


            if(_this.options.disableAll){
                _this.setDisableAll(true);
            }



            // Set viewBox
            _this.setViewBox(_this._viewBox);
            _this.setResponsive(_this.options.responsive,true);


            // var markers = _this.options.markers || _this.options.marks || [];
            // _this.setMarkers(markers);

            _this.setScroll(_this.options.scroll, true);

            _this.setZoom(_this.options.zoom);

            _this.setControls(_this.options.controls);
            _this.setGoogleMaps();

            // _this.setViewBox([0,0,_this.svgDefault.viewBox[0]*2+_this.svgDefault.viewBox[2],_this.svgDefault.viewBox[1]*2+_this.svgDefault.viewBox[3]]);


            // Set tooltips
            // tooltipsMode is deprecated, need this for backward compatibility
            if (_this.options.tooltipsMode)
                _this.options.tooltips.mode = _this.options.tooltipsMode;
            _this.setTooltips(_this.options.tooltips);

            // Set popovers
            // popover is deprecated (now it's popoverS), need this for backward compatibility
            if (_this.options.popover)
                _this.options.popovers = _this.options.popover;
            _this.setPopovers(_this.options.popovers);

            if(_this.options.cursor)
                _this.setCursor(_this.options.cursor);

            _this.setTemplates(_this.options.templates);


            // Load extension (frontend things)
            if(!_this.options.backend && _this.options.extension &&  $().mapSvg.extensions &&  $().mapSvg.extensions[_this.options.extension]){
                var ext = $().mapSvg.extensions[_this.options.extension];
                ext && ext.frontend(_this);
            }

            _this.filtersSchema = new MapSVG.Filters(_this.options.filtersSchema);

            // Load data from Database and finish loading
            _this.database = new MapSVG.DatabaseService({
                map_id    : _this.id,
                perpage   : _this.options.database.pagination.on ? _this.options.database.pagination.perpage : 0,
                sortBy    : _this.options.menu.source == 'database' ? _this.options.menu.sortBy : 'id',
                sortDir   : _this.options.menu.source == 'database' ?_this.options.menu.sortDirection : 'desc',
                table     : 'database'
            }, _this);
            // _this.events['databaseLoaded'] && _this.database.on('dataLoaded', _this.events['databaseLoaded']);

            // _this.firstDataLoad = true;
            _this.database.on('dataLoaded', function(){
                _this.fitOnDataLoadDone = false;
                _this.addLocations();
                _this.fixMarkersWorldScreen();
                // _this.addDataObjectsAsMarkers();
                _this.attachDataToRegions();
                _this.loadDirectory();
                if(_this.options.labelsMarkers.on){
                    _this.setLabelsMarkers();
                }
                // Check if there's a call to "{{objects}}" inside of the labelRegion template
                // and if it's found then reload Region Labels
                if(_this.options.templates.labelRegion.indexOf('{{objects' !== -1)){
                    _this.setLabelsRegions();
                }
                try{
                    _this.events['databaseLoaded'] && _this.events['databaseLoaded'].call(_this);
                }catch (err){
                    console.log(err);
                }

                _this.updateFiltersState();
            });
            _this.database.on('schemaChange', function () {
                // _this.setMarkersClickAsLink();
                _this.database.getAll();
            });

            _this.database.on('create', function(obj){
                _this.attachDataToRegions(obj);
                _this.reloadRegionsFull();
            });
            _this.database.on('update', function(obj){
                _this.attachDataToRegions(obj);
                _this.reloadRegionsFull();
            });
            _this.database.on('delete', function(id){
                _this.attachDataToRegions();
                _this.reloadRegionsFull();
            });

            _this.regionsDatabase = new MapSVG.DatabaseService({
                map_id    : _this.id,
                perpage   : 0,
                sortBy    : _this.options.menu.source == 'regions' ? _this.options.menu.sortBy : 'id',
                sortDir   : _this.options.menu.source == 'regions' ? _this.options.menu.sortDirection : 'desc',
                table     : 'regions'
            }, _this);
            // _this.events['regionsLoaded'] && _this.regionsDatabase.on('dataLoaded', _this.events['regionsLoaded']);
            _this.regionsDatabase.on('dataLoaded', function(){
                _this.reloadRegionsFull();
                if(_this.events['regionsLoaded']){
                    if(_this.events['regionsLoaded'].length && _this.events['regionsLoaded'].length > 0){
                        _this.trigger('regionsLoaded', _this);
                    } else {
                        try{
                            _this.events['regionsLoaded'].call(_this);
                        } catch (err){
                            console.log(err);
                        }
                    }
                }
            });

            // Event 'change' covers 3 events: create, update, delete
            _this.regionsDatabase.on('change', function() {
                _this.reloadRegionsFull();
            });


            _this.menuDatabase = _this.options.menu.source == 'regions' ? _this.regionsDatabase : _this.database;
            _this.setMenu();
            _this.setFilters();

            if(_this.options.menu.filterout.field){
                var f = {};
                f[_this.options.menu.filterout.field] = _this.options.menu.filterout.val;
                if(_this.options.menu.source == 'regions'){
                    _this.regionsDatabase.query.setFilterOut(f);
                }else{
                    _this.database.query.setFilterOut(f);
                }
            }

            _this.setEventHandlers();

            if(!_this.id){
                _this.final();
                return;
            }

            if(!_this.options.data_regions || !_this.options.data_db){
                _this.regionsDatabase.getAll().done(function(regions){
                    if(_this.options.database.loadOnStart || _this.editMode){
                        _this.database.getAll().done(function (data) {
                            _this.final();
                        });
                    } else {
                        _this.final();
                    }
                });
            } else {
                _this.regionsDatabase.fill(_this.options.data_regions);
                if(_this.editMode || _this.options.database.loadOnStart){
                    _this.database.fill(_this.options.data_db);
                }
                delete _this.options.data_regions;
                delete _this.options.data_db;
            }

            _this.final();

        }); // end of SVG LOAD AJAX

        return _this;

    },
    /**
     * Final stage of initialization.
     * Initialization is split into 2 steps because the 1st step contains async ajax request.
     * When the ajax requires is done, the final initialization step is called.
     * @private
     */
    final: function(){

        var _this = this;

        if(_this.options.googleMaps.on && !_this.googleMaps.map){
            _this.on('googleMapsLoaded',function(){
                _this.final();
            });
            return;
        }

        // Select region from URL
        if( match = RegExp('[?&]mapsvg_select=([^&]*)').exec(window.location.search)){
            var select = decodeURIComponent(match[1].replace(/\+/g, ' '));
            _this.selectRegion(select);
        }
        if(window.location.hash){
            var query = window.location.hash.replace('#/m/','');
            var region = _this.getRegion(query);
            if(region && _this.options.actions.map.afterLoad.selectRegion){
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
                _this.$map.removeClass('no-transitions');
            },200);
        },100);

        if(_this.events['afterLoad'] && typeof _this.events['afterLoad'] === 'function'){
            try{
                _this.events['afterLoad'].call(_this);
            }catch (err){
                console.log(err);
            }

        }

        if (_this.editMode && _this.options.afterLoad){
            _this.options.afterLoad.call(_this);
        }
        if (_this.options.dataLoaded)
            _this.options.dataLoaded.call();

        _this.$loading.hide();
        MapSVG.addInstance(_this);
    }
};

$.fn.mapSvg = function( opts ) {
    if(typeof opts === 'object'){
        var map = new MapSVG.Map($(this).attr('id'), opts);
        $(this).data('_mapsvg', map);
        return map;
    } else {
        return $(this).data('_mapsvg');
    }
}; // end of $.fn.mapSvg


/**
 * MapOptions object used to define the properties that can be set on a Map.
 * @typedef {Object} MapSVG.MapOptions
 * @property {string} source - SVG file URL
 * @property {boolean} disableAll - Disables all regions
 * @property {boolean} responsive - Map responsiveness. Default: true
 * @property {string} loadingText - Loading text message. Default is "Loading map..."
 * @property {number} width - Width of the map
 * @property {number} height - Height of the map
 * @property (array) viewBox - Map viewBox: [x,y,width,height]
 * @property {boolean} lockAspectRatio - Keep aspect ratio when changing width or height
 * @property {object} padding - Map padding, default: {top: 0, left: 0, right: 0, bottom: 0}
 * @property {string} cursor - Mouse pointer style: "default' or "pointer"
 * @property {boolean} multiSelect - Allows to select multiple Regions. Default: false
 * @property {object} colors - Color settings. Example: {base: "#E1F1F1", background: "#eeeeee", hover: "#548eac", selected: "#065A85", stroke: "#7eadc0"},
 * @property {object} clustering - Clustering settings. Default: {on: false},
 * @property {object} zoom - Zoom options. Default: {on: false, limit: [0,10], delta: 2, buttons: {on: true, location: 'right'}, mousewheel: true, fingers: true},
 * @property {object} scroll - Scroll options. Default: {on: false, limit: false, background: false, spacebar: false},
 * @property {object} tooltips - Tooltip options. Default: {on: false, position: 'bottom-right', template: '', maxWidth: '', minWidth: 100},
 * @property {object} popovers - Popover options. Default: {on: false, position: 'top', template: '', centerOn: true, width: 300, maxWidth: 50, maxHeight: 50},
 * @property {object} regionStatuses - List of Region statuses
 * @property {object} events - List of callbacks
 * @property {object} gauge - Choropleth map settings. Default: {on: false, labels: {low: "low", high: "high"}, colors: {lowRGB: null, highRGB: null, low: "#550000", high: "#ee0000"}, min: 0, max: 0},
 * @property {object} labelsMarkers - Marker labels settings. Default: {on:false}
 * @property {object} labelsMarkers - Region labels settings. Default: {on:false}
 */
})( jQuery );
(function( $ ) {
/**
 * Abstract MapObject class. Extended by {@link #region|MapSVG.Region} & {@link #marker|MapSVG.Marker}
 * @abstract
 * @param {object} jQueryObject
 * @param {MapSVG.Map} mapsvg
 * @constructor
 */
MapSVG.MapObject = function MapObject(jQueryObject, mapsvg){
    this.id = "";
    this.objects = [];
    this.events = {};
    this.data = {};
    this.node = jQueryObject;
    this.mapsvg = mapsvg;
    this.nodeType = jQueryObject[0].tagName;
};

/**
 * Checks whether the object is Marker
 * @returns {boolean}
 */
MapSVG.MapObject.prototype.isMarker = function(){
    return this instanceof MapSVG.Marker;
};
/**
 * Checks whether the object is Region
 * @returns {boolean}
 */
MapSVG.MapObject.prototype.isRegion = function(){
    return this instanceof MapSVG.Region;
};
/**
 * Adds custom data to object
 * @param {object} data - Any set of {key:value} pairs
 */
MapSVG.MapObject.prototype.setData = function(data){
    var _this = this;
    for(var name in data){
        _this.data[name] = data[name];
    }
};
/**
 * Returns bounding box of an object in SVG coordinates
 * @returns {*[]} - [x,y,width,height]
 * @abstract
 * @private
 */
MapSVG.MapObject.prototype.getBBox = function(){
};
/**
 * Returns geo-bounds of an object - South-West & North-East points.
 * @returns {{sw: (number[]), ne: (number[])}}
 */
MapSVG.MapObject.prototype.getGeoBounds = function(){
    var bbox = this.getBBox();
    var sw = this.mapsvg.convertSVGToGeo(bbox[0], (bbox[1] + bbox[3]));
    var ne = this.mapsvg.convertSVGToGeo((bbox[0] + bbox[2]), bbox[1]);

    return {sw: sw,ne: ne};
};
/**
 * Returns style of a given property of an SVG object
 * @param {string} prop - property name
 * @param {object} node - SVG object
 * @returns {string} - style
 */
MapSVG.MapObject.prototype.getComputedStyle = function(prop, node){
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
/**
 * Returns style of a property of the SVG object
 * @param {string} prop - property name
 * @returns {string}
 */
MapSVG.MapObject.prototype.getStyle = function(prop){
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
/**
 * Returns center of an object in pixel coordinates
 * @returns {number[]} - [x,y]
 */
MapSVG.MapObject.prototype.getCenter = function(){

    // var c = this.getBBox();

    var x = this.node[0].getBoundingClientRect().left;
    var y = this.node[0].getBoundingClientRect().top;
    var w = this.node[0].getBoundingClientRect().width;
    var h = this.node[0].getBoundingClientRect().height;
    return [x+w/2,y+h/2];
};
/**
 * Returns center of an object in SVG coordinates
 * @returns {{x: number, y: number}}
 */
MapSVG.MapObject.prototype.getCenterSVG = function(){
    var _this = this;
    var c = _this.getBBox();
    return {x: c[0]+c[2]/2, y: c[1]+c[3]/2};
};
/**
 * Returns center of an object in geo-coordinates
 * @returns {{lat: number, lng: number}}
 */
MapSVG.MapObject.prototype.getCenterLatLng = function(yShift){
    yShift = yShift ? yShift : 0;
    var bbox = this.getBBox();
    var x = bbox[0] + bbox[2]/2;
    var y = bbox[1] + bbox[3]/2 - yShift;
    var latlng = this.mapsvg.convertSVGToGeo(x,y);
    return {lat: latlng[0], lng: latlng[1]};
};
/**
 * Sets attribute of an SVG object
 * @param {string|object} v1 - attribute name or object: {name: value, name: value}
 * @param {string|number} v2 - value
 * @returns {*}
 */
MapSVG.MapObject.prototype.attr = function(v1,v2){
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
/**
 * Set ID of an object
 * @param {string} id
 */
MapSVG.MapObject.prototype.setId = function(id){
    if(id !== undefined) {
        this.id = id;
        this.node[0].setAttribute('id',id);
    }
};

/**
 * Attaches event handler to the object
 * @param {string} event - Event name
 * @param {function} callback
 */
MapSVG.MapObject.prototype.on = function(event, callback) {
    if (!this.events[event]){
        this.events[event] = [];
    }
    this.events[event].push(callback);
};
/**
 * Removes event hanlder
 * @param {string} event - Event name
 */
MapSVG.MapObject.prototype.off = function(event) {
    for(var eventName in this.events){
        if(this.events[eventName] && this.events[eventName].length > 0){
            if(eventName.indexOf(event) === 0 && event.length <= eventName){
                this.events[eventName] = [];
            }
        }
    }
};
/**
 * Fires an event
 * @param {string} event - Event name
 */
MapSVG.MapObject.prototype.trigger = function(event){
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

})( jQuery );
(function( $ ) {
/**
 * Marker class.
 * @extends MapSVG.MapObject
 * @param params
 * @returns {boolean}
 * @constructor
 *
 * @example
 * var location = new MapSVG.Location({
 *   lat: 55.22,
 *   lng: 64.12,
 *   img: "/path/to/image.png"
 *  });
 *
 * var marker = new MapSVG.Marker({
 *   location: location,
 *   mapsvg: mapsvgInstance
 * });
 *
 * // The marker is created but still not added to the map. Let's add it:
 * mapsvg.markerAdd(marker);
 *
 */
MapSVG.Marker = function(params){

    this.imagePath = params.location.markerImageUrl;
    var img = $('<img src="'+this.imagePath+'" />').addClass('mapsvg-marker');
    MapSVG.MapObject.call(this, img, this.mapsvg);

    this.location = params.location;
    this.location.marker = this;
    this.mapsvg   = params.mapsvg;
    params.object && this.setObject(params.object);
    this.positioned = false;

    if(params.width && params.height){
        this.width = params.width;
        this.height = params.height;
    }

    this.setId(this.mapsvg.markerId());

    if(this.mapsvg.isGeo() && MapSVG.isNumber(this.location.lat) && MapSVG.isNumber(this.location.lng)){
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

    this.setAltAttr();

    // this.adjustPosition();
    // this.mapsvg.markerAdd(this);

    this.positioned = true;
}
MapSVG.extend(MapSVG.Marker, MapSVG.MapObject);
/**
 * Set ID of the Marker
 * @param {string} id
 */
MapSVG.Marker.prototype.setId = function(id){
    MapSVG.MapObject.prototype.setId.call(this, id);
    this.mapsvg.updateMarkersDict();
};
/**
 * Get SVG bounding box of the Marker
 * @returns {*[]} - [x,y,width,height]
 */
MapSVG.Marker.prototype.getBBox = function(){
    var _data = this.mapsvg.getData();
    // TODo this place needs marker.width/height!
    var bbox = {x: this.x, y: this.y, width: this.width/_data.scale, height: this.  height/_data.scale};
    bbox = $.extend(true, {}, bbox);

    return [bbox.x,bbox.y,bbox.width,bbox.height];
};
/**
 * Get Marker options
 * @returns {{id: string, src: string, x: number, y: number, geoCoords: []}}
 */
MapSVG.Marker.prototype.getOptions = function(){
    var o = {
        id: this.id,
        src: this.src,
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
/**
 * Update marker settings
 * @param {object} data - Options
 * @param {float} mapScale - scale of the map
 */
MapSVG.Marker.prototype.update = function(data, mapScale){
    for(var key in data){
        // check if there's a setter for a property
        var setter = 'set'+MapSVG.ucfirst(key);
        if (setter in this)
            this[setter](data[key],mapScale);
    }
};
/**
 * Set image of the Marker
 * @param {string} src
 * @param {number} mapScale
 */
MapSVG.Marker.prototype.setImage = function(src, mapScale){
    if(!src)
        return false;
    var _this = this;
    src = MapSVG.safeURL(src);
    mapScale = mapScale || this.mapScale;
    var img  = new Image();
    var marker = this;
    this.src = src;
    if(marker.node[0].getAttribute('src')!=='src'){
        marker.node[0].setAttribute('src', src);
    }
    img.onload = function(){
        // marker.default.width = this.width;
        // marker.default.height = this.height;
        // marker.attr({x: marker.x, y: marker.y, width: this.width, height: this.height});
        marker.width = this.width;
        marker.height = this.height;
        _this.adjustPosition();
    };
    img.src  = src;
    if(this.location){
        this.location.setImage(src);
    }

};
/**
 * Set 'alt' attribute of the Marker
 */
MapSVG.Marker.prototype.setAltAttr = function(){
    var marker = this;
    var altAttr = (typeof marker.object != 'undefined') && (typeof marker.object.title != 'undefined')&&(marker.object.title !== '') ? marker.object.title : marker.id;

    this.node[0].alt = altAttr;
};
/**
 * Set x/y coordinates of the Marker
 * @param {Array} xy - [x,y]
 */
MapSVG.Marker.prototype.setXy = function(xy){
    this.x = xy[0] || this.x;
    this.y = xy[1] || this.y;
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
/**
 * Moves marker to a point.
 * @private
 * @param {Array} xy
 */
MapSVG.Marker.prototype.moveToClick = function(xy){

    var _data = this.mapsvg.getData();
    var markerOptions = {};

    xy[0] = xy[0] + _data.viewBox[0];
    xy[1] = xy[1] + _data.viewBox[1];


    if(_data.mapIsGeo)
        this.geoCoords = this.mapsvg.convertSVGToGeo(xy[0], xy[1]);

    markerOptions.xy = xy;
    this.update(markerOptions);
};
/**
 * Adjusts position of the Marker. Called on map zoom and on map container resize.
 */
MapSVG.Marker.prototype.adjustPosition = function(){
    var _this = this;
    var pos = _this.mapsvg.convertSVGToPixel([this.x, this.y]);

    if(pos[0] > 30000000){
        this.node[0].style.left = pos[0]-30000000+'px';
        pos[0] = 30000000;
        if(this.textLabel) {
            this.textLabel[0].style.left = pos[0]-30000000+'px';
        }
    }else{
        this.node[0].style.left = 0;
    }
    if(pos[1] > 30000000){
        this.node[0].style.top = pos[1]-30000000+'px';
        pos[1] = 30000000;
        if(this.textLabel) {
            this.textLabel[0].style.top = pos[1]-30000000+'px';
        }
    }else{
        this.node[0].style.top = 0;
    }

    pos[0] -= this.width/2;
    pos[1] -= !this.centered ? this.height : this.height/2;
    pos[0] = Math.round(pos[0]);
    pos[1] = Math.round(pos[1]);

    this.node[0].style.transform = 'translate('+pos[0]+'px,'+pos[1]+'px)';

    if(this.textLabel){
       var x = Math.round(pos[0]+this.width/2-this.textLabel.outerWidth()/2);
       var y = Math.round(pos[1] - this.textLabel.outerHeight());
       this.textLabel[0].style.transform = 'translate(' + x + 'px,' + y + 'px)';
    }
};
/**
 * Sets geo-coordinates of the Marker and calculates its x/y coordinates.
 * @param {Array} coords - [lat,lon] Latitude and Longitude coordinates
 */
MapSVG.Marker.prototype.setGeoCoords = function(coords){
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
/**
 * Marker drag event handler
 * @private
 * @param startCoords
 * @param scale
 * @param endCallback
 * @param clickCallback
 */
MapSVG.Marker.prototype.drag = function(startCoords, scale, endCallback, clickCallback){
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
/**
 * Marker undrag event handler
 * @private
 */
MapSVG.Marker.prototype.undrag = function(){
    //this.node.closest('svg').css('pointer-events','auto');
    //$('body').css('cursor','default');
    $('body').off('.drag.mapsvg');
    this.mapsvg.getData().$map.removeClass('no-transitions');
};
/**
 * Deletes the Marker
 */
MapSVG.Marker.prototype.delete = function(){
    if(this.textLabel){
        this.textLabel.remove();
        this.textLabel = null;
    }
    this.node.empty().remove();
    this.mapsvg.markerDelete(this);
};
/**
 * Sets parent DB object of the Marker
 * @param {object} obj
 */
MapSVG.Marker.prototype.setObject = function(obj){
    this.object = obj;
    this.objects = [obj];
    this.node.attr('data-object-id', this.object.id);
};
/**
 * Hides the Marker
 */
MapSVG.Marker.prototype.hide = function(){
    this.node.addClass('mapsvg-marker-hidden');
    if(this.textLabel){
        this.textLabel.hide();
    }
};
/**
 * Shows the Marker
 */
MapSVG.Marker.prototype.show = function(){
    this.node.removeClass('mapsvg-marker-hidden');
    if(this.textLabel){
        this.textLabel.show();
    }
};

/**
 * Highlight the Marker.
 * Used on mouseover.
 */
MapSVG.Marker.prototype.highlight = function(){
    this.node.addClass('mapsvg-marker-hover');
};
/**
 * Unhighlight the Marker.
 * Used on mouseout.
 */
MapSVG.Marker.prototype.unhighlight = function(){
    this.node.removeClass('mapsvg-marker-hover');
};
/**
 * Select the Marker.
 */
MapSVG.Marker.prototype.select = function(){
    this.selected = true;
    this.node.addClass('mapsvg-marker-active');
};
/**
 * Deselect the Marker.
 */
MapSVG.Marker.prototype.deselect = function(){
    this.selected = false;
    this.node.removeClass('mapsvg-marker-active');
};

})( jQuery );
(function( $ ) {
/**
 * Cluster class. Groups markers into a clickable circle with a number indicating how many markers it contains.
 * @param options
 * @constructor
 * @extends MapSVG.MapObject
 */
MapSVG.MarkersCluster = function(options){

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

    MapSVG.MapObject.call(this, node, this.mapsvg);

    if(this.markers.length < 2){
        this.node.hide(); // don't show cluster at the start
    }

    this.adjustPosition();
};
MapSVG.extend(MapSVG.MarkersCluster, MapSVG.MapObject);


/**
 * Adds marker to the cluster.
 * @param {MapSVG.Marker} marker
 */
MapSVG.MarkersCluster.prototype.addMarker = function(marker){
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
/**
 * Checks if provided marker should be added into this cluster.
 * @param {MapSVG.Marker} marker
 * @returns {boolean}
 */
MapSVG.MarkersCluster.prototype.canTakeMarker = function(marker){

    var _this = this;

    var xy = _this.mapsvg.convertSVGToPixel([marker.x, marker.y]);

    return (this.cellX === Math.ceil(xy[0] / this.cellSize )
        &&
        this.cellY === Math.ceil(xy[1] / this.cellSize ))
};

/**
 * Destroys the cluster
 */
MapSVG.MarkersCluster.prototype.destroy = function(){
    // this.markers.forEach(function(marker){
    //     marker.unclusterize();
    // });
    this.markers = null;
    this.node.remove();
};
/**
 * Adjusts position of the cluster.
 * Called on zoom and map container resize.
 */
MapSVG.MarkersCluster.prototype.adjustPosition = function(){

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

    pos[0] -= this.width/2;
    pos[1] -= this.width/2;

    this.node[0].style.transform = 'translate('+pos[0]+'px,'+pos[1]+'px)';
};
/**
 * Get SVG bounding box of the MarkersCluster
 * @returns {*[]} - [x,y,width,height]
 */
MapSVG.MarkersCluster.prototype.getBBox = function(){

    var _this = this;

    var bbox = {x: this.x, y: this.y, width: this.cellSize/this.mapsvg.getScale(), height: this.cellSize/this.mapsvg.getScale()};
    bbox = $.extend(true, {}, bbox);

    return [bbox.x,bbox.y,bbox.width,bbox.height];
};

})( jQuery );

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
(function( $ ) {
/**
 * Region class. Contains a reference to an SVG element.
 * @extends MapSVG.MapObject
 * @param {object} jQueryObject - jQuery Object containing SVG element
 * @param {MapSVG.MapOptions} globalOptions - MapSVG options
 * @param {string} regionID - Region ID
 * @param {MapSVG.Map} mapsvg - MapSVG instance
 * @constructor
 */
MapSVG.Region = function (jQueryObject, globalOptions, regionID, mapsvg){
    MapSVG.MapObject.call(this, jQueryObject);

    this.globalOptions = globalOptions;
    this.mapsvg = mapsvg;

    this.id = this.node.attr('id');

    if(this.id && globalOptions.regionPrefix){
        this.setId(this.id.replace(globalOptions.regionPrefix, ''));
    }

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
};
MapSVG.extend(MapSVG.Region, MapSVG.MapObject);

/**
 * Sets initial style of a Region, computed from SVG
 * @private
 */
MapSVG.Region.prototype.setStyleInitial = function(){
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
/**
 * Save state of a Region (all parameters)
 * @private
 */
MapSVG.Region.prototype.saveState = function(){
    this.initialState = JSON.stringify(this.getOptions());
};
/**
 * Returns SVG bounding box of the Region
 * @returns {[number,number,number,number]} - [x,y,width,height]
 */
MapSVG.Region.prototype.getBBox = function(){
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
/**
 * Checks whether the Region was changed from the initial state
 * @returns {boolean}
 * @private
 */
MapSVG.Region.prototype.changed = function(){
    return JSON.stringify(this.getOptions()) != this.initialState;
};
/**
 * Saves a copy of the Region SVG node.
 * Used in Map Editor by "Edit SVG file" mode.
 * @private
 */
MapSVG.Region.prototype.edit = function(){
    this.nodeOriginal = this.node.clone();
};
/**
 * Deletes the copy of the Region SVG node created by .edit() method.
 * Used in Map Editor by "Edit SVG file" mode.
 * @private
 */
MapSVG.Region.prototype.editCommit = function(){
    this.nodeOriginal = null;
};
/**
 * Restores SVG node.
 * Used in Map Editor by "Edit SVG file" mode.
 * @private
 */
MapSVG.Region.prototype.editCancel = function(){
    this.nodeOriginal.appendTo(_this.mapsvg.getData().$svg);
    this.node = this.nodeOriginal;
    this.nodeOriginal = null;
};
/**
 * Returns Region properties
 * @param {bool} forTemplate - adds special properties for use in a template
 * @returns {object}
 */
MapSVG.Region.prototype.getOptions = function(forTemplate){
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
/**
 * Returns an object with properties of the Region formatted for a template
 * @returns {object}
 */
MapSVG.Region.prototype.forTemplate = function(){
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
/**
 * Updates the Region
 * @param {object} options
 *
 * @example
 * var region = mapsvg.getRegion("US-TX");
 * region.update({
 *   fill: "#FF3322"
 * });
 */
MapSVG.Region.prototype.update = function(options){
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
/**
 * Sets ID of the Region
 * @param {string} id
 */
MapSVG.Region.prototype.setId = function(id){
    this.id = id;
    this.node.prop('id', id);
};
/**
 * Sets ID of the Region
 * @param {string} id
 */
MapSVG.Region.prototype.setTitle = function(title){
    this.title = title;
};
/**
 * Sets CSS style of the Region
 * @param {object} style - CSS-format styles
 * @private
 */
MapSVG.Region.prototype.setStyle = function(style){
    $.extend(true, this.style, style);
    this.setFill();
};
/**
 * Returns color of the Region for choropleth map
 * @returns {{r: number, g: number, b: number, a: number}}
 */
MapSVG.Region.prototype.getChoroplethColor = function(){
    var o = this.globalOptions.gauge;
    var w = o.maxAdjusted === 0 ? 0 : (parseFloat(this.data[this.globalOptions.regionChoroplethField]) - o.min) / o.maxAdjusted;

    return {
        r: Math.round(o.colors.diffRGB.r * w + o.colors.lowRGB.r),
        g: Math.round(o.colors.diffRGB.g * w + o.colors.lowRGB.g),
        b: Math.round(o.colors.diffRGB.b * w + o.colors.lowRGB.b),
        a: (o.colors.diffRGB.a * w + o.colors.lowRGB.a).toFixed(2)
    };
};
/**
 * Sets fill color of the Region
 * @param {string} fill - color in a CSS format
 * @example
 * region.setFill("#FF2233");
 * region.setFill("rgba(255,255,100,0.5");
 */
MapSVG.Region.prototype.setFill = function(fill){

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
    if(this.globalOptions.gauge.on && this.data && (typeof this.data[this.globalOptions.regionChoroplethField] !== 'undefined' &&  this.data[this.globalOptions.regionChoroplethField] !== '' )){
        var rgb = this.getChoroplethColor();
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
/**
 * Disables the Region.
 * @param {bool} on - true/false = disable/enable
 * @param {bool} skipSetFill - If false, color of the Region will not be changed
 */
MapSVG.Region.prototype.setDisabled = function(on, skipSetFill){
    on = on !== undefined ? MapSVG.parseBoolean(on) : this.getDisabledState(); // get default disabled state if undefined
    var prevDisabled = this.disabled;
    this.disabled = on;
    this.attr('class',this.attr('class').replace('mapsvg-disabled',''))
    if(on){
        this.attr('class',this.attr('class')+' mapsvg-disabled');
    }
    if(this.disabled != prevDisabled)
        this.mapsvg.deselectRegion(this);
    !skipSetFill && this.setFill();
};
/**
 * Sets status of the Region.
 * Takes the list of statuses from global MapSVG options.
 * @param {number} status
 */
MapSVG.Region.prototype.setStatus = function(status){
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
/**
 * Selects the Region.
 */
MapSVG.Region.prototype.setSelected = function(){
    this.mapsvg.selectRegion(this);
};
/**
 * Set Region choropleth value. Used to calculate color of the Region.
 * @param (number} val
 */
MapSVG.Region.prototype.setGaugeValue = function(val){
    this.gaugeValue = $.isNumeric(val) ? parseFloat(val) : undefined;
};
/**
 * Checks if Region should be disabled
 * @param {bool} asDefault
 * @returns {*}
 */
MapSVG.Region.prototype.getDisabledState = function(asDefault){
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
/**
 * Highlight the Region.
 * Used on mouseover.
 */
MapSVG.Region.prototype.highlight = function(){
    this.node.css({'fill' : this.hover_attr.fill});
    this.node.addClass('mapsvg-region-hover');
};
/**
 * Unhighlight the Region.
 * Used on mouseout.
 */
MapSVG.Region.prototype.unhighlight = function(){
    this.node.css({'fill' : this.default_attr.fill});
    this.node.removeClass('mapsvg-region-hover');
};
/**
 * Select the Region.
 */
MapSVG.Region.prototype.select = function(){
    this.node.css({'fill' : this.selected_attr.fill});
    this.selected = true;
    this.node.addClass('mapsvg-region-active');
};
/**
 * Deselect the Region.
 */
MapSVG.Region.prototype.deselect = function(){
    this.node.css({'fill' : this.default_attr.fill});
    this.selected = false;
    this.node.removeClass('mapsvg-region-active');
};

})( jQuery );
(function( $ ) {
/**
 * Resize sensor.
 * @private
 * @param element
 * @param callback
 * @constructor
 */
MapSVG.ResizeSensor = function(element, callback) {

    var _this = this;

    _this.element       = element;
    _this.callback      = callback;

    var style = getComputedStyle(element);
    var  zIndex = parseInt(style.zIndex);
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

})( jQuery );

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