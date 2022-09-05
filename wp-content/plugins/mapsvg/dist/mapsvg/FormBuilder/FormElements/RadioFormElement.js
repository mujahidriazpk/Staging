import { FormElement } from "./FormElement";
export class RadioFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.setOptions(options.options);
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.radio = $(this.domElements.main).find('input[type="radio"]')[0];
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.inputs.radio).on('change', (e) => {
            this.value = e.target.value;
            this.events.trigger('changed', this, [this]);
        });
    }
}
//# sourceMappingURL=RadioFormElement.js.map