import { FormElement } from "./FormElement";
import { MapSVG } from "../../Core/globals";
import { CodeMirror } from "../../../codemirror";
export class TextareaFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.searchType = options.searchType || 'fulltext';
        this.searchable = MapSVG.parseBoolean(options.searchable);
        this.autobr = options.autobr;
        this.html = options.html;
        this.db_type = 'text';
        if (this.html) {
            this.editor = CodeMirror.fromTextArea(this.inputs.textarea, {
                mode: { name: "handlebars", base: "text/html" },
                matchBrackets: true,
                lineNumbers: true
            });
            if (this.formBuilder.admin) {
                this.editor.on('change', this.setTextareaValue);
            }
        }
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.textarea = $(this.domElements.main).find('textarea')[0];
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.inputs.textarea).on('change keyup paste', (e) => {
            this.value = e.target.value;
            this.events.trigger('changed', this, [this]);
        });
    }
    getSchema() {
        let schema = super.getSchema();
        schema.autobr = this.autobr;
        schema.html = this.html;
        return schema;
    }
    getDataForTemplate() {
        let data = super.getDataForTemplate();
        data.html = this.html;
        return data;
    }
    setTextareaValue(codemirror, changeobj) {
        var handler = codemirror.getValue();
        var textarea = $(codemirror.getTextArea());
        textarea.val(handler).trigger('change');
    }
    destroy() {
        var cm = $(this.domElements.main).find('.CodeMirror');
        if (cm.length) {
            cm.empty().remove();
        }
    }
}
//# sourceMappingURL=TextareaFormElement.js.map