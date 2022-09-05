import { FormElement } from "./FormElement";
export class CheckboxesFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
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
        });
    }
}
//# sourceMappingURL=CheckboxesFormElement.js.map