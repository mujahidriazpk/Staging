import { FormElement } from "./FormElement.js";
import { MapSVG } from "../../Core/globals.js";
const $ = jQuery;
export class TextFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        this.searchFallback = MapSVG.parseBoolean(options.searchFallback);
        this.width = this.formBuilder.filtersHide && !this.formBuilder.modal ? null : (options.width || '100%');
        this.db_type = 'varchar(255)';
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.text = $(this.domElements.main).find('input[type="text"]')[0];
    }
    getSchema() {
        let schema = super.getSchema();
        schema.searchType = this.searchType;
        return schema;
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.inputs.text).on('change keyup paste', (e) => {
            this.value = e.target.value;
            this.events.trigger('changed', this, [this]);
        });
    }
}
//# sourceMappingURL=TextFormElement.js.map