import { GeoPoint, Location, SVGPoint } from "../Location/Location.js";
import { LocationAddress } from "../Location/LocationAddress.js";
export class CustomObject {
    constructor(params, schema) {
        this.initialLoad = true;
        this.schema = schema;
        this.fields = schema.getFieldNames();
        this.dirtyFields = [];
        this.regions = [];
        this._regions = {};
        if (params.id !== undefined) {
            this.id = params.id;
        }
        this.initialLoad = true;
        this.build(params);
        this.initialLoad = false;
        if (this.id) {
            this.clearDirtyFields();
        }
    }
    build(params) {
        for (let fieldName in params) {
            let field = this.schema.getField(fieldName);
            if (field) {
                if (!this.initialLoad) {
                    this.dirtyFields.push(fieldName);
                }
                switch (field.type) {
                    case 'region':
                        if (params[fieldName].hasOwnProperty('length')) {
                            this.regions = params[fieldName];
                            this._regions[this.schema.name] = this.regions;
                        }
                        else {
                            this._regions = params[fieldName];
                            this.regions = typeof this._regions[this.schema.name] != null ? this._regions[this.schema.name] : [];
                        }
                        break;
                    case 'location':
                        if (params[fieldName] != null && params[fieldName] != '' && Object.keys(params[fieldName]).length !== 0) {
                            let data = {
                                img: params[fieldName].img,
                                address: new LocationAddress(params[fieldName].address)
                            };
                            if (params[fieldName].geoPoint && params[fieldName].geoPoint.lat && params[fieldName].geoPoint.lng) {
                                data.geoPoint = new GeoPoint(params[fieldName].geoPoint);
                            }
                            else if (params[fieldName].svgPoint && params[fieldName].svgPoint.x && params[fieldName].svgPoint.y) {
                                data.svgPoint = new SVGPoint(params[fieldName].svgPoint);
                            }
                            if (this.location != null) {
                                this.location.update(data);
                            }
                            else {
                                this.location = new Location(data);
                            }
                        }
                        else {
                            params[fieldName] = null;
                        }
                        break;
                    case 'post':
                        if (params.post_id && params.post) {
                            this.post = params.post;
                            this.post_id = params.post.id;
                        }
                        break;
                    case 'select':
                        this[fieldName] = params[fieldName];
                        if (!field.multiselect) {
                            this[fieldName + '_text'] = field.options.get(params[fieldName]);
                        }
                        break;
                    case 'radio':
                        this[fieldName] = params[fieldName];
                        this[fieldName + '_text'] = field.options.get(params[fieldName]);
                        break;
                    default:
                        this[fieldName] = params[fieldName];
                        break;
                }
            }
        }
    }
    update(params) {
        this.build(params);
    }
    getDirtyFields() {
        let data = {};
        this.dirtyFields.forEach((field) => { data[field] = this[field]; });
        data.id = this.id;
        if (data.location != null) {
            data.location = data.location.getData();
        }
        if (this.schema.getFieldByType('region')) {
            data.regions = this._regions;
        }
        return data;
    }
    clearDirtyFields() {
        this.dirtyFields = [];
    }
    getData(regionsTableName) {
        var data = {};
        let fields = this.schema.getFields();
        fields.forEach((field) => {
            switch (field.type) {
                case 'region':
                    data.regions = this._regions[regionsTableName];
                    break;
                case 'select':
                    data[field.name] = this[field.name];
                    if (!field.multiselect) {
                        data[field.name + '_text'] = this[field.name + '_text'];
                    }
                    break;
                case 'radio':
                    data[field.name] = this[field.name];
                    data[field.name + '_text'] = this[field.name + '_text'];
                    break;
                default:
                    data[field.name] = this[field.name];
                    break;
            }
        });
        return data;
    }
    getRegions(regionsTableName) {
        return this._regions[regionsTableName];
    }
}
//# sourceMappingURL=CustomObject.js.map