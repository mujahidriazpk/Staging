import { FormElement } from "./FormElement";
export class IdFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
    }
    getData() {
        return this.value;
    }
}
//# sourceMappingURL=IdFormElement.js.map