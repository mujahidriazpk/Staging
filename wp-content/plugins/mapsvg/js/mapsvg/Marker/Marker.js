import { MapSVG } from "../Core/globals.js";
import { MapObject } from "../MapObject/MapObject.js";
import { SVGPoint } from "../Location/Location.js";
import { ViewBox } from "../Map/MapOptionsInterface.js";
const $ = jQuery;
export class Marker extends MapObject {
    constructor(params) {
        super(null, params.mapsvg);
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
        this.svgPoint = this.location.svgPoint || this.mapsvg.convertGeoToSVG(this.location.geoPoint);
        this.setImage(this.src);
        this.setAltAttr();
        this.bubbleMode = false;
    }
    setId(id) {
        MapObject.prototype.setId.call(this, id);
        this.mapsvg.markers.reindex();
    }
    ;
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
            svgPoint: this.svgPoint,
            geoPoint: this.geoPoint
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
        if (marker.element.getAttribute('src') !== 'src') {
            marker.element.setAttribute('src', src);
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
    setAltAttr() {
        var marker = this;
        marker.altAttr = (typeof marker.object != 'undefined') && (typeof marker.object.title != 'undefined') && (marker.object.title !== '') ? marker.object.title : marker.id;
        marker.element.setAttribute('alt', marker.altAttr);
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
                this.textLabel.style.left = pos.x - 30000000 + 'px';
            }
        }
        else {
            this.element.style.left = '0';
        }
        if (pos.y > 30000000) {
            this.element.style.top = pos.y - 30000000 + 'px';
            pos.y = 30000000;
            if (this.textLabel) {
                this.textLabel.style.top = pos.y - 30000000 + 'px';
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
            this.textLabel.style.transform = 'translate(' + x + 'px,' + y + 'px)';
        }
    }
    ;
    drag(startCoords, scale, endCallback, clickCallback) {
        var _this = this;
        this.svgPointBeforeDrag = new SVGPoint(this.svgPoint.x, this.svgPoint.y);
        this.dragging = true;
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
            if (_this.mapsvg.isGeo()) {
                _this.geoPoint = _this.mapsvg.convertSVGToGeo(newSvgPoint);
            }
            endCallback && endCallback.call(_this);
            if (_this.svgPointBeforeDrag.x == _this.svgPoint.x && _this.svgPointBeforeDrag.y == _this.svgPoint.y)
                clickCallback && clickCallback.call(_this);
        });
    }
    ;
    undrag() {
        this.dragging = false;
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
    getData() {
        return this.object;
    }
    getChoroplethColor() {
        let markerValue = parseFloat(this.object[this.mapsvg.options.choropleth.sourceField]);
        let segments = this.mapsvg.options.choropleth.segments;
        let currentSegment;
        segments.forEach(function (segment) {
            if (markerValue >= segment.min && markerValue <= segment.max) {
                currentSegment = segment;
            }
        });
        let w = currentSegment.maxAdjusted === 0 ? 0 : (markerValue - currentSegment.min) / currentSegment.maxAdjusted;
        return {
            r: Math.round(currentSegment.colors.diffRGB.r * w + currentSegment.colors.lowRGB.r),
            g: Math.round(currentSegment.colors.diffRGB.g * w + currentSegment.colors.lowRGB.g),
            b: Math.round(currentSegment.colors.diffRGB.b * w + currentSegment.colors.lowRGB.b),
            a: (currentSegment.colors.diffRGB.a * w + currentSegment.colors.lowRGB.a).toFixed(2)
        };
    }
    ;
    getBubbleSize() {
        let bubbleSize;
        if (this.object[this.mapsvg.options.choropleth.sourceField]) {
            let maxBubbleSize = this.mapsvg.options.choropleth.bubbleSize.max, minBubbleSize = this.mapsvg.options.choropleth.bubbleSize.min, maxSourceFieldvalue = this.mapsvg.options.choropleth.segments[this.mapsvg.options.choropleth.segments.length - 1].max, minSourceFieldvalue = this.mapsvg.options.choropleth.segments[0].min, sourceFieldvalue = parseFloat(this.object[this.mapsvg.options.choropleth.sourceField]);
            bubbleSize = ((sourceFieldvalue - minSourceFieldvalue) * (maxBubbleSize - minBubbleSize) / (maxSourceFieldvalue - minSourceFieldvalue)) + Number(minBubbleSize);
        }
        else {
            bubbleSize = false;
        }
        return bubbleSize;
    }
    drawBubble() {
        let bubbleId = 'mapsvg-bubble-' + this.object.id;
        let bubbleValue = parseFloat(this.object[this.mapsvg.options.choropleth.sourceField]);
        if (bubbleValue) {
            if ($('#' + bubbleId).length === 0) {
                $(this.mapsvg.layers.markers).append('<div id="' + bubbleId + '" class="mapsvg-bubble mapsvg-marker-bubble"></div>');
            }
            let bubble = $('#' + bubbleId);
            let rgb = this.getChoroplethColor();
            let bubbleSize = Number(this.getBubbleSize());
            let pos = this.mapsvg.convertSVGToPixel(this.svgPoint);
            pos.x -= bubbleSize / 2;
            pos.y -= bubbleSize / 2;
            pos.x = Math.round(pos.x);
            pos.y = Math.round(pos.y);
            $(bubble).text(bubbleValue);
            $(bubble).css('transform', 'translate(' + pos.x + 'px,' + pos.y + 'px)')
                .css('background-color', 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + rgb.a + ')')
                .css('width', bubbleSize + 'px')
                .css('height', bubbleSize + 'px')
                .css('lineHeight', (bubbleSize - 2) + 'px');
        }
        else {
            $('#' + bubbleId).remove();
        }
    }
}
//# sourceMappingURL=Marker.js.map