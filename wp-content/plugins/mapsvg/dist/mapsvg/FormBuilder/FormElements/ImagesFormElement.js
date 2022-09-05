import { FormElement } from "./FormElement";
import { MapSVG } from "../../Core/globals";
import Sortable from "../../../sortable.min.js";
export class ImagesFormElement extends FormElement {
    constructor(options, formBuilder) {
        super(options, formBuilder);
        this.searchType = options.searchType || 'fulltext';
        this.mediaUploader = MapSVG.mediaUploader;
        this.button_text = options.button_text || 'Browse...';
        this.db_type = 'text';
        this.label = options.label || 'Images';
        this.name = options.name || 'images';
        this.images = this.value || [];
        this.value = JSON.stringify(this.value);
        this.redrawImages();
    }
    setDomElements() {
        super.setDomElements();
    }
    getData() {
        this.updateData();
        return this.images;
    }
    getSchema() {
        let schema = super.getSchema();
        schema.button_text = this.button_text;
        return schema;
    }
    updateData() {
        let newListOfImages = [];
        $(this.domElements.main).find('.mapsvg-thumbnail-wrap').each(function (index, el) {
            var imageData = $(el).data('image');
            newListOfImages.push(imageData);
        });
        this.images = newListOfImages;
        this.value = JSON.stringify(this.images);
        $(this.domElements.main).find('input').val(this.value);
    }
    setEventHandlers() {
        super.setEventHandlers();
        let _this = this;
        var imageDOM = $(this.domElements.main).find('.mapsvg-data-images');
        this.mediaUploader.on('select', function () {
            var attachments = _this.mediaUploader.state().get('selection').toJSON();
            attachments.forEach(function (img) {
                let image;
                image = { sizes: {} };
                for (var type in img.sizes) {
                    image[type] = img.sizes[type].url.replace('http://', '//').replace('https://', '//');
                    image.sizes[type] = { width: img.sizes[type].width, height: img.sizes[type].height };
                }
                if (!image.thumbnail) {
                    image.thumbnail = image.full;
                    image.sizes.thumbnail = { width: img.sizes.full.width, height: img.sizes.full.height };
                }
                if (!image.medium) {
                    image.medium = image.full;
                    image.sizes.medium = { width: img.sizes.full.width, height: img.sizes.full.height };
                }
                image.caption = img.caption;
                image.description = img.description;
                _this.images.push(image);
            });
            _this.value = JSON.stringify(this.images);
            _this.redrawImages();
        });
        $(_this.domElements.main).on('click', '.mapsvg-upload-image', function (e) {
            e.preventDefault();
            _this.mediaUploader.open();
        });
        $(_this.domElements.main).on('click', '.mapsvg-image-delete', function (e) {
            e.preventDefault();
            $(this).closest('.mapsvg-thumbnail-wrap').remove();
            _this.updateData();
        });
        _this.sortable = new Sortable(imageDOM[0], {
            animation: 150,
            onStart: function () {
                $(_this.domElements.main).addClass('sorting');
            },
            onEnd: function (evt) {
                _this.images = [];
                $(_this.domElements.main).find('img').each(function (i, image) {
                    _this.images.push($(image).data('image'));
                });
                this.value = JSON.stringify(_this.images);
                $(_this.domElements.main).find('input').val(this.value);
                $(_this.domElements.main).removeClass('sorting');
            }
        });
    }
    redrawImages() {
        var _this = this;
        var imageDOM = $(this.domElements.main).find('.mapsvg-data-images');
        imageDOM.empty();
        this.images && this.images.forEach(function (image) {
            var img = $('<img class="mapsvg-data-thumbnail" />').attr('src', image.thumbnail).data('image', image);
            var imgContainer = $('<div class="mapsvg-thumbnail-wrap"></div>').data('image', image);
            imgContainer.append(img);
            imgContainer.append('<i class="fa fa-times  mapsvg-image-delete"></i>');
            imageDOM.append(imgContainer);
        });
        $(this.domElements.main).find('input').val(this.value);
    }
    ;
}
//# sourceMappingURL=ImagesFormElement.js.map