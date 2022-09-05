import * as FormElementTypes from './index.js';
export class FormElementFactory {
    constructor(options) {
        this.mapsvg = options.mapsvg;
        this.editMode = options.editMode;
        this.filtersMode = options.filtersMode;
        this.namespace = options.namespace;
        this.mediaUploader = options.mediaUploader;
    }
    create(options) {
        let types = {
            'select': FormElementTypes.SelectFormElement,
            'radio': FormElementTypes.RadioFormElement,
            'checkbox': FormElementTypes.CheckboxFormElement,
            'regions': FormElementTypes.RegionsFormElement,
            'text': FormElementTypes.TextFormElement,
            'textarea': FormElementTypes.TextareaFormElement,
            'images': FormElementTypes.ImagesFormElement,
            'post': FormElementTypes.PostFormElement,
            'save': FormElementTypes.SaveFormElement,
            'location': FormElementTypes.LocationFormElement,
            'empty': FormElementTypes.EmptyFormElement,
            'date': FormElementTypes.DateFormElement,
            'id': FormElementTypes.IdFormElement,
        };
        if (options.type === 'images' || options.type === 'location') {
            options.mediaUploader = this.mediaUploader;
        }
        $.extend(true, options, { external: this.getExtraParams() });
        return new types[options.type](options);
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
        let regions = [];
        this.mapsvg.regions.forEach(region => {
            regions.push({ id: region.id, title: region.title });
        });
        return {
            databaseFields: databaseFields,
            databaseFieldsFilterableShort: databaseFieldsFilterableShort,
            regionFields: regionFields,
            regions: regions,
            mapIsGeo: this.mapsvg.isGeo()
        };
    }
}
//# sourceMappingURL=FormElementFactory.js.map