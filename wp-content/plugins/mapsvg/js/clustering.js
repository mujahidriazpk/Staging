postMessage('Clusterizer is running');

function Clusterizer(options){

    this.svgViewBox = options.svgViewBox;
    this.zoomLevels = options.zoomLevels;
    this.zoomDelta  = options.zoomDelta;
    this.cellSize   = options.cellSize;

    this.markersClustersDict = {};
    this.markersClusters = [];

    this.setZoomLevel(options.zoomLevel);
    this.setMapWidth(options.mapWidth);
    this.setObjects(options.objects);
}

Clusterizer.prototype.setZoomLevel = function(level){
    this.zoomLevel = level;
    this.setScale();
};
Clusterizer.prototype.setScale = function(){
    var viewBox = this.zoomLevels[this.zoomLevel].viewBox;
    this.scale = (this.mapWidth / viewBox[2]) || 1;
};
Clusterizer.prototype.setMapWidth = function(width){
    this.mapWidth = width;
    this.setScale();
};
Clusterizer.prototype.setObjects = function(objects){
    this.objects = objects;
};
Clusterizer.prototype.convertSVGToPixel = function(x,y){
    return {x:(x-this.svgViewBox[0])*this.scale, y:(y-this.svgViewBox[1])*this.scale};
};
Clusterizer.prototype.convertPixelToSVG = function(x,y){
    return {x: x/this.scale+this.svgViewBox[0], y: y/this.scale+this.svgViewBox[1]};
};
Clusterizer.prototype.calculate = function(startZoomLevel, delta){

    var _this = this;

    var zoomLevels = [];
    for(var i = startZoomLevel; i <= startZoomLevel+delta; i++){
        _this.zoomLevels[i] && !_this.markersClustersDict[i] && zoomLevels.push(i)
    }
    for(var i = -1; i >= -delta; i--){
        _this.zoomLevels[startZoomLevel+i] && !_this.markersClustersDict[startZoomLevel+i] && zoomLevels.push(startZoomLevel+i)
    }

    zoomLevels.forEach(function(zl){

        // TODO This is called 2 times at start, fix it

        _this.setZoomLevel(zl);

        _this.markersClustersDict[zl] = {};

        _this.objects && _this.objects.forEach(function (object, index, array) {

            var last = _this.objects.length - 1;

            var coords = _this.convertSVGToPixel(object.x, object.y);

            var cellX = Math.ceil(coords.x / _this.cellSize);
            var cellY = Math.ceil(coords.y / _this.cellSize);

            var cluster = _this.markersClustersDict[zl][cellX + '|' + cellY];

            if (cluster) {
                cluster.addMarker(object);
            } else {
                _this.markersClustersDict[zl][cellX + '|' + cellY] = new MarkersCluster({
                    clusterizer: _this,
                    markers: [object],
                    x: object.x,
                    y: object.y
                });
            }

            if (index === last) {
                postMessage({clusters: _this.markersClustersDict[zl], zoomLevel: zl});
            }
        });
    });
};
Clusterizer.prototype.addCluster = function(markersCluster) {
    _data.layers.markers.append(markersCluster.node);
    _data.markersClusters.push(markersCluster);
    _data.markersClustersDict[markersCluster.cellX+'|'+markersCluster.cellY] = markersCluster;
};


function MarkersCluster(options){

    var _this = this;

    this.x = options.x; // SVG-x (not pixel-x)
    this.y = options.y; // SVG-y (not pixel-y)
    this.clusterizer = options.clusterizer;
    this.markers = [];

    this.width = 30;

    if(options.markers && typeof options.markers == 'object' && options.markers.length){
        options.markers.forEach(function(marker){
            _this.addMarker(marker);
        });
    }
}

MarkersCluster.prototype.addMarker = function(marker){

    var _this = this;

    this.markers.push(marker);

    if(this.markers.length === 1 ){
        this.coordsPixel = this.clusterizer.convertSVGToPixel(this.x, this.y);

        this.cellX = Math.ceil(this.coordsPixel.x / this.clusterizer.cellSize );
        this.cellY = Math.ceil(this.coordsPixel.y / this.clusterizer.cellSize );

        this.x = marker.x;
        this.y = marker.y;

    } else if(this.markers.length > 1){

        if(this.markers.length === 2){
            var x = this.markers.map(function(m){ return m.x });
            this.min_x = Math.min.apply(null, x);
            this.max_x = Math.max.apply(null, x);

            var y = this.markers.map(function(m){ return m.y });
            this.min_y = Math.min.apply(null, y);
            this.max_y = Math.max.apply(null, y);

            this.x = this.min_x + ((this.max_x - this.min_x) / 2);
            this.y = this.min_y + ((this.max_y - this.min_y) / 2);

        } else if(this.markers.length > 2){
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
    }

};
MarkersCluster.prototype.canTakeMarker = function(marker){

    var _this = this;

    var coords = _this.clusterizer.convertSVGToPixel(marker.x, marker.y);

    return (this.cellX === Math.ceil(coords.x / this.cellSize )
        &&
        this.cellY === Math.ceil(coords.y / this.cellSize ))
};


var clusterizer;


onmessage = function(e) {
    if(e.data.objects){
        clusterizer = null;
        clusterizer = new Clusterizer({
            objects: e.data.objects,
            svgViewBox: e.data.svgViewBox,
            zoomLevels: e.data.zoomLevels,
            zoomLevel: e.data.zoomLevel,
            zoomDelta: e.data.zoomDelta,
            mapWidth: e.data.mapWidth,
            cellSize: e.data.cellSize
        });
        clusterizer.calculate(e.data.zoomLevel, 3);
    }
    if(e.data.message == "zoom"){
        clusterizer.calculate(e.data.zoomLevel, 3);
    }
    // postMessage("Clusterizer received data: ");
    // postMessage("Clusterizer received data: " + JSON.stringify(evt.data));
};