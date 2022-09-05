import { DetailsController } from "../Details/Details";
import { FormBuilder } from "../FormBuilder/FormBuilder";
export class FiltersController extends DetailsController {
    constructor(options) {
        super(options);
        this.showButtonText = options.showButtonText;
        this.clearButton = options.clearButton;
        this.clearButtonText = options.clearButtonText;
        this.schema = options.schema;
        this.hideFilters = options.hide;
        this.repository = options.repository;
        this.query = options.query;
        this._init();
    }
    viewDidLoad() {
        super.viewDidLoad();
        var _this = this;
        var filtersController = this;
        var formBuilder = new FormBuilder({
            container: this.containers.contentView,
            filtersMode: true,
            schema: this.schema,
            modal: this.modal,
            filtersHide: this.hideFilters,
            showButtonText: this.showButtonText,
            clearButton: this.clearButton,
            clearButtonText: this.clearButtonText,
            editMode: false,
            mapsvg: this.mapsvg,
            data: this.query,
            admin: false,
            events: {
                'changed.field': (field, value) => {
                    let filters = {};
                    filters[field] = value;
                    this.query.setFilters(filters);
                    _this.events.trigger('changed.field', _this, [field, value]);
                    _this.events.trigger('changed.fields', _this, [field, value]);
                },
                'cleared': () => {
                    this.query.clearFilters();
                    _this.events.trigger('cleared.filters', _this, []);
                },
                'loaded': (_formBuilder) => {
                    _formBuilder.container.find('.mapsvg-form-builder').css({
                        padding: _this.padding
                    });
                    filtersController.updateScroll();
                    if (_this.hideFilters) {
                        var setFiltersCounter = function () {
                            var filtersCounter = Object.keys(_this.repository.query.filters).length;
                            if (_this.repository.query.filters.search && _this.repository.query.filters.search.length > 0) {
                                filtersCounter--;
                            }
                            var filtersCounterString = filtersCounter === 0 ? '' : filtersCounter.toString();
                            _formBuilder && _formBuilder.showFiltersButton && _formBuilder.showFiltersButton.views.result.find('button').html(_this.showButtonText + ' <b>' + filtersCounterString + '</b>');
                        };
                        setFiltersCounter();
                        _this.repository.events.on('dataLoaded', function () {
                            setFiltersCounter();
                        });
                    }
                    _this.events.trigger('loaded');
                }
            }
        });
    }
    setEventHandlers() {
        super.setEventHandlers();
        var _this = this;
        $(this.containers.view).on('click', '.mapsvg-btn-show-filters', function () {
            _this.events.trigger('click.btn.showFilters');
        });
        var filterDatabase = _this.repository;
        $(this.containers.view).on('change paste keyup', 'select,input[type="radio"],input', function () {
            if ($(this).data('ignoreSelect2Change')) {
                $(this).data('ignoreSelect2Change', false);
                return;
            }
            var filter = {};
            var field = $(this).data('parameter-name');
            if ($(this).attr('data-parameter-name') == "search") {
                return;
            }
            if ($(this).attr('name') === 'distanceAddress' || field == "search") {
                return;
            }
            if ($(this).attr('name') === 'distanceLatLng' || $(this).attr('name') === 'distanceLength') {
            }
            else if ($(this).closest('.mapsvg-checkbox-group').length > 0) {
                filter[field] = [];
                $(this).closest('.mapsvg-checkbox-group').find('input[type="checkbox"]:checked').each(function (i, el) {
                    filter[field].push($(el).val());
                });
            }
            else {
                filter[field] = $(this).val();
            }
            filterDatabase.query.setFilters(filter);
        });
    }
}
//# sourceMappingURL=Filters.js.map