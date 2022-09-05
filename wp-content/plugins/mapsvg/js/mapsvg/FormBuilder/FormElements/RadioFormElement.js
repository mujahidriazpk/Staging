import { FormElement } from "./FormElement";
const $ = jQuery;
export class RadioFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        this.setOptions(options.options);
    }
    setDomElements() {
        super.setDomElements();
        this.radiosjQueryObject = $(this.domElements.main).find('input[type="radio"]');
    }
    setEventHandlers() {
        super.setEventHandlers();
        this.radiosjQueryObject.on('change', (e) => {
            this.value = e.target.value;
            this.events.trigger('changed', this, [this]);
        });
    }
}
//# sourceMappingURL=RadioFormElement.js.map