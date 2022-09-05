export class LocationAddress {
    constructor(fields) {
        for (var i in fields) {
            this[i] = fields[i];
        }
    }
    get state() {
        return this.country_short === 'US' ? this.administrative_area_level_1 : null;
    }
    get state_short() {
        return this.country_short === 'US' ? this.administrative_area_level_1_short : null;
    }
    get county() {
        return this.country_short === 'US' ? this.administrative_area_level_2 : null;
    }
    get zip() {
        return this.postal_code;
    }
}
//# sourceMappingURL=LocationAddress.js.map