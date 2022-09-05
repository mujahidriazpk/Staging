import { MapObject } from "../MapObject/MapObject";
export class MarkerCluster extends MapObject {
    constructor(options, mapsvg) {
        super(null, mapsvg);
        this.destroy = function () {
            this.markers = null;
            $(this.elem).remove();
        };
        this.adjustPosition = function () {
            var _this = this;
            var pos = _this.mapsvg.convertSVGToPixel([this.x, this.y]);
            if (pos[0] > 30000000) {
                $(this.elem)[0].style.left = pos[0] - 30000000;
                pos[0] = 30000000;
            }
            else {
                $(this.elem)[0].style.left = 0;
            }
            if (pos[1] > 30000000) {
                $(this.elem)[0].style.top = pos[1] - 30000000;
                pos[1] = 30000000;
            }
            else {
                $(this.elem)[0].style.top = 0;
            }
            pos[0] -= this.width / 2;
            pos[1] -= this.width / 2;
            $(this.elem)[0].style.transform = 'translate(' + pos[0] + 'px,' + pos[1] + 'px)';
        };
        this.getBBox = function () {
            var _this = this;
            var bbox = {
                x: this.x,
                y: this.y,
                width: this.cellSize / this.mapsvg.getScale(),
                height: this.cellSize / this.mapsvg.getScale()
            };
            bbox = $.extend(true, {}, bbox);
            return [bbox.x, bbox.y, bbox.width, bbox.height];
        };
        this.x = options.x;
        this.y = options.y;
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
                this.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.y = this.min_y + ((this.max_y - this.min_y) / 2);
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
                this.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.y = this.min_y + ((this.max_y - this.min_y) / 2);
            }
        }
        else {
            this.x = marker.svgPoint.x;
            this.y = marker.svgPoint.y;
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
}
//# sourceMappingURL=MarkerCluster.js.map