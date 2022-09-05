import { FormElement } from "./FormElement";
import { MapSVG } from "../../Core/globals";
export class DistanceFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.label = this.label || (options.label === undefined ? 'Search radius' : options.label);
        this.distanceControl = options.distanceControl || 'select';
        this.distanceUnits = options.distanceUnits || 'km';
        this.distanceUnitsLabel = options.distanceUnitsLabel || 'km';
        this.fromLabel = options.fromLabel || 'from';
        this.placeholder = options.placeholder;
        this.userLocationButton = options.userLocationButton || false;
        this.type = options.type;
        this.addressField = options.addressField || true;
        this.addressFieldPlaceholder = options.addressFieldPlaceholder || 'Address';
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
        this.countries = MapSVG.countries;
        this.country = options.country;
        this.language = options.language;
        this.searchByZip = options.searchByZip;
        this.zipLength = options.zipLength || 5;
        this.userLocationButton = MapSVG.parseBoolean(options.userLocationButton);
        this.options = options.options || [
            { value: '10', default: true },
            { value: '30', default: false },
            { value: '50', default: false },
            { value: '100', default: false }
        ];
        var selected = false;
        if (this.value) {
            this.options.forEach((option) => {
                if (option.value === this.value.length) {
                    option.selected = true;
                    selected = true;
                }
            });
        }
        if (!selected) {
            this.options.forEach(function (option) {
                if (option.default) {
                    option.selected = true;
                }
            });
        }
        this.value = {
            units: this.distanceUnits,
            latlng: '',
            length: '',
            address: '',
            country: this.country
        };
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.units = $(this.domElements.main).find('[name="distanceUnits"]')[0];
        this.inputs.latlng = $(this.domElements.main).find('[name="distanceLatLng"]')[0];
        this.inputs.length = $(this.domElements.main).find('[name="distanceLength"]')[0];
        this.inputs.address = $(this.domElements.main).find('[name="distanceAddress"]')[0];
    }
    getSchema() {
        let schema = super.getSchema();
        schema.distanceControl = this.distanceControl;
        schema.distanceUnits = this.distanceUnits;
        schema.distanceUnitsLabel = this.distanceUnitsLabel;
        schema.fromLabel = this.fromLabel;
        schema.addressField = this.addressField;
        schema.addressFieldPlaceholder = this.addressFieldPlaceholder;
        schema.userLocationButton = this.userLocationButton;
        schema.placeholder = this.placeholder;
        schema.language = this.language;
        schema.country = this.country;
        schema.searchByZip = this.searchByZip;
        schema.zipLength = this.zipLength;
        schema.userLocationButton = MapSVG.parseBoolean(this.userLocationButton);
        if (schema.distanceControl === 'none') {
            schema.distanceDefault = schema.options.filter(function (o) {
                return o.default;
            })[0].value;
        }
        schema.options.forEach(function (option, index) {
            if (schema.options[index].value === '') {
                schema.options.splice(index, 1);
            }
            else {
                schema.options[index].default = MapSVG.parseBoolean(schema.options[index].default);
            }
        });
        return schema;
    }
    getDataForTemplate() {
        let data = super.getDataForTemplate();
        if (this.formBuilder.admin) {
            data.languages = this.languages;
            data.countries = this.countries;
        }
        data.language = this.language;
        data.country = this.country;
        data.searchByZip = this.searchByZip;
        data.zipLength = this.zipLength;
        data.userLocationButton = MapSVG.parseBoolean(this.userLocationButton);
        return data;
    }
    destroy() {
        if ($().mselect2) {
            var sel = $(this.domElements.main).find('.mapsvg-select2');
            if (sel.length) {
                sel.mselect2('destroy');
            }
        }
    }
    initEditor() {
        super.initEditor();
        this.mayBeAddDistanceRow();
        if ($().mselect2) {
            $(this.domElements.edit).find('select').mselect2();
        }
    }
    setEventHandlers() {
        super.setEventHandlers();
        var _this = this;
        $(this.domElements.edit).on('keyup change paste', '.mapsvg-edit-distance-row input', function () {
            _this.mayBeAddDistanceRow();
        });
        var locations = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('formatted_address'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: MapSVG.urls.ajaxurl + '?action=mapsvg_geocoding&address=%QUERY%&language=' + this.language + (this.country ? '&country=' + this.country : ''),
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
        if (this.searchByZip) {
            $(this.domElements.main).find('.mapsvg-distance-fields').addClass('search-by-zip');
            thContainer.on('change keyup', function () {
                if ($(this).val().toString().length === _this.zipLength) {
                    locations.search($(this).val(), null, function (data) {
                        if (data && data[0]) {
                            var latlng = data[0].geometry.location;
                            _this.inputs.latlng.value = latlng.lat + ',' + latlng.lng;
                            _this.value.latlng = latlng.lat + ',' + latlng.lng;
                            _this.events.trigger('changed');
                        }
                    });
                }
            });
        }
        else {
            var tH = thContainer.typeahead({ minLength: 3 }, {
                name: 'mapsvg-addresses',
                display: 'formatted_address',
                source: locations,
                limit: 5
            });
            $(this.domElements.main).find('.mapsvg-distance-fields').removeClass('search-by-zip');
        }
        if (_this.userLocationButton) {
            var userLocationButton = $(this.domElements.main).find('.user-location-button');
            userLocationButton.on('click', function () {
                _this.formBuilder.mapsvg.showUserLocation(function (location) {
                    locations.search(location.lat + ',' + location.lng, null, function (data) {
                        if (data && data[0]) {
                            thContainer.val(data[0].formatted_address);
                        }
                        else {
                            thContainer.val(location.lat + ',' + location.lng);
                        }
                    });
                    _this.inputs.latlng.value = location.lat + ',' + location.lng;
                    _this.events.trigger('changed');
                });
            });
        }
        thContainer.on('change keyup', function () {
            if ($(this).val() === '') {
                _this.inputs.latlng.value = '';
                _this.events.trigger('changed');
            }
        });
        thContainer.on('typeahead:select', function (ev, item) {
            let address;
            address = {};
            address.formatted = item.formatted_address;
            var latlng = item.geometry.location;
            _this.inputs.latlng.value = latlng.lat + ',' + latlng.lng;
            _this.events.trigger('changed');
            thContainer.blur();
        });
        $(this.inputs.latlng).on('change', function () {
            _this.value.latlng = this.value;
            _this.events.trigger('changed');
        });
        $(this.inputs.length).on('change', function () {
            _this.value.length = this.value;
            _this.events.trigger('changed');
        });
    }
    addSelect2() {
        if ($().mselect2) {
            $(this.views.element).find('select').mselect2().on('select2:focus', function () {
                $(this).mselect2('open');
            });
        }
    }
    mayBeAddDistanceRow() {
        var _this = this;
        let editDistanceRow = $($('#mapsvg-edit-distance-row').html());
        var z = $(_this.domElements.edit).find('.mapsvg-edit-distance-row:last-child input');
        if (z && z.last() && z.last().val() && z.last().val().toString().trim().length) {
            var newRow = editDistanceRow.clone();
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
}
//# sourceMappingURL=DistanceFormElement.js.map