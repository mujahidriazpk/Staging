import { MapSVG } from '../../Core/globals';
import { Events } from "../../Core/Events";
export class Schema {
    constructor(options) {
        this.id = options.id;
        this.type = options.type;
        this.name = options.name;
        this.title = options.title;
        this.fields = new Map();
        options.fields && options.fields.forEach((field) => {
            field.visible = MapSVG.parseBoolean(field.visible);
            field.searchable = MapSVG.parseBoolean(field.searchable);
            if (field.options) {
                field.options = new Map(field.options.map(obj => [obj.label, obj]));
            }
            this.fields.set(field.name, field);
        });
        this.lastChangeTime = Date.now();
        this.events = new Events();
    }
    ;
    loaded() {
        return this.fields.size !== 0;
    }
    setFields(fields) {
        var _this = this;
        this.fields.clear();
        fields.forEach(function (field) {
            field.visible = MapSVG.parseBoolean(field.visible);
            _this.fields.set(field.name, field);
            return field;
        });
    }
    ;
    getFields() {
        return this.fields;
    }
    getFieldsAsArray() {
        let res;
        this.fields.forEach((field) => {
            res.push(field);
        });
        return res;
    }
    getFieldNames() {
        return Array.from(this.fields.keys());
    }
    getField(field) {
        if (field === 'id') {
            return { name: 'id', visible: true, type: 'id' };
        }
        return this.fields.get(field);
    }
    getFieldByType(type) {
        var f = null;
        this.fields.forEach(function (field) {
            if (field.type === type)
                f = field;
        });
        return f;
    }
    getColumns(filters) {
        filters = filters || {};
        var columns = Array.from(this.getFields().values());
        var needfilters = Object.keys(filters).length !== 0;
        var results = [];
        if (needfilters) {
            var filterpass;
            columns.forEach(function (obj) {
                filterpass = true;
                for (var param in filters) {
                    filterpass = (obj[param] == filters[param]);
                }
                filterpass && results.push(obj);
            });
        }
        else {
            results = columns;
        }
        return results;
    }
    ;
}
//# sourceMappingURL=Schema.js.map