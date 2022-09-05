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

MapSVG.formBuilder = {};
MapSVG.mediaUploader = {};

if(typeof wp !== undefined && typeof wp.media !== undefined){
    MapSVG.mediaUploader = wp.media({
        title: 'Choose images',
        button: {
            text: 'Choose images'
        },
        multiple: true
    });
}


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
if(typeof ajaxurl !== "undefined"){
    MapSVG.urls.ajaxurl = ajaxurl;
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

export {MapSVG};