import Handlebars from "../../../handlebars.js";
import { Events } from "../../Core/Events";
export class FormElement {
    constructor(options, formBuilder) {
        options = options || {};
        this.formBuilder = formBuilder;
        this.events = new Events(this);
        this.type = options.type;
        this.value = options.value;
        this.db_type = 'varchar(255)';
        this.label = this.label || (options.label === undefined ? 'Label' : options.label);
        this.name = this.name || options.name || 'label';
        this.help = options.help || '';
        this.placeholder = options.placeholder;
        this.mapIsGeo = options.mapIsGeo;
        this.editMode = options.editMode;
        this.filtersMode = options.filtersMode;
        this.namespace = options.namespace;
        if (options.external) {
            this.external = options.external;
        }
        var t = this.type;
        if (t === 'marker' && this.mapIsGeo) {
            t = 'marker-geo';
        }
        if (t === 'location' && this.mapIsGeo) {
            t = 'location-geo';
        }
        if (this.filtersMode) {
            this.parameterName = options.parameterName || '';
            this.parameterNameShort = this.parameterName.split('.')[1];
            this.placeholder = options.placeholder || '';
            this.templates = {
                main: Handlebars.compile($('#mapsvg-filters-tmpl-' + t + '-view').html())
            };
        }
        else {
            this.templates = {
                main: Handlebars.compile($('#mapsvg-data-tmpl-' + t + '-view').html())
            };
        }
        this.setDomElements();
        this.setEventHandlers();
    }
    setDomElements() {
        this.domElements = {
            main: $(this.templates.main(this.getDataForTemplate()))[0]
        };
        $(this.domElements.main).data('formElement', this);
    }
    setEventHandlers() {
        var _this = this;
        if (this.editMode) {
            $(this.domElements.main).on('click', function () {
                _this.events.trigger('click');
            });
        }
        this.addSelect2();
    }
    addSelect2() {
        if ($().mselect2) {
            $(this.domElements.main).find('select').css({ width: '100%', display: 'block' })
                .mselect2()
                .on('select2:focus', function () {
                $(this).mselect2('open');
            });
            $(this.domElements.main).find('.select2-selection--multiple .select2-search__field').css('width', '100%');
        }
    }
    setEditorEventHandlers() {
        var _this = this;
        $(this.domElements.edit).on('click', 'button.mapsvg-remove', function () {
            $(_this.domElements.main).empty().remove();
            $(_this.domElements.edit).empty().remove();
            _this.events.trigger('delete');
        });
        $(this.domElements.edit).on('click', '.mapsvg-filter-insert-options', function () {
            var objType = _this.parameterName.split('.')[0];
            var fieldName = _this.parameterName.split('.')[1];
            var field;
            if (objType == 'Object') {
                field = _this.formBuilder.mapsvg.objectsRepository.getSchema().getField(fieldName);
            }
            else {
                if (fieldName == 'id') {
                    let options = [];
                    _this.formBuilder.mapsvg.regions.forEach(function (r) {
                        options.push({ label: r.id, value: r.id });
                    });
                    field = {
                        options: options
                    };
                }
                else if (fieldName == 'region_title') {
                    let options = [];
                    _this.formBuilder.mapsvg.regions.forEach(function (r) {
                        options.push({ label: r.title, value: r.title });
                    });
                    field = { options: options };
                }
                else {
                    field = _this.formBuilder.mapsvg.regionsRepository.getSchema().getField(fieldName);
                }
            }
            if (field && field.options) {
                var options;
                if (fieldName == 'regions') {
                    if (field.options[0].title && field.options[0].title.length)
                        field.options.sort(function (a, b) {
                            if (a.title < b.title)
                                return -1;
                            if (a.title > b.title)
                                return 1;
                            return 0;
                        });
                    options = field.options.map(function (o) {
                        return (o.title || o.id) + ':' + o.id;
                    });
                }
                else {
                    options = field.options.map(function (o) {
                        return o.label + ':' + o.value;
                    });
                }
                $(this).closest('.form-group').find('textarea').val(options.join("\n")).trigger('change');
            }
        });
        $(this.domElements.edit).on('keyup change paste', 'input, textarea, select', function () {
            var prop = $(this).attr('name');
            var array = $(this).data('array');
            if (_this.type === 'status' && array) {
                var param = $(this).data('param');
                var index = $(this).closest('tr').index();
                _this.options[index] = _this.options[index] || { label: '', value: '', color: '', disabled: false };
                _this.options[index][param] = $(this).is(':checkbox') ? $(this).prop('checked') : $(this).val();
                _this.redraw();
            }
            else if (_this.type === 'distance' && array) {
                var param = $(this).data('param');
                var index = $(this).closest('tr').index();
                if (!_this.options[index]) {
                    _this.options[index] = { value: '', default: false };
                }
                if (param === 'default') {
                    _this.options.forEach(function (option) {
                        option.default = false;
                    });
                    _this.options[index].default = $(this).prop('checked');
                }
                else {
                    _this.options[index].value = $(this).val();
                }
                _this.redraw();
            }
            else if (prop == 'label' || prop == 'name') {
                return false;
            }
            else {
                var value;
                value = ($(this).attr('type') == 'checkbox') ? $(this).prop('checked') : $(this).val();
                if ($(this).attr('type') == 'radio') {
                    var name = $(this).attr('name');
                    value = $('input[name="' + name + '"]:checked').val();
                }
                _this.update(prop, value);
            }
        });
        $(this.domElements.edit).on('keyup change paste', 'input[name="label"]', function () {
            if (!_this.nameChanged) {
                _this.label = $(this).val() + '';
                if (_this.type != 'region' && _this.type != 'location') {
                    var str = $(this).val() + '';
                    str = str.toLowerCase().replace(/ /g, '_').replace(/\W/g, '');
                    $(_this.domElements.edit).find('input[name="name"]').val(str);
                    _this.name = str + '';
                }
                $(_this.domElements.main).find('label').first().html(_this.label);
                if (!_this.filtersMode) {
                    $(_this.domElements.main).find('label').first().append('<div class="field-name">' + _this.name + '</div>');
                }
            }
        });
        $(this.domElements.edit).on('keyup change paste', 'input[name="name"]', function () {
            if (this.value) {
                if (this.value.match(/[^a-zA-Z0-9_]/g)) {
                    this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
                    $(this).trigger('change');
                }
                if (this.value[0].match(/[^a-zA-Z_]/g)) {
                    this.value = this.value[0].replace(/[^a-zA-Z_]/g, '') + this.value.slice(1);
                    $(this).trigger('change');
                }
            }
            if (_this.type != 'region')
                _this.name = this.value;
            $(_this.domElements.main).find('label').html(_this.label + '<div class="field-name">' + _this.name + '</div>');
            _this.nameChanged = true;
        });
    }
    ;
    getEditor() {
        if (!this.filtersMode) {
            this.templates.edit = this.templates.edit || Handlebars.compile($('#mapsvg-data-tmpl-' + this.type + '-control').html());
        }
        else {
            this.templates.edit = this.templates.edit || Handlebars.compile($('#mapsvg-filters-tmpl-' + this.type + '-control').html());
        }
        this.domElements.edit = $(this.templates.edit(this.getDataForTemplate()))[0];
        return this.domElements.edit;
    }
    ;
    destroyEditor() {
        $(this.domElements.edit).empty().remove();
    }
    ;
    initEditor() {
        $(this.domElements.edit).find('input').first().select();
        if ($().mselect2) {
            if (this.type !== 'distance') {
                $(this.domElements.edit).find('select').css({ width: '100%', display: 'block' }).mselect2();
            }
        }
        $(this.domElements.edit).find('.mapsvg-onoff').bootstrapToggle({
            onstyle: 'default',
            offstyle: 'default'
        });
        this.setEditorEventHandlers();
    }
    ;
    getSchema() {
        let data;
        data = {
            type: this.type,
            db_type: this.db_type,
            label: this.label,
            name: this.name,
            value: this.value,
            searchable: this.searchable,
            help: this.help,
            visible: this.visible === undefined ? true : this.visible,
            options: this.getSchemaFieldOptionsList(),
            placeholder: this.placeholder
        };
        if (this.filtersMode) {
            data.parameterName = this.parameterName;
            data.parameterNameShort = this.parameterName.split('.')[1];
        }
        return data;
    }
    ;
    getSchemaFieldOptionsList() {
        let _this = this;
        this.options.forEach((option, index) => {
            if (this.options[index].value === '') {
                this.options.splice(index, 1);
            }
        });
        return this.options;
    }
    getDataForTemplate() {
        var data = this.getSchema();
        data._name = data.name;
        if (this.namespace) {
            data.name = this.name.split('[')[0];
            var suffix = this.name.split('[')[1] || '';
            if (suffix)
                suffix = '[' + suffix;
            data.name = this.namespace + '[' + data.name + ']' + suffix;
        }
        data.external = this.external;
        return data;
    }
    ;
    update(prop, value) {
        var _this = this;
        if (prop == 'options') {
            var options = [];
            value = value.split("\n").forEach(function (row) {
                row = row.trim().split(':');
                if (_this.type == 'checkbox' && row.length == 3) {
                    options.push({
                        label: row[0],
                        name: row[1],
                        value: row[2]
                    });
                }
                else if ((_this.type == 'radio' || _this.type == 'select' || _this.type == 'checkboxes') && row.length == 2) {
                    options.push({
                        label: row[0],
                        value: row[1]
                    });
                }
            });
            this.options = options;
        }
        else {
            this[prop] = value;
        }
        if (prop == 'parameterName') {
            $(this.domElements.edit).find('.mapsvg-filter-param-name').text(value);
        }
        this.redraw();
    }
    ;
    addParams(params) {
    }
    redraw() {
        var _this = this;
        var newView = $(this.templates.result(this.getDataForTemplate()));
        $(this.domElements.main).html(newView.html());
        if ($().mselect2) {
            if (this.type !== 'distance') {
                $(this.domElements.main).find('select').css({ width: '100%', display: 'block' })
                    .mselect2()
                    .on('select2:focus', function () {
                    $(this).mselect2('open');
                });
            }
            else {
                $(this.domElements.main).find('select').mselect2().on('select2:focus', function () {
                    $(this).mselect2('open');
                });
            }
        }
        if ($().colorpicker) {
            this.domElements.edit && $(this.domElements.edit).find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
                var input = $(this).find('input');
                if (input.val() == '')
                    $(this).find('i').css({ 'background-color': '' });
            });
        }
    }
    setOptions(options) {
        this.options = options || [
            { label: 'Option one', name: 'option_one', value: 1 },
            { label: 'Option two', name: 'option_two', value: 2 }
        ];
        return this.options;
    }
    getData() {
    }
    updateData() { }
    ;
    destroy() {
        if ($().mselect2) {
            var sel = $(this.domElements.main).find('.mapsvg-select2');
            if (sel.length) {
                sel.mselect2('destroy');
            }
        }
    }
    ;
}
//# sourceMappingURL=FormElement.js.map