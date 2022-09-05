import { FormElement } from "./FormElement";
const $ = jQuery;
export class CheckboxesFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        this.db_type = 'text';
        this.checkboxLabel = options.checkboxLabel;
        this.setOptions(options.options);
    }
    setDomElements() {
        super.setDomElements();
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.domElements.main).on('change', 'input', (e) => {
            var a = [];
            $(this.domElements.main).find('input:checked').map((i, el) => { a.push(jQuery(el).attr('name')); });
            this.value = a;
            this.events.trigger('changed', this, [this]);
        });
    }
}
//# sourceMappingURL=CheckboxesFormElement.js.map