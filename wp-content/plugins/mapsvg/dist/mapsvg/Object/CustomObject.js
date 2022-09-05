import { GeoPoint, Location, SVGPoint } from "../Location/Location";
import { LocationAddress } from "../Location/LocationAddress";
export class CustomObject {
    constructor(params, schema) {
        this.schema = schema;
        this.fields = schema.getFieldNames();
        if (params.id !== undefined) {
            this.id = params.id;
        }
        for (let fieldName in params) {
            let field = this.schema.getField(fieldName);
            if (field) {
                switch (field.type) {
                    case 'location':
                        let data = {
                            img: params[fieldName].img,
                            address: new LocationAddress(params[fieldName].address)
                        };
                        if (params[fieldName].lat && params[fieldName].lng) {
                            data.geoPoint = new GeoPoint(params[fieldName].lat, params[fieldName].lng);
                        }
                        else if (params[fieldName].x && params[fieldName].y) {
                            data.svgPoint = new SVGPoint(params[fieldName].x, params[fieldName].y);
                        }
                        let location = new Location(data);
                        break;
                    default:
                        this[fieldName] = params[fieldName];
                        break;
                }
            }
        }
    }
}
//# sourceMappingURL=CustomObject.js.map