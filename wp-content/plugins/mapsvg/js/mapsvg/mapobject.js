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