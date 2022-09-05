import { FormElement } from "./FormElement.js";
const $ = jQuery;
export class SaveFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        this.readonly = true;
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.btnSave = $(this.domElements.main).find('.btn-save')[0];
        this.inputs.btnClose = $(this.domElements.main).find('.btn-close')[0];
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.inputs.btnSave).on('click', () => {
            this.events.trigger('click.btn.save');
        });
        $(this.inputs.btnClose).on('click', () => {
            this.events.trigger('click.btn.close');
        });
    }
}
//# sourceMappingURL=SaveFormElement.js.map