import { FormElement } from "./FormElement";
import { MapSVG } from "../../Core/globals";
export class RegionsFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.searchable = MapSVG.parseBoolean(options.searchable);
        this.options = this.formBuilder.getRegionsAsArray();
        this.label = 'Regions';
        this.name = 'regions';
        this.db_type = 'text';
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.select = $(this.domElements.main).find('select')[0];
    }
    getData() {
        let data;
        if (data.regions && typeof data.regions == 'object' && data.regions.length) {
            data.regions = data.regions.map(function (region_id) {
                return { id: region_id, title: this.mapsvg.getRegion(region_id).title };
            });
        }
        return data;
    }
    getSchema() {
        let schema = super.getSchema();
        if (schema.multiselect)
            schema.db_type = 'text';
        var opts = $.extend(true, {}, { options: this.options });
        schema.options = opts.options;
        schema.optionsDict = {};
        schema.options.forEach(function (option) {
            schema.optionsDict[option.id] = option.title || option.id;
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
    destroy() {
        if ($().mselect2) {
            var sel = $(this.domElements.main).find('.mapsvg-select2');
            if (sel.length) {
                sel.mselect2('destroy');
            }
        }
    }
}
//# sourceMappingURL=RegionsFormElement.js.map