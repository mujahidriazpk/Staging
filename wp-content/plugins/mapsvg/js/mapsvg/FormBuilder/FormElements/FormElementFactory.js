import * as FormElementTypes from './index.js';
import { ArrayIndexed } from "../../Core/ArrayIndexed";
const $ = jQuery;
export class FormElementFactory {
    constructor(options) {
        this.mapsvg = options.mapsvg;
        this.editMode = options.editMode;
        this.filtersMode = options.filtersMode;
        this.namespace = options.namespace;
        this.mediaUploader = options.mediaUploader;
        this.formBuilder = options.formBuilder;
    }
    create(options) {
        let types = {
            'checkbox': FormElementTypes.CheckboxFormElement,
            'checkboxes': FormElementTypes.CheckboxesFormElement,
            'date': FormElementTypes.DateFormElement,
            'distance': FormElementTypes.DistanceFormElement,
            'empty': FormElementTypes.EmptyFormElement,
            'id': FormElementTypes.IdFormElement,
            'image': FormElementTypes.ImagesFormElement,
            'location': FormElementTypes.LocationFormElement,
            'modal': FormElementTypes.ModalFormElement,
            'post': FormElementTypes.PostFormElement,
            'radio': FormElementTypes.RadioFormElement,
            'region': FormElementTypes.RegionsFormElement,
            'save': FormElementTypes.SaveFormElement,
            'search': FormElementTypes.SearchFormElement,
            'select': FormElementTypes.SelectFormElement,
            'status': FormElementTypes.StatusFormElement,
            'text': FormElementTypes.TextFormElement,
            'textarea': FormElementTypes.TextareaFormElement,
        };
        var formElement = new types[options.type](options, this.formBuilder, this.getExtraParams());
        formElement.init();
        return formElement;
    }
    getExtraParams() {
        let databaseFields = [];
        this.mapsvg.objectsRepository.getSchema().getFields().forEach(function (obj) {
            if (obj.type == 'text' || obj.type == 'region' || obj.type == 'textarea' || obj.type == 'post' || obj.type == 'select' || obj.type == 'radio' || obj.type == 'checkbox') {
                if (obj.type == 'post') {
                    databaseFields.push('Object.post.post_title');
                }
                else {
                    databaseFields.push('Object.' + obj.name);
                }
            }
        });
        let databaseFieldsFilterableShort = [];
        databaseFieldsFilterableShort = this.mapsvg.objectsRepository.getSchema().getFieldsAsArray().filter(function (obj) {
            return (obj.type == 'select' || obj.type == 'radio' || obj.type == 'region');
        }).map(function (obj) {
            return obj.name;
        });
        let regionFields = this.mapsvg.regionsRepository.getSchema().getFieldsAsArray().map(function (obj) {
            if (obj.type == 'status' || obj.type == 'text' || obj.type == 'textarea' || obj.type == 'post' || obj.type == 'select' || obj.type == 'radio' || obj.type == 'checkbox') {
                if (obj.type == 'post') {
                    return 'Region.post.post_title';
                }
                else {
                    return 'Region.' + obj.name;
                }
            }
        });
        let regions = new ArrayIndexed('id');
        this.mapsvg.regions.forEach(region => {
            regions.push({ id: region.id, title: region.title });
        });
        return {
            databaseFields: databaseFields,
            databaseFieldsFilterableShort: databaseFieldsFilterableShort,
            regionFields: regionFields,
            regions: regions,
            mapIsGeo: this.mapsvg.isGeo(),
            mediaUploader: this.mediaUploader,
            filtersMode: this.filtersMode
        };
    }
}
//# sourceMappingURL=FormElementFactory.js.map