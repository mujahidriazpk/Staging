import { Repository } from '../../Core/Repository';
export class SchemaRepository extends Repository {
    constructor(objectName, mapsvg) {
        super(objectName);
        this.className = 'Schema';
        this.objectNameSingle = objectName;
        this.objectNameMany = objectName + 's';
        this.path = '/' + objectName + 's/';
    }
    create(schema) {
        let defer = jQuery.Deferred();
        defer.promise();
        this.server.post(this.path, this.encodeData(schema)).done((response) => {
            let data = this.decodeData(response);
            schema.id = data[this.objectNameSingle].id;
            this.objects.set(schema.id, schema);
            this.events.trigger('create');
            schema.events.trigger('create');
            defer.resolve(schema);
        }).fail(() => { defer.reject(); });
        return defer;
    }
    update(schema) {
        let defer = jQuery.Deferred();
        defer.promise();
        this.server.put(this.path + schema.id, schema).done((response) => {
            let data = this.decodeData(response);
            this.objects.set(schema.id, schema);
            this.events.trigger('change');
            schema.events.trigger('change');
            defer.resolve(schema);
        }).fail(() => { defer.reject(); });
        return defer;
    }
    encodeData(schema) {
        let fieldsJsonString = JSON.stringify(schema);
        fieldsJsonString = fieldsJsonString.replace(/select/g, "!mapsvg-encoded-slct");
        fieldsJsonString = fieldsJsonString.replace(/table/g, "!mapsvg-encoded-tbl");
        fieldsJsonString = fieldsJsonString.replace(/database/g, "!mapsvg-encoded-db");
        fieldsJsonString = fieldsJsonString.replace(/varchar/g, "!mapsvg-encoded-vc");
        return JSON.parse(fieldsJsonString);
    }
}
//# sourceMappingURL=SchemaRepository.js.map