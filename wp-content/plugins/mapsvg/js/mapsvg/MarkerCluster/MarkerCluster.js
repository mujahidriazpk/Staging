import { MapObject } from "../MapObject/MapObject.js";
import { ViewBox } from "../Map/MapOptionsInterface";
const $ = jQuery;
export class MarkerCluster extends MapObject {
    constructor(options, mapsvg) {
        super(null, mapsvg);
        this.svgPoint = options.svgPoint;
        this.cellX = options.cellX;
        this.cellY = options.cellY;
        this.markers = options.markers || [];
        this.cellSize = 50;
        this.width = 30;
        var _this = this;
        this.elem = $('<div class="mapsvg-marker-cluster">' + this.markers.length + '</div>')[0];
        $(this.elem).data("cluster", this);
        if (this.markers.length < 2) {
            $(this.elem).hide();
        }
        this.adjustPosition();
    }
    addMarker(marker) {
        this.markers.push(marker);
        if (this.markers.length > 1) {
            if (this.markers.length === 2) {
                $(this.elem).show();
            }
            if (this.markers.length === 2) {
                var x = this.markers.map(function (m) {
                    return m.svgPoint.x;
                });
                this.min_x = Math.min.apply(null, x);
                this.max_x = Math.max.apply(null, x);
                var y = this.markers.map(function (m) {
                    return m.svgPoint.y;
                });
                this.min_y = Math.min.apply(null, y);
                this.max_y = Math.max.apply(null, y);
                this.svgPoint.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.svgPoint.y = this.min_y + ((this.max_y - this.min_y) / 2);
            }
            if (this.markers.length > 2) {
                if (marker.svgPoint.x < this.min_x) {
                    this.min_x = marker.svgPoint.x;
                }
                else if (marker.svgPoint.x > this.max_x) {
                    this.max_x = marker.svgPoint.x;
                }
                if (marker.svgPoint.y < this.min_y) {
                    this.min_y = marker.svgPoint.y;
                }
                else if (marker.svgPoint.x > this.max_x) {
                    this.max_y = marker.svgPoint.y;
                }
                this.svgPoint.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.svgPoint.y = this.min_y + ((this.max_y - this.min_y) / 2);
            }
        }
        else {
            this.svgPoint.x = marker.svgPoint.x;
            this.svgPoint.y = marker.svgPoint.y;
        }
        $(this.elem).text(this.markers.length);
        this.adjustPosition();
    }
    canTakeMarker(marker) {
        var _this = this;
        var screenPoint = _this.mapsvg.convertSVGToPixel(marker.svgPoint);
        return (this.cellX === Math.ceil(screenPoint.x / this.cellSize)
            &&
                this.cellY === Math.ceil(screenPoint.y / this.cellSize));
    }
    destroy() {
        this.markers = null;
        $(this.elem).remove();
    }
    adjustPosition() {
        var _this = this;
        var pos = _this.mapsvg.convertSVGToPixel(this.svgPoint);
        if (pos.x > 30000000) {
            $(this.elem)[0].style.left = (pos.x - 30000000).toString();
            pos.x = 30000000;
        }
        else {
            $(this.elem)[0].style.left = (0).toString();
        }
        if (pos.y > 30000000) {
            $(this.elem)[0].style.top = (pos.y - 30000000).toString();
            pos.y = 30000000;
        }
        else {
            $(this.elem)[0].style.top = (0).toString();
        }
        pos.x -= this.width / 2;
        pos.y -= this.width / 2;
        $(this.elem).css({ 'transform': 'translate(' + pos.x + 'px,' + pos.y + 'px)' });
    }
    ;
    getBBox() {
        var _this = this;
        var bbox = {
            x: this.svgPoint.x,
            y: this.svgPoint.y,
            width: this.cellSize / this.mapsvg.getScale(),
            height: this.cellSize / this.mapsvg.getScale()
        };
        bbox = $.extend(true, {}, bbox);
        return new ViewBox(bbox.x, bbox.y, bbox.width, bbox.height);
    }
    ;
    getData() {
        return this.markers.map(m => m.object);
    }
}
//# sourceMappingURL=MarkerCluster.js.map