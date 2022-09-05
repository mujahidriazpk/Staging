import { FormElement } from "./FormElement";
import { MapSVG } from "../../Core/globals";
export class SelectFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.searchable = MapSVG.parseBoolean(options.searchable);
        this.setOptions(options.options);
        this.multiselect = MapSVG.parseBoolean(options.multiselect);
        this.optionsGrouped = options.optionsGrouped;
        this.db_type = this.multiselect ? 'text' : 'varchar(255)';
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.select = $(this.domElements.main).find('select')[0];
    }
    getSchema() {
        let schema = super.getSchema();
        schema.multiselect = MapSVG.parseBoolean(this.multiselect);
        if (schema.multiselect)
            schema.db_type = 'text';
        schema.optionsGrouped = this.optionsGrouped;
        var opts = $.extend(true, {}, { options: this.options });
        schema.options = opts.options;
        schema.optionsDict = {};
        schema.options.forEach(function (option) {
            schema.optionsDict[option.value] = option.label;
        });
        return schema;
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.inputs.select).on('change', (e) => {
            this.value = e.target.value;
            this.events.trigger('changed', this, [this]);
        });
    }
    addSelect2() {
        if ($().mselect2) {
            let select2Options;
            select2Options = {};
            if (this.formBuilder.filtersMode && this.type == 'select') {
                select2Options.placeholder = this.placeholder;
                if (!this.multiselect) {
                    select2Options.allowClear = true;
                }
            }
            $(this.domElements.main).find('select').css({ width: '100%', display: 'block' })
                .mselect2(select2Options)
                .on('select2:focus', function () {
                $(this).mselect2('open');
            });
            $(this.domElements.main).find('.select2-selection--multiple .select2-search__field').css('width', '100%');
        }
    }
}
//# sourceMappingURL=SelectFormElement.js.map