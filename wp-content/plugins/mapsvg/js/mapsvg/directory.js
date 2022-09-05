(function( $ ) {
/**
 * Creates a container with a list of objects near the map
 * @class
 * @extends MapSVG.Controller
 * @param options - List of options
 *
 * @example
 * var directory = mapsvg.controllers.directory;
 *
 *  // Toggle map/list view on mobile devices
 * if(MapSVG.isPhone){
 *   directory.toggle(true); // show directory
 * }
 */
MapSVG.DirectoryController = function(options){
    this.database = options.database;
    this.noPadding = true;
    this.position = options.position;
    this.search = options.search;
    this.filters = options.filters;
    MapSVG.Controller.call(this, options);
};
MapSVG.extend(MapSVG.DirectoryController, MapSVG.Controller);

/**
 * Returns a HTML content for the Directory toolbar
 * @returns {string} HTML content
 */
MapSVG.DirectoryController.prototype.getToolbarTemplate = function(){
    var _this = this;

    var t = '<div class="mapsvg-directory-search-wrap">';
    // t    += '<input class="mapsvg-directory-search" placeholder="{{menu.searchPlaceholder}}" />';

    // if(this.search){loadFiltersController
    //     t    += '<div class="mapsvg-directory-search-wrap-margin" >';
    //     t    += '<input class="mapsvg-directory-search" placeholder="{{options.menu.searchPlaceholder}}" />';
    //     t    += '</div>';
    // }

    t    += '<div class="mapsvg-directory-filter-wrap filter-wrap"></div>';
    t    += '</div>';
    t    += '</div>';

    return t;
};

/**
 * Does all required actions when the view is loaded: adds mobile buttons for mobile devices.
 * @private
 */
MapSVG.DirectoryController.prototype.viewDidLoad = function() {

    var _this = this;
    this.menuBtn = $('<div class="mapsvg-button-menu"><i class="mapsvg-icon-menu"></i> ' + this.mapsvg.getData().options.mobileView.labelList + '</div>');
    this.mapBtn  = $('<div class="mapsvg-button-map"><i class="mapsvg-icon-map"></i> '   + this.mapsvg.getData().options.mobileView.labelMap  + '</div>');

    // Make directory hidden by default on mobiles
    if(MapSVG.isPhone && _this.mapsvg.options.menu.hideOnMobile){
        if(this.mapsvg.getData().options.menu.showFirst == 'map'){
            this.toggle(false);
        }else{
            this.toggle(true);
        }
    }

    this.mobileButtons = $('<div class="mapsvg-mobile-buttons"></div>');
    this.mobileButtons.append(this.menuBtn, this.mapBtn);

    if(this.mapsvg.getData().options.menu.on !== false)
        this.mobileButtons.appendTo(this.mapsvg.getData().$wrapAll);

    this.events && this.events['shown'] && this.events['shown'].call(this.view);
};


MapSVG.DirectoryController.prototype.setEventHandlers = function(){

    var _this = this;
    var _data = _this.mapsvg.getData();

    $(window).on('resize',function(){
        _this.updateTopShift();
    });

    this.menuBtn.on('click', function(){
        _this.toggle(true);
        // if(!$(this).hasClass('active')){
        //     _this.toggle();
        //     $(this).parent().find('div').removeClass('active');
        //     $(this).addClass('active');
        // }
    });
    this.mapBtn.on('click', function(){
        _this.toggle(false);
        _this.mapsvg.redraw();
        // if(!$(this).hasClass('active')){
        //     _this.toggle();
        //     $(this).parent().find('div').removeClass('active');
        //     $(this).addClass('active');
        // }
    });

    this.view.on('click.menu.mapsvg', '.mapsvg-directory-item', function (e) {
        e.preventDefault();

        var objID  = $(this).data('object-id');

        var regions;
        var marker;
        var detailsViewObject;
        var eventObject;

        _this.deselectItems();
        _this.selectItems(objID);

        if(MapSVG.isPhone && _this.mapsvg.getData().options.menu.showMapOnClick){
            _this.toggle(false);
        }


        if(_this.mapsvg.getData().options.menu.source == 'regions'){
            regions = [_this.mapsvg.getRegion(objID)];
            eventObject = regions[0];
            detailsViewObject = regions[0];
        } else {
            detailsViewObject = _this.database.getLoadedObject(objID);
            eventObject = detailsViewObject;
            if(detailsViewObject.regions){
                regions = detailsViewObject.regions.map(function(region){
                    return _this.mapsvg.getRegion(region.id);
                }).filter(function(r){
                    return r !== undefined;
                });
            }
        }

        if(detailsViewObject.location && detailsViewObject.location.marker)
            marker = detailsViewObject.location.marker;

        if(_this.mapsvg.getData().options.actions.directoryItem.click.showDetails){
            _this.mapsvg.loadDetailsView(detailsViewObject);
        }
        var skipPopover;

        if(regions && regions.length > 0) {

            if(_this.mapsvg.getData().options.actions.directoryItem.click.zoom){
                _this.mapsvg.zoomTo(regions, _this.mapsvg.getData().options.actions.directoryItem.click.zoomToLevel);
            }

            if(regions.length > 1){
                _this.mapsvg.setMultiSelect(true);
            }
            regions.forEach(function(region){

                var center = region.getCenter();
                e.clientX = center[0];
                e.clientY = center[1];

                if(_this.mapsvg.getData().options.actions.directoryItem.click.selectRegion){
                    //region.setSelected(true);
                    _this.mapsvg.selectRegion(region, true);
                }
                if(_this.mapsvg.getData().options.actions.directoryItem.click.showRegionPopover){
                    if(_this.mapsvg.getData().options.actions.directoryItem.click.zoom){
                        setTimeout(function(){
                            _this.mapsvg.showPopover(region);
                        },500);
                    }else{
                        _this.mapsvg.showPopover(region);
                    }
                }
                if(_this.mapsvg.getData().options.actions.directoryItem.click.fireRegionOnClick){
                    var events = _this.mapsvg.getData().events;
                    if(events && events['click.region'])
                        events && events['click.region'].call(region, e, _this.mapsvg);
                }
            });
            if(regions.length > 1){
                _this.mapsvg.setMultiSelect(false, false);
            }

        }
        if(marker){
            if(_this.mapsvg.getData().options.actions.directoryItem.click.zoomToMarker){
                _this.mapsvg.zoomTo(marker, _this.mapsvg.getData().options.actions.directoryItem.click.zoomToMarkerLevel);
            }
            if(_this.mapsvg.getData().options.actions.directoryItem.click.showMarkerPopover){
                if(_this.mapsvg.getData().options.actions.directoryItem.click.zoomToMarker){
                    setTimeout(function(){
                        _this.mapsvg.showPopover(detailsViewObject);
                    },500);
                }else{
                    _this.mapsvg.showPopover(detailsViewObject);
                }
            }
            if(_this.mapsvg.getData().options.actions.directoryItem.click.fireMarkerOnClick){
                var events = _this.mapsvg.getData().events;
                if(events && events['click.marker'])
                    events && events['click.marker'].call(marker, e, _this.mapsvg);
            }
            _this.mapsvg.selectMarker(marker);
        }

        _this.events['click'] && _this.events['click'].call($(this), e, eventObject, _this.mapsvg);

        var actions = _this.mapsvg.getData().options.actions;


        if(actions.directoryItem.click.goToLink){
            var linkParts = actions.directoryItem.click.linkField.split('.');
            var url;
            if(linkParts.length > 1){
                var obj = linkParts.shift();
                var attr = '.'+linkParts.join('.');
                if(obj == 'Region'){
                    if(regions[0] && regions[0].data)
                        url = eval('regions[0].data'+attr);
                }else{
                    if(detailsViewObject)
                        url = eval('detailsViewObject'+attr);
                }

                if(url){
                    if(actions.directoryItem.click.newTab){
                        var win = window.open(url, '_blank');
                        win.focus();
                    }else{
                        window.location.href = url;
                    }
                }
            }
        }
        if(actions.directoryItem.click.showAnotherMap){
            if(_data.editMode){
                alert('"Show another map" action is disabled in the preview');
                return true;
            }
            var linkParts = actions.directoryItem.click.showAnotherMapField.split('.');
            var url;
            if(linkParts.length > 1){
                var obj = linkParts.shift();
                var attr = '.'+linkParts.join('.');
                var map_id;
                if(obj == 'Region'){
                    if(regions[0] && regions[0].data)
                        map_id = eval('regions[0].data'+attr);
                }else{
                    if(detailsViewObject)
                        map_id = eval('detailsViewObject'+attr);
                }

                if(map_id){
                    var container = actions.directoryItem.click.showAnotherMapContainerId ? $('#'+actions.directoryItem.click.showAnotherMapContainerId) : _data.$map;
                    jQuery.get(ajaxurl, {action:"mapsvg_get",id: map_id},function(data){
                        if(container.find('svg').length)
                            container.mapSvg().destroy();
                        eval('var options = '+data);
                        container.mapSvg(options);
                    });
                }
            }
        }
    }).on('mouseover.menu.mapsvg',  '.mapsvg-directory-item', function (e) {

        var objID = $(this).data('object-id');
        var regions;
        var detailsViewObject;
        var eventObject;
        var marker;

        if(_this.mapsvg.getData().options.menu.source == 'regions'){
            regions = [_this.mapsvg.getRegion(objID)];
            eventObject = regions[0];
            detailsViewObject = regions[0];
        } else {
            detailsViewObject = _this.database.getLoadedObject(objID);
            eventObject = detailsViewObject;
            if(detailsViewObject.regions){
                regions = detailsViewObject.regions.map(function(region){
                    return _this.mapsvg.getRegion(region.id);
                });
            }
            if(detailsViewObject.location){
                marker = detailsViewObject.location.marker;
            }
        }

        if(regions && regions.length){
            _this.mapsvg.highlightRegions(regions);

            regions.forEach(function(region){
                if(region && !region.disabled){
                    _this.mapsvg.getData().options.mouseOver && _this.mapsvg.getData().options.mouseOver.call(region, e, _this);
                }
            });
        }
        if(marker){
            _this.mapsvg.highlightMarker(marker);
            if(_this.mapsvg.options.actions.directoryItem.hover.centerOnMarker){
                _this.mapsvg.centerOn(marker);
            }
        }
        _this.events['mouseover'] && _this.events['mouseover'].call($(this), e, eventObject, _this.mapsvg);
    }).on('mouseout.menu.mapsvg',  '.mapsvg-directory-item', function (e) {

        var objID = $(this).data('object-id');
        var regions;
        var detailsViewObject;
        var eventObject;
        var marker;

        if(_this.mapsvg.getData().options.menu.source == 'regions'){
            regions = [_this.mapsvg.getRegion(objID)];
            eventObject = regions[0];
            detailsViewObject = regions[0];
        } else {
            detailsViewObject = _this.database.getLoadedObject(objID);
            eventObject = detailsViewObject;
            if(detailsViewObject.regions){
                regions = detailsViewObject.regions.map(function(region){
                    return _this.mapsvg.getRegion(region.id);
                });
            }
            if(detailsViewObject.location){
                marker = detailsViewObject.location.marker;
            }
        }

        if(regions && regions.length){
            _this.mapsvg.unhighlightRegions(regions);
            regions.forEach(function(region){
                if(region && !region.disabled){
                    _this.mapsvg.getData().options.mouseOut && _this.mapsvg.getData().options.mouseOut.call(region, e, _this);
                }
            });
        }
        if(marker){
            _this.mapsvg.unhighlightMarker(marker);
        }


        _this.events['mouseout'] && _this.events['mouseout'].call($(this), e, eventObject, _this.mapsvg);

    });

    this.contentView.on('click','.mapsvg-category-item',function(){


        var panel = $(this).next('.mapsvg-category-block');

        if (panel[0].style.maxHeight || panel.hasClass('active')) {
            panel[0].style.maxHeight = null;
        } else {
            panel[0].style.maxHeight = panel[0].scrollHeight + "px";
        }

        if($(this).hasClass('active')){
            $(this).toggleClass('active', false);
            $(this).next('.mapsvg-category-block').addClass('collapsed').removeClass('active');
        } else {
            if(_this.mapsvg.options.menu.categories.collapseOther){
                $(this).parent().find('.mapsvg-category-item.active').removeClass('active');
                $(this).parent().find('.mapsvg-category-block.active').removeClass('active').addClass('collapsed');
            }
            $(this).toggleClass('active', true);
            $(this).next('.mapsvg-category-block').removeClass('collapsed').addClass('active');
        }

        var panels = $('.mapsvg-category-block.collapsed');
        panels.each(function(i,panel){
            panel.style.maxHeight = null;
        });

    });


};

/**
 * Highlights directory items
 * @param {array} ids - A list of object IDs
 */
MapSVG.DirectoryController.prototype.highlightItems = function(ids){
    var _this = this;
    if(typeof ids != 'object')
        ids = [ids];
    ids.forEach(function(id){
        _this.view.find('#mapsvg-directory-item-'+id).addClass('hover');
    });
};

/**
 * Unhighlights directory items
 */
MapSVG.DirectoryController.prototype.unhighlightItems = function(){
    this.view.find('.mapsvg-directory-item').removeClass('hover');
};

/**
 * Highlights directory items
 * @param {array} ids - A list of object IDs
 */
MapSVG.DirectoryController.prototype.selectItems = function(ids){
    var _this = this;
    if(typeof ids != 'object')
        ids = [ids];
    ids.forEach(function(id){
        _this.view.find('#mapsvg-directory-item-'+id).addClass('selected');
    });

    _this.scrollable && _this.contentWrap.nanoScroller({scrollTo: _this.view.find('#mapsvg-directory-item-'+ids[0])});
};

/**
 * Deselects directory items
 */
MapSVG.DirectoryController.prototype.deselectItems = function(){
    this.view.find('.mapsvg-directory-item').removeClass('selected');
};

/**
 * Remove items
 */
MapSVG.DirectoryController.prototype.removeItems = function(ids){
    this.view.find('#mapsvg-directory-item-'+ids).remove();
};

/**
 * @deprecated
 */
MapSVG.DirectoryController.prototype.addFilter = function(field){
    // var schema = this.database.getSchema();
};

/**
 * Filter out directory items
 */
MapSVG.DirectoryController.prototype.filterOut = function(items){

    var _this = this;

    if(this.database.table == 'regions'){

        var f = {};
        if(_this.mapsvg.getData().options.menu.filterout.field){
            f.field = _this.mapsvg.getData().options.menu.filterout.field;
            f.val   = _this.mapsvg.getData().options.menu.filterout.val;
        }

        items = items.filter(function(item){
            var ok = true;
            var status = _this.mapsvg.getData().options.regionStatuses;
            if(status[item.status]){
                ok = !status[item.status].disabled;
            }

            if(ok && f.field){
                ok = (item[f.field] != f.val);
            }
            return ok;
        });
    }

    return items;
}
/**
 * Loads items from a source defined in settings  to the directory. The source can be "Regions" or "Database".
 */
MapSVG.DirectoryController.prototype.loadItemsToDirectory = function(){

    var items;
    var _this = this;

    if(!_this.database.loaded) return false;

    // If "categories" option is enabled, then:
    if(_this.mapsvg.options.menu.categories && _this.mapsvg.options.menu.categories.on && _this.mapsvg.options.menu.categories.groupBy){
        // Get category field to group objects by
        var categoryField = _this.mapsvg.options.menu.categories.groupBy;
        // Get the list of categories
        if(_this.database.getSchemaField(categoryField) === undefined ||_this.database.getSchemaField(categoryField).options === undefined){
            return false;
        }
        var categories = _this.database.getSchemaField(categoryField).options;
        // Get the list of items for every category
        items = categories.map(function(category){
            var dbItems = _this.database.getLoaded();
            dbItems = _this.filterOut(dbItems);
            var catItems = dbItems.filter(function(object){
                if(categoryField === 'regions'){
                    var objectRegions = (typeof object[categoryField] !== 'undefined' && object[categoryField].length) ? object[categoryField] : [];
                    var objectRegionIDs = objectRegions.map(function(region){
                        return region.id;
                    });
                    return objectRegionIDs.indexOf(category.id) !== -1;
                } else {
                    return object[categoryField] == category.value;
                }
            });
            category.counter = catItems.length;
            if(categoryField === 'regions'){
                category.label = category.title;
                category.value = category.id;
            }

            return {category: category, items: catItems};
        });
        // Filter out empty categories, if needed:
        if(_this.mapsvg.options.menu.categories.hideEmpty){
            items = items.filter(function(item){
               return item.category.counter > 0;
            });
        }
    // If categories are not enabled then just get the list of DB items:
    } else {
        items = this.database.getLoaded();
    }

    try{
        this.contentView.html( this.templates.main({'items': items}) );
    }catch (err) {
        console.error('MapSVG: Error in the "Directory item" template');
        console.error(err);
    }
    if(items.length == 0){
        this.contentView.html('<div class="mapsvg-no-results">'+this.mapsvg.getData().options.menu.noResultsText+'</div>');
    }

    if(_this.mapsvg.options.menu.categories.on){
        if(_this.mapsvg.options.menu.categories.collapse && items.length > 1){
            this.contentView.find('.mapsvg-category-block').addClass('collapsed');
        } else if(_this.mapsvg.options.menu.categories.collapse && items.length === 1){
            this.contentView.find('.mapsvg-category-item').addClass('active');
            this.contentView.find('.mapsvg-category-block').addClass('active');
            var panel = this.contentView.find('.mapsvg-category-block');
            if(panel.length > 0){
                panel[0].style.maxHeight = panel.scrollHeight + "px";
            }
        } else if(!_this.mapsvg.options.menu.categories.collapse){
            this.contentView.find('.mapsvg-category-item').addClass('active');
            this.contentView.find('.mapsvg-category-block').addClass('active');
            var panels = this.contentView.find('.mapsvg-category-block');
            panels.each(function(i,panel){
                panel.style.maxHeight = panel.scrollHeight + "px";
            });
        }
    }
    this.setFilters();
    this.updateScroll();
};
MapSVG.DirectoryController.prototype.getRegion = function(id){
    var _this = this;
    var region;
    if(_this.mapsvg.getData().options.menu.source == 'regions'){
        region = _this.mapsvg.getRegion(id);
    }else{
        var obj = _this.database.getLoadedObject(id);
        if(obj.region_id)
            region = _this.mapsvg.getRegion(obj.region_id);
    }
    return region;
};

/**
 * @deprecated
 */
MapSVG.DirectoryController.prototype.setFilters = function(){
    var _this = this;

    /*
    var filters = this.toolbarView.find('.mapsvg-directory-filter-wrap');
    this.toolbarView.find('.mapsvg-filter-tag').remove();


    if(this.filterButton){
    }

    if(!_this.formBuilder && _this.mapsvg.getData().options.filters && _this.mapsvg.getData().options.filters.on){


        if(this.mapsvg.getData().options.filters.hide){

            if(!this.filterButton){
                this.filterButton = $('<div class="mapsvg-show-filters"><button class="btn">'
                    +this.mapsvg.getData().options.filters.buttonText
                    +'</button></div>');
                this.toolbarView.find('.mapsvg-directory-filter-wrap').empty().append(this.filterButton);
                this.filterButton.on('click',function(){
                    _this.mapsvg.loadFiltersView();
                });
            }

            var filtersCounter = Object.keys(_this.database.query.filters).length;
            filtersCounter = filtersCounter > 0 ? filtersCounter : '';
            this.filterButton.find('button').html(this.mapsvg.getData().options.filters.buttonText+' <b>'+filtersCounter+'</b>');

            return;
        }

        _this.formBuilder = new MapSVG.FormBuilder({
            container: _this.view.find('.mapsvg-directory-filter-wrap'),
            filtersMode: true,
            schema: _this.mapsvg.filtersSchema.getSchema(),
            editMode: false,
            mapsvg: _this.mapsvg,
            // mediaUploader: mediaUploader,
            // data: _dataRecord,
            admin: _this.admin,
            events: {
                // save: function(data){_this.saveDataObject(data); },
                // update:  function(data){ _this.updateDataObject(data); },
                // close: function(){ _this.closeFormHandler(); },
                // load: function(){_this.updateScroll(); }
            }
        });

        // _this.formBuilder.view.find('.select2').select2().on('select2:select',function(){
        //
        // });
        _this.formBuilder.view.on('change','select,input[type="radio"],input',function(){
            var filter = {};
            var field = $(this).data('parameter-name');

            if($(this).attr('name') === 'distanceAddress'){
                return;
            }
            if($(this).attr('name') === 'distanceLatLng' || $(this).attr('name') === 'distanceLength'){
                var distanceData = {
                    units: _this.formBuilder.view.find('[name="distanceUnits"]').val(),
                    latlng: _this.formBuilder.view.find('[name="distanceLatLng"]').val(),
                    length: _this.formBuilder.view.find('[name="distanceLength"]').val()
                    // ,
                    // address: _this.formBuilder.view.find('[name="distanceAddress"]').val()
                };
                if(distanceData.units && distanceData.length && distanceData.latlng){
                    filter.distance = distanceData;
                } else {
                  return;
                }
            } else {
                filter[field] = $(this).val();
            }


            _this.database.query.setFilters(filter);

            // _this.formBuilder.view.find('select,input[type="radio"]').each(function(index){
            //     var field = $(this).data('parameter-name');
            //     var val = $(this).val();
            //     filters[field] = val;
            // });
            _this.database.getAll(filter);
        });
        setTimeout(
            function(){
                _this.updateTopShift();
                _this.updateScroll();
            }, 200);
    }

    if(_this.mapsvg.getData().options.filters && _this.mapsvg.getData().options.filters.on || ( _this.database.query.filters && Object.keys(_this.database.query.filters).length > 0)){

        for(var field_name in _this.database.query.filters){
            var field_value = _this.database.query.filters[field_name];
            var _field_name = field_name;
            var filterField = _this.mapsvg.filtersSchema.getField(_field_name);

            if(_this.mapsvg.getData().options.filters.on && filterField){
                filters.find('[data-parameter-name="'+_field_name+'"]').val(field_value);
            }else{
                if(field_name == 'regions'){
                    // check if there is such filter. If there is then change its value
                    // if there isn't then add a tag with close button
                    _field_name = '';
                    field_value = _this.mapsvg.getRegion(field_value).title || field_value;
                }

                if(field_name !== 'distance'){
                    filters.append('<div class="mapsvg-filter-tag">'+(_field_name?_field_name+': ':'')+field_value+' <span class="mapsvg-filter-delete" data-filter="'+field_name+'">×</span></div>');
                }
            }
        }
        this.view.addClass('mapsvg-with-filter');

    }else{
        this.view.removeClass('mapsvg-with-filter');
    }
    */
    this.updateTopShift();
};

/**
 * Toggles view between map and directory on mobile devices
 * @param {bool} on - If "true", directory is shown and map is hidden, and vice-versa.
 */
MapSVG.DirectoryController.prototype.toggle = function(on){
    var _this = this;
    if(on){
        this.container.parent().show();
        _this.mapsvg.getData().$mapContainer.hide();
        // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-map', true);
        // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-directory', ƒalse);
        this.menuBtn.addClass('active');
        this.mapBtn.removeClass('active');
        // redraw?
    }else{
        this.container.parent().hide();
        _this.mapsvg.getData().$mapContainer.show();
        // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-map', false);
        // _this.mapsvg.getData().$wrapAll.toggleClass('mapsvg-mobile-show-directory', true);
        this.menuBtn.removeClass('active');
        this.mapBtn.addClass('active');
    }

    if(!this.container.parent().is(':visible')){
        if(MapSVG.isPhone){
            _this.mapsvg.getData().$wrap.css('height','auto');
            _this.updateScroll();
        }
    }else{
        if(MapSVG.isPhone && this.container.height() < parseInt(this.mapsvg.getData().options.menu.minHeight)){
            _this.mapsvg.getData().$wrap.css('height',parseInt(this.mapsvg.getData().options.menu.minHeight)+'px');
            _this.updateScroll();
        }
    }

    this.updateTopShift();
};

/**
 * Adds pagination buttons to the directory.
 * @param {string} pager - HTML string with the buttons
 */
MapSVG.DirectoryController.prototype.addPagination = function(pager){
    this.contentView.append('<div class="mapsvg-pagination-container"></div>');
    this.contentView.find('.mapsvg-pagination-container').html(pager);
};

})( jQuery );