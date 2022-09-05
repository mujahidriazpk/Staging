import { LocationAddress } from "./LocationAddress";
import { MapSVG } from "../Core/globals";
export class ScreenPoint {
    constructor(x, y) {
        this.x = x;
        this.y = y;
    }
}
export class SVGPoint {
    constructor(x, y) {
        this.x = x;
        this.y = y;
    }
}
export class GeoPoint {
    constructor(lat, lng) {
        if (typeof lat === 'string') {
            lat = parseFloat(lat);
        }
        if (typeof lng === 'string') {
            lng = parseFloat(lng);
        }
        this.lat = lat;
        this.lng = lng;
    }
}
export class Location {
    constructor(options) {
        this.img = options.img;
        this.setImage(this.img);
        this.address = new LocationAddress(options.address);
        if (typeof options.geoPoint !== undefined) {
            this.geoPoint = options.geoPoint;
        }
        if (typeof options.svgPoint !== undefined) {
            this.svgPoint = options.svgPoint;
        }
    }
    setImage(img) {
        var src = img.split('/').pop();
        if (img.indexOf('uploads') !== -1) {
            src = 'uploads/' + src;
        }
        this.img = src;
    }
    setSvgPoint(svgPoint) {
        this.svgPoint = svgPoint;
    }
    setGeoPoint(geoPoint) {
        this.geoPoint = geoPoint;
    }
    getMarkerImageUrl() {
        if ((this.img && this.img.indexOf('uploads/') === 0)) {
            return MapSVG.urls.uploads + 'markers/' + (this.img.replace('uploads/', ''));
        }
        else {
            return MapSVG.urls.root + 'markers/' + (this.img || '_pin_default.png');
        }
    }
    toJSON() {
        let data;
        data.img = this.img;
        if (this.geoPoint) {
            data.geoPoint = { lat: this.geoPoint.lat, lng: this.geoPoint.lng };
        }
        if (this.svgPoint) {
            data.svgPoint = { lat: this.svgPoint.x, y: this.svgPoint.y };
        }
        data.address = this.address;
        return data;
    }
}
//# sourceMappingURL=Location.js.map