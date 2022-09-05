class ArrayIndexed extends Array {
    constructor(indexKey, items, options) {
        if (items) {
            super(...items);
        }
        else {
            super();
        }
        this.key = indexKey;
        this.dict = {};
        this.nextId = 1;
        if (options) {
            this.options = options;
        }
        else {
            this.options = { autoId: false, unique: false };
        }
        if (this.length > 0) {
            var i = 0;
            var _this = this;
            if (this.options.autoId) {
                var maxId = 0;
                var missingIds = false;
                this.forEach(function (item) {
                    if (item[_this.key] != null) {
                        if (item[_this.key] > maxId) {
                            maxId = item[_this.key];
                        }
                    }
                    else {
                        missingIds = true;
                    }
                });
                this.nextId = maxId++;
                if (missingIds) {
                    this.forEach(function (item) {
                        if (item[_this.key] == null) {
                            item[_this.key] = _this.nextId;
                            _this.nextId++;
                        }
                    });
                }
            }
            this.forEach(function (item) {
                _this.dict[item[_this.key]] = i;
                i++;
            });
        }
    }
    push(item) {
        var length = super.push(item);
        if (this.options.autoId === true) {
            item[this.key] = this.nextId;
            this.nextId++;
        }
        this.dict[item[this.key]] = length - 1;
        return length;
    }
    pop() {
        var item = this[this.length - 1];
        var id = item[this.key];
        var length = super.pop();
        delete this.dict[id];
        this.reindex();
        return super.pop();
    }
    update(data) {
        if (data[this.key] != null) {
            var obj = this.get(data[this.key]);
            for (var i in data) {
                obj[i] = data[i];
            }
            return obj;
        }
        return false;
    }
    get(id) {
        return this.findById(id);
    }
    findById(id) {
        return this[this.dict[id]];
    }
    deleteById(id) {
        var index = this.dict[id];
        if (typeof index !== 'undefined') {
            delete this.dict[id];
            this.splice(index, 1);
        }
    }
    delete(id) {
        this.deleteById(id);
    }
    clear() {
        this.length = 0;
        this.reindex();
    }
    reindex() {
        var _this = this;
        this.dict = {};
        this.forEach(function (item, index) {
            _this.dict[item[_this.key]] = index;
        });
    }
    sort(compareFn) {
        super.sort(compareFn);
        this.reindex();
        return this;
    }
    splice(start, deleteCount) {
        let res = super.splice(start, deleteCount);
        this.reindex();
        return res;
    }
}
export { ArrayIndexed };
//# sourceMappingURL=ArrayIndexed.js.map