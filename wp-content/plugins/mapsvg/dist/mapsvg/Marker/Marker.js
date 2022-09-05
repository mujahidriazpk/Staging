import MapSVG from "../Core/globals";
import { MapObject } from "../MapObject/MapObject";
import { SVGPoint } from "../Location/Location";
import { ViewBox } from "../Map/MapOptionsInterface";
export class Marker extends MapObject {
    constructor(params) {
        super(null, params.mapsvg);
        this.setId = function (id) {
            MapSVG.MapObject.prototype.setId.call(this, id);
            this.mapsvg.updateMarkersDict();
        };
        this.update = function (data) {
            for (var key in data) {
                var setter = 'set' + MapSVG.ucfirst(key);
                if (setter in this)
                    this[setter](data[key]);
            }
        };
        this.src = params.location.getMarkerImageUrl();
        var img = $('<img src="' + this.src + '" />').addClass('mapsvg-marker');
        this.element = img[0];
        this.location = params.location;
        this.location.marker = this;
        this.mapsvg = params.mapsvg;
        params.object && this.setObject(params.object);
        if (params.width && params.height) {
            this.width = params.width;
            this.height = params.height;
        }
        this.setId(this.mapsvg.markerId());
        this.svgPoint = this.location.svgPoint;
        this.setImage(this.src);
    }
    getBBox() {
        var bbox = { x: this.svgPoint.x, y: this.svgPoint.y, width: this.width / this.mapsvg.scale, height: this.height / this.mapsvg.scale };
        bbox = $.extend(true, {}, bbox);
        return new ViewBox(bbox);
    }
    ;
    getOptions() {
        var o = {
            id: this.id,
            src: this.src,
            x: this.svgPoint.x,
            y: this.svgPoint.y,
            geoCoords: this.location.geoPoint
        };
        $.each(o, function (key, val) {
            if (val == undefined) {
                delete o[key];
            }
        });
        return o;
    }
    ;
    setImage(src) {
        if (!src)
            return false;
        var _this = this;
        src = MapSVG.safeURL(src);
        var img = new Image();
        var marker = this;
        this.src = src;
        if (marker.elem.getAttribute('src') !== 'src') {
            marker.elem.setAttribute('src', src);
        }
        img.onload = function () {
            marker.width = this.width;
            marker.height = this.height;
            _this.adjustPosition();
        };
        img.src = src;
        if (this.location) {
            this.location.setImage(src);
        }
    }
    ;
    setPoint(svgPoint) {
        this.svgPoint = svgPoint;
        if (this.location) {
            this.location.setSvgPoint(this.svgPoint);
        }
        if (this.mapsvg.mapIsGeo) {
            this.geoPoint = this.mapsvg.convertSVGToGeo(this.svgPoint);
            this.location.setGeoPoint(this.geoPoint);
        }
        this.adjustPosition();
        this.events.trigger('change');
    }
    ;
    adjustPosition() {
        var _this = this;
        var pos = _this.mapsvg.convertSVGToPixel(this.svgPoint);
        if (pos.x > 30000000) {
            this.element.style.left = pos.x - 30000000 + 'px';
            pos.x = 30000000;
            if (this.textLabel) {
                this.textLabel[0].style.left = pos.x - 30000000 + 'px';
            }
        }
        else {
            this.element.style.left = '0';
        }
        if (pos.y > 30000000) {
            this.element.style.top = pos.y - 30000000 + 'px';
            pos.y = 30000000;
            if (this.textLabel) {
                this.textLabel[0].style.top = pos.y - 30000000 + 'px';
            }
        }
        else {
            this.element.style.top = '0';
        }
        pos.x -= this.width / 2;
        pos.y -= !this.centered ? this.height : this.height / 2;
        pos.x = Math.round(pos.x);
        pos.y = Math.round(pos.y);
        this.element.style.transform = 'translate(' + pos.x + 'px,' + pos.y + 'px)';
        if (this.textLabel) {
            var x = Math.round(pos.x + this.width / 2 - $(this.textLabel).outerWidth() / 2);
            var y = Math.round(pos.y - $(this.textLabel).outerHeight());
            this.textLabel[0].style.transform = 'translate(' + x + 'px,' + y + 'px)';
        }
    }
    ;
    drag(startCoords, scale, endCallback, clickCallback) {
        var _this = this;
        this.svgPointBeforeDrag = new SVGPoint(this.svgPoint.x, this.svgPoint.y);
        $('body').on('mousemove.drag.mapsvg', function (e) {
            e.preventDefault();
            $(_this.mapsvg.containers.map).addClass('no-transitions');
            var mouseNew = MapSVG.mouseCoords(e);
            var dx = mouseNew.x - startCoords.x;
            var dy = mouseNew.y - startCoords.y;
            var newSvgPoint = new SVGPoint(_this.svgPointBeforeDrag.x + dx / scale, _this.svgPointBeforeDrag.y + dy / scale);
            _this.setPoint(newSvgPoint);
        });
        $('body').on('mouseup.drag.mapsvg', function (e) {
            e.preventDefault();
            _this.undrag();
            var mouseNew = MapSVG.mouseCoords(e);
            var dx = mouseNew.x - startCoords.x;
            var dy = mouseNew.y - startCoords.y;
            var newSvgPoint = new SVGPoint(_this.svgPointBeforeDrag.x + dx / scale, _this.svgPointBeforeDrag.y + dy / scale);
            _this.setPoint(newSvgPoint);
            endCallback.call(_this);
            if (_this.svgPointBeforeDrag.x == _this.svgPoint.x && _this.svgPointBeforeDrag.y == _this.svgPoint.y)
                clickCallback.call(_this);
        });
    }
    ;
    undrag() {
        $('body').off('.drag.mapsvg');
        $(this.mapsvg.containers.map).removeClass('no-transitions');
    }
    ;
    delete() {
        if (this.textLabel) {
            this.textLabel.remove();
            this.textLabel = null;
        }
        $(this.element).empty().remove();
        this.mapsvg.markerDelete(this);
    }
    ;
    setObject(obj) {
        this.object = obj;
        $(this.element).attr('data-object-id', this.object.id);
    }
    ;
    hide() {
        $(this.element).addClass('mapsvg-marker-hidden');
        if (this.textLabel) {
            $(this.textLabel).hide();
        }
    }
    ;
    show() {
        $(this.element).removeClass('mapsvg-marker-hidden');
        if (this.textLabel) {
            $(this.textLabel).show();
        }
    }
    ;
    highlight() {
        $(this.element).addClass('mapsvg-marker-hover');
    }
    ;
    unhighlight() {
        $(this.element).removeClass('mapsvg-marker-hover');
    }
    ;
    select() {
        this.selected = true;
        $(this.element).addClass('mapsvg-marker-active');
    }
    ;
    deselect() {
        this.selected = false;
        $(this.element).removeClass('mapsvg-marker-active');
    }
    ;
}
//# sourceMappingURL=Marker.js.map