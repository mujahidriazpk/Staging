export class ArrayIndexed extends Array {
    add(item) {
        var length = this.push(item);
        this.dict[item.id] = length - 1;
        return length;
    }
    findById(id) {
        return this[this.dict[id]];
    }
}
//# sourceMappingURL=ArrayIndexed.js.map