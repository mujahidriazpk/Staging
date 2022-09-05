import { Events } from "../Core/Events";
import { ScreenPoint, SVGPoint } from '../Location/Location';
export class MapObject {
    constructor(element, mapsvg) {
        this.id = "";
        this.objects = [];
        this.events = new Events(this);
        this.data = {};
        this.elem = element;
        this.elemType = element.tagName;
        this.mapsvg = mapsvg;
    }
    ;
    isMarker() {
    }
    ;
    isRegion() {
    }
    ;
    setData(data) {
        var _this = this;
        for (var name in data) {
            _this.data[name] = data[name];
        }
    }
    ;
    getBBox() { }
    ;
    getGeoBounds() {
        var bbox = this.getBBox();
        var pointSW = new SVGPoint(bbox[0], (bbox[1] + bbox[3]));
        var pointNE = new SVGPoint((bbox[0] + bbox[2]), bbox[1]);
        var sw = this.mapsvg.convertSVGToGeo(pointSW);
        var ne = this.mapsvg.convertSVGToGeo(pointNE);
        return { sw: sw, ne: ne };
    }
    ;
    getComputedStyle(prop, elem) {
        var _p1 = elem.getAttribute(prop);
        if (_p1) {
            return _p1;
        }
        var _p2 = elem.getAttribute('style');
        if (_p2) {
            var s = _p2.split(';');
            var z = s.filter(function (e) {
                e = e.trim();
                var attr = e.split(':');
                if (attr[0] == prop)
                    return true;
            });
            if (z.length) {
                return z[0].split(':').pop().trim();
            }
        }
        var parent = elem.parentElement;
        var elemType = parent ? parent.tagName : null;
        if (elemType && elemType != 'svg')
            return this.getComputedStyle(prop, parent);
        else
            return undefined;
    }
    ;
    getStyle(prop) {
        var _p1 = this.attr(prop);
        if (_p1) {
            return _p1;
        }
        var _p2 = this.attr('style');
        if (_p2) {
            var s = _p2.split(';');
            var z = s.filter(function (e) {
                var e = e.trim();
                if (e.indexOf(prop) === 0)
                    return e;
            });
            return z.length ? z[0].split(':').pop().trim() : undefined;
        }
        return "";
    }
    ;
    getCenter() {
        var x = this.elem.getBoundingClientRect().left;
        var y = this.elem.getBoundingClientRect().top;
        var w = this.elem.getBoundingClientRect().width;
        var h = this.elem.getBoundingClientRect().height;
        var point = new ScreenPoint(x + w / 2, y + h / 2);
        return point;
    }
    ;
    getCenterSVG() {
        var c = this.getBBox();
        var point = new SVGPoint(c[0] + c[2] / 2, c[1] + c[3] / 2);
        return point;
    }
    ;
    getCenterLatLng(yShift) {
        yShift = yShift ? yShift : 0;
        var bbox = this.getBBox();
        var x = bbox[0] + bbox[2] / 2;
        var y = bbox[1] + bbox[3] / 2 - yShift;
        var point = new SVGPoint(x, y);
        return this.mapsvg.convertSVGToGeo(point);
    }
    ;
    attr(v1, v2 = null) {
        var svgDom = this.elem;
        if (typeof v1 == "object") {
            for (var key in v1) {
                var item = v1[key];
                if (typeof item === "string" || typeof item === "number") {
                    svgDom.setAttribute(key, '' + item);
                }
            }
        }
        else if (typeof v1 == "string" && (typeof v2 == "string" || typeof v2 == "number")) {
            svgDom.setAttribute(v1, '' + v2);
        }
        else if (v2 == undefined) {
            return svgDom.getAttribute(v1);
        }
    }
    ;
    setId(id) {
        if (id !== undefined) {
            this.id = id;
            this.elem.setAttribute('id', id);
        }
    }
    ;
}
//# sourceMappingURL=MapObject.js.map