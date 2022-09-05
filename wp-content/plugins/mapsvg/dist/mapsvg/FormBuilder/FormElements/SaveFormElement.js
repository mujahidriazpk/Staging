import { FormElement } from "./FormElement";
export class SaveFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
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
            this.events.trigger('close.btn.close');
        });
    }
}
//# sourceMappingURL=SaveFormElement.js.map