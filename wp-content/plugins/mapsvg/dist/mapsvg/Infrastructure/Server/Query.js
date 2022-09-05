export class Query {
    constructor(options) {
        this.setFilterOut = function (fields) {
            var _this = this;
            for (var key in fields) {
                _this.filterout[key] = fields[key];
            }
        };
        if (options) {
            for (var i in options) {
                if (typeof options[i] !== "undefined") {
                    this[i] = options[i];
                }
            }
        }
    }
    setFields(fields) {
        var _this = this;
        for (var key in fields) {
            if (key == 'filters') {
                _this.setFilters(fields[key]);
            }
            else {
                _this[key] = fields[key];
            }
        }
    }
    ;
    update(query) {
        for (var i in query) {
            if (typeof query[i] !== 'undefined') {
                if (i === 'filters') {
                    this.setFilters(query[i]);
                }
                else {
                    this[i] = query[i];
                }
            }
        }
    }
    get() {
        return {
            search: this.search,
            searchField: this.searchField,
            searchFallback: this.searchFallback,
            filters: this.filters,
            filterout: this.filterout,
            page: this.page,
            sort: this.sort,
            perpage: this.perpage,
            lastpage: this.lastpage
        };
    }
    ;
    clearFilters() {
        this.filters = {};
    }
    setFilters(fields) {
        var _this = this;
        for (var key in fields) {
            if (fields[key] === null || fields[key] === "" || fields[key] === undefined) {
                if (_this.filters[key]) {
                    delete _this.filters[key];
                }
            }
            else {
                _this.filters[key] = fields[key];
            }
        }
    }
    ;
    resetFilters(fields) {
        this.filters = {};
    }
    ;
    setFilterField(field, value) {
        this.filters[field] = value;
    }
    ;
    hasFilters() {
        return Object.keys(this.filters).length > 0;
    }
    removeFilter(fieldName) {
        this.filters[fieldName] = null;
        delete this.filters[fieldName];
    }
}
//# sourceMappingURL=Query.js.map