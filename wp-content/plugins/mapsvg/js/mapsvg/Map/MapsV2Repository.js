import { Repository } from "../Core/Repository.js";
export class MapsV2Repository extends Repository {
    constructor() {
        super('map', 'maps');
        this.path = 'maps-v2/';
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
}
//# sourceMappingURL=MapsV2Repository.js.map