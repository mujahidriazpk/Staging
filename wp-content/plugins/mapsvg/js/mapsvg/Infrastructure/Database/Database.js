"use strict";
exports.__esModule = true;
exports.Database = void 0;
var $ = require("../../../../node_modules/jquery/dist/jquery");
var Database = /** @class */ (function () {
    function Database() {
        this.apiUrl = '/wp-json/mapsvg/v6.0';
    }
    Database.prototype.get = function (path, data) {
        return $.get(this.apiUrl + path, data);
    };
    Database.prototype.post = function (path, data) {
        return $.post(this.apiUrl + path, data);
    };
    Database.prototype.put = function (path, data) {
        return $.ajax({
            url: this.apiUrl + path,
            type: 'PUT',
            data: data
        });
    };
    Database.prototype["delete"] = function (path, data) {
        return $.ajax({
            url: this.apiUrl + path,
            type: 'DELETE',
            data: data
        });
    };
    return Database;
}());
exports.Database = Database;
module.exports = Database;
