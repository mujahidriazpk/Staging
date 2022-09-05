import { Repository } from "../Core/Repository.js";
export class MapsRepository extends Repository {
    constructor() {
        super('map', 'maps');
        this.path = 'maps/';
    }
    encodeData(params) {
        let data = {};
        data.options = JSON.stringify(params.options);
        data.options = data.options.replace(/select/g, "!mapsvg-encoded-slct");
        data.options = data.options.replace(/table/g, "!mapsvg-encoded-tbl");
        data.options = data.options.replace(/database/g, "!mapsvg-encoded-db");
        data.options = data.options.replace(/varchar/g, "!mapsvg-encoded-vc");
        data.id = params.id;
        data.title = params.title;
        return data;
    }
    decodeData(dataJSON) {
        let data;
        if (typeof dataJSON === 'string') {
            data = JSON.parse(dataJSON);
        }
        else {
            data = dataJSON;
        }
        return data;
    }
    copy(id, title) {
        let defer = jQuery.Deferred();
        defer.promise();
        let data = { options: { title: title } };
        this.server.post(this.path + id + '/copy', this.encodeData(data)).done((response) => {
            var data = this.decodeData(response);
            this.objects.clear();
            this.events.trigger('loaded');
            this.events.trigger('cleared');
            defer.resolve(data.map);
        }).fail(() => { defer.reject(); });
        return defer;
    }
    createFromV2(object) {
        let defer = jQuery.Deferred();
        defer.promise();
        let data = {};
        data[this.objectNameSingle] = this.encodeData(object);
        this.server.post(this.path + '/createFromV2', data).done((response) => {
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
}
//# sourceMappingURL=MapsRepository.js.map