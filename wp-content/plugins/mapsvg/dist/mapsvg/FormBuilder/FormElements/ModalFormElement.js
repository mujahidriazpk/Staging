import { FormElement } from "./FormElement";
export class ModalFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.showButtonText = options.showButtonText;
    }
    getSchema() {
        let schema = super.getSchema();
        schema.showButtonText = this.showButtonText;
        return schema;
    }
}
//# sourceMappingURL=ModalFormElement.js.map