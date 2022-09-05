import { MapSVG } from "../Core/globals.js";
import { tinycolor } from "../Vendor/tinycolor.js";
import { MapObject } from "../MapObject/MapObject.js";
import { ViewBox } from "../Map/MapOptionsInterface.js";
const $ = jQuery;
export class Region extends MapObject {
    constructor(element, mapsvg) {
        super(element, mapsvg);
        this.id = this.element.getAttribute('id');
        if (this.id && this.mapsvg.options.regionPrefix) {
            this.setId(this.id.replace(this.mapsvg.options.regionPrefix, ''));
        }
        this.id_no_spaces = this.id.replace(/\s/g, '_');
        this.element.setAttribute('class', (this.element.className || '') + ' mapsvg-region');
        this.setStyleInitial();
        var regionOptions = this.mapsvg.options.regions && this.mapsvg.options.regions[this.id] ? this.mapsvg.options.regions[this.id] : null;
        this.disabled = this.getDisabledState();
        this.disabled && this.attr('class', this.attr('class') + ' mapsvg-disabled');
        this.default_attr = {};
        this.selected_attr = {};
        this.hover_attr = {};
        var selected = false;
        if (regionOptions && regionOptions.selected) {
            selected = true;
            delete regionOptions.selected;
        }
        regionOptions && this.update(regionOptions);
        this.setFill();
        if (selected) {
            this.setSelected();
        }
        this.saveState();
    }
    ;
    adjustStroke(scale) {
        $(this.element).css({ 'stroke-width': this.style['stroke-width'] / scale });
    }
    setStyleInitial() {
        this.style = { fill: this.getComputedStyle('fill') };
        this.style.stroke = this.getComputedStyle('stroke') || '';
        var w;
        w = this.getComputedStyle('stroke-width');
        w = w ? w.replace('px', '') : '1';
        w = w == "1" ? 1.2 : parseFloat(w);
        this.style['stroke-width'] = w;
    }
    ;
    saveState() {
        this.initialState = JSON.stringify(this.getOptions());
    }
    ;
    getBBox() {
        var _bbox = this.element.getBBox();
        let bbox = new ViewBox(_bbox.x, _bbox.y, _bbox.width, _bbox.height);
        var matrix = this.element.getTransformToElement(this.mapsvg.containers.svg);
        var x2 = bbox.x + bbox.width;
        var y2 = bbox.y + bbox.height;
        var position = this.mapsvg.containers.svg.createSVGPoint();
        position.x = bbox.x;
        position.y = bbox.y;
        position = position.matrixTransform(matrix);
        bbox.x = position.x;
        bbox.y = position.y;
        position.x = x2;
        position.y = y2;
        position = position.matrixTransform(matrix);
        bbox.width = position.x - bbox.x;
        bbox.height = position.y - bbox.y;
        return bbox;
    }
    ;
    changed() {
        return JSON.stringify(this.getOptions()) != this.initialState;
    }
    ;
    edit() {
        this.elemOriginal = $(this.element).clone()[0];
    }
    ;
    editCommit() {
        this.elemOriginal = null;
    }
    ;
    editCancel() {
        this.mapsvg.containers.svg.appendChild(this.elemOriginal);
        this.element = this.elemOriginal;
        this.elemOriginal = null;
    }
    ;
    getOptions(forTemplate) {
        let o;
        o = {
            id: this.id,
            id_no_spaces: this.id_no_spaces,
            title: this.title,
            fill: this.mapsvg.options.regions[this.id] && this.mapsvg.options.regions[this.id].fill,
            data: this.data,
            choroplethValue: this.choroplethValue
        };
        if (forTemplate) {
            o.disabled = this.disabled;
            o.dataCounter = (this.data && this.data.length) || 0;
        }
        for (var key in o) {
            if (typeof o[key] === 'undefined') {
                delete o[key];
            }
        }
        if (this.customAttrs) {
            var that = this;
            this.customAttrs.forEach(function (attr) {
                o[attr] = that[attr];
            });
        }
        return o;
    }
    ;
    forTemplate() {
        var data = {
            id: this.id,
            title: this.title,
            objects: this.objects,
            data: this.data
        };
        for (var key in this.data) {
            if (key != 'title' && key != 'id')
                data[key] = this.data[key];
        }
        return data;
    }
    ;
    getData() {
        return this.forTemplate();
    }
    update(options) {
        for (var key in options) {
            var setter = 'set' + MapSVG.ucfirst(key);
            if (setter in this)
                this[setter](options[key]);
            else {
                this[key] = options[key];
                this.customAttrs = this.customAttrs || [];
                this.customAttrs.push(key);
            }
        }
    }
    ;
    setTitle(title) {
        if (title) {
            this.title = title;
        }
        this.element.setAttribute('title', this.title);
    }
    ;
    setStyle(style) {
        $.extend(true, this.style, style);
        this.setFill();
    }
    ;
    getChoroplethColor() {
        let regionValue = parseFloat(this.data[this.mapsvg.options.choropleth.sourceField]);
        let segments = this.mapsvg.options.choropleth.segments;
        let currentSegment;
        segments.forEach(function (segment) {
            if (regionValue >= segment.min && regionValue <= segment.max) {
                currentSegment = segment;
            }
        });
        let w = currentSegment.maxAdjusted === 0 ? 0 : (regionValue - currentSegment.min) / currentSegment.maxAdjusted;
        return {
            r: Math.round(currentSegment.colors.diffRGB.r * w + currentSegment.colors.lowRGB.r),
            g: Math.round(currentSegment.colors.diffRGB.g * w + currentSegment.colors.lowRGB.g),
            b: Math.round(currentSegment.colors.diffRGB.b * w + currentSegment.colors.lowRGB.b),
            a: (currentSegment.colors.diffRGB.a * w + currentSegment.colors.lowRGB.a).toFixed(2)
        };
    }
    ;
    getBubbleSize() {
        let bubbleSize;
        if (this.data[this.mapsvg.options.choropleth.sourceField]) {
            let maxBubbleSize = this.mapsvg.options.choropleth.bubbleSize.max, minBubbleSize = this.mapsvg.options.choropleth.bubbleSize.min, maxSourceFieldvalue = this.mapsvg.options.choropleth.segments[this.mapsvg.options.choropleth.segments.length - 1].max, minSourceFieldvalue = this.mapsvg.options.choropleth.segments[0].min, sourceFieldvalue = parseFloat(this.data[this.mapsvg.options.choropleth.sourceField]);
            bubbleSize = ((sourceFieldvalue - minSourceFieldvalue) * (maxBubbleSize - minBubbleSize) / (maxSourceFieldvalue - minSourceFieldvalue)) + Number(minBubbleSize);
        }
        else {
            bubbleSize = false;
        }
        return bubbleSize;
    }
    setFill(fill) {
        if (this.mapsvg.options.colorsIgnore) {
            $(this.element).css(this.style);
            return;
        }
        if (fill) {
            var regions = {};
            regions[this.id] = { fill: fill };
            $.extend(true, this.mapsvg.options, { regions: regions });
        }
        else if (!fill && fill !== undefined && this.mapsvg.options.regions && this.mapsvg.options.regions[this.id] && this.mapsvg.options.regions[this.id].fill) {
            delete this.mapsvg.options.regions[this.id].fill;
        }
        if (this.mapsvg.options.choropleth.on
            && this.mapsvg.options.choropleth.source === 'regions'
            && this.mapsvg.options.choropleth.sourceField
            && this.mapsvg.options.choropleth.bubbleMode === false
            && this.data
            && typeof this.data[this.mapsvg.options.choropleth.sourceField] !== 'undefined'
            && this.data[this.mapsvg.options.choropleth.sourceField] !== '') {
            var rgb = this.getChoroplethColor();
            this.default_attr['fill'] = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + rgb.a + ')';
        }
        else if (this.status !== undefined && this.mapsvg.regions && this.mapsvg.regionsRepository.getSchema().getFieldByType('status') && this.mapsvg.regionsRepository.getSchema().getFieldByType('status').optionsDict && this.mapsvg.regionsRepository.getSchema().getFieldByType('status').optionsDict[this.status] && this.mapsvg.regionsRepository.getSchema().getFieldByType('status').optionsDict[this.status].color) {
            this.default_attr['fill'] = this.mapsvg.regionsRepository.getSchema().getFieldByType('status').optionsDict[this.status].color;
        }
        else if (this.mapsvg.options.regions[this.id] && this.mapsvg.options.regions[this.id].fill) {
            this.default_attr['fill'] = this.mapsvg.options.regions[this.id].fill;
        }
        else if (this.mapsvg.options.colors.base) {
            this.default_attr['fill'] = this.mapsvg.options.colors.base;
        }
        else if (this.style.fill != 'none') {
            this.default_attr['fill'] = this.style.fill ? this.style.fill : this.mapsvg.options.colors.baseDefault;
        }
        else {
            this.default_attr['fill'] = 'none';
        }
        if (MapSVG.isNumber(this.mapsvg.options.colors.selected))
            this.selected_attr['fill'] = tinycolor(this.default_attr.fill).lighten(parseFloat('' + this.mapsvg.options.colors.selected)).toRgbString();
        else
            this.selected_attr['fill'] = this.mapsvg.options.colors.selected;
        if (MapSVG.isNumber(this.mapsvg.options.colors.hover))
            this.hover_attr['fill'] = tinycolor(this.default_attr.fill).lighten(parseFloat('' + this.mapsvg.options.colors.hover)).toRgbString();
        else
            this.hover_attr['fill'] = this.mapsvg.options.colors.hover;
        $(this.element).css('fill', this.default_attr['fill']);
        this.fill = this.default_attr['fill'];
        if (this.style.stroke != 'none' && this.mapsvg.options.colors.stroke != undefined) {
            $(this.element).css('stroke', this.mapsvg.options.colors.stroke);
        }
        else {
            var s = this.style.stroke == undefined ? '' : this.style.stroke;
            $(this.element).css('stroke', s);
        }
        if (this.selected)
            this.setSelected();
    }
    ;
    setDisabled(on, skipSetFill) {
        on = on !== undefined ? MapSVG.parseBoolean(on) : this.getDisabledState();
        var prevDisabled = this.disabled;
        this.disabled = on;
        this.attr('class', this.attr('class').replace('mapsvg-disabled', ''));
        if (on) {
            this.attr('class', this.attr('class') + ' mapsvg-disabled');
        }
        if (this.disabled != prevDisabled)
            this.mapsvg.deselectRegion(this);
        !skipSetFill && this.setFill();
    }
    ;
    setStatus(status) {
        var statusOptions = this.mapsvg.options.regionStatuses && this.mapsvg.options.regionStatuses[status];
        if (statusOptions) {
            this.status = status;
            this.data.status = status;
            this.data.status_text = statusOptions.label;
            this.setDisabled(statusOptions.disabled, true);
        }
        else {
            this.status = undefined;
            this.data.status = undefined;
            this.data.status_text = undefined;
            this.setDisabled(false, true);
        }
        this.setFill();
    }
    ;
    setSelected() {
        this.mapsvg.selectRegion(this);
    }
    ;
    setchoroplethValue(val) {
        if ($.isNumeric(val)) {
            if (typeof val === 'string') {
                val = parseFloat(val);
            }
            this.choroplethValue = val;
        }
        else {
            this.choroplethValue = undefined;
        }
    }
    ;
    getDisabledState(asDefault) {
        var opts = this.mapsvg.options.regions[this.id];
        if (!asDefault && opts && opts.disabled !== undefined) {
            return opts.disabled;
        }
        else {
            return this.mapsvg.options.disableAll || this.style.fill === 'none' || this.id == 'labels' || this.id == 'Labels';
        }
    }
    ;
    highlight() {
        $(this.element).css({ 'fill': this.hover_attr.fill });
        $(this.element).addClass('mapsvg-region-hover');
    }
    ;
    unhighlight() {
        $(this.element).css({ 'fill': this.default_attr.fill });
        $(this.element).removeClass('mapsvg-region-hover');
    }
    ;
    select() {
        $(this.element).css({ 'fill': this.selected_attr.fill });
        this.selected = true;
        $(this.element).addClass('mapsvg-region-active');
    }
    ;
    deselect() {
        $(this.element).css({ 'fill': this.default_attr.fill });
        this.selected = false;
        $(this.element).removeClass('mapsvg-region-active');
    }
    ;
    setData(data) {
        this.data = data;
        this.setTitle(data.title);
    }
    ;
    drawBubble() {
        if (this.data) {
            let bubbleId = 'mapsvg-bubble-' + this.id;
            let bubbleValue = parseFloat(this.data[this.mapsvg.options.choropleth.sourceField]);
            if (bubbleValue) {
                if (!this.center) {
                    this.center = this.getCenterSVG();
                }
                let pos = this.mapsvg.convertSVGToPixel(this.center);
                if ($('#' + bubbleId).length === 0) {
                    $(this.mapsvg.containers.scrollpane).append('<div id="' + bubbleId + '" class="mapsvg-bubble mapsvg-region-bubble"></div>');
                }
                let bubble = $('#' + bubbleId);
                let rgb = this.getChoroplethColor();
                let bubbleSize = Number(this.getBubbleSize());
                $(bubble).text(bubbleValue);
                $(bubble).css('transform', 'translate(-50%,-50%) translate(' + pos.x + 'px,' + pos.y + 'px)')
                    .css('background-color', 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + rgb.a + ')')
                    .css('width', bubbleSize + 'px')
                    .css('height', bubbleSize + 'px')
                    .css('lineHeight', (bubbleSize - 2) + 'px');
            }
            else {
                $('#' + bubbleId).remove();
            }
        }
    }
}
//# sourceMappingURL=Region.js.map