"use strict";
exports.__esModule = true;
exports.Schema = void 0;
var globals_1 = require("../../Core/globals");
var Schema = /** @class */ (function () {
    function Schema(options, mapsvg) {
        this.mapsvg = mapsvg;
        this.name = options.name;
        this.title = options.title;
        this.lastChangeTime = Date.now();
        this.fields = [];
        this.fieldsDict = {};
        this.events = {
            'create': [],
            'update': [],
            'change': []
        }; // array of callbacks
    }
    ;
    Schema.prototype.loaded = function (options) {
        return this.fields.length !== 0;
    };
    Schema.prototype.setFields = function (fields) {
        var _this = this;
        if (options) {
            this.fields = fields.map(function (field) {
                field.visible = globals_1.MapSVG.parseBoolean(field.visible);
                if (field.type == 'region') {
                    field.options = [];
                    field.optionsDict = {};
                    _this.mapsvg.regions.objects.forEach(function (region) {
                        field.options.push({ id: region.id, title: region.title });
                        field.optionsDict[region.id] = region.title ? region.title : region.id;
                    });
                }
                _this.fieldsDict[field.name] = field;
                return field;
            });
        }
    };
    ;
    Schema.prototype.save = function (fields) {
        var _this = this;
        this.set(fields);
        for (var i in this.schema) {
            if (!this.schema[i])
                this.schema.splice(i, 1);
        }
        fields = JSON.stringify(fields);
        fields = fields.replace(/select/g, "!mapsvg-encoded-slct");
        fields = fields.replace(/table/g, "!mapsvg-encoded-tbl");
        fields = fields.replace(/database/g, "!mapsvg-encoded-db");
        fields = fields.replace(/varchar/g, "!mapsvg-encoded-vc");
        return $.post(ajaxurl, { action: 'mapsvg_save_schema', schema: fields, map_id: this.map_id, table: this.table, _wpnonce: globals_1.MapSVG.nonce }).done(function () {
            _this.trigger('change');
        });
    };
    ;
    Schema.prototype.getFields = function () {
        return this.fields;
    };
    Schema.prototype.getFieldNames = function () {
        return this.fields.map(function (f) { return f.name; });
    };
    Schema.prototype.getField = function (field) {
        if (field === 'id') {
            return { name: 'id', visible: true, type: 'id' };
        }
        return this.fieldsDict[field];
    };
    Schema.prototype.getFieldByType = function (type) {
        var f = null;
        this.schema.forEach(function (field) {
            if (field.type === type)
                f = field;
        });
        return f;
    };
    Schema.prototype.load = function (options) {
        var _this = this;
        return $.get(ajaxurl, { action: 'mapsvg_get_schema', map_id: this.map_id, table: this.table }, null, 'json')
            .done(function (schema) {
            _this.set(schema);
        });
    };
    Schema.prototype.getColumns = function (filters) {
        filters = filters || {};
        var _this = this;
        var columns = this.get().slice(0); // clone array
        if (this.table == 'regions')
            columns.unshift({ name: 'title', visible: true, type: 'title' }); // add Title column to the beginning
        var needAddId = true;
        columns.forEach(function (col) {
            if (col.name == 'id') {
                needAddId = false;
            }
        });
        if (needAddId)
            columns.unshift({ name: 'id', visible: true, type: 'id' }); // add ID column to the beginning
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
    };
    ;
    Schema.prototype.on = function (event, callback) {
        this.lastChangeTime = Date.now();
        if (!this.events[event])
            this.events = {};
        this.events[event].push(callback);
    };
    ;
    Schema.prototype.trigger = function (event) {
        var _this = this;
        if (this.events[event] && this.events[event].length)
            this.events[event].forEach(function (callback) {
                callback && callback();
            });
    };
    ;
    return Schema;
}());
exports.Schema = Schema;
