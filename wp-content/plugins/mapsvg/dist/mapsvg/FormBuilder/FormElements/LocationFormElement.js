import { MapSVG } from "../../Core/globals";
import { FormElement } from "./FormElement";
import Handlebars from "../../../handlebars.js";
import Bloodhound from "../../../typeahead.bundle.min.js";
export class LocationFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.location = this.value;
        this.label = this.label || (options.label === undefined ? 'Location' : options.label);
        this.name = 'location';
        this.db_type = 'text';
        this.languages = [{ "value": "sq", "label": "Albanian" }, { "value": "ar", "label": "Arabic" }, {
                "value": "eu",
                "label": "Basque"
            }, { "value": "be", "label": "Belarusian" }, { "value": "bg", "label": "Bulgarian" }, {
                "value": "my",
                "label": "Burmese"
            }, { "value": "bn", "label": "Bengali" }, { "value": "ca", "label": "Catalan" }, {
                "value": "zh-cn",
                "label": "Chinese (simplified)"
            }, { "value": "zh-tw", "label": "Chinese (traditional)" }, {
                "value": "hr",
                "label": "Croatian"
            }, { "value": "cs", "label": "Czech" }, { "value": "da", "label": "Danish" }, {
                "value": "nl",
                "label": "Dutch"
            }, { "value": "en", "label": "English" }, {
                "value": "en-au",
                "label": "English (australian)"
            }, { "value": "en-gb", "label": "English (great Britain)" }, {
                "value": "fa",
                "label": "Farsi"
            }, { "value": "fi", "label": "Finnish" }, { "value": "fil", "label": "Filipino" }, {
                "value": "fr",
                "label": "French"
            }, { "value": "gl", "label": "Galician" }, { "value": "de", "label": "German" }, {
                "value": "el",
                "label": "Greek"
            }, { "value": "gu", "label": "Gujarati" }, { "value": "iw", "label": "Hebrew" }, {
                "value": "hi",
                "label": "Hindi"
            }, { "value": "hu", "label": "Hungarian" }, { "value": "id", "label": "Indonesian" }, {
                "value": "it",
                "label": "Italian"
            }, { "value": "ja", "label": "Japanese" }, { "value": "kn", "label": "Kannada" }, {
                "value": "kk",
                "label": "Kazakh"
            }, { "value": "ko", "label": "Korean" }, { "value": "ky", "label": "Kyrgyz" }, {
                "value": "lt",
                "label": "Lithuanian"
            }, { "value": "lv", "label": "Latvian" }, { "value": "mk", "label": "Macedonian" }, {
                "value": "ml",
                "label": "Malayalam"
            }, { "value": "mr", "label": "Marathi" }, { "value": "no", "label": "Norwegian" }, {
                "value": "pl",
                "label": "Polish"
            }, { "value": "pt", "label": "Portuguese" }, {
                "value": "pt-br",
                "label": "Portuguese (brazil)"
            }, { "value": "pt-pt", "label": "Portuguese (portugal)" }, {
                "value": "pa",
                "label": "Punjabi"
            }, { "value": "ro", "label": "Romanian" }, { "value": "ru", "label": "Russian" }, {
                "value": "sr",
                "label": "Serbian"
            }, { "value": "sk", "label": "Slovak" }, { "value": "sl", "label": "Slovenian" }, {
                "value": "es",
                "label": "Spanish"
            }, { "value": "sv", "label": "Swedish" }, { "value": "tl", "label": "Tagalog" }, {
                "value": "ta",
                "label": "Tamil"
            }, { "value": "te", "label": "Telugu" }, { "value": "th", "label": "Thai" }, {
                "value": "tr",
                "label": "Turkish"
            }, { "value": "uk", "label": "Ukrainian" }, { "value": "uz", "label": "Uzbek" }, {
                "value": "vi",
                "label": "Vietnamese"
            }];
        this.language = options.language;
        this.markerImages = MapSVG.markerImages;
        this.markersByField = options.markersByField;
        this.markerField = options.markerField;
        this.markersByFieldEnabled = MapSVG.parseBoolean(options.markersByFieldEnabled);
        this.templates.marker = Handlebars.compile($('#mapsvg-data-tmpl-marker').html());
        this.location && this.location.marker && this.renderMarker();
    }
    setDomElements() {
        super.setDomElements();
    }
    getSchema() {
        let schema = super.getSchema();
        schema.language = this.language;
        schema.markersByField = this.markersByField;
        schema.markerField = this.markerField;
        schema.markersByFieldEnabled = MapSVG.parseBoolean(this.markersByFieldEnabled);
        return schema;
    }
    getData() {
        return this.location;
    }
    getDataForTemplate() {
        let data = super.getDataForTemplate();
        if (this.formBuilder.admin) {
            data.languages = this.languages;
            data.markerImages = MapSVG.markerImages;
            data.markersByField = this.markersByField;
            data.markerField = this.markerField;
            data.markersByFieldEnabled = MapSVG.parseBoolean(this.markersByFieldEnabled);
            var _this = this;
            data.markerImages.forEach(function (m) {
                if (m.url === _this.formBuilder.mapsvg.getData().options.defaultMarkerImage) {
                    m.default = true;
                }
                else {
                    m.default = false;
                }
            });
        }
        data.language = this.language;
        if (this.location) {
            data.location = this.location;
            if (this.location.marker) {
                data.location.img = (this.location.marker.src.indexOf(MapSVG.urls.uploads) === 0 ? 'uploads/' : '') + (this.location.marker.src.split('/').pop());
            }
        }
        return data;
    }
    initEditor() {
        super.initEditor();
        this.fillMarkersByFieldOptions(this.markerField);
    }
    setEventHandlers() {
        super.setEventHandlers();
        var _this = this;
        if (_this.formBuilder.mapsvg.isGeo()) {
            var locations = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('formatted_address'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: MapSVG.urls.ajaxurl + '?action=mapsvg_geocoding&address=%QUERY%&language=' + this.language,
                    wildcard: '%QUERY%',
                    transform: function (response) {
                        if (response.error_message) {
                            alert(response.error_message);
                        }
                        return response.results;
                    },
                    rateLimitWait: 600
                }
            });
            var thContainer = $(this.domElements.main).find('.typeahead');
            var tH = thContainer.typeahead({
                minLength: 3
            }, {
                name: 'mapsvg-addresses',
                display: 'formatted_address',
                source: locations
            });
            thContainer.on('typeahead:select', function (ev, item) {
                _this.location && _this.location.marker && _this.deleteMarker();
                let address;
                address = {};
                address.formatted = item.formatted_address;
                item.address_components.forEach(function (addr_item) {
                    var type = addr_item.types[0];
                    address[type] = addr_item.long_name;
                    if (addr_item.short_name != addr_item.long_name) {
                        address[type + '_short'] = addr_item.short_name;
                    }
                });
                var locationData = {
                    address: address,
                    lat: item.geometry.location.lat,
                    lng: item.geometry.location.lng,
                    img: _this.formBuilder.mapsvg.getMarkerImage(_this.formBuilder.getData())
                };
                _this.location = new MapSVG.Location(locationData, _this.formBuilder.mapsvg);
                _this.formBuilder.location = _this.location;
                var marker = new MapSVG.Marker({
                    location: _this.location,
                    mapsvg: _this.formBuilder.mapsvg
                });
                _this.location.marker = marker;
                _this.formBuilder.mapsvg.markerAdd(_this.location.marker);
                _this.formBuilder.mapsvg.setEditingMarker(marker);
                _this.formBuilder.marker = marker.getOptions();
                _this.renderMarker();
                var select = $(_this.formBuilder.view).find('select[name="regions"]');
                if (_this.formBuilder.mapsvg.options.source.indexOf('/geo-calibrated/usa.svg') !== -1) {
                    if (select.length !== 0 && _this.location.address.state_short) {
                        select.val(['US-' + _this.location.address.state_short]);
                        select.trigger('change');
                    }
                }
                else if (_this.formBuilder.mapsvg.options.source.indexOf('/geo-calibrated/world.svg') !== -1) {
                    if (select.length !== 0 && _this.location.address.country_short) {
                        select.val([_this.location.address.country_short]);
                        select.trigger('change');
                    }
                }
                else {
                    if (select.length !== 0 && _this.location.address.administrative_area_level_1) {
                        _this.formBuilder.mapsvg.regions.forEach((_region) => {
                            if (_region.title === _this.location.address.administrative_area_level_1
                                ||
                                    _region.title === _this.location.address.administrative_area_level_2
                                ||
                                    _region.id === _this.location.address.country_short + '-' + _this.location.address.administrative_area_level_1_short) {
                                select.val([_region.id]);
                                select.trigger('change');
                            }
                        });
                    }
                }
                thContainer.typeahead('val', '');
            });
        }
        $(this.domElements.main).on('click', '.mapsvg-marker-image-btn-trigger', function (e) {
            $(this).toggleClass('active');
            _this.toggleMarkerSelector.call(_this, $(this), e);
        });
        $(this.domElements.main).on('click', '.mapsvg-marker-delete', function (e) {
            e.preventDefault();
            _this.deleteMarker();
        });
    }
    setEditorEventHandlers() {
        super.setEditorEventHandlers();
        var _this = this;
        var imgSelector = $('#marker-file-uploader').closest('.form-group').find('.mapsvg-marker-image-selector');
        $(this.domElements.edit).on('change', 'select[name="markerField"]', function () {
            var fieldName = $(this).val();
            _this.fillMarkersByFieldOptions(fieldName);
        });
        $(this.domElements.edit).on('click', '.mapsvg-marker-image-btn-trigger', function (e) {
            $(this).toggleClass('active');
            _this.toggleMarkerSelectorInLocationEditor.call(_this, $(this), e);
        });
        $(this.domElements.edit).on('change', '#marker-file-uploader', function () {
            let uploadBtn = $(this).closest('.btn-file')._button('loading');
            for (var i = 0; i < this.files.length; i++) {
                var data = new FormData();
                data.append('file', this.files[0]);
                data.append('action', 'mapsvg_marker_upload');
                data.append('_wpnonce', MapSVG.nonce);
                $.ajax({
                    url: MapSVG.urls.ajaxurl,
                    type: "POST",
                    data: data,
                    processData: false,
                    contentType: false
                }).done(function (resp) {
                    resp = JSON.parse(resp);
                    if (resp.error) {
                        alert(resp.error);
                    }
                    else {
                        var newMarker = '<button class="btn btn-default mapsvg-marker-image-btn mapsvg-marker-image-btn-choose">'
                            + '<img src="' + resp.url + '" />'
                            + '</button>';
                        $(newMarker).appendTo(imgSelector);
                        MapSVG.markerImages.push(resp);
                    }
                }).always(function () {
                    uploadBtn._button('reset');
                });
            }
        });
    }
    updateData() {
    }
    mayBeAddDistanceRow() {
        var _this = this;
        if (!this.domElements.editDistanceRow) {
            this.domElements.editDistanceRow = $($('#mapsvg-edit-distance-row').html())[0];
        }
        var z = $(this.domElements.edit).find('.mapsvg-edit-distance-row:last-child input');
        if (z && z.last() && z.last().val() && (z.last().val() + '').trim().length) {
            var newRow = $(this.templates.editDistanceRow).clone();
            newRow.insertAfter($(_this.domElements.edit).find('.mapsvg-edit-distance-row:last-child'));
        }
        var rows = $(_this.domElements.edit).find('.mapsvg-edit-distance-row');
        var row1 = rows.eq(rows.length - 2);
        var row2 = rows.eq(rows.length - 1);
        if (row1.length && row2.length && !row1.find('input:eq(0)').val().toString().trim() && !row2.find('input:eq(0)').val().toString().trim()) {
            row2.remove();
        }
    }
    ;
    fillMarkersByFieldOptions(fieldName) {
        var _this = this;
        var field = _this.formBuilder.mapsvg.objectsRepository.getSchema().getField(fieldName);
        if (field) {
            var markerImg = _this.formBuilder.mapsvg.options.defaultMarkerImage;
            var rows = [];
            field.options.forEach(function (option) {
                var img = _this.markersByField && _this.markersByField[option.value] ? _this.markersByField[option.value] : markerImg;
                rows.push('<tr data-option-id="' + option.value + '"><td>' + option.label + '</td><td><button class="btn btn-default mapsvg-marker-image-btn-trigger mapsvg-marker-image-btn"><img src="' + img + '" class="new-marker-img" style="margin-right: 4px;"/><span class="caret"></span></button></td></tr>');
            });
            $("#markers-by-field").empty().append(rows);
        }
    }
    ;
    renderMarker(marker) {
        var _this = this;
        if (!this.location && !(marker && marker.location)) {
            return false;
        }
        if (marker && marker.location) {
            this.location = marker.location;
        }
        $(this.domElements.main).find('.mapsvg-new-marker').show().html(this.templates.marker(this.location));
        this.location.marker.events.on('change', () => {
            this.renderMarker();
        });
    }
    ;
    toggleMarkerSelector(jQueryObj, e) {
        e.preventDefault();
        var _this = this;
        if ($(_this.domElements.markerSelector) && $(_this.domElements.markerSelector).is(':visible')) {
            $(_this.domElements.markerSelector).hide();
            return;
        }
        if ($(_this.domElements.markerSelector) && $(_this.domElements.markerSelector).not(':visible')) {
            $(_this.domElements.markerSelector).show();
            return;
        }
        _this.domElements.markerImageButton = jQueryObj.find('img')[0];
        var currentImage = $(_this.domElements.markerImageButton).attr('src');
        var images = MapSVG.markerImages.map(function (image) {
            return '<button class="btn btn-default mapsvg-marker-image-btn mapsvg-marker-image-btn-choose ' + (currentImage == image.url ? 'active' : '') + '"><img src="' + image.url + '" /></button>';
        });
        if (!$(_this.domElements.markerSelector)) {
            _this.domElements.markerSelector = $(this.domElements.main).find('.mapsvg-marker-image-selector')[0];
        }
        if (_this.domElements.markerSelector) {
            $(_this.domElements.markerSelector).empty();
        }
        if (_this.formBuilder.marker) {
            $(_this.domElements.markerSelector).data('marker', _this.formBuilder.marker);
        }
        else {
            $(_this.domElements.markerSelector).data('marker', null);
        }
        $(_this.domElements.markerSelector).html(images.join(''));
        $(_this.domElements.markerSelector).on('click', '.mapsvg-marker-image-btn-choose', function (e) {
            e.preventDefault();
            var src = $(this).find('img').attr('src');
            if (_this.formBuilder.marker) {
                var marker = _this.formBuilder.mapsvg.getMarker(_this.formBuilder.marker.id);
                marker.setImage(src);
            }
            $(_this.domElements.markerSelector).hide();
            $(_this.domElements.main).find('.mapsvg-marker-image-btn-trigger').toggleClass('active', false);
            $(_this.domElements.markerImageButton).attr('src', src);
        });
    }
    ;
    toggleMarkerSelectorInLocationEditor(jQueryObj, e) {
        e.preventDefault();
        var _this = this;
        if (jQueryObj.data('markerSelector') && jQueryObj.data('markerSelector').is(':visible')) {
            jQueryObj.data('markerSelector').hide();
            return;
        }
        if (jQueryObj.data('markerSelector') && jQueryObj.data('markerSelector').not(':visible')) {
            jQueryObj.data('markerSelector').show();
            return;
        }
        var markerBtn = $(this).closest('td').find('.mapsvg-marker-image-btn-trigger');
        var currentImage = markerBtn.attr('src');
        var images = MapSVG.markerImages.map(function (image) {
            return '<button class="btn btn-default mapsvg-marker-image-btn mapsvg-marker-image-btn-choose ' + (currentImage == image.url ? 'active' : '') + '"><img src="' + image.url + '" /></button>';
        });
        if (!jQueryObj.data('markerSelector')) {
            var ms = $('<div class="mapsvg-marker-image-selector"></div>');
            jQueryObj.closest('td').append(ms);
            jQueryObj.data('markerSelector', ms);
        }
        else {
            jQueryObj.data('markerSelector').empty();
        }
        jQueryObj.data('markerSelector').html(images.join(''));
        jQueryObj.data('markerSelector').on('click', '.mapsvg-marker-image-btn-choose', function (e) {
            e.preventDefault();
            var src = $(this).find('img').attr('src');
            jQueryObj.data('markerSelector').hide();
            var td = $(this).closest('td');
            var fieldId = $(this).closest('tr').data('option-id');
            var btn = td.find('.mapsvg-marker-image-btn-trigger');
            btn.toggleClass('active', false);
            btn.find('img').attr('src', src);
            _this.setMarkerByField(fieldId, src);
        });
    }
    ;
    setMarkerByField(fieldId, markerImg) {
        this.markersByField = this.markersByField || {};
        this.markersByField[fieldId] = markerImg;
    }
    ;
    deleteMarker() {
        var _this = this;
        if (this.formBuilder.backupData) {
            this.formBuilder.backupData.location = this.location;
            this.formBuilder.backupData.marker = this.marker;
        }
        else {
            this.formBuilder.backupData = {
                location: this.location,
                marker: this.marker
            };
        }
        this.location = null;
        this.marker = null;
        if (this.formBuilder.marker) {
            this.formBuilder.mapsvg.getMarker(this.formBuilder.marker.id).delete();
            _this.formBuilder.mapsvg.editingMarker = null;
        }
        $(this.domElements.main).find('.mapsvg-new-marker').hide();
        $(this.domElements.main).find('.mapsvg-marker-id').attr('disabled', 'disabled');
    }
    ;
    destroy() {
        if ($().mselect2) {
            var sel = $(this.domElements.main).find('.mapsvg-select2');
            if (sel.length) {
                sel.mselect2('destroy');
            }
        }
        this.domElements.markerSelector && $(this.domElements.markerSelector).popover('destroy');
    }
}
//# sourceMappingURL=LocationFormElement.js.map