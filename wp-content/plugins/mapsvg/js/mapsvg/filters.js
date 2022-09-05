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
