(function( $ ) {
/**
 * Region class. Contains a reference to an SVG element.
 * @extends MapSVG.MapObject
 * @param {object} jQueryObject - jQuery Object containing SVG element
 * @param {MapSVG.MapOptions} globalOptions - MapSVG options
 * @param {string} regionID - Region ID
 * @param {MapSVG.Map} mapsvg - MapSVG instance
 * @constructor
 */
MapSVG.Region = function (jQueryObject, globalOptions, regionID, mapsvg){
    MapSVG.MapObject.call(this, jQueryObject);

    this.globalOptions = globalOptions;
    this.mapsvg = mapsvg;

    this.id = this.node.attr('id');

    if(this.id && globalOptions.regionPrefix){
        this.setId(this.id.replace(globalOptions.regionPrefix, ''));
    }

    this.id_no_spaces = this.id.replace(' ','_');

    this.title = this.node.attr('title');

    this.node[0].setAttribute('class',(this.node.attr('class')||'')+' mapsvg-region');

    this.setStyleInitial();

    var regionOptions  = globalOptions.regions && globalOptions.regions[this.id] ? globalOptions.regions[this.id] : null;

    this.disabled      = this.getDisabledState();
    this.disabled &&   this.attr('class',this.attr('class')+' mapsvg-disabled');

    this.default_attr  = {};
    this.selected_attr = {};
    this.hover_attr    = {};
    var selected = false;
    if(regionOptions && regionOptions.selected){
        selected = true;
        delete regionOptions.selected;
    }
    regionOptions && this.update(regionOptions);
    this.setFill();
    if(selected) {
        this.setSelected(true);
    }
    this.saveState();
};
MapSVG.extend(MapSVG.Region, MapSVG.MapObject);

/**
 * Sets initial style of a Region, computed from SVG
 * @private
 */
MapSVG.Region.prototype.setStyleInitial = function(){
    this.style = {fill: this.getComputedStyle('fill')};
    this.style.stroke = this.getComputedStyle('stroke') || '';
    var w;
    if(this.node.data('stroke-width')){
        w = this.node.data('stroke-width');
    }else{
        w = this.getComputedStyle('stroke-width');
        w = w ? w.replace('px','') : '1';
        w = w == "1" ? 1.2 : parseFloat(w);
    }
    this.style['stroke-width'] = w;
    this.node.data('stroke-width', w);
};
/**
 * Save state of a Region (all parameters)
 * @private
 */
MapSVG.Region.prototype.saveState = function(){
    this.initialState = JSON.stringify(this.getOptions());
};
/**
 * Returns SVG bounding box of the Region
 * @returns {[number,number,number,number]} - [x,y,width,height]
 */
MapSVG.Region.prototype.getBBox = function(){
    var _data = this.mapsvg.getData();
    var bbox = this.node[0].getBBox();
    bbox = $.extend(true, {}, bbox);

    var matrix = this.node[0].getTransformToElement(this.mapsvg.getData().$svg[0]);
    var x2 = bbox.x+bbox.width;
    var y2 = bbox.y+bbox.height;


    // transform a point using the transformed matrix
    var position = this.mapsvg.getData().$svg[0].createSVGPoint();
    position.x = bbox.x;
    position.y = bbox.y;
    position = position.matrixTransform(matrix);
    bbox.x = position.x;
    bbox.y = position.y;
    // var position = this.mapsvg.getData().$svg[0].createSVGPoint();
    position.x = x2;
    position.y = y2;
    position = position.matrixTransform(matrix);
    bbox.width = position.x - bbox.x;
    bbox.height = position.y - bbox.y;

    return [bbox.x,bbox.y,bbox.width,bbox.height];
};
/**
 * Checks whether the Region was changed from the initial state
 * @returns {boolean}
 * @private
 */
MapSVG.Region.prototype.changed = function(){
    return JSON.stringify(this.getOptions()) != this.initialState;
};
/**
 * Saves a copy of the Region SVG node.
 * Used in Map Editor by "Edit SVG file" mode.
 * @private
 */
MapSVG.Region.prototype.edit = function(){
    this.nodeOriginal = this.node.clone();
};
/**
 * Deletes the copy of the Region SVG node created by .edit() method.
 * Used in Map Editor by "Edit SVG file" mode.
 * @private
 */
MapSVG.Region.prototype.editCommit = function(){
    this.nodeOriginal = null;
};
/**
 * Restores SVG node.
 * Used in Map Editor by "Edit SVG file" mode.
 * @private
 */
MapSVG.Region.prototype.editCancel = function(){
    this.nodeOriginal.appendTo(_this.mapsvg.getData().$svg);
    this.node = this.nodeOriginal;
    this.nodeOriginal = null;
};
/**
 * Returns Region properties
 * @param {bool} forTemplate - adds special properties for use in a template
 * @returns {object}
 */
MapSVG.Region.prototype.getOptions = function(forTemplate){
    var globals = this.globalOptions.regions[this.id];
    var o = {
        id: this.id,
        id_no_spaces: this.id_no_spaces,
        title: this.title,
        // disabled: this.disabled === this.getDisabledState(true) ? undefined : this.disabled,
        // status: this.status === undefined ? null : this.status,
        fill: this.globalOptions.regions[this.id] && this.globalOptions.regions[this.id].fill,
        tooltip: this.tooltip,
        popover: this.popover,
        href: this.href,
        target: this.target,
        data: this.data,
        gaugeValue: this.gaugeValue
    };
    if(forTemplate){
        o.disabled  = this.disabled;
        o.dataCounter = (this.data && this.data.length) || 0;
    }
    $.each(o,function(key,val){
        if(val == undefined){
            delete o[key];
        }
    });
    if(this.customAttrs){
        var that = this;
        this.customAttrs.forEach(function(attr){
            o[attr] = that[attr];
        });
    }
    return o;
};
/**
 * Returns an object with properties of the Region formatted for a template
 * @returns {object}
 */
MapSVG.Region.prototype.forTemplate = function(){
    var data = {
        id: this.id,
        title: this.title,
        objects: this.objects,
        data: this.data
    };
    for(var key in this.data){
        if(key!='title' && key!='id')
            data[key] = this.data[key];
    }

    return data;
};
/**
 * Updates the Region
 * @param {object} options
 *
 * @example
 * var region = mapsvg.getRegion("US-TX");
 * region.update({
 *   fill: "#FF3322"
 * });
 */
MapSVG.Region.prototype.update = function(options){
    for(var key in options){
        // check if there's a setter for a property
        var setter = 'set'+MapSVG.ucfirst(key);
        if (setter in this)
            this[setter](options[key]);
        else{
            this[key] = options[key];
            this.customAttrs = this.customAttrs || [];
            this.customAttrs.push(key);
        }
    }
};
/**
 * Sets ID of the Region
 * @param {string} id
 */
MapSVG.Region.prototype.setId = function(id){
    this.id = id;
    this.node.prop('id', id);
};
/**
 * Sets ID of the Region
 * @param {string} id
 */
MapSVG.Region.prototype.setTitle = function(title){
    this.title = title;
};
/**
 * Sets CSS style of the Region
 * @param {object} style - CSS-format styles
 * @private
 */
MapSVG.Region.prototype.setStyle = function(style){
    $.extend(true, this.style, style);
    this.setFill();
};
/**
 * Returns color of the Region for choropleth map
 * @returns {{r: number, g: number, b: number, a: number}}
 */
MapSVG.Region.prototype.getChoroplethColor = function(){
    var o = this.globalOptions.gauge;
    var w = o.maxAdjusted === 0 ? 0 : (parseFloat(this.data[this.globalOptions.regionChoroplethField]) - o.min) / o.maxAdjusted;

    return {
        r: Math.round(o.colors.diffRGB.r * w + o.colors.lowRGB.r),
        g: Math.round(o.colors.diffRGB.g * w + o.colors.lowRGB.g),
        b: Math.round(o.colors.diffRGB.b * w + o.colors.lowRGB.b),
        a: (o.colors.diffRGB.a * w + o.colors.lowRGB.a).toFixed(2)
    };
};
/**
 * Sets fill color of the Region
 * @param {string} fill - color in a CSS format
 * @example
 * region.setFill("#FF2233");
 * region.setFill("rgba(255,255,100,0.5");
 */
MapSVG.Region.prototype.setFill = function(fill){

    var _this = this;

    if(this.globalOptions.colorsIgnore){
        this.node.css(this.style);
        return;
    }

    if(fill){
        var regions = {};
        regions[this.id] = {fill: fill};
        $.extend(true, this.globalOptions, {regions: regions});
    }else if(!fill && fill!==undefined && this.globalOptions.regions && this.globalOptions.regions[this.id] && this.globalOptions.regions[this.id].fill){
        delete this.globalOptions.regions[this.id].fill;
    }

    // Priority: gauge > status > options.fill > disabled > base > svg
    if(this.globalOptions.gauge.on && this.data && (typeof this.data[this.globalOptions.regionChoroplethField] !== 'undefined' &&  this.data[this.globalOptions.regionChoroplethField] !== '' )){
        var rgb = this.getChoroplethColor();
        this.default_attr['fill'] = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + rgb.a+')';

    }else if(this.status!==undefined && this.mapsvg.regionsDatabase && this.mapsvg.regionsDatabase.getSchemaFieldByType('status') && this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict && this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict[this.status] && this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict[this.status].color){
        this.default_attr['fill'] = this.mapsvg.regionsDatabase.getSchemaFieldByType('status').optionsDict[this.status].color;

    }else if(this.globalOptions.regions[this.id] && this.globalOptions.regions[this.id].fill) {
        this.default_attr['fill'] = this.globalOptions.regions[this.id].fill;

        // }else if(this.disabled && this.globalOptions.colors.disabled){
        //     this.default_attr['fill'] = this.globalOptions.colors.disabled;

    }else if(this.globalOptions.colors.base){
        this.default_attr['fill'] = this.globalOptions.colors.base;

    }else if(this.style.fill!='none'){
        this.default_attr['fill'] = this.style.fill ? this.style.fill : this.globalOptions.colors.baseDefault;

    }else{
        this.default_attr['fill'] = 'none';
    }


    if(MapSVG.isNumber(this.globalOptions.colors.selected))
        this.selected_attr['fill'] = MapSVG.tinycolor(this.default_attr.fill).lighten(parseFloat(this.globalOptions.colors.selected)).toRgbString();
    else
        this.selected_attr['fill'] = this.globalOptions.colors.selected;

    if(MapSVG.isNumber(this.globalOptions.colors.hover))
        this.hover_attr['fill'] = MapSVG.tinycolor(this.default_attr.fill).lighten(parseFloat(this.globalOptions.colors.hover)).toRgbString();
    else
        this.hover_attr['fill'] = this.globalOptions.colors.hover;


    this.node.css('fill',this.default_attr['fill']);
    this.fill = this.default_attr['fill'];

    if(this.style.stroke!='none' && this.globalOptions.colors.stroke != undefined){
        this.node.css('stroke',this.globalOptions.colors.stroke);
    }else{
        var s = this.style.stroke == undefined ? '' : this.style.stroke;
        this.node.css('stroke', s);
    }

    if(this.selected)
        this.setSelected();

};
/**
 * Disables the Region.
 * @param {bool} on - true/false = disable/enable
 * @param {bool} skipSetFill - If false, color of the Region will not be changed
 */
MapSVG.Region.prototype.setDisabled = function(on, skipSetFill){
    on = on !== undefined ? MapSVG.parseBoolean(on) : this.getDisabledState(); // get default disabled state if undefined
    var prevDisabled = this.disabled;
    this.disabled = on;
    this.attr('class',this.attr('class').replace('mapsvg-disabled',''))
    if(on){
        this.attr('class',this.attr('class')+' mapsvg-disabled');
    }
    if(this.disabled != prevDisabled)
        this.mapsvg.deselectRegion(this);
    !skipSetFill && this.setFill();
};
/**
 * Sets status of the Region.
 * Takes the list of statuses from global MapSVG options.
 * @param {number} status
 */
MapSVG.Region.prototype.setStatus = function(status){
    var statusOptions;
    if(statusOptions = this.globalOptions.regionStatuses && this.globalOptions.regionStatuses[status]){
        this.status = status;
        this.data.status = status;
        this.setDisabled(statusOptions.disabled, true);
    }else{
        this.status = undefined;
        this.data.status = undefined;
        this.setDisabled(false, true);
    }
    this.setFill();
};
/**
 * Selects the Region.
 */
MapSVG.Region.prototype.setSelected = function(){
    this.mapsvg.selectRegion(this);
};
/**
 * Set Region choropleth value. Used to calculate color of the Region.
 * @param (number} val
 */
MapSVG.Region.prototype.setGaugeValue = function(val){
    this.gaugeValue = $.isNumeric(val) ? parseFloat(val) : undefined;
};
/**
 * Checks if Region should be disabled
 * @param {bool} asDefault
 * @returns {*}
 */
MapSVG.Region.prototype.getDisabledState = function(asDefault){
    var opts = this.globalOptions.regions[this.id];
    if(!asDefault && opts && opts.disabled !== undefined){
        return opts.disabled;
    }else if(
        this.globalOptions.disableAll || this.style.fill == 'none' || this.id == 'labels' || this.id == 'Labels'
    ){
        return true;
    }else{
        return false;
    }
};
/**
 * Highlight the Region.
 * Used on mouseover.
 */
MapSVG.Region.prototype.highlight = function(){
    this.node.css({'fill' : this.hover_attr.fill});
    this.node.addClass('mapsvg-region-hover');
};
/**
 * Unhighlight the Region.
 * Used on mouseout.
 */
MapSVG.Region.prototype.unhighlight = function(){
    this.node.css({'fill' : this.default_attr.fill});
    this.node.removeClass('mapsvg-region-hover');
};
/**
 * Select the Region.
 */
MapSVG.Region.prototype.select = function(){
    this.node.css({'fill' : this.selected_attr.fill});
    this.selected = true;
    this.node.addClass('mapsvg-region-active');
};
/**
 * Deselect the Region.
 */
MapSVG.Region.prototype.deselect = function(){
    this.node.css({'fill' : this.default_attr.fill});
    this.selected = false;
    this.node.removeClass('mapsvg-region-active');
};

})( jQuery );