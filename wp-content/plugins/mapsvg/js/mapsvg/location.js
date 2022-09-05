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