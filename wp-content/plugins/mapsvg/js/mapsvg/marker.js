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