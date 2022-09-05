import { MapSVG } from "../Core/globals";
import { Handlebars } from "../../handlebars.js";
import { ResizeSensor } from "../Core/ResizeSensor";
import { Events } from "../Core/Events";
import { FormElementFactory } from './FormElements/FormElementFactory';
export class FormBuilder {
    constructor(options) {
        var _this = this;
        this.events = new Events();
        this.container = options.container;
        this.namespace = options.namespace;
        this.mediaUploader = options.mediaUploader;
        this.schema = options.schema || [];
        this.editMode = options.editMode == undefined ? false : options.editMode;
        this.filtersMode = options.filtersMode == undefined ? false : options.filtersMode;
        this.filtersHide = options.filtersHide == undefined ? false : options.filtersHide;
        this.modal = options.modal == undefined ? false : options.modal;
        this.admin = options.admin;
        this.mapsvg = options.mapsvg;
        this.data = options.data || {};
        this.clearButton = options.clearButton || false;
        this.clearButtonText = options.clearButtonText || '';
        this.showButtonText = options.showButtonText || '';
        this.formElementFactory = new FormElementFactory({
            mapsvg: this.mapsvg,
            mediaUploader: this.mediaUploader,
            editMode: this.editMode,
            filtersMode: this.filtersMode,
            namespace: this.namespace,
        });
        this.events = new Events(this);
        if (options.events && Object.keys(options.events).length > 0) {
            for (var eventName in options.events) {
                this.events.on(eventName, options.events[eventName]);
            }
        }
        this.template = 'form-builder';
        this.closeOnSave = options.closeOnSave === true;
        this.newRecord = options.newRecord === true;
        this.types = options.types || ['text', 'textarea', 'checkbox', 'radio', 'select', 'image', 'region', 'location', 'post', 'date'];
        this.templates = {};
        this.elements = {};
        this.view = $('<div />').addClass('mapsvg-form-builder')[0];
        if (this.editMode)
            $(this.view).addClass('full-flex');
        this.formElements = [];
        if (!MapSVG.templatesLoaded[this.template]) {
            $.get(MapSVG.urls.templates + _this.template + '.html', function (data) {
                $(data).appendTo('body');
                MapSVG.templatesLoaded[_this.template] = true;
                Handlebars.registerPartial('dataMarkerPartial', $('#mapsvg-data-tmpl-marker').html());
                if (_this.editMode) {
                    Handlebars.registerPartial('markerByFieldPartial', $('#mapsvg-markers-by-field-tmpl-partial').html());
                }
                _this.init();
            });
        }
        else {
            this.init();
        }
    }
    init() {
        var _this = this;
        MapSVG.formBuilder = this;
        if (_this.editMode) {
            var templateUI = Handlebars.compile($('#mapsvg-form-editor-tmpl-ui').html());
            $(_this.view).append(templateUI({ types: this.types }));
            $(_this.view).addClass('edit');
        }
        else {
            var form = $('<div class="mapsvg-data-form-view"></div>');
            $(_this.view).append(form);
            if (!this.filtersMode) {
                form.addClass('form-horizontal');
            }
        }
        _this.elements = {
            buttons: {
                text: $(_this.view).find('#mapsvg-data-btn-text')[0],
                textarea: $(_this.view).find('#mapsvg-data-btn-textarea')[0],
                checkbox: $(_this.view).find('#mapsvg-data-btn-checkbox')[0],
                radio: $(_this.view).find('#mapsvg-data-btn-radio')[0],
                select: $(_this.view).find('#mapsvg-data-btn-select')[0],
                image: $(_this.view).find('#mapsvg-data-btn-image')[0],
                region: $(_this.view).find('#mapsvg-data-btn-region')[0],
                marker: $(_this.view).find('#mapsvg-data-btn-marker')[0],
                saveSchema: $(_this.view).find('#mapsvg-data-btn-save-schema')[0]
            },
            containers: {
                buttons_add: $(_this.view).find('#mapsvg-data-buttons-add')[0],
                formView: $(_this.view).find('.mapsvg-data-form-view')[0],
                form_edit: $(_this.view).find('#mapsvg-data-form-edit')[0]
            }
        };
        _this.redraw();
    }
    ;
    viewDidLoad() { }
    ;
    setEventHandlers() {
        var _this = this;
        if (_this.filtersMode && _this.clearButton) {
            $(_this.elements.buttons.clearButton).on('click', function () {
                $(_this.elements.containers.formView).find('input')
                    .not(':button, :submit, :reset, :hidden, :checkbox, :radio')
                    .val('')
                    .prop('selected', false);
                $(_this.elements.containers.formView).find('input[type="radio"]').prop('checked', false);
                $(_this.elements.containers.formView).find('input[type="checkbox"]').prop('checked', false);
                $(_this.elements.containers.formView).find('select').val('').trigger('change.select2');
                _this.events.trigger('clear');
            });
        }
        $(window).off('keydown.form.mapsvg').on('keydown.form.mapsvg', function (e) {
            if (MapSVG.formBuilder) {
                if ((e.metaKey || e.ctrlKey) && e.keyCode == 13)
                    MapSVG.formBuilder.save();
                else if (e.keyCode == 27)
                    MapSVG.formBuilder.close();
            }
        });
        if (this.editMode) {
            $(this.view).on('click', '.mapsvg-marker-image-selector button', function (e) {
                e.preventDefault();
                var src = $(this).find('img').attr('src');
                $(this).parent().find('button').removeClass('active');
                $(this).addClass('active');
                _this.mapsvg.setDefaultMarkerImage(src);
            });
            $(this.view).on('click', '#mapsvg-data-buttons-add button', function (e) {
                e.preventDefault();
                var type = $(this).data('create');
                let formElement = this.formElementFactory.create({ type: type });
                _this.addField(formElement);
            });
            $(this.view).on('click', '#mapsvg-data-btn-save-schema', function (e) {
                e.preventDefault();
                var fields = _this.getSchema();
                var counts = {};
                _this.formElements.forEach(function (elem) { counts[elem.name] = (counts[elem.name] || 0) + 1; });
                $(_this.elements.containers.formView).find('.form-group').removeClass('has-error');
                var errors = [];
                var reservedFields = ['id', 'lat', 'lon', 'lng', 'location', 'location_lat', 'location_lon', 'location_lng', 'location_address', 'location_img', 'marker', 'marker_id', 'regions', 'region_id', 'post_id', 'post', 'post_title', 'post_url', 'keywords', 'status'];
                var reservedFieldsToTypes = { 'regions': 'region', 'status': 'status', 'post_id': 'post', 'marker': 'marker', 'location': 'location' };
                var errUnique, errEmpty;
                _this.formElements.forEach(function (formElement, index) {
                    var err = false;
                    if (!_this.filtersMode) {
                        if (counts[formElement.name] > 1) {
                            if (!errUnique) {
                                errUnique = 'Field names should be unique';
                                errors.push(errUnique);
                                err = true;
                            }
                        }
                        else if (formElement.name.length === 0) {
                            if (!errEmpty) {
                                errEmpty = 'Field name can\'t be empty';
                                errors.push(errEmpty);
                                err = true;
                            }
                        }
                        else if (reservedFields.indexOf(formElement.name) != -1) {
                            if (!reservedFieldsToTypes[formElement.name] || (reservedFieldsToTypes[formElement.name] && reservedFieldsToTypes[formElement.name] != formElement.type)) {
                                var msg = 'Field name "' + formElement.name + '" is reserved, please set another name';
                                errors.push(msg);
                                err = true;
                            }
                        }
                    }
                    if (formElement.options && formElement.type != 'region' && formElement.type != 'marker') {
                        var vals = Array.from(formElement.options.values()).map(function (obj) {
                            return obj.value;
                        });
                        let uniq = [...Array.from((new Set(vals)).values())];
                        if (vals.length != uniq.length) {
                            errors.push('Check "Options" list - values should not repeat');
                            err = true;
                        }
                    }
                    err && $(formElement.views.element).addClass('has-error');
                });
                if (errors.length == 0) {
                    this.events.trigger('saveSchema', this, fields);
                }
                else {
                    jQuery.growl.error({ title: "Errors", message: errors.join('<br />') });
                }
            });
            setTimeout(function () {
                var el = _this.elements.containers.formView[0];
                _this.sortable = new Sortable(el, {
                    animation: 150,
                    onStart: function () {
                        $(_this.elements.containers.formView).addClass('sorting');
                    },
                    onEnd: function () {
                        setTimeout(function () {
                            $(_this.elements.containers.formView).removeClass('sorting');
                            _this.formElements = [];
                            $(el).find('.form-group').each(function (index, elem) {
                                _this.formElements.push($(elem).data('formElement'));
                            });
                        }, 500);
                    }
                });
            }, 1000);
        }
        else {
            $(_this.view).on('click', 'button.btn-save', function (e) {
                e.preventDefault();
                _this.save();
            });
            $(_this.view).on('click', 'button.btn-close', function (e) {
                e.preventDefault();
                _this.close();
            });
        }
        let locationField = _this.mapsvg.objectsRepository.getSchema().getField('location');
        this.formElements.forEach((formElement) => {
            formElement.events.on('changed', (_formElement) => {
                let name = _formElement.name;
                let value = _formElement.value;
                this.events.trigger('changed.field', _formElement, [name, value]);
            });
            if (locationField && locationField.markersByFieldEnabled && locationField.markerField && formElement.name == locationField.markerField && Object.values(locationField.markersByField).length > 0) {
                formElement.events.on('changed', (_formElement) => {
                    let name = _formElement.name;
                    let value = _formElement.value;
                    var src = locationField.markersByField[value];
                    if (src) {
                        if (_this.marker) {
                            var marker = _this.mapsvg.getMarker(_this.marker.id);
                            marker.setImage(src);
                            $(_this.view).find('.mapsvg-marker-image-btn img').attr('src', src);
                        }
                    }
                });
            }
        });
        new ResizeSensor(this.view[0], function () {
            _this.scrollApi && _this.scrollApi.reinitialise();
        });
    }
    save() {
        var _this = this;
        if (_this.marker) {
            _this.marker.events.off('change');
            _this.mapsvg.unsetEditingMarker();
        }
        var data = _this.getData();
        _this.saved = true;
        this.events.trigger('save', _this, data);
    }
    getFormElementByType(type) {
        return this.formElements.find((el) => el.type === type);
    }
    getData() {
        return this.formElements.map((formElement) => formElement.getData());
    }
    redraw() {
        var _this = this;
        delete _this.marker;
        $(_this.container).empty();
        $(_this.elements.containers.formView).empty();
        _this.formElements = [];
        if (_this.data && _this.data.id) {
            let formElement = this.formElementFactory.create({ type: 'id', label: 'ID', name: 'id', value: _this.data.id });
            _this.addField(formElement);
        }
        if (_this.data && _this.data._title) {
            let formElement = this.formElementFactory.create({ type: 'title', label: 'Title', name: 'title', value: _this.data._title });
            _this.addField(formElement);
        }
        _this.schema && _this.schema.fields.size > 0 && _this.schema.fields.forEach(function (elem) {
            if (_this.admin && _this.admin.isMetabox && elem.type == 'post') {
            }
            else {
                if (_this.filtersMode) {
                    if (elem.type == 'distance') {
                        elem.value = _this.data.distance ? _this.data.distance : elem.value !== undefined ? elem.value : null;
                    }
                    else {
                        elem.value = _this.data[elem.parameterNameShort];
                    }
                }
                else {
                    elem.value = _this.data ? _this.data[elem.name] : elem.value !== undefined ? elem.value : null;
                }
                if (elem.type == 'location' && !_this.editMode) {
                    if (elem.value && elem.value.marker && elem.value.marker.id) {
                        _this.marker = elem.value.marker.getOptions();
                        _this.mapsvg.setEditingMarker(elem.value.marker);
                    }
                    _this.admin && _this.admin.setMode && _this.admin.setMode('editMarkers');
                    _this.admin && _this.admin.enableMarkersMode(true);
                    _this.mapsvg.setMarkerEditHandler(function () {
                        _this.marker = this.getOptions();
                        _this.mapsvg.setEditingMarker(this);
                        var object = _this.getData();
                        var img = _this.mapsvg.getMarkerImage(object);
                        var marker = this;
                        marker.setImage(img);
                        let locationFormElement = _this.getFormElementByType('location');
                        locationFormElement && locationFormElement.renderMarker(_this.mapsvg.getMarker(_this.marker.id));
                    });
                }
                else if (elem.type == 'post') {
                    elem.post = _this.data['post'];
                }
                else if (elem.type === 'region') {
                    elem.options = _this.getRegionsList();
                }
                let formElement = _this.formElementFactory.create(elem);
                if (_this.filtersMode) {
                    if (!_this.filtersHide || (_this.filtersHide && (_this.modal && elem.type !== 'search') || (!_this.modal && elem.type === 'search'))) {
                        _this.addField(formElement);
                    }
                }
                else {
                    _this.addField(formElement);
                }
            }
        });
        if (!_this.editMode) {
            if (this.schema.fields.size === 0) {
                let formElement = this.formElementFactory.create({ type: 'empty' });
                _this.addField(formElement);
            }
            else {
                if (_this.admin && !_this.admin.isMetabox) {
                    let formElement = this.formElementFactory.create({ type: 'save' });
                    _this.addField(formElement);
                }
            }
        }
        if (_this.filtersMode && _this.filtersHide && !_this.modal) {
            let formElement = this.formElementFactory.create({ type: 'modal', 'buttonText': _this.showButtonText });
            this.showFiltersButton = _this.addField(formElement);
        }
        if (!_this.editMode && !_this.filtersMode) {
            var nano = $('<div class="nano"></div>');
            var nanoContent = $('<div class="nano-content"></div>');
            nano.append(nanoContent);
            nanoContent.html(this.view);
            $(_this.container).html(nano.html());
            nano.jScrollPane();
            _this.scrollApi = nano.data('jsp');
        }
        else {
            $(_this.container).html(this.view);
        }
        if (_this.filtersMode && _this.clearButton) {
            _this.elements.buttons.clearButton = $('<div class="form-group mapsvg-filters-reset-container"><button class="btn btn-default mapsvg-filters-reset">' + _this.clearButtonText + '</button></div>')[0];
            $(this.elements.containers.formView).find('.mapsvg-data-form-view').append(_this.elements.buttons.clearButton);
        }
        this.events.trigger('load');
        if (!this.editMode && !_this.filtersMode)
            $(this.view).find('input:visible,textarea:visible').not('.tt-hint').first().focus();
        var cm = $(this.container).find('.CodeMirror');
        cm.each(function (index, el) {
            el && el.CodeMirror.refresh();
        });
        _this.setEventHandlers();
        this.events.trigger('init', this, [this.data]);
    }
    deleteField(formElement) {
        var _this = this;
        _this.formElements.forEach(function (fc, index) {
            if (fc === formElement) {
                _this.formElements.splice(index, 1);
                _this.structureChanged = true;
            }
        });
    }
    getExtraParams() {
        let databaseFields = [];
        this.mapsvg.objectsRepository.getSchema().getFields().forEach(function (obj) {
            if (obj.type == 'text' || obj.type == 'region' || obj.type == 'textarea' || obj.type == 'post' || obj.type == 'select' || obj.type == 'radio' || obj.type == 'checkbox') {
                if (obj.type == 'post') {
                    databaseFields.push('Object.post.post_title');
                }
                else {
                    databaseFields.push('Object.' + obj.name);
                }
            }
        });
        let databaseFieldsFilterableShort = [];
        databaseFieldsFilterableShort = this.mapsvg.objectsRepository.getSchema().getFieldsAsArray().filter(function (obj) {
            return (obj.type == 'select' || obj.type == 'radio' || obj.type == 'region');
        }).map(function (obj) {
            return obj.name;
        });
        let regionFields = this.mapsvg.regionsRepository.getSchema().getFieldsAsArray().map(function (obj) {
            if (obj.type == 'status' || obj.type == 'text' || obj.type == 'textarea' || obj.type == 'post' || obj.type == 'select' || obj.type == 'radio' || obj.type == 'checkbox') {
                if (obj.type == 'post') {
                    return 'Region.post.post_title';
                }
                else {
                    return 'Region.' + obj.name;
                }
            }
        });
        return {
            databaseFields: databaseFields,
            databaseFieldsFilterableShort: databaseFieldsFilterableShort,
            regionFields: regionFields
        };
    }
    addField(formElement) {
        var _this = this;
        if (['region', 'marker', 'post', 'status', 'distance', 'location', 'search'].indexOf(formElement.type) != -1) {
            var repeat = false;
            _this.formElements.forEach(function (control) {
                if (control.type == formElement.type)
                    repeat = true;
            });
            if (repeat) {
                jQuery.growl.error({ title: 'Error', message: 'You can add only 1 "' + MapSVG.ucfirst(formElement.type) + '" field' });
                return;
            }
        }
        _this.formElements.push(formElement);
        _this.elements.containers.formView.append(formElement.views.element);
        if (this.editMode) {
            this.edit(formElement);
            formElement.events.on('click', (elem) => {
                this.edit(elem);
            });
            formElement.events.on('delete', (elem) => {
                this.deleteField(elem);
            });
        }
        return formElement;
    }
    edit(formElement) {
        var _this = this;
        _this.currentlyEditing && _this.currentlyEditing.destroyEditor();
        _this.elements.containers.form_edit.append(formElement.getEditor());
        formElement.initEditor();
        _this.currentlyEditing = formElement;
        $(_this.elements.containers.formView).find('.form-group.active').removeClass('active');
        formElement.views.element.addClass('active');
    }
    get() {
        return this.formElements.map(function (c) {
            return c.get();
        });
    }
    getSchema() {
        return this.formElements.map(function (c) {
            return c.getSchema();
        });
    }
    close() {
        var _this = this;
        this.formElements.forEach(formElement => formElement.destroy());
        if (!_this.saved) {
            if (_this.data.id == undefined && _this.marker) {
                var marker = _this.mapsvg.getMarker(_this.marker.id);
                marker.events.off('change');
                marker.delete();
                delete _this.marker;
            }
            if (this.backupData) {
                if (this.backupData.location) {
                    _this.mapsvg.markerAdd(this.backupData.location.marker);
                    _this.mapsvg.setEditingMarker(this.backupData.location.marker);
                }
            }
            if (_this.marker) {
                var editingMarker = _this.mapsvg.getEditingMarker();
                if (editingMarker) {
                    editingMarker.setImage(_this.marker.src);
                    _this.editingMarker.setPoint(_this.marker.svgPoint);
                    _this.mapsvg.unsetEditingMarker();
                }
            }
        }
        _this.admin && _this.admin.enableMarkersMode(false);
        MapSVG.formBuilder = null;
        this.events.trigger('close');
    }
    destroy() {
        $(this.view).empty().remove();
        this.sortable = null;
    }
    toJSON(addEmpty) {
        var obj = {};
        function add(obj, name, value) {
            if (!addEmpty && !value)
                return false;
            if (name.length == 1) {
                obj[name[0]] = value;
            }
            else {
                if (obj[name[0]] == null) {
                    if (name[1] === '') {
                        obj[name[0]] = [];
                    }
                    else {
                        obj[name[0]] = {};
                    }
                }
                if (obj[name[0]].length !== undefined) {
                    obj[name[0]].push(value);
                }
                else {
                    add(obj[name[0]], name.slice(1), value);
                }
            }
        }
        $(this.elements.containers.formView).find('input, textarea, select').each(function () {
            if (!$(this).data('skip')
                &&
                    !$(this).prop('disabled')
                &&
                    $(this).attr('name')
                &&
                    !(!addEmpty && $(this).attr('type') == 'checkbox' && $(this).attr('checked') == undefined)
                &&
                    !($(this).attr('type') == 'radio' && $(this).attr('checked') == undefined)) {
                var value;
                if ($(this).attr('type') == 'checkbox') {
                    value = $(this).prop('checked');
                }
                else {
                    value = $(this).val();
                }
                add(obj, $(this).attr('name').replace(/]/g, '').split('['), value);
            }
        });
        return obj;
    }
    getRegionsList() {
        let list = new Map();
        this.mapsvg.regions.forEach(region => {
            list.set(region.id, { id: region.id, title: region.title });
        });
        return list;
    }
    getRegionsAsArray() {
        let list = [];
        this.mapsvg.regions.forEach(region => {
            list.push(region);
        });
        return list;
    }
}
//# sourceMappingURL=FormBuilder.js.map