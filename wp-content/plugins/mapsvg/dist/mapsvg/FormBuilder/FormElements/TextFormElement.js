import { FormElement } from "./FormElement";
import { MapSVG } from "../../Core/globals";
export class TextFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.searchFallback = MapSVG.parseBoolean(options.searchFallback);
        this.placeholder = options.placeholder || 'Search';
        this.noResultsText = options.noResultsText || 'No results found';
        this.width = this.formBuilder.filtersHide && !this.formBuilder.modal ? null : (options.width || '100%');
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