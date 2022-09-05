import * as $ from "../../../../node_modules/jquery/dist/jquery";
export class Server {
    constructor() {
        this.apiUrl = '/wp-json/mapsvg/v6.0';
    }
    get(path, data) {
        return $.get(this.apiUrl + path, data);
    }
    post(path, data) {
        return $.post(this.apiUrl + path, data);
    }
    put(path, data) {
        return $.ajax({
            url: this.apiUrl + path,
            type: 'PUT',
            data: data
        });
    }
    delete(path, data) {
        return $.ajax({
            url: this.apiUrl + path,
            type: 'DELETE',
            data: data
        });
    }
}
module.exports = Server;
//# sourceMappingURL=Server.js.map