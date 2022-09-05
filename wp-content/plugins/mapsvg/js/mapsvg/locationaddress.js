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