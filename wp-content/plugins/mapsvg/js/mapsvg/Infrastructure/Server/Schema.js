import { MapSVG } from "../../Core/globals.js";
import { Events } from "../../Core/Events";
import { SchemaField } from "./SchemaField";
import { ArrayIndexed } from "../../Core/ArrayIndexed";
export class Schema {
    constructor(options) {
        this.fields = new ArrayIndexed('name');
        this.build(options);
        this.lastChangeTime = Date.now();
        this.events = new Events(this);
    }
    ;
    build(options) {
        let allowedParams = ['id', 'title', 'type', 'name', 'fields'];
        allowedParams.forEach((paramName) => {
            var setter = 'set' + MapSVG.ucfirst(paramName);
            if (typeof options[paramName] !== 'undefined' && typeof this[setter] == 'function') {
                this[setter](options[paramName]);
            }
        });
    }
    update(options) {
        this.build(options);
    }
    setId(id) {
        this.id = id;
    }
    setTitle(title) {
        this.title = title;
    }
    setName(name) {
        this.name = name;
    }
    loaded() {
        return this.fields.length !== 0;
    }
    setFields(fields) {
        if (fields) {
            this.fields.clear();
            fields.forEach((fieldParams) => {
                this.fields.push(new SchemaField(fieldParams));
            });
        }
    }
    ;
    getFields() {
        return this.fields;
    }
    getFieldsAsArray() {
        return this.fields;
    }
    getFieldNames() {
        return this.fields.map((f) => f.name);
    }
    getField(field) {
        return this.fields.findById(field);
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
        var columns = this.fields;
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
    getData() {
        let data = {
            id: this.id,
            title: this.title,
            name: this.name,
            fields: this.fields,
            type: this.type
        };
        return data;
    }
}
//# sourceMappingURL=Schema.js.map