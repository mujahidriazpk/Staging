import { Repository } from '../../Core/Repository';
import { Events } from "../../Core/Events";
export class SchemaRepository extends Repository {
    constructor() {
        let objectName = 'schema';
        super(objectName, +objectName + 's');
        this.className = 'Schema';
        this.objectNameSingle = objectName;
        this.objectNameMany = objectName + 's';
        this.path = objectName + 's/';
        this.events = new Events(this);
    }
    create(schema) {
        let defer = jQuery.Deferred();
        defer.promise();
        let data = {};
        data[this.objectNameSingle] = this.encodeData(schema);
        this.server.post(this.path, data).done((response) => {
            let data = this.decodeData(response);
            schema.id = data[this.objectNameSingle].id;
            this.objects.push(schema);
            this.events.trigger('created');
            schema.events.trigger('created');
            defer.resolve(schema);
        }).fail(() => { defer.reject(); });
        return defer;
    }
    update(schema) {
        let defer = jQuery.Deferred();
        defer.promise();
        let data = {};
        data[this.objectNameSingle] = this.encodeData(schema);
        this.server.put(this.path + schema.id, data).done((response) => {
            let data = this.decodeData(response);
            this.objects.push(schema);
            defer.resolve(schema);
            this.events.trigger('changed');
            schema.events.trigger('changed');
        }).fail(() => { defer.reject(); });
        return defer;
    }
    encodeData(schema) {
        let _schema = schema.getData();
        let fieldsJsonString = JSON.stringify(_schema);
        fieldsJsonString = fieldsJsonString.replace(/select/g, "!mapsvg-encoded-slct");
        fieldsJsonString = fieldsJsonString.replace(/table/g, "!mapsvg-encoded-tbl");
        fieldsJsonString = fieldsJsonString.replace(/database/g, "!mapsvg-encoded-db");
        fieldsJsonString = fieldsJsonString.replace(/varchar/g, "!mapsvg-encoded-vc");
        return JSON.parse(fieldsJsonString);
    }
}
//# sourceMappingURL=SchemaRepository.js.map