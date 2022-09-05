import { FormElement } from "./FormElement";
const $ = jQuery;
export class ModalFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        this.showButtonText = options.showButtonText;
    }
    getSchema() {
        let schema = super.getSchema();
        schema.showButtonText = this.showButtonText;
        return schema;
    }
}
//# sourceMappingURL=ModalFormElement.js.map