import { MapSVG } from "../Core/globals.js";
import Handlebars from "Handlebars";
import { ResizeSensor } from "../Core/ResizeSensor";
import { Events } from "../Core/Events";
import { FormElementFactory } from './FormElements/FormElementFactory.js';
import { ArrayIndexed } from "../Core/ArrayIndexed";
const $ = jQuery;
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
            formBuilder: this,
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
        this.formElements = new ArrayIndexed('name');
        if (!MapSVG.templatesLoaded[this.template]) {
            $.get(MapSVG.urls.root + "dist/" + _this.template + '.html', function (data) {
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
                let formElement = _this.formElementFactory.create({ type: type });
                _this.addField(formElement);
            });
            $(this.view).on('click', '#mapsvg-data-btn-save-schema', function (e) {
                e.preventDefault();
                var fields = _this.getSchema();
                var counts = {};
                _this.formElements.forEach(function (elem) { counts[elem.name] = (counts[elem.name] || 0) + 1; });
                $(_this.elements.containers.formView).find('.form-group').removeClass('has-error');
                var errors = [];
                var reservedFields = ['lat', 'lon', 'lng', 'location', 'location_lat', 'location_lon', 'location_lng', 'location_address', 'location_img', 'marker', 'marker_id', 'regions', 'region_id', 'post_id', 'post', 'post_title', 'post_url', 'keywords', 'status'];
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
                        var vals = formElement.options.map(function (obj) {
                            return obj.value;
                        });
                        let uniq = [...Array.from((new Set(vals)).values())];
                        if (vals.length != uniq.length) {
                            errors.push('Check "Options" list - values should not repeat');
                            err = true;
                        }
                    }
                    err && $(formElement.domElements.main).addClass('has-error');
                });
                if (errors.length === 0) {
                    _this.events.trigger('saveSchema', _this, [fields]);
                }
                else {
                    jQuery.growl.error({ title: "Errors", message: errors.join('<br />') });
                }
            });
            setTimeout(function () {
                var el = _this.elements.containers.formView;
                _this.sortable = new Sortable(el, {
                    animation: 150,
                    onStart: function () {
                        $(_this.elements.containers.formView).addClass('sorting');
                    },
                    onEnd: function () {
                        setTimeout(function () {
                            $(_this.elements.containers.formView).removeClass('sorting');
                            _this.formElements.clear();
                            $(el).find('.form-group').each(function (index, elem) {
                                _this.formElements.push($(elem).data('formElement'));
                            });
                        }, 500);
                    }
                });
            }, 1000);
        }
        else {
        }
        new ResizeSensor(this.view, function () {
            _this.scrollApi && _this.scrollApi.reinitialise();
        });
    }
    setFormElementEventHandlers(formElement) {
        var _this = this;
        if (this.editMode) {
            formElement.events.on('click', (elem) => {
                this.edit(elem);
            });
            formElement.events.on('delete', (elem) => {
                this.deleteField(elem);
            });
        }
        else {
            formElement.events.on('changed', (_formElement) => {
                let name = _formElement.name;
                let value = _formElement.value;
                if (_formElement.type !== 'search') {
                    this.events.trigger('changed.field', _formElement, [name, value]);
                }
                else {
                    this.events.trigger('changed.search', _formElement, [value]);
                }
            });
            let locationField = _this.mapsvg.objectsRepository.getSchema().getField('location');
            if (locationField && locationField.markersByFieldEnabled && locationField.markerField && formElement.name == locationField.markerField && Object.values(locationField.markersByField).length > 0) {
                formElement.events.on('changed', (_formElement) => {
                    let name = _formElement.name;
                    let value = _formElement.value;
                    var src = locationField.markersByField[value];
                    if (src) {
                        if (_this.markerBackup) {
                            var marker = _this.mapsvg.getMarker(_this.markerBackup.id);
                            marker.setImage(src);
                            $(_this.view).find('.mapsvg-marker-image-btn img').attr('src', src);
                        }
                    }
                });
            }
        }
    }
    save() {
        var _this = this;
        if (_this.markerBackup) {
            var marker = _this.mapsvg.getEditingMarker();
            marker.events.off('change');
            _this.markerBackup = marker.getOptions();
            _this.mapsvg.unsetEditingMarker();
        }
        var data = _this.getData();
        _this.saved = true;
        this.events.trigger('save', _this, [data]);
    }
    getFormElementByType(type) {
        return this.formElements.find((el) => el.type === type);
    }
    getData() {
        let data = {};
        this.formElements.forEach((formElement) => {
            if (formElement.readonly === false || formElement.type === 'id') {
                let _formElementData = formElement.getData();
                data[_formElementData.name] = _formElementData.value;
            }
        });
        return data;
    }
    redraw() {
        var _this = this;
        delete _this.markerBackup;
        $(_this.container).empty();
        $(_this.elements.containers.formView).empty();
        _this.formElements.clear();
        _this.schema && _this.schema.fields.length > 0 && _this.schema.fields.forEach(function (elem) {
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
                        _this.markerBackup = elem.value.marker.getOptions();
                        _this.mapsvg.setEditingMarker(elem.value.marker);
                    }
                    _this.admin && _this.admin.setMode && _this.admin.setMode('editMarkers');
                    _this.admin && _this.admin.enableMarkersMode(true);
                    _this.mapsvg.setMarkerEditHandler(function () {
                        _this.markerBackup = this.getOptions();
                        _this.mapsvg.setEditingMarker(this);
                        var object = _this.getData();
                        var img = _this.mapsvg.getMarkerImage(object);
                        var marker = this;
                        marker.setImage(img);
                        let locationFormElement = _this.getFormElementByType('location');
                        locationFormElement && locationFormElement.renderMarker(marker);
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
            if (this.schema.fields.length === 0) {
                let formElement = this.formElementFactory.create({ type: 'empty' });
                _this.addField(formElement);
            }
            else {
                if (_this.admin && !_this.admin.isMetabox) {
                    let formElement = this.formElementFactory.create({ type: 'save' });
                    formElement.events.on('click.btn.save', () => {
                        this.save();
                    });
                    formElement.events.on('click.btn.close', () => {
                        this.close();
                    });
                    _this.addField(formElement);
                }
            }
        }
        if (_this.filtersMode && _this.filtersHide && !_this.modal) {
            let formElement = this.formElementFactory.create({ type: 'modal', 'showButtonText': _this.showButtonText });
            this.showFiltersButton = _this.addField(formElement);
        }
        if (!_this.editMode && !_this.filtersMode) {
            var nano = $('<div class="nano"></div>');
            var nanoContent = $('<div class="nano-content"></div>');
            nano.append(nanoContent);
            nanoContent.append(this.view);
            $(_this.container).append(nano);
            nano.jScrollPane({ mouseWheelSpeed: 30 });
            _this.scrollApi = nano.data('jsp');
        }
        else {
            $(_this.container).append(this.view);
        }
        if (_this.filtersMode && _this.clearButton) {
            _this.elements.buttons.clearButton = $('<div class="form-group mapsvg-filters-reset-container"><button class="btn btn-default mapsvg-filters-reset">' + _this.clearButtonText + '</button></div>')[0];
            $(this.elements.containers.formView).append(_this.elements.buttons.clearButton);
        }
        if (!this.editMode && !_this.filtersMode)
            $(this.view).find('input:visible,textarea:visible').not('.tt-hint').first().focus();
        var cm = $(this.container).find('.CodeMirror');
        cm.each(function (index, el) {
            el && el.CodeMirror.refresh();
        });
        _this.setEventHandlers();
        this.events.trigger('init', this, [this.getData()]);
        this.events.trigger('loaded');
    }
    deleteField(formElement) {
        var _this = this;
        _this.formElements.delete(formElement.name);
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
        _this.elements.containers.formView.append(formElement.domElements.main);
        this.setFormElementEventHandlers(formElement);
        if (this.editMode) {
            if (formElement.protected) {
                formElement.hide();
            }
            else {
                this.edit(formElement);
            }
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
        $(formElement.domElements.main).addClass('active');
    }
    get() {
    }
    getSchema() {
        return this.formElements.map(function (formElement) {
            return formElement.getSchema();
        });
    }
    close() {
        var _this = this;
        this.formElements.forEach(formElement => formElement.destroy());
        if (!_this.saved) {
            if (_this.data.id == undefined && _this.markerBackup) {
                var marker = _this.mapsvg.getMarker(_this.markerBackup.id);
                marker.events.off('change');
                marker.delete();
                delete _this.markerBackup;
            }
            if (this.backupData) {
                if (this.backupData.location) {
                    _this.mapsvg.markerAdd(this.backupData.location.marker);
                    _this.mapsvg.setEditingMarker(this.backupData.location.marker);
                }
            }
            if (_this.markerBackup) {
                var editingMarker = _this.mapsvg.getEditingMarker();
                if (editingMarker) {
                    editingMarker.setImage(_this.markerBackup.src);
                    editingMarker.setPoint(_this.markerBackup.svgPoint);
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
        return this.mapsvg.regions.map(function (r) {
            return { id: r.id, title: r.title };
        });
    }
    getRegionsAsArray() {
        return this.mapsvg.regions;
    }
}
//# sourceMappingURL=FormBuilder.js.map