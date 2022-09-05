import { Server } from '../Infrastructure/Server/Server';
import { Schema } from "../Infrastructure/Server/Schema";
import { Query } from "../Infrastructure/Server/Query";
import { CustomObject } from "../Object/CustomObject";
import { Events } from "./Events";
import { ArrayIndexed } from "./ArrayIndexed";
const $ = jQuery;
export class Repository {
    constructor(objectName, path) {
        this.server = new Server();
        this.query = new Query();
        this.events = new Events(this);
        this.className = '';
        this.objectNameSingle = objectName;
        this.objectNameMany = objectName + 's';
        this.path = path + '/';
        this.objects = new ArrayIndexed('id');
        this.completeChunks = 0;
    }
    setSchema(schema) {
        this.schema = schema;
    }
    getSchema() {
        return this.schema;
    }
    loadDataFromResponse(response) {
        let data;
        data = this.decodeData(response);
        this.objects.clear();
        if (data[this.objectNameMany] && data[this.objectNameMany].length) {
            this.hasMoreRecords = this.query.perpage && (data[this.objectNameMany].length > this.query.perpage);
            if (this.hasMoreRecords) {
                data[this.objectNameMany].pop();
            }
            data[this.objectNameMany].forEach(obj => { this.objects.push(obj); });
        }
        else {
            this.hasMoreRecords = false;
        }
        this.loaded = true;
        this.events.trigger('loaded');
    }
    ;
    reload() {
        return this.find();
    }
    ;
    create(object) {
        let defer = jQuery.Deferred();
        defer.promise();
        let data = {};
        data[this.objectNameSingle] = this.encodeData(object);
        this.server.post(this.path, data).done((response) => {
            let data = this.decodeData(response);
            let object = data[this.objectNameSingle];
            this.objects.push(object);
            defer.resolve(object);
            this.events.trigger('created', this, [object]);
        }).fail(() => {
            defer.reject();
        });
        return defer;
    }
    findById(id, nocache = false) {
        let defer = jQuery.Deferred();
        defer.promise();
        let object;
        if (!nocache) {
            object = this.objects.findById(id.toString());
        }
        if (!nocache && object) {
            defer.resolve(object);
        }
        else {
            this.server.get(this.path + id).done((response) => {
                let data = this.decodeData(response);
                defer.resolve(data[this.objectNameSingle]);
            }).fail(() => { defer.reject(); });
        }
        return defer;
    }
    find(query) {
        let defer = jQuery.Deferred();
        defer.promise();
        if (typeof query !== "undefined") {
            this.query.update(query);
        }
        this.server.get(this.path, this.query).done((response) => {
            this.loadDataFromResponse(response);
            defer.resolve(this.getLoaded());
        }).fail(() => { defer.reject(); });
        return defer;
    }
    getLoaded() {
        return this.objects;
    }
    getLoadedObject(id) {
        return this.objects.findById(id.toString());
    }
    getLoadedAsArray() {
        return this.objects;
    }
    update(object) {
        let defer = jQuery.Deferred();
        defer.promise();
        let data = {};
        let objectUpdatedFields = object.getDirtyFields();
        data[this.objectNameSingle] = this.encodeData(objectUpdatedFields);
        this.server.put(this.path + objectUpdatedFields.id, data).done((response) => {
            object.clearDirtyFields();
            defer.resolve(object);
            this.events.trigger('updated', this, object);
        }).fail(() => { defer.reject(); });
        return defer;
    }
    delete(id) {
        let defer = jQuery.Deferred();
        defer.promise();
        this.server.delete(this.path + id).done((response) => {
            this.objects.delete(id.toString());
            this.events.trigger('deleted');
            defer.resolve();
        }).fail(() => { defer.reject(); });
        return defer;
    }
    clear() {
        let defer = jQuery.Deferred();
        defer.promise();
        this.server.delete(this.path).done((response) => {
            this.objects.clear();
            this.events.trigger('loaded');
            this.events.trigger('cleared');
            defer.resolve();
        }).fail(() => { defer.reject(); });
        return defer;
    }
    onFirstPage() {
        return this.query.page === 1;
    }
    onLastPage() {
        return this.hasMoreRecords === false;
    }
    encodeData(params) {
        return params;
    }
    decodeData(dataJSON) {
        let data;
        if (typeof dataJSON === 'string') {
            data = JSON.parse(dataJSON);
        }
        else {
            data = dataJSON;
        }
        if ((data.object || data.region || data.regions || data.objects) && data.schema) {
            this.setSchema(new Schema(data.schema));
        }
        let dataFormatted = {};
        for (let key in data) {
            switch (key) {
                case 'object':
                case 'region':
                    dataFormatted[key] = new CustomObject(data[key], this.schema);
                    break;
                case 'objects':
                case 'regions':
                    dataFormatted[key] = data[key].map((obj) => new CustomObject(obj, this.schema));
                    break;
                case 'schema':
                    dataFormatted[key] = this.schema || new Schema(data[key]);
                    break;
                case 'schemas':
                    dataFormatted[key] = data[key].map((obj) => new Schema(obj));
                    break;
                default: break;
            }
        }
        return dataFormatted;
    }
    import(data, convertLatlngToAddress, mapsvg) {
        var _this = this;
        var locationField = _this.schema.getFieldByType('location');
        var language = 'en';
        if (locationField && locationField.language) {
            language = locationField.language;
        }
        data = this.formatCSV(data, mapsvg);
        return this.importByChunks(data, language, convertLatlngToAddress).done(function () {
            _this.find();
        });
    }
    importByChunks(data, language, convertLatlngToAddress) {
        var _this = this;
        var i, j, temparray, chunk = 50;
        var chunks = [];
        for (i = 0, j = data.length; i < j; i += chunk) {
            temparray = data.slice(i, i + chunk);
            chunks.push(temparray);
        }
        if (chunks.length > 0) {
            var delay = 0;
            var delayPlus = chunks[0][0] && chunks[0][0].location ? 1000 : 0;
            var defer = $.Deferred();
            defer.promise();
            _this.completeChunks = 0;
            chunks.forEach(function (chunk) {
                delay += delayPlus;
                setTimeout(function () {
                    var data = {
                        language: language,
                        convertLatlngToAddress: convertLatlngToAddress
                    };
                    data[_this.objectNameMany] = JSON.stringify(chunk);
                    _this.server.post(_this.path + 'import', data).done(function (_data) {
                        _this.completeChunk(chunks, defer);
                    });
                }, delay);
            });
        }
        return defer;
    }
    completeChunk(chunks, defer) {
        var _this = this;
        _this.completeChunks++;
        if (_this.completeChunks === chunks.length) {
            defer.resolve();
        }
    }
    ;
    formatCSV(data, mapsvg) {
        var _this = this;
        var newdata = [];
        var regionsTable = mapsvg.regionsRepository.getSchema().name;
        data.forEach(function (object, index) {
            var newObject = {};
            for (var key in object) {
                var field = _this.schema.getField(key);
                if (field !== undefined) {
                    switch (field.type) {
                        case "region":
                            newObject[key] = {};
                            newObject[key][regionsTable] = object[key].split(',')
                                .map(function (regionId) {
                                return regionId.trim();
                            }).filter(function (rId) {
                                return mapsvg.getRegion(rId) !== undefined || mapsvg.regions.find(function (item) { return item.title === rId; }) !== undefined;
                            }).map(function (rId) {
                                var r = mapsvg.getRegion(rId);
                                if (typeof r === 'undefined') {
                                    r = mapsvg.regions.find(function (item) { return item.title === rId; });
                                }
                                return { id: r.id, title: r.title };
                            });
                            break;
                        case "location":
                            var latLngRegex = /^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/g;
                            if (object[key].match(latLngRegex)) {
                                var coords = object[key].split(',').map(function (n) { return parseFloat(n); });
                                if (coords.length == 2 && (coords[0] > -90 && coords[0] < 90) && (coords[1] > -180 && coords[1] < 180)) {
                                    newObject[key] = { geoPoint: { lat: coords[0], lng: coords[1] } };
                                }
                                else {
                                    newObject[key] = '';
                                }
                            }
                            else if (object[key]) {
                                newObject[key] = { address: object[key] };
                            }
                            if (typeof newObject[key] == 'object') {
                                newObject[key].img = mapsvg.options.defaultMarkerImage;
                            }
                            break;
                        case "select":
                            var field = _this.schema.getField(key);
                            if (field.multiselect) {
                                var labels = _this.schema.getField(key).options.map(function (f) {
                                    return f.label;
                                });
                                newObject[key] = object[key].split(',')
                                    .map(function (label) {
                                    return label.trim();
                                }).filter(function (label) {
                                    return labels.indexOf(label) !== -1;
                                }).map(function (label) {
                                    return _this.schema.getField(key).options.filter(function (option) {
                                        return option.label == label;
                                    })[0];
                                });
                            }
                            else {
                                newObject[key] = object[key];
                            }
                            break;
                        case "radio":
                        case "text":
                        case "textarea":
                        case "status":
                        default:
                            newObject[key] = object[key];
                            break;
                    }
                }
            }
            data[index] = newObject;
        });
        return data;
    }
}
//# sourceMappingURL=Repository.js.map