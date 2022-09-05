import { FormElement } from "./FormElement";
const $ = jQuery;
export class EmptyFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        this.readonly = true;
    }
}
//# sourceMappingURL=EmptyFormElement.js.map