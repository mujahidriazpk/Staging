import { FormElement } from "./FormElement";
import { MapSVG } from "../../Core/globals";
export class StatusFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.label = options.label || 'Status';
        this.name = 'status';
        this.setOptions(options.options);
        if ($().colorpicker) {
            $(this.domElements.main).find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
                var input = $(this).find('input');
                if (input.val() == '')
                    $(this).find('i').css({ 'background-color': '' });
            });
            this.domElements.edit && $(this.domElements.edit).find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
                var input = $(this).find('input');
                if (input.val() == '')
                    $(this).find('i').css({ 'background-color': '' });
            });
        }
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.select = $(this.domElements.main).find('select')[0];
    }
    destroy() {
        if ($().mselect2) {
            var sel = $(this.domElements.main).find('.mapsvg-select2');
            if (sel.length) {
                sel.mselect2('destroy');
            }
        }
    }
    setEditorEventHandlers() {
        super.setEditorEventHandlers();
        var _this = this;
        $(this.domElements.edit).on('keyup change paste', '.mapsvg-edit-status-row input', function () {
            _this.mayBeAddStatusRow();
        });
    }
    initEditor() {
        super.initEditor();
        var _this = this;
        $(_this.domElements.edit).find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
            var input = $(this).find('input');
            var index = $(this).closest('tr').index();
            if (input.val() == '')
                $(this).find('i').css({ 'background-color': '' });
            _this.options[index] = _this.options[index] || { label: '', value: '', color: '', disabled: false };
            _this.options[index]['color'] = input.val();
        });
        _this.mayBeAddStatusRow();
    }
    mayBeAddStatusRow() {
        var _this = this;
        let editStatusRow = $($('#mapsvg-edit-status-row').html());
        var z = $(_this.domElements.edit).find('.mapsvg-edit-status-label:last-child');
        if (z && z.last() && z.last().val() && (z.last().val() + '').trim().length) {
            var newRow = editStatusRow.clone();
            newRow.insertAfter($(_this.domElements.edit).find('.mapsvg-edit-status-row:last-child'));
            newRow.find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
                var input = $(this).find('input');
                var index = $(this).closest('tr').index();
                if (input.val() == '')
                    $(this).find('i').css({ 'background-color': '' });
                _this.options[index] = _this.options[index] || { label: '', value: '', color: '', disabled: false };
                _this.options[index]['color'] = input.val();
            });
        }
        var rows = $(_this.domElements.edit).find('.mapsvg-edit-status-row');
        var row1 = rows.eq(rows.length - 2);
        var row2 = rows.eq(rows.length - 1);
        if (row1.length && row2.length &&
            !(row1.find('input:eq(0)').val().toString().trim() || row1.find('input:eq(1)').val().toString().trim() || row1.find('input:eq(2)').val().toString().trim())
            &&
                !(row2.find('input:eq(0)').val().toString().trim() || row2.find('input:eq(1)').val().toString().trim() || row2.find('input:eq(2)').val().toString().trim())) {
            row2.remove();
        }
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.inputs.select).on('change keyup paste', (e) => {
            this.value = e.target.value;
            this.events.trigger('changed', this, [this]);
        });
    }
    getSchema() {
        let schema = super.getSchema();
        var opts = $.extend(true, {}, { options: this.options });
        schema.options = opts.options;
        schema.optionsDict = {};
        schema.options.forEach(function (option, index) {
            if (schema.options[index].value === '') {
                schema.options.splice(index, 1);
            }
            else {
                schema.options[index].disabled = MapSVG.parseBoolean(schema.options[index].disabled);
                schema.optionsDict[option.value] = option;
            }
        });
        return schema;
    }
}
//# sourceMappingURL=StatusFormElement.js.map