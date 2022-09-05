export class Form {
    constructor(options) {
        this.title = options.title;
        this.fields = options.fields;
    }
    inputToObject(formattedValue) {
        var obj = {};
        function add(obj, name, value) {
            if (name.length == 1) {
                obj[name[0]] = value;
            }
            else {
                if (obj[name[0]] == null)
                    obj[name[0]] = {};
                add(obj[name[0]], name.slice(1), value);
            }
        }
        if ($(this).attr('name') && !($(this).attr('type') == 'radio' && !$(this).prop('checked'))) {
            add(obj, $(this).attr('name').replace(/]/g, '').split('['), formattedValue);
        }
        return obj;
    }
}
//# sourceMappingURL=Form.js.map