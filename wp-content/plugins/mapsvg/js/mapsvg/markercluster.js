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
