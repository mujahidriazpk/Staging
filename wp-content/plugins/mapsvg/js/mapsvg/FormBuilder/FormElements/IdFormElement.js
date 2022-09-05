import { FormElement } from "./FormElement";
export class IdFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
    }
    getData() {
        return { name: 'id', value: this.value };
    }
}
//# sourceMappingURL=IdFormElement.js.map