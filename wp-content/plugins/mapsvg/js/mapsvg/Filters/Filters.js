import { DetailsController } from "../Details/Details.js";
import { FormBuilder } from "../FormBuilder/FormBuilder.js";
const $ = jQuery;
export class FiltersController extends DetailsController {
    constructor(options) {
        super(options);
        this.showButtonText = options.showButtonText;
        this.clearButton = options.clearButton;
        this.clearButtonText = options.clearButtonText;
        this.padding = options.padding;
        this.schema = options.schema;
        this.hideFilters = options.hide;
        this.query = options.query;
    }
    viewDidLoad() {
        super.viewDidLoad();
        var _this = this;
        var filtersController = this;
        this.formBuilder = new FormBuilder({
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
                'changed.search': (value) => {
                    this.query.setSearch(value);
                    _this.events.trigger('changed.search', _this, [value]);
                },
                'cleared': () => {
                    this.query.clearFilters();
                    _this.events.trigger('cleared.filters', _this, []);
                },
                'loaded': (_formBuilder) => {
                    $(_formBuilder.container).find('.mapsvg-form-builder').css({
                        padding: _this.padding
                    });
                    _this.updateScroll();
                    _this.events.trigger('loaded');
                }
            }
        });
    }
    setFiltersCounter() {
        if (this.hideFilters) {
            var filtersCounter = Object.keys(this.query.filters).length;
            var filtersCounterString = filtersCounter === 0 ? '' : filtersCounter.toString();
            this.formBuilder && this.formBuilder.showFiltersButton && $(this.formBuilder.showFiltersButton.domElements.main).find('button').html(this.showButtonText + ' <b>' + filtersCounterString + '</b>');
        }
    }
    setEventHandlers() {
        super.setEventHandlers();
        var _this = this;
        $(this.containers.view).on('click', '.mapsvg-btn-show-filters', function () {
            _this.events.trigger('click.btn.showFilters');
        });
    }
}
//# sourceMappingURL=Filters.js.map