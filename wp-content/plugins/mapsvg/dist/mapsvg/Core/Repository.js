import { Server } from '../Infrastructure/Server/Server';
import { Schema } from "../Infrastructure/Server/Schema";
import { Query } from "../Infrastructure/Server/Query";
import { CustomObject } from "../Object/CustomObject";
export class Repository {
    constructor(objectName) {
        this.server = new Server();
        this.query = new Query();
        this.className = '';
        this.objectNameSingle = objectName;
        this.objectNameMany = objectName + 's';
        this.path = '/' + objectName + 's/';
    }
    setSchema(schema) {
        this.schema = schema;
    }
    getSchema() {
        return this.schema;
    }
    loadDataFromResponse(response) {
        let data = this.decodeData(response);
        if (data[this.objectNameMany] && data[this.objectNameMany].length) {
            this.hasMoreRecords = this.query.perpage && (data.objects.length > this.query.perpage);
            if (this.hasMoreRecords) {
                data[this.objectNameMany].pop();
            }
            this.objects = new Map(data[this.objectNameMany].map(obj => [obj.id, obj]));
        }
        else {
            this.hasMoreRecords = false;
        }
        if (data.schema) {
            this.setSchema(data.schema);
        }
        this.loaded = true;
    }
    ;
    reload() {
        return this.find();
    }
    ;
    create(object) {
        let defer = jQuery.Deferred();
        defer.promise();
        this.server.post(this.path, this.encodeData(object)).done((response) => {
            let data = this.decodeData(response);
            this.objects.set(data[this.objectNameSingle].id, data[this.objectNameSingle]);
            defer.resolve(data[this.objectNameSingle]);
        }).fail(() => { defer.reject(); });
        return defer;
    }
    findById(id, nocache = false) {
        let defer = jQuery.Deferred();
        defer.promise();
        let object;
        if (!nocache) {
            object = this.objects.get(id.toString());
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
    update(object) {
        let defer = jQuery.Deferred();
        defer.promise();
        this.server.put(this.path + object.id, object).done((response) => {
            let data = this.decodeData(response);
            let object = data[this.objectNameSingle];
            this.objects.set(object.id, object);
            defer.resolve(object);
        }).fail(() => { defer.reject(); });
        return defer;
    }
    delete(id) {
        let defer = jQuery.Deferred();
        defer.promise();
        this.server.delete(this.path + id).done((response) => {
            this.objects.delete(id.toString());
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
                    dataFormatted[key] = new Schema(data[key]);
                    break;
                case 'schemas':
                    dataFormatted[key] = data[key].map((obj) => new Schema(obj));
                    break;
                default: break;
            }
        }
        return dataFormatted;
    }
}
//# sourceMappingURL=Repository.js.map