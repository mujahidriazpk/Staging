import { FormElement } from "./FormElement.js";
import { MapSVG } from "../../Core/globals.js";
const $ = jQuery;
export class RegionsFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        this.searchable = MapSVG.parseBoolean(options.searchable);
        this.options = this.formBuilder.getRegionsList();
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
        let table = this.formBuilder.mapsvg.regionsRepository.getSchema().name;
        data = $(this.inputs.select).val() || [];
        let data2 = {};
        if (data && data.length > 0) {
            data = data.map((rId) => {
                let region = this.external.regions.findById(rId);
                return { id: region.id, title: region.title };
            });
            data2[table] = data;
        }
        return { name: 'regions', value: data2 };
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